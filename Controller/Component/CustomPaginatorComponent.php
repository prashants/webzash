<?php
/**
 * Paginator Component
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This component extends the CakePHP core PaginatorComponent while over-riding
 * the paginate() method to fix a bug. See below for more details regarding
 * the bug.
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
 * The MIT License (MIT)
 */

App::uses('PaginatorComponent', 'Controller');

class CustomPaginatorComponent extends PaginatorComponent {

	public function paginate($object = null, $scope = array(), $whitelist = array()) {
		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}

		$object = $this->_getObject($object);

		if (!is_object($object)) {
			throw new MissingModelException($object);
		}

		$options = $this->mergeOptions($object->alias);
		$options = $this->validateSort($object, $options, $whitelist);
		$options = $this->checkLimit($options);

		$conditions = $fields = $order = $limit = $page = $recursive = null;

		if (!isset($options['conditions'])) {
			$options['conditions'] = array();
		}

		$type = 'all';

		if (isset($options[0])) {
			$type = $options[0];
			unset($options[0]);
		}

		extract($options);

		if (is_array($scope) && !empty($scope)) {
			$conditions = array_merge($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		if ($recursive === null) {
			$recursive = $object->recursive;
		}

		$extra = array_diff_key($options, compact(
			'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
		));

		if (!empty($extra['findType'])) {
			$type = $extra['findType'];
			unset($extra['findType']);
		}

		if ($type !== 'all') {
			$extra['type'] = $type;
		}

		if ((int)$page < 1) {
			$page = 1;
		}
		$page = $options['page'] = (int)$page;

		if ($object->hasMethod('paginate')) {
			$results = $object->paginate(
				$conditions, $fields, $order, $limit, $page, $recursive, $extra
			);
		} else {
			$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			/***********************************************************************************/
			/****************************** CUSTOMIZED BY WEBZASH ******************************/
			/***********************************************************************************/

			/**
			 * This fix is related to bug reported at
			 * https://groups.google.com/forum/#!topic/webzash-help/A6fpPwOzHfA
			 * MySQL seems to mess up the results when its at last page of pagination.
			 * This is due to the limit clause being eg : 30, 10 if there are total 36 rows.
			 * Changing the limit clause at the last page to 30, 6 to match the number of exact
			 * number of rows remaining fixes the problem.
			 */
			$temp_params = $parameters;
			{
				$temp_parameters = compact('conditions');
				if ($recursive != $object->recursive) {
					$temp_parameters['recursive'] = $recursive;
				}
				$temp_count = $object->find('count', array_merge($temp_parameters, $extra));
				$temp_pageCount = (int)ceil($temp_count / $limit);
				$temp_requestedPage = $page;
				$temp_page = max(min($page, $temp_pageCount), 1);

				/* If last page, then remove the page parameter and set the limit and offset parameters */
				if ($temp_pageCount == $temp_requestedPage) {
					$temp_params['offset'] = ($temp_requestedPage - 1) * $temp_params['limit'];
					$temp_params['limit'] = ($temp_count - $temp_params['offset']);
					unset($temp_params['page']);
				}
			}
			$results = $object->find($type, array_merge($temp_params, $extra));
		}
		$defaults = $this->getDefaults($object->alias);
		unset($defaults[0]);

		if (!$results) {
			$count = 0;
		} elseif ($object->hasMethod('paginateCount')) {
			$count = $object->paginateCount($conditions, $recursive, $extra);
		} elseif ($page === 1 && count($results) < $limit) {
			$count = count($results);
		} else {
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		}
		$pageCount = (int)ceil($count / $limit);
		$requestedPage = $page;
		$page = max(min($page, $pageCount), 1);

		$paging = array(
			'page' => $page,
			'current' => count($results),
			'count' => $count,
			'prevPage' => ($page > 1),
			'nextPage' => ($count > ($page * $limit)),
			'pageCount' => $pageCount,
			'order' => $order,
			'limit' => $limit,
			'options' => Hash::diff($options, $defaults),
			'paramType' => $options['paramType']
		);

		if (!isset($this->Controller->request['paging'])) {
			$this->Controller->request['paging'] = array();
		}
		$this->Controller->request['paging'] = array_merge(
			(array)$this->Controller->request['paging'],
			array($object->alias => $paging)
		);

		if ($requestedPage > $page) {
			throw new NotFoundException();
		}

		if (
			!in_array('Paginator', $this->Controller->helpers) &&
			!array_key_exists('Paginator', $this->Controller->helpers)
		) {
			$this->Controller->helpers[] = 'Paginator';
		}
		return $results;
	}

}
