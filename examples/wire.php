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
echo "defineWire:\n";
$result = $Teleduino328->defineWire();
echo print_r($result, true)."\n";
*/

/*
echo "setWire:\n";
$address = 1; // Slave device address
$bytes = "Teleduino rocks!\n"; // String of bytes to be written
$result = $Teleduino328->setWire($address, $bytes);
echo print_r($result, true)."\n";
*/

/*
echo "getWire:\n";
$address = 1; // Slave device address
$byte_count = 32; // Length of byte string to read and return
$result = $Teleduino328->getWire($address, $byte_count);
echo print_r($result, true)."\n";
*/