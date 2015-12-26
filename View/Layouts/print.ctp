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
		Webzash | <?php echo $title_for_layout; ?>
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

		echo $this->Html->css('Webzash.custom.css?'.time());

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>
</head>
<body>
	<div id="container">
		<div id="page-title">
		</div>
		<div id="content">
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
		<div id="printer">
			<form>
			<input class="hide-print print-button" type="button" onClick="window.print()" value="Print">
			</form>
		</div>
	</div>
</body>
</html>
