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

	var entryId = 0;
	$("button#send").click(function() {
		$(".modal-body").hide();
		$(".modal-footer").hide();
		$(".modal-ajax").show();
		$.ajax({
			type: "POST",
			url: '<?php echo $this->Html->url(array("controller" => "entries", "action" => "email")); ?>/' + entryId,
			data: $('form#emailSubmit').serialize(),
				success: function(response) {
					msg = JSON.parse(response); console.log(msg);
					if (msg['status'] == 'success') {
						$(".modal-error-msg").html("");
						$(".modal-error-msg").hide();
						$(".modal-body").show();
						$(".modal-footer").show();
						$(".modal-ajax").hide();
						$("#emailModal").modal('hide');
					} else {
						$(".modal-error-msg").html(msg['msg']);
						$(".modal-error-msg").show();
						$(".modal-body").show();
						$(".modal-footer").show();
						$(".modal-ajax").hide();
					}
				},
				error: function() {
					$(".modal-error-msg").html("Error sending email.");
					$(".error-msg").show();
					$(".modal-body").show();
					$(".modal-footer").show();
					$(".modal-ajax").hide();
				}
		});
	});

	$('#emailModal').on('show.bs.modal', function(e) {
		$(".modal-error-msg").html("");
		$(".modal-ajax").hide();
		$(".modal-error-msg").hide();
		entryId = $(e.relatedTarget).data('id');
		var entryType = $(e.relatedTarget).data('type');
		var entryNumber = $(e.relatedTarget).data('number');
		$("#emailModelType").html(entryType);
		$("#emailModelNumber").html(entryNumber);
	});
});

</script>

<div>
<?php
	echo __d('webzash', 'Number') . ' : ' . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id']));
	echo '<br /><br />';
	echo __d('webzash', 'Date') . ' : ' . h(dateFromSql($entry['Entry']['date']));
	echo '<br /><br />';

	echo '<table class="stripped">';

	/* Header */
	echo '<tr>';
	if ($this->Session->read('Wzsetting.drcr_toby') == 'toby') {
		echo '<th>' . __d('webzash', 'To/By') . '</th>';
	} else {
		echo '<th>' . __d('webzash', 'Dr/Cr') . '</th>';
	}
	echo '<th>' . __d('webzash', 'Ledger') . '</th>';
	echo '<th>' . __d('webzash', 'Dr Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '</th>';
	echo '<th>' . __d('webzash', 'Cr Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '</th>';
	echo '</tr>';

	/* Intial rows */
	foreach ($curEntryitems as $row => $entryitem) {
		echo '<tr>';

		echo '<td>';
		if ($this->Session->read('Wzsetting.drcr_toby') == 'toby') {
			if ($entryitem['dc'] == 'D') {
				echo 'By';
			} else {
				echo 'To';
			}
		} else {
			if ($entryitem['dc'] == 'D') {
				echo 'Dr';
			} else {
				echo 'Cr';
			}
		}
		echo '</td>';

		echo '<td>';
		echo h($entryitem['ledger_name']);
		echo '</td>';

		echo '<td>';
		if ($entryitem['dc'] == 'D') {
			echo $entryitem['dr_amount'];
		} else {
			echo '';
		}
		echo '</td>';

		echo '<td>';
		if ($entryitem['dc'] == 'C') {
			echo $entryitem['cr_amount'];
		} else {
			echo '';
		}
		echo '</td>';
		echo '</tr>';
	}

	/* Total */
	echo '<tr class="bold-text">' . '<td></td>' . '<td>' . __d('webzash', 'Total') . '</td>' . '<td id="dr-total">' . toCurrency('D', $entry['Entry']['dr_total']) . '</td>' . '<td id="cr-total">' . toCurrency('C', $entry['Entry']['cr_total']) . '</td>' . '</tr>';

	/* Difference */
	if (calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '==')) {
		/* Do nothing */
	} else {
		if (calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '>')) {
			echo '<tr class="error-text">' . '<td></td>' . '<td>' . __d('webzash', 'Difference') . '</td>' . '<td id="dr-diff">' . toCurrency('D', calculate($entry['Entry']['dr_total'], $entry['Entry']['cr_total'], '-')) . '</td>' . '<td></td>' . '</tr>';
		} else {
			echo '<tr class="error-text">' . '<td></td>' . '<td>' . __d('webzash', 'Difference') . '</td>' . '<td></td>' . '<td id="cr-diff">' . toCurrency('C', calculate($entry['Entry']['cr_total'], $entry['Entry']['dr_total'], '-')) . '</td>' . '</tr>';

		}
	}

	echo '</table>';

	echo '<br />';

	echo __d('webzash', 'Narration') . ' : ' . h($entry['Entry']['narration']);
	echo '<br /><br />';
	echo __d('webzash', 'Tag') . ' : ' . $this->Generic->showTag($entry['Entry']['tag_id']);

	echo '<br /><br />';

	/* Edit */
	echo $this->Html->link(__d('webzash', 'Edit'), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'edit', $entrytype['Entrytype']['label'], $entry['Entry']['id']), array('class' => 'btn btn-primary'));

	echo $this->Html->tag('span', '', array('class' => 'link-pad'));

	/* Delete */
	echo $this->Form->postLink(__d('webzash', 'Delete'), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'delete', $entrytype['Entrytype']['label'], $entry['Entry']['id']), array('class' => 'btn btn-primary', 'confirm' => __d('webzash', 'Are you sure ?')));

	echo $this->Html->tag('span', '', array('class' => 'link-pad'));

	/* Email */
	echo '<a href="#" data-toggle="modal" data-id="' . $entry['Entry']['id'] . '" data-type="' . h($entrytype['Entrytype']['name']) . '" data-number="' . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])) . '" data-target="#emailModal">' . $this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-envelope')) . '</a>';

	echo $this->Html->tag('span', '', array('class' => 'link-pad'));

	/* Download */
	echo $this->Html->link($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-download-alt')), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'download', $entry['Entry']['id']), array('class' => 'no-hover', 'escape' => false));

	echo $this->Html->tag('span', '', array('class' => 'link-pad'));

	/* Print */
	echo $this->Html->link($this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-print')), '', array('escape' => false, 'onClick' => "window.open('" . $this->Html->url(array('controller' => 'entries', 'action' => 'printpreview', $entry['Entry']['id'])) . "', 'windowname','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=600,height=600'); return false;"));

	echo $this->Html->tag('span', '', array('class' => 'link-pad'));

	/* Cancel */
	echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'), array('class' => 'btn btn-default'));
?>
</div>

<!-- email modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="emailSubmit" name="emailSubmit">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
				<h4 class="modal-title" id="myModalLabel">Email <span id="emailModelType"></span> Entry Number "<span id="emailModelNumber"></span>"</h4>
			</div>
			<div class="modal-error-msg"></div>
			<div class="modal-body">
				<?php echo $this->Form->input('email', array('type' => 'email', 'label' => __d('webzash', 'Email to'), 'class' => 'form-control')); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="send">Send</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			<div class="modal-ajax">Please wait, sending email...</div>
			</form>
		</div>
	</div>
</div>
