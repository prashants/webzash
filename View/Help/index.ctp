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

<ul id="qa-list">
	<li><span class="qa-heading">General</span>
		<ul>
		<li><a href="#general-1" class="anchor-link-a">How to enable / disable logging ?</a></li>
		<li><a href="#general-2" class="anchor-link-a">What is account lock and how do to enable / disable it ?</a></li>
		<li><a href="#general-3" class="anchor-link-a">How to fix the "PHP BC Math library is missing" error ?</a></li>
		</ul>
	</li>
	<li><span class="qa-heading">Printing</span>
		<ul>
		<li><a href="#print-1" class="anchor-link-a">How to modify the entry print format ?</a></li>
		<li><a href="#print-2" class="anchor-link-a">How to modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</a></li>
		</ul>
	</li>
	<li><span class="qa-heading">Email</span>
		<ul>
		<li><a href="#email-1" class="anchor-link-a">How to modify the entry email format ?</a></li>
		<li><a href="#email-2" class="anchor-link-a">How to send entries using gmail ?</a></li>
		</ul>
	</li>
</ul>

<br /><br />

<div class="qa-section" id="general-1">
	<a name="general-1"></a>
	<div class="qa-question">Q. How to enable / disable logging ?</div>
	<div class="qa-answer"></div>
</div>

<div class="qa-section" id="general-2">
	<a name="general-2"></a>
	<div class="qa-question">Q. What is account lock and how do to enable / disable it ?</div>
	<div class="qa-answer">Once a account is locked it cannot be modified any further, it becomes read-only. Click on "Settings" in Main Menu and then select "Lock account". You need to check / uncheck the option called "Account Locked" to enable or disable the account lock respectively.<br /><br />Note: If account is locked you can see 'Status : Locked' in the 'Account details' section of the Dashboard.</div>
</div>

<div class="qa-section" id="general-3">
	<a name="general-3"></a>
	<div class="qa-question">Q. How to fix the "PHP BC Math library is missing" error ?</div>
	<div class="qa-answer">You need to have <?php echo $this->Html->link('PHP BC Math library', 'http://php.net/manual/en/book.bc.php', array('target' => '_blank')); ?> to perform all calculations. Search for "php bcmath install" on the internet and you will definitely find many guides for installing php bcmath library for your setup. Remember to restart the web server after installing the library.</div>
</div>

<div class="qa-section" id="print-1">
	<a name="print-1"></a>
	<div class="qa-question">Q. How to modify the entry print format ?</div>
	<div class="qa-answer">Entry print template is located at "". Modify this file to change the entry print format.</div>
</div>

<div class="qa-section" id="print-2">
	<a name="print-2"></a>
	<div class="qa-question">Q. How to modify the reports print format (balance sheet, profit and loss statement, trial balance, ledger statement) ?</div>
	<div class="qa-answer">Report print template is located at "". Modify this file to change the report print format.</div>
</div>

<div class="qa-section" id="email-1">
	<a name="email-1"></a>
	<div class="qa-question">Q. How to modify the entry email format ?</div>
	<div class="qa-answer">Entry email template is located at "". Modify this file to change the entry email format.</div>
</div>

<div class="qa-section" id="email-2">
	<a name="email-2"></a>
	<div class="qa-question">Q. How to send entries using gmail ?</div>
	<div class="qa-answer">You need to use the following gmail settings in Settings > Email Settings<br /><br />
	Email protocol : smtp<br />
	Hostname : ssl://smtp.googlemail.com<br />
	Port : 465<br />
	Email username : your-username@gmail.com<br />
	Email Password : your-password<br />
	</div>
</div>

</div>

