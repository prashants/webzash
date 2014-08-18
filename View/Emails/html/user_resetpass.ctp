<p>Hi <?php echo $fullname; ?>,</p>

<p>Admin has changed the password for your account.</p>

<p>Your Username : <?php echo $username; ?></p>
<p>New Password : <?php echo $password; ?></p>

<p>Login <a href="<?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'login'), true); ?>">here</a>.</p>

<b>Please change your password immediatly after login.</b>

<p>--  <?php echo $sitename; ?> team</p>
