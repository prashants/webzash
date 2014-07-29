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
<th><?php echo $this->Paginator->sort('date', __d('webzash', 'Date')); ?></th>
<th><?php echo $this->Paginator->sort('number', __d('webzash', 'Number')); ?></th>
<th><?php echo __d('webzash', 'Ledger'); ?></th>
<th><?php echo $this->Paginator->sort('entrytype_id', __d('webzash', 'Type')); ?></th>
<th><?php echo $this->Paginator->sort('tag_id', __d('webzash', 'Tag')); ?></th>
<th><?php echo $this->Paginator->sort('dr_total', __d('webzash', 'Debit Amount')); ?></th>
<th><?php echo $this->Paginator->sort('cr_total', __d('webzash', 'Credit Amount')); ?></th>
<th><?php echo __d('webzash', 'Actions'); ?></th>
</tr>

<?php
foreach ($entries as $entry) {
	list($entryTypeName, $entryTypeLabel) = $this->Generic->showEntrytype($entry['Entry']['entrytype_id']);
	echo '<tr>';
	echo '<td>' . dateFromSql($entry['Entry']['date']) . '</td>';
	echo '<td>' . $entry['Entry']['number']. '</td>';
	echo '<td>' . '</td>';
	echo '<td>' . $entryTypeName . '</td>';
	echo '<td>' . $this->Generic->showTag($entry['Entry']['tag_id']) . '</td>';
	echo '<td>' . $entry['Entry']['dr_total']. '</td>';
	echo '<td>' . $entry['Entry']['cr_total']. '</td>';
	echo '<td>';
	echo $this->Html->link(__d('webzash', 'Edit'), array('controller' => 'entries', 'action' => 'edit', $entryTypeLabel, $entry['Entry']['id']));
	echo ' ';
	echo $this->Form->postLink(__d('webzash', 'Delete'), array('controller' => 'entries', 'action' => 'delete', $entryTypeLabel, $entry['Entry']['id']), array('confirm' => __d('webzash', 'Are you sure ?')));
	echo '</td>';
	echo '</tr>';
}
?>
</table>

<?php
	echo "<div class='paging'>";
	echo $this->Paginator->first(__d('webzash', 'First'));
	if ($this->Paginator->hasPrev()) {
		echo $this->Paginator->prev(__d('webzash', 'Prev'));
	}
	echo $this->Paginator->numbers();
	if ($this->Paginator->hasNext()){
		echo $this->Paginator->next(__d('webzash', 'Next'));
	}
	echo $this->Paginator->last(__d('webzash', 'Last'));
	echo ' ' . __d('webzash', 'Entries') . ' ' . $this->Paginator->counter();
	echo "</div>";
