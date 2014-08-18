<p>Hi <?php echo $fullname; ?>,</p>

<p>Your login details are given below :</p>

<p>Username : <?php echo $username; ?></p>
<p>Password : <?php echo $password; ?></p>

<p>Login <a href="<?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'login'), true); ?>">here</a>.</p>

<b>Please change your password immediatly after login.</b>

<p>--  <?php echo $sitename; ?> team</p>
