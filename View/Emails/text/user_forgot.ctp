Hi <?php echo $fullname; ?>,

Your login details are given below :

Username : <?php echo $username; ?>

Password : <?php echo $password; ?>


Login URL : <?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'login'), true); ?>


Please change your password immediatly after login.

--  <?php echo $sitename; ?> team
