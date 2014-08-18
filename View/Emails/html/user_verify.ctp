<p>Hi <?php echo $fullname; ?>,</p>

<p>Your account has been verified.</p>

<p>You can login <a href="<?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'login'), true); ?>">here</a>.</p>

<p>--  <?php echo $sitename; ?> team</p>
