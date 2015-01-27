<?php
/*
  presets.php - Teleduino328 PHP presets example
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
echo "getPresets:\n";
$result = $Teleduino328->getPresets();
echo print_r($result, true)."\n";
*/

/*
echo "setPresets:\n";
$result = $Teleduino328->getPresets();
if($result['result'])
{
	$presets = $result['values'];
	$presets['pin_modes'][3] = 0; // Set pin 3 mode to 'input'
	$presets['pin_modes'][4] = 1; // Set pin 4 mode to 'output'
	$presets['pin_outputs'][4] = 1; // Set pin 4 state to 'high'
	$presets['pin_modes'][5] = 2; // Set pin 5 mode to 'PWM'
	$presets['pin_outputs'][5] = 64; // Set pin 5 duty cycle to 64
	$result = $Teleduino328->setPresets($presets);
	echo print_r($result, true)."\n";
}
else
{
	echo "Failed to fetch presets.\n";
}
*/

?>