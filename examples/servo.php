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
echo "defineServo:\n";
$servo = 0; // Servo (0 - 5)
$pin = 1; // Servo pin (0 - 21)
$result = $Teleduino328->defineServo($servo, $pin);
echo print_r($result, true)."\n";
*/

/*
echo "setServo:\n";
$servo = 0; // Servo (0 - 5)
$position = 90; // Servo position (0 - 180)
$result = $Teleduino328->setServo($servo, $position);
echo print_r($result, true)."\n";
*/

/*
echo "getServo:\n";
$servo = 0; // Servo (0 - 5)
$result = $Teleduino328->getServo($servo);
echo print_r($result, true)."\n";
*/