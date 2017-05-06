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

<script type="text/javascript">

Mousetrap.bind(['alt+r'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'entries',
                        'action' => 'add', 'receipt'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+p'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'entries',
                        'action' => 'add', 'payment'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+c'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'entries',
                        'action' => 'add', 'contra'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+j'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'entries',
                        'action' => 'add', 'journal'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+e'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'entries',
                        'action' => 'index'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+a'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'accounts',
                        'action' => 'show'
                )); ?>";
        return false;
});
Mousetrap.bind(['alt+l'], function(e) {
        location.href =
                "<?php echo $this->Html->url(array(
                        'plugin' => 'webzash',
                        'controller' => 'ledgers',
                        'action' => 'add'
                )); ?>";
        return false;
});

</script>
