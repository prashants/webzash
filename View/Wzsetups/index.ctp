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
<div>
	<?php echo '<div id="page-title-second">' . __d('webzash', 'Please click on the desired option below') . '</div>'; ?>
</div>

<div class="wzinstall">
	<?php echo $this->Html->link(__d('webzash', 'Install %s', Configure::read('Webzash.AppName')
			. ' v' . Configure::read('Webzash.AppVersion')), array('plugin' => 'webzash', 'controller' => 'wzsetups', 'action' => 'install'), array('class' => 'btn btn-primary btn-lg')); ?>
	<br /><br />
	<?php echo $this->Html->link(__d('webzash', 'Upgrade from 2.x'), array('plugin' => 'webzash', 'controller' => 'wzsetups', 'action' => 'upgrade'), array('class' => 'btn btn-info btn-lg')); ?>
</div>
