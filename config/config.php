<?php   
/*************************************************************
* Required parameters for system
**************************************************************/

$cfg['site']['name'] = "ZEN Booking Calendar";
$cfg['site']['owner'] = "Max G";
$cfg['site']['title'] = $cfg['site']['name'] . " : " . $cfg['site']['name'];
$cfg['site']['version'] = '1.0.0';
$cfg['site']['contacts'] = array('max@v-integ.com');
$cfg['site']['email_sender'] = 'max@v-integ.com';

/************************************************************* 
* Database  settings
*************************************************************/

$cfg['db']['hostname'] = 'localhost'; // use 127.0.0.1 instead of localhost in windows
$cfg['db']['username'] = 'root';
$cfg['db']['password'] = '';
$cfg['db']['database'] = 'booking_calendar';