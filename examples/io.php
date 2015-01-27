<?php
/*
 * https://www.teleduino.org
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

include_once(__DIR__.'/_bootstrap.php');

use Teleduino\Teleduino328Client\Client as Teleduino328;

if(isset($_SERVER['HTTP_HOST'])) {
    echo "<pre>";
}

$Teleduino328 = new Teleduino328();

//$Teleduino328->setModeEthernetServerWeb($config['ethernet_server_web']);
$Teleduino328->setModeEthernetClientProxy($config['ethernet_client_proxy']);

/*
echo "definePinMode:\n";
$pin = 2; // Digital pin (0 - 19)
$mode = 1; // Mode (0 - input, 1 - output)
$result = $Teleduino328->definePinMode($pin, $mode);
echo print_r($result, true)."\n";
*/

/*
echo "setDigitalOutput:\n";
$pin = 2; // Digital pin (0 - 19)
$output = 2; // Output (0 - low, 1 - high, 2 - toggle)
$expire_time = 1000; // Expire time in milliseconds (0 to never expire)
$save = false; // Save digital output as preset
$result = $Teleduino328->setDigitalOutput($pin, $output, $expire_time, $save);
echo print_r($result, true)."\n";
*/

/*
echo "setPwmOutput:\n";
$pin = 6; // Pwm pin (3, 5, 6, 9)
$output = 128; // Output (0 - low, 255 - high)
$result = $Teleduino328->setPwmOutput($pin, $output);
echo print_r($result, true)."\n";
*/

/*
echo "getDigitalInput:\n";
$pin = 2; // Digital pin (0 - 19)
$result = $Teleduino328->getDigitalInput($pin);
echo print_r($result, true)."\n";
*/

/*
echo "getAnalogInput:\n";
$pin = 14; // Analog pin (14 - 21)
$result = $Teleduino328->getAnalogInput($pin);
echo print_r($result, true)."\n";
*/

/*
echo "getAllInputs:\n";
$result = $Teleduino328->getAllInputs();
echo print_r($result, true)."\n";
*/

/*
echo "setDigitalOutputs:\n";
$offset = 1;
$outputs = array(1, 1, 1, 1, 1, 1, 1, 1);
$expire_times = array(100, 150, 200, 250, 300, 350, 400, 450);
$result = $Teleduino328->setDigitalOutputs($offset, $outputs, $expire_times);
echo print_r($result, true)."\n";
*/