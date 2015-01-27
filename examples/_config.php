<?php
/*
 * https://www.teleduino.org
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

$config = array();

// If you're using the Teleduino328EthernetServerWeb sketch, set the IP
$config['ethernet_server_web'] = array();
$config['ethernet_server_web']['address'] = 'http://192.168.1.100';

// If you're using the Teleduino328EthernetClientProxy sketch, set the key
$config['ethernet_client_proxy'] = array();
$config['ethernet_client_proxy']['address'] = 'https://us01.proxy.teleduino.org';
$config['ethernet_client_proxy']['key'] = '00000000000000000000000000000000';

if(file_exists(__DIR__.'/_config.local.php')) {
    include_once(__DIR__.'/_config.local.php');
}