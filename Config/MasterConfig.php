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
<?php
/**
 * Master database configuration
 *
 * Note : $root_path is set to Webzash plugin root folder
 *
 * If you want to use a MySQL database instead of SQLite then create
 * a database and run the MasterSchema.MySQL.sql script from the
 * current folder. This script will create the necessary tables. Next,
 * update the below configuration values as per the database settings.
 *
 * Read more :
 * http://book.cakephp.org/2.0/en/development/configuration.html
 *
 * Example MySQL configuration :
 *
 * $wz['datasource'] = 'Database/Mysql';
 * $wz['database'] = '';
 * $wz['host'] = '';
 * $wz['port'] = '';
 * $wz['login'] = '';
 * $wz['password'] = '';
 * $wz['prefix'] = ''; // Optional parameter
 * $wz['encoding'] = 'utf8';
 * $wz['persistent'] = false;
 * $wz['schema'] = ''; // Optional parameter
 * $wz['unixsocket'] = ''; // Optional parameter
 * $wz['settings'] = ''; // Optional parameter
 */

$wz['datasource'] = 'Database/Sqlite';
$wz['database'] = $root_path . 'Database/' . 'webzash.sqlite';
$wz['prefix'] = '';
$wz['encoding'] = 'utf8';
$wz['persistent'] = false;

?>
