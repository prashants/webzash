Hi <?php echo $fullname; ?>,

Admin has changed the password for your account.

Your Username : <?php echo $username; ?>

New Password : <?php echo $password; ?>


Login URL : <?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'login'), true); ?>


Please change your password immediatly after login.

--  <?php echo $sitename; ?> team
