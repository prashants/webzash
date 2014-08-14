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
		<th><?php echo $this->Paginator->sort('label', __d('webzash', 'Label')); ?></th>
		<th><?php echo $this->Paginator->sort('name', __d('webzash', 'Name')); ?></th>
		<th><?php echo $this->Paginator->sort('description', __d('webzash', 'Description')); ?></th>
		<th><?php echo $this->Paginator->sort('prefix', __d('webzash', 'Prefix')); ?></th>
		<th><?php echo $this->Paginator->sort('suffix', __d('webzash', 'Suffix')); ?></th>
		<th><?php echo $this->Paginator->sort('zero_padding', __d('webzash', 'Zero Padding')); ?></th>
		<th><?php echo __d('webzash', 'Actions'); ?></th>
	</tr>
	<?php foreach ($entrytypes as $entrytype) { ?>
		<tr>
			<td><?php echo h($entrytype['Entrytype']['label']); ?></td>
			<td><?php echo h($entrytype['Entrytype']['name']); ?></td>
			<td><?php echo h($entrytype['Entrytype']['description']); ?></td>
			<td><?php echo h($entrytype['Entrytype']['prefix']); ?></td>
			<td><?php echo h($entrytype['Entrytype']['suffix']); ?></td>
			<td><?php echo h($entrytype['Entrytype']['zero_padding']); ?></td>
			<td>
				<?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-edit')) . __d('webzash', ' Edit'), array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'edit', $entrytype['Entrytype']['id']), array('class' => 'no-hover', 'escape' => false)) . '<span class="link-pad"></span>'; ?>
				<?php echo $this->Form->postLink($this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-trash')) . __d('webzash', ' Delete'), array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'delete', $entrytype['Entrytype']['id']), array('class' => 'no-hover', 'escape' => false, 'confirm' => __d('webzash', 'Are you sure you want to delete the entry type?'))); ?>
			</td>
		</tr>
	<?php } ?>
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
	echo ' ' . __d('webzash', 'Entrytypes') . ' ' . $this->Paginator->counter();
	echo "</div>";
