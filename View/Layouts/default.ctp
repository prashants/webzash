<?php
/**
 *
 * PHP 5
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
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo Configure::read('Webzash.AppName') . ' | ' . $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('favicon.ico',
			$this->Html->url('/' . 'webzash/img/favicon.ico', true),
			array('type' => 'icon')
		);

		echo $this->Html->script('Webzash.jquery-1.10.2.js');

		echo $this->Html->css('Webzash.jquery-ui.css');
		echo $this->Html->css('Webzash.jquery-ui.structure.css');
		echo $this->Html->css('Webzash.jquery-ui.theme.css');
		echo $this->Html->script('Webzash.jquery-1.11.0.ui.js');

		echo $this->Html->css('Webzash.bootstrap.min.css');
		echo $this->Html->script('Webzash.bootstrap.min.js');

		echo $this->Html->script('Webzash.mousetrap.min.js');
		echo $this->element('shortcuts');

		echo $this->Html->css('Webzash.select2.min.css');
		echo $this->Html->script('Webzash.select2.min.js');

		echo $this->Html->css('Webzash.custom.css?'.time());

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php echo $this->element('defaultnavbar'); ?>
		</div>
		<div id="page-info" class="pull-right">
			<?php echo $this->element('accountinfo'); ?>
		</div>
		<div id="page-title">
			<?php echo h($title_for_layout); ?>
		</div>
		<div id="content">
			<?php echo $this->element('actionlinks'); ?>

			<?php echo $this->Session->flash(); ?>
			<?php if ($this->Session->read('emailError') == true) { echo $this->element('emailerror'); } ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<div class="kb-shorcuts">
				<ul>
					<li>alt + a<span><?php echo __d('webzash', 'Chart of Accounts'); ?></span></li>
					<li>alt + e<span><?php echo __d('webzash', 'All Entries'); ?></span></li>
					<li>alt + l<span><?php echo __d('webzash', 'Add Ledger'); ?></span></li>
					<li>alt + r<span><?php echo __d('webzash', 'Add Receipt'); ?></span></li>
					<li>alt + p<span><?php echo __d('webzash', 'Add Payment'); ?></span></li>
					<li>alt + c<span><?php echo __d('webzash', 'Add Contra'); ?></span></li>
					<li>alt + j<span><?php echo __d('webzash', 'Add Journal'); ?></span></li>
				</ul>
			</div>
			<div class="credits">
					Powered by
				<?php echo $this->Html->link(
					Configure::read('Webzash.AppName') . ' v' . Configure::read('Webzash.AppVersion'),
					Configure::read('Webzash.AppURL'),
					array('class' => 'footer-power', 'target' => '_blank')
				); ?>
			</div>
		</div>
	</div>
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
