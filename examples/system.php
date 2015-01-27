<?php
/*
  system.php - Teleduino328 PHP system example
  Version 0.3.3
  Nathan Kennedy 2009 - 2014
  http://www.teleduino.org

  This code is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'_config.php');
include_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'teleduino328.php');

if(isset($_SERVER['HTTP_HOST']))
{
	echo "<pre>";
}

$Teleduino328 = new Teleduino328();

//$Teleduino328->setModeEthernetServerWeb($config['ethernet_server_web']);
$Teleduino328->setModeEthernetClientProxy($config['ethernet_client_proxy']);

/*
echo "reset:\n";
$result = $Teleduino328->reset();
echo print_r($result, true)."\n";
*/

/*
echo "getVersion:\n";
$result = $Teleduino328->getVersion();
echo print_r($result, true)."\n";
*/

/*
echo "setStatusLedPin:\n";
$pin = 8; // Digital pin to be used for the status LED
$result = $Teleduino328->setStatusLedPin($pin);
echo print_r($result, true)."\n";
*/

/*
echo "setStatusLed:\n";
$count = 2; // Flash count to be sent to the status LED
$result = $Teleduino328->setStatusLed($count);
echo print_r($result, true)."\n";
*/

/*
echo "getFreeMemory:\n";
$result = $Teleduino328->getFreeMemory();
echo print_r($result, true)."\n";
*/

/*
echo "ping:\n";
$result = $Teleduino328->ping();
echo print_r($result, true)."\n";
*/

/*
echo "getUptime:\n";
$result = $Teleduino328->getUptime();
echo print_r($result, true)."\n";
*/

/*
 echo "loadPresets:\n";
$result = $Teleduino328->loadPresets();
echo print_r($result, true)."\n";
*/

?>