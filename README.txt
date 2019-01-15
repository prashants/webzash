Webzash - Easy to use web based double entry accounting software

Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>

Website     : http://webzash.org
Source code : https://github.com/prashants/webzash

Files included :

README.txt    - General readme file
LICENSE.txt   - License under which Webzash is distributed (MIT License)
NOTICE.txt    - Attribution notices and list of 3rd party software used
CHANGELOG.txt - Webzash changelog file

IMPORTANT NOTICE !!
===================

Webzash is developed as a CakePHP plugin, hence this repository is just the
plugin code. You will need a full CakePHP setup to use Webzash.

NOTE : The setup available from the webzash.org is the full setup that includes
everything. You dont have to do anything else. Alternatively, full setup can
also be downloaded from the releases section of github.com given below :
https://github.com/prashants/webzash/releases

Following step describes how to setup Webzash with CakePHP from scratch,
remmember these steps are only needed if you wish to develop or contribute
back to Webzash :

Step 1. Download CakePHP version 2.10.14 from the below link
https://github.com/cakephp/cakephp/archive/2.10.14.tar.gz

Step 2. Extract CakePHP into your web server directory and rename the folder
to "webzash"

Step 3. Edit the app/Config/core.php file and change the following lines

 Configure::write('debug', 2);
 Configure::write('Security.salt', 'IMPORTANT_CHANGE_THIS_VALUE');
 Configure::write('Security.cipherSeed', 'IMPORTANT_CHANGE_THIS_VALUE');
 date_default_timezone_set('UTC');

Note : Please change the above security salts before changing your password
or creating any new user.

Step 4. Edit the app/Config/bootstrap.php file and add the following lines

 CakePlugin::load('BoostCake');
 CakePlugin::load('Webzash', array('routes' => true, 'bootstrap' => true));

Step 5. Edit the app/Config/routes.php file and comment out the default route
on line no. 27 and 31 by prepending two forward slashes as shown below

 // Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

 // Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

Step 6. Copy the app/Config/database.php.default file to app/Config/database.php

Step 7. Install BoostCake plugin

 $git clone https://github.com/slywalker/cakephp-plugin-boost_cake.git app/Plugin/BoostCake

Step 8. Install Webzash plugin

 $git clone https://github.com/prashants/webzash.git app/Plugin/Webzash

Thats it ! You are done :)

The full source code for Webzash is in the app/Plugin/Webzash folder.

Note :

Backup copy of the CakePHP and BoostCake repository is available at :
https://github.com/prashants/cakephp
https://github.com/prashants/cakephp-plugin-boost_cake

HOW TO USE WEBZASH
==================

Coming soon...

REPORT BUGS : https://github.com/prashants/webzash/issues

FEATURE REQUEST : https://github.com/prashants/webzash/issues
