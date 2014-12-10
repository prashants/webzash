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

Step 1. Download CakePHP version 2.5.6 from the below link
https://github.com/cakephp/cakephp/zipball/2.5.6

Step 2. Extract CakePHP into your web server directory and rename the folder
to "webzash"

Step 3. Edit the app/Config/core.php file and change the following lines

 Configure::write('debug', 0);
 Configure::write('Security.salt', 'IBs5T2I3gFdLcqUQoIP5NIFM4woPy4RqeGHqxo8h');
 Configure::write('Security.cipherSeed', '2990451816135972911329758922326');
 date_default_timezone_set('UTC');

Note : If you change any of the above security salt the default passwords
will not work.

Step 4. Edit the app/Config/bootstrap.php file and add the following lines

 CakePlugin::load('BoostCake');
 CakePlugin::load('Webzash', array('routes' => true, 'bootstrap' => true));

Step 5. Edit the app/Controller/PagesController.php and add the following line
after line no. 47 just after the "public function display() {"

 $this->redirect('/webzash');

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
