<?php echo h(Configure::read('Account.name')); ?>

<?php echo h(Configure::read('Account.address')); ?>


<?php echo h($entrytype['Entrytype']['name']) . ' ' . __d('webzash', 'Entry'); ?>


<?php echo __d('webzash', 'Number') . ' : ' . h($this->Generic->showEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])); ?>


<?php echo __d('webzash', 'Date') . ' : ' . h(dateFromSql($entry['Entry']['date'])); ?>


<?php
	foreach ($entryitems as $row => $entryitem) {
		if ($entryitem['dc'] == 'D') {
			echo 'Dr ';
		} else {
			echo 'Cr ';
		}

		echo h($this->Generic->getLedgerName($entryitem['ledger_id']));

		echo ' - ';

		if ($entryitem['dc'] == 'D') {
			echo h($entryitem['dr_amount']);
		} else {
			echo h($entryitem['cr_amount']);
		}
?>

<?php

	}
?>

<?php
	/* Total */
	echo __d('webzash', 'Total') . ' : ' . h(toCurrency('D', $entry['Entry']['dr_total'])) . ' & ' . h(toCurrency('C', $entry['Entry']['cr_total']));
?>

<?php
	/* Difference */
	if (calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '==')) {
		/* Do nothing */
	} else {
		if (calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '>')) {
			echo __d('webzash', 'Difference') . h(toCurrency('D', calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '-')));
		} else {
			echo __d('webzash', 'Difference') . h(toCurrency('C', calculate($entry['Entry']['cr_total'], $entry['Entry']['dr_total'], '-')));
		}
	}
?>

<?php echo __d('webzash', 'Narration') . ' : ' . h($entry['Entry']['narration']); ?>
