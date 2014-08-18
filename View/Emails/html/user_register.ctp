<p>Hi <?php echo $fullname; ?>,</p>

<p>Thank you for registering at <?php echo $sitename; ?>.</p>

<?php if ($email_verification == 1) : ?>

<p>You need to verify your email address. Please click <a href="<?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify', '?' => array('u' => $username, 'k' => $verification_key)), true); ?>">here</a> to verify your email. If you cannot see the link please copy and paste the below link in your browser to verify your email.</p>

<p><?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify', '?' => array('u' => $username, 'k' => $verification_key)), true); ?></p>

<?php endif; ?>

<?php if ($admin_verification == 1) : ?>

<p>Your application for an account is currently pending approval.</p>

<?php endif; ?>

<p>--  <?php echo $sitename; ?> team</p>
