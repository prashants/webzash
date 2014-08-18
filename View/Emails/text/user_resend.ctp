Hi <?php echo $fullname; ?>,

You need to verify your email address. Please click <?php echo $this->Html->url(array('controller' => 'wzusers', 'action' => 'verify'), true) . '?u=' . $username . '&k=' . $verification_key; ?> to verify your email.

--  <?php echo $sitename; ?> team
