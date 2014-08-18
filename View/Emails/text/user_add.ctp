Hi <?php echo $fullname; ?>,

Admin has created an account for you at <?php echo $sitename; ?>.

<?php if ($email_verification == 1) : ?>
You need to verify your email address. Please click <?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify'), true) . '?u=' . $username . '&k=' . $verification_key; ?> to verify your email.
<?php endif; ?>

<?php if ($admin_verification == 1) : ?>
Your application for an account is currently pending approval.
<?php endif; ?>

--  <?php echo $sitename; ?> team
