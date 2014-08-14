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
<div class="wzuser add form">
	<?php
		if ($registration) {
			echo $this->Form->create('Wzuser');
			echo $this->Form->input('username', array('label' => __d('webzash', 'Username')));
			echo $this->Form->input('password', array('label' => __d('webzash', 'Password')));
			echo $this->Form->input('fullname', array('label' => __d('webzash', 'Fullname')));
			echo $this->Form->input('email', array('type' => 'email', 'label' => __d('webzash', 'Email')));
			echo $this->Form->end(__d('webzash', 'Submit'));
			echo $this->Html->link(__d('webzash', 'Login'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		} else {
			echo '<h4>' . __d('webzash', 'User registration is disabled.') . '</h4>';
		}
	?>
</div>