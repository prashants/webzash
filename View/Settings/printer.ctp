<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
?>
<div class="printer form">
	<?php
		$prefixInches = '<div class="input-group">';
		$suffixInches = '<span class="input-group-addon">' . __d('webzash', 'inches') .'</span></div>';

		echo $this->Form->create('Setting', array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control',
			),
		));
	?>
	<fieldset>
		<legend><?php echo __d('webzash', 'Paper Size'); ?></legend>
		<?php echo $this->Form->input('print_paper_height', array('label' => __d('webzash', 'Height'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
		<?php echo $this->Form->input('print_paper_width', array('label' => __d('webzash', 'Width'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __d('webzash', 'Paper Margin'); ?></legend>
		<?php echo $this->Form->input('print_margin_top', array('label' => __d('webzash', 'Top'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
		<?php echo $this->Form->input('print_margin_bottom', array('label' => __d('webzash', 'Bottom'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
		<?php echo $this->Form->input('print_margin_left', array('label' => __d('webzash', 'Left'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
		<?php echo $this->Form->input('print_margin_right', array('label' => __d('webzash', 'Right'), 'beforeInput' => $prefixInches, 'afterInput' => $suffixInches)); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __d('webzash', 'Orientation'); ?></legend>
		<?php echo $this->Form->input('print_orientation', array('type' => 'radio', 'options' => $this->Generic->printer_orientation_options(), 'legend' => false, 'class' => 'radio')); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __d('webzash', 'Output Format'); ?></legend>
		<?php echo $this->Form->input('print_page_format', array('type' => 'radio', 'options' => $this->Generic->printer_pageformat_options(), 'legend' => false, 'class' => 'radio')); ?>
	</fieldset>
	<br />
	<?php
		echo '<div class="form-group">';
		echo $this->Form->submit(__d('webzash', 'Submit'), array(
			'div' => false,
			'class' => 'btn btn-primary'
		));
		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'), array('class' => 'btn btn-default'));
		echo '</div>';

		echo $this->Form->end();
	?>
</div>
