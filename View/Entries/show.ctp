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
<table>

<tr>
<th><?php echo __('Date'); ?></th>
<th><?php echo __('Number'); ?></th>
<th><?php echo __('Ledger'); ?></th>
<th><?php echo __('Type'); ?></th>
<th><?php echo __('Tag'); ?></th>
<th><?php echo __('Debit Amount'); ?></th>
<th><?php echo __('Credit Amount'); ?></th>
<th><?php echo __('Actions'); ?></th>
</tr>

<?php
foreach ($entries as $entry) {
	echo '<tr>';
	echo '<td>' . $entry['Entry']['date']. '</td>';
	echo '<td>' . $entry['Entry']['number']. '</td>';
	echo '<td>' . '</td>';
	echo '<td>' . $entrytype['Entrytype']['name'] . '</td>';
	echo '<td>' . $this->Generic->showTag($entry['Entry']['tag_id']) . '</td>';
	echo '<td>' . $entry['Entry']['dr_total']. '</td>';
	echo '<td>' . $entry['Entry']['cr_total']. '</td>';
	echo '<td>';
	echo $this->Html->link(__('Edit'), array('controller' => 'entries', 'action' => 'edit', $entrytype['Entrytype']['label'], $entry['Entry']['id']));
	echo ' ';
	echo $this->Form->postLink(__('Delete'), array('controller' => 'entries', 'action' => 'delete', $entrytype['Entrytype']['label'], $entry['Entry']['id']));
	echo '</td>';
	echo '</tr>';
}
?>
</table>
