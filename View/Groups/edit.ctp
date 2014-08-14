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

<script type="text/javascript">
$(document).ready(function() {
	/**
	 * On changing the parent group select box check whether the selected value
	 * should show the "Affects Gross Profit/Loss Calculations".
	 */
	$('#GroupParentId').change(function() {
		if ($(this).val() == '3' || $(this).val() == '4') {
			$('#AffectsGross').show();
		} else {
			$('#AffectsGross').hide();
		}
	});
	$('#GroupParentId').trigger('change');
});
</script>

<div class="groups edit form">
	<?php
		$options = array(
			1 => __d('webzash', 'Affects gross profit/loss calculations'),
			0 => __d('webzash', 'Affects net profit/loss calculations'),
		);

		echo $this->Form->create('Group');
		echo $this->Form->input('name', array('label' => __d('webzash', 'Group name')));
		echo $this->Form->input('parent_id', array('type' => 'select', 'options' => $parents, 'value' => $this->data['Group']['parent_id'], 'label' => __d('webzash', 'Parent group')));
		echo $this->Form->input('affects_gross', array('type' => 'select', 'options' => $options, 'label' => false, 'div' => array('id' => 'AffectsGross')));
		echo $this->Form->end(__d('webzash', 'Submit'));
		echo $this->Html->link(__d('webzash', 'Back'), array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
	?>
</div>
