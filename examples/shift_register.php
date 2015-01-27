<?php
/*
  shift_register.php - Teleduino328 PHP shift register example
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
echo "defineShiftRegister:\n";
$shift_register = 0; // Shift register (0 - 1)
$clock_pin = 3; // Pin for 'clock' signal on shift register (0 - 21)
$data_pin = 5; // Pin for 'data' signal on shift register (0 - 21)
$latch_pin = 4; // Pin for 'latch' signal on shift register (0 - 21)
$enable_pin = 2; // Pin for enabling shift register (0 - 21, 255). Optional, set to 255 if not used
$result = $Teleduino328->defineShiftRegister($shift_register, $clock_pin, $data_pin, $latch_pin, $enable_pin);
echo print_r($result, true)."\n";
*/

/*
echo "setShiftRegister:\n";
$shift_register = 0; // Shift register (0 - 1)
$outputs = array(85, 0); // Byte value outputs for shift registers as an array
$result = $Teleduino328->setShiftRegister($shift_register, $outputs);
echo print_r($result, true)."\n";
*/

/*
echo "mergeShiftRegister:\n";
$shift_register = 0; // Shift register (0 - 1)
$action = 2; // Action to merge (0 - low, 1 - high, 2 - toggle)
$expire_time = 1000; // Expire time in milliseconds (0 to never expire)
$outputs = array(255, 0); // Byte value outputs to merge with current values for shift registers as an array
$result = $Teleduino328->mergeShiftRegister($shift_register, $action, $expire_time, $outputs);
echo print_r($result, true)."\n";
*/

/*
echo "getShiftRegister:\n";
$shift_register = 0; // Shift register (0 - 1)
$result = $Teleduino328->getShiftRegister($shift_register);
echo print_r($result, true)."\n";
*/

?>