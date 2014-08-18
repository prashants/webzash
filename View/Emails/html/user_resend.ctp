<p>Hi <?php echo $fullname; ?>,</p>

<p>You need to verify your email address. Please click <a href="<?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify', '?' => array('u' => $username, 'k' => $verification_key)), true); ?>">here</a> to verify your email. If you cannot see the link please copy and paste the below link in your browser to verify your email.</p>

<p><?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify', '?' => array('u' => $username, 'k' => $verification_key)), true); ?></p>

<p>--  <?php echo $sitename; ?> team</p>
