<?php
/*
 * https://www.teleduino.org
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Teleduino\Teleduino328Client;

class Client
{
    protected $data;
    protected $mode;
    protected $modeSettings;
    protected $version;

    public function __construct()
    {
        $this->data = array();
        for($i = 0; $i < 257; $i++) {
            $this->data[$i] = null;
        }
        $this->mode = null;
        $this->modeSettings = array(
            'ethernetClientProxy' => array(
                'address' => null,
                'version' => null,
                'key' => null
            ),
            'ethernetServerWeb' => array(
                'address' => null
            )
        );
        $this->version = '0.4.0';
    }

    protected function curl($url, $post = false, $post_data = array())
    {
        $request = array();
        $request['url'] = $url;
        $request['post'] = $post;
        $request['post_data'] = $post_data;
        $requests = array($request);
        $responses = $this->curlMulti($requests);
        if(!is_array($responses) || !count($responses)) {
            return false;
        }
        $response = array_shift($responses);

        return $response;
    }

    protected function curlMulti($requests)
    {
        if(!is_array($requests) || !count($requests)) {
            return false;
        }
        $chs = array();
        foreach($requests as $request) {
            $url = isset($request['url']) ? $request['url'] : null;
            $post = isset($request['post']) ? $request['post'] : false;
            $post_data = isset($request['post_data']) && is_array($request['post_data']) ? $request['post_data'] : array();
            if($url) {
                $ch_index = count($chs);
                $chs[$ch_index] = curl_init();
                curl_setopt($chs[$ch_index], CURLOPT_URL, $url);
                curl_setopt($chs[$ch_index], CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chs[$ch_index], CURLOPT_TIMEOUT, 10);
                curl_setopt($chs[$ch_index], CURLOPT_USERAGENT, 'Teleduino PHP Class v'.$this->version);
                if($post) {
                    curl_setopt($chs[$ch_index], CURLOPT_POST, true);
                    if(is_array($post_data) && count($post_data)) {
                        curl_setopt($chs[$ch_index], CURLOPT_POSTFIELDS, $post_data);
                    }
                }
            }
        }
        if(!count($chs)) {
            return false;
        }
        $mh = curl_multi_init();
        for($i = 0; $i < count($chs); $i++) {
            curl_multi_add_handle($mh, $chs[$i]);
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while($mrc == CURLM_CALL_MULTI_PERFORM);
        while($active && $mrc == CURLM_OK) {
            if(curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        $responses = array();
        for($i = 0; $i < count($chs); $i++) {
            $responses[$i] = curl_multi_getcontent($chs[$i]);
        }
        for($i = 0; $i < count($chs); $i++) {
            curl_multi_remove_handle($mh, $chs[$i]);
        }
        curl_multi_close($mh);
        for($i = 0; $i < count($chs); $i++) {
            curl_close($chs[$i]);
        }

        return $responses;
    }

    protected function dataHexDecode($data_hex)
    {
        if(strlen($data_hex) < 4 || strlen($data_hex) != 4 + (2 * ord($this->hexDecode(substr($data_hex, 2, 2))))) {
            return false;
        }
        $this->data[0] = $this->hexDecode(substr($data_hex, 0, 2));
        $this->data[1] = $this->hexDecode(substr($data_hex, 2, 2));
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $this->data[2 + $i] = $this->hexDecode(substr($data_hex, 4 + (2 * $i), 2));
        }

        return true;
    }

    protected function dataHexEncode()
    {
        $data_hex = '';
        $data_hex .= $this->hexEncode($this->data[0]);
        $data_hex .= $this->hexEncode($this->data[1]);
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_hex .= $this->hexEncode($this->data[2 + $i]);
        }

        return $data_hex;
    }

    protected function getDataPacket()
    {
        $data_packet = array(
            'result' => null,
            'time' => null,
            'values' => array()
        );

        return $data_packet;
    }

    protected function getPresetPacket()
    {
        $presets = array(
            'pin_modes' => array(),
            'pin_outputs' => array(),
            'shift_registers' => array(),
            'serial' => array(),
            'servos' => array(),
            'wire' => array()
        );
        for($i = 0; $i < 22; $i++) {
            $presets['pin_modes'][$i] = null;
            $presets['pin_outputs'][$i] = null;
        }
        for($i = 0; $i < 2; $i++) {
            $shift_register = array(
                'clock_pin' => null,
                'data_pin' => null,
                'latch_pin' => null,
                'enable_pin' => null,
                'outputs' => array()
            );
            for($j = 0; $j < 32; $j++) {
                $shift_register['outputs'][$j] = null;
            }
            $presets['shift_registers'][$i] = $shift_register;
        }
        for($i = 0; $i < 4; $i++) {
            $presets['serial'][$i] = null;
        }
        for($i = 0; $i < 6; $i++) {
            $servo = array(
                'pin' => null,
                'position' => null
            );
            $presets['servos'][$i] = $servo;
        }
        for($i = 0; $i < 1; $i++) {
            $wire = array(
                'define' => null
            );
            $presets['wire'][$i] = $wire;
        }

        return $presets;
    }

    protected function hexDecode($hex)
    {
        if(strlen($hex) != 2) {
            return false;
        }

        return chr(hexdec($hex));
    }

    protected function hexEncode($byte)
    {
        if(strlen($byte) > 1) {
            return false;
        }

        return sprintf('%02X', ord($byte));
    }

    protected function process()
    {
        switch($this->mode) {
            case 'ethernetClientProxy':
                return $this->processEthernetClientProxy();
            case 'ethernetServerWeb':
                return $this->processEthernetServerWeb();
            default:
                return false;
        }
    }

    protected function processEthernetClientProxy()
    {
        $address = $this->modeSettings['ethernetClientProxy']['address'];
        $version = $this->modeSettings['ethernetClientProxy']['version'];
        $post_data = array();
        $post_data['k'] = $this->modeSettings['ethernetClientProxy']['key'];
        $post_data['r'] = $this->dataHexEncode();
        $url = $address.'/api/'.$version.'/basic.php';
        $data_hex = $this->curl($url, true, $post_data);
        if(!$data_hex) {
            return false;
        }
        if(!$this->dataHexDecode(trim($data_hex))) {
            return false;
        }

        return true;
    }

    protected function processEthernetServerWeb()
    {
        $address = $this->modeSettings['ethernetServerWeb']['address'];
        $data_hex = $this->dataHexEncode();
        $url = $address.'/'.$data_hex;
        $data_hex = $this->curl($url);
        if(!$data_hex) {
            return false;
        }
        if(!$this->dataHexDecode(trim($data_hex))) {
            return false;
        }

        return true;
    }

    public function definePinMode($pin, $mode)
    {
        if($pin > 255 || $mode > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('20');
        $this->data[1] = $this->hexDecode('02');
        $this->data[2] = chr($pin);
        $this->data[3] = chr($mode);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function defineSerial($port, $baud)
    {
        if($port > 255) {
            return false;
        }
        switch($baud) {
            case 300:
                $baud_int = 0;
                break;
            case 1200:
                $baud_int = 1;
                break;
            case 2400:
                $baud_int = 2;
                break;
            case 4800:
                $baud_int = 3;
                break;
            case 9600:
                $baud_int = 4;
                break;
            case 14400:
                $baud_int = 5;
                break;
            case 19200:
                $baud_int = 6;
                break;
            case 28800:
                $baud_int = 7;
                break;
            case 38400:
                $baud_int = 8;
                break;
            case 57600:
                $baud_int = 9;
                break;
            case 115200:
                $baud_int = 10;
                break;
            default:
                return false;
        }
        $this->data[0] = $this->hexDecode('40');
        $this->data[1] = $this->hexDecode('02');
        $this->data[2] = chr($port);
        $this->data[3] = chr($baud_int);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function defineServo($servo, $pin)
    {
        if($servo > 255 || $pin > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('50');
        $this->data[1] = $this->hexDecode('02');
        $this->data[2] = chr($servo);
        $this->data[3] = chr($pin);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function defineShiftRegister($shift_register, $clock_pin, $data_pin, $latch_pin, $enable_pin = 255)
    {
        if($shift_register > 255 || $clock_pin > 255 || $data_pin > 255 || $latch_pin > 255 || $enable_pin > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('30');
        $this->data[1] = $this->hexDecode($enable_pin == 255 ? '04' : '05');
        $this->data[2] = chr($shift_register);
        $this->data[3] = chr($clock_pin);
        $this->data[4] = chr($data_pin);
        $this->data[5] = chr($latch_pin);
        $this->data[6] = chr($enable_pin);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function defineWire()
    {
        $this->data[0] = $this->hexDecode('70');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function flushSerial($port)
    {
        if($port > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('43');
        $this->data[1] = chr(1);
        $this->data[2] = chr($port);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function getAllInputs()
    {
        $this->data[0] = $this->hexDecode('25');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 30) {
            for($i = 0; $i < 22; $i++) {
                if($i < 14) {
                    $data_packet['values'][$i] = ord($this->data[2 + $i]);
                } else {
                    $data_packet['values'][$i] = 0;
                    $data_packet['values'][$i] += 256 * ord($this->data[16 + (2 * ($i - 14))]);
                    $data_packet['values'][$i] += ord($this->data[17 + (2 * ($i - 14))]);
                }
            }
        }

        return $data_packet;
    }

    public function getAnalogInput($pin)
    {
        if($pin > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('24');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($pin);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 2) {
            $data_packet['values'][0] = 0;
            $data_packet['values'][0] += 256 * ord($this->data[2]);
            $data_packet['values'][0] += ord($this->data[3]);
        }

        return $data_packet;
    }

    public function getDigitalInput($pin)
    {
        if($pin > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('23');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($pin);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 1) {
            $data_packet['values'][0] = ord($this->data[2]);
        }

        return $data_packet;
    }

    public function getEeprom($offset, $byte_count)
    {
        if($offset > 1023 || $byte_count > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('62');
        $this->data[1] = chr(3);
        $this->data[2] = chr(floor($offset / 256));
        $this->data[3] = chr($offset % 256);
        $this->data[4] = chr($byte_count);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        $data_packet['values'][0] = '';
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_packet['values'][0] .= $this->data[2 + $i];
        }

        return $data_packet;
    }

    public function getFreeMemory()
    {
        $this->data[0] = $this->hexDecode('14');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 2) {
            $data_packet['values'][0] = 0;
            $data_packet['values'][0] += 256 * ord($this->data[2]);
            $data_packet['values'][0] += ord($this->data[3]);
        }

        return $data_packet;
    }

    public function getPresets()
    {
        $presets = $this->getPresetPacket();
        $eeprom = $this->getEeprom(0, 144);
        if(!isset($eeprom['values'][0]) ||
            strlen($eeprom['values'][0]) != 144 ||
            substr($eeprom['values'][0], 0, 1) != '#' ||
            substr($eeprom['values'][0], 1, 1) != chr(0) ||
            substr($eeprom['values'][0], 2, 1) != '#'
        ) {
            $eeprom['result'] = 0;
            $eeprom['values'] = array();
            return $eeprom;
        }
        if(substr($eeprom['values'][0], 2, 1) == '#' &&
            substr($eeprom['values'][0], 25, 1) == '#' &&
            substr($eeprom['values'][0], 48, 1) == '#'
        ) {
            for($i = 0; $i < 22; $i++) {
                $pin_mode = ord(substr($eeprom['values'][0], 3 + $i, 1));
                $pin_output = ord(substr($eeprom['values'][0], 26 + $i, 1));
                switch($pin_mode) {
                    case 0:
                    case 1:
                    case 2:
                        $presets['pin_modes'][$i] = $pin_mode;
                        switch($pin_mode) {
                            case 1:
                                if($pin_output >= 0 && $pin_output <= 1) {
                                    $presets['pin_outputs'][$i] = $pin_output;
                                }
                                break;
                            case 2:
                                $presets['pin_outputs'][$i] = $pin_output;
                                break;
                        }
                        break;
                }
            }
        }
        if(substr($eeprom['values'][0], 48, 1) == '#' &&
            substr($eeprom['values'][0], 57, 1) == '#' &&
            substr($eeprom['values'][0], 122, 1) == '#'
        ) {
            for($i = 0; $i < 2; $i++) {
                $pin_offset = ($i == 0) ? 49 : 53;
                $output_offset = ($i == 0) ? 58 : 90;
                $clock_pin = ord(substr($eeprom['values'][0], $pin_offset, 1));
                $data_pin = ord(substr($eeprom['values'][0], $pin_offset + 1, 1));
                $latch_pin = ord(substr($eeprom['values'][0], $pin_offset + 2, 1));
                $enable_pin = ord(substr($eeprom['values'][0], $pin_offset + 3, 1));
                $outputs = array();
                for($j = 0; $j < 32; $j++) {
                    $outputs[$j] = ord(substr($eeprom['values'][0], $output_offset + $j, 1));
                }
                if($clock_pin >= 0 && $clock_pin <= 22 &&
                    $data_pin >= 0 && $data_pin <= 22 &&
                    $latch_pin >= 0 && $latch_pin <= 22
                ) {
                    $presets['shift_registers'][$i]['clock_pin'] = $clock_pin;
                    $presets['shift_registers'][$i]['data_pin'] = $data_pin;
                    $presets['shift_registers'][$i]['latch_pin'] = $latch_pin;
                    if($enable_pin >= 0 && $enable_pin <= 22) {
                        $presets['shift_registers'][$i]['enable_pin'] = $enable_pin;
                    }
                    $presets['shift_registers'][$i]['outputs'] = $outputs;
                }
            }
        }
        if(substr($eeprom['values'][0], 122, 1) == '#' &&
            substr($eeprom['values'][0], 127, 1) == '#'
        ) {
            $serial = null;
            switch(ord(substr($eeprom['values'][0], 123, 1))) {
                case 0:
                    $serial = 300;
                    break;
                case 1:
                    $serial = 1200;
                    break;
                case 2:
                    $serial = 2400;
                    break;
                case 3:
                    $serial = 4800;
                    break;
                case 4:
                    $serial = 9600;
                    break;
                case 5:
                    $serial = 14400;
                    break;
                case 6:
                    $serial = 19200;
                    break;
                case 7:
                    $serial = 28800;
                    break;
                case 8:
                    $serial = 38400;
                    break;
                case 9:
                    $serial = 57600;
                    break;
                case 10:
                    $serial = 115200;
                    break;
            }
            $presets['serial'][0] = $serial;
        }
        if(substr($eeprom['values'][0], 127, 1) == '#' &&
            substr($eeprom['values'][0], 134, 1) == '#' &&
            substr($eeprom['values'][0], 141, 1) == '#'
        ) {
            for($i = 0; $i < 6; $i++) {
                $pin = ord(substr($eeprom['values'][0], 128 + $i, 1));
                $position = ord(substr($eeprom['values'][0], 135 + $i, 1));
                if($pin >= 0 && $pin <= 22) {
                    $presets['servos'][$i]['pin'] = $pin;
                    if($position >= 0 && $position <= 180) {
                        $presets['servos'][$i]['position'] = $position;
                    }
                }
            }
        }
        if(substr($eeprom['values'][0], 141, 1) == '#' &&
            substr($eeprom['values'][0], 143, 1) == '#'
        ) {
            for($i = 0; $i < 1; $i++) {
                $define = ord(substr($eeprom['values'][0], 142 + $i, 1));
                if($define >= 0 && $define <= 1) {
                    $presets['wire'][$i]['define'] = $define;
                }
            }
        }
        $eeprom['values'] = $presets;

        return $eeprom;
    }

    public function getSerial($port, $byte_count)
    {
        if($port > 255 || $byte_count > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('42');
        $this->data[1] = chr(2);
        $this->data[2] = chr($port);
        $this->data[3] = chr($byte_count);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        $data_packet['values'][0] = '';
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_packet['values'][0] .= $this->data[2 + $i];
        }

        return $data_packet;
    }

    public function getServo($servo)
    {
        if($servo > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('52');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($servo);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 1) {
            $data_packet['values'][0] = ord($this->data[2]) < 255 ? ord($this->data[2]) : null;
        }

        return $data_packet;
    }

    public function getShiftRegister($shift_register)
    {
        if($shift_register > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('33');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($shift_register);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_packet['values'][$i] = ord($this->data[2 + $i]);
        }

        return $data_packet;
    }

    public function getUptime()
    {
        $this->data[0] = $this->hexDecode('16');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 4) {
            $data_packet['values'][0] = 0;
            $data_packet['values'][0] += 16777216 * ord($this->data[2]);
            $data_packet['values'][0] += 65536 * ord($this->data[3]);
            $data_packet['values'][0] += 256 * ord($this->data[4]);
            $data_packet['values'][0] += ord($this->data[5]);
        }

        return $data_packet;
    }

    public function getVersion()
    {
        $this->data[0] = $this->hexDecode('11');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        $data_packet['values'][0] = '';
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_packet['values'][0] .= $this->data[2 + $i];
        }

        return $data_packet;
    }

    public function getWire($address, $byte_count)
    {
        if($address > 127 || $byte_count > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('72');
        $this->data[1] = chr(2);
        $this->data[2] = chr($address);
        $this->data[3] = chr($byte_count);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        $data_packet['values'][0] = '';
        for($i = 0; $i < ord($this->data[1]); $i++) {
            $data_packet['values'][0] .= $this->data[2 + $i];
        }

        return $data_packet;
    }

    public function loadPresets()
    {
        $this->data[0] = $this->hexDecode('17');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function mergeShiftRegister($shift_register, $action, $expire_time, $outputs)
    {
        if($shift_register > 255 ||
            $action > 255 ||
            $expire_time > 16777215 ||
            !is_array($outputs) ||
            !count($outputs)
        ) {
            return false;
        }
        foreach($outputs as $output) {
            if($output > 255) {
                return false;
            }
        }
        $this->data[0] = $this->hexDecode('32');
        $this->data[1] = chr(37);
        $this->data[2] = chr($shift_register);
        $this->data[3] = chr($action);
        $this->data[4] = chr(floor($expire_time / 65536));
        $this->data[5] = chr(floor(($expire_time % 65536) / 256));
        $this->data[6] = chr(floor(($expire_time % 65536) % 256));
        for($i = 0; $i < 32; $i++) {
            $this->data[7 + $i] = chr(isset($outputs[$i]) ? $outputs[$i] : 0);
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function ping()
    {
        $this->data[0] = $this->hexDecode('15');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function reset()
    {
        $this->data[0] = $this->hexDecode('10');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function resetEeprom()
    {
        $this->data[0] = $this->hexDecode('60');
        $this->data[1] = $this->hexDecode('00');
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setDigitalOutput($pin, $output, $expire_time = 0, $save = false)
    {
        if($pin > 255 || $output > 255 || $expire_time > 16777215) {
            return false;
        }
        $this->data[0] = $this->hexDecode('21');
        $this->data[1] = $save ? $this->hexDecode('06') : ($expire_time ? $this->hexDecode('05') : $this->hexDecode('02'));
        $this->data[2] = chr($pin);
        $this->data[3] = chr($output);
        if($expire_time) {
            $this->data[4] = chr(floor($expire_time / 65536));
            $this->data[5] = chr(floor(($expire_time % 65536) / 256));
            $this->data[6] = chr(floor(($expire_time % 65536) % 256));
        } else if($save) {
            $this->data[4] = chr(0);
            $this->data[5] = chr(0);
            $this->data[6] = chr(0);
        }
        if($save) {
            $this->data[7] = $this->hexDecode('01');
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setDigitalOutputs($offset, $outputs, $expire_times = array())
    {
        if($offset > 255 ||
            !is_array($outputs) ||
            !is_array($expire_times) ||
            !count($outputs)
        ) {
            return false;
        }
        foreach($outputs as $output) {
            if($output > 255) {
                return false;
            }
        }
        foreach($expire_times as $expire_time) {
            if($expire_time > 16777215) {
                return false;
            }
        }
        $actual_outputs = array();
        $actual_expire_times = array();
        for($i = 0; $i < 22; $i++) {
            $actual_outputs[$i] = isset($outputs[$i]) ? $outputs[$i] : 255;
            $actual_expire_times[$i] = isset($expire_times[$i]) ? $expire_times[$i] : 0;
        }
        for($i = 22; $i > 0; $i--) {
            if($actual_outputs[$i - 1] == 255) {
                unset($actual_outputs[$i - 1]);
                unset($actual_expire_times[$i - 1]);
            } else {
                break;
            }
        }
        $this->data[0] = $this->hexDecode('26');
        $this->data[1] = chr(1 + (count($actual_outputs) * 4));
        $this->data[2] = chr($offset);
        for($i = 0; $i < count($actual_outputs); $i++) {
            $this->data[3 + ($i * 4)] = chr($actual_outputs[$i]);
            $this->data[3 + ($i * 4) + 1] = chr(floor($actual_expire_times[$i] / 65536));
            $this->data[3 + ($i * 4) + 2] = chr(floor(($actual_expire_times[$i] % 65536) / 256));
            $this->data[3 + ($i * 4) + 3] = chr(floor(($actual_expire_times[$i] % 65536) % 256));
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setEeprom($offset, $bytes)
    {
        $bytes = str_split($bytes);
        if($offset > 1023 ||
            !is_array($bytes) ||
            !count($bytes) ||
            count($bytes) > 253
        ) {
            return false;
        }
        foreach($bytes as $byte) {
            if(strlen($byte) > 1) {
                return false;
            }
        }
        $this->data[0] = $this->hexDecode('61');
        $this->data[1] = chr(2 + count($bytes));
        $this->data[2] = chr(floor($offset / 256));
        $this->data[3] = chr($offset % 256);
        $i = 0;
        foreach($bytes as $byte) {
            $this->data[4 + $i] = $byte;
            $i++;
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setModeEthernetClientProxy($config)
    {
        $address = isset($config['address']) ? $config['address'] : 'https://us01.proxy.teleduino.org';
        $version = isset($config['version']) ? $config['version'] : '1.0';
        $key = isset($config['key']) ? $config['key'] : null;
        if(!isset($key)) {
            return false;
        }
        $this->mode = 'ethernetClientProxy';
        $this->modeSettings['ethernetClientProxy']['address'] = $address;
        $this->modeSettings['ethernetClientProxy']['version'] = $version;
        $this->modeSettings['ethernetClientProxy']['key'] = $key;

        return true;
    }

    public function setModeEthernetServerWeb($config)
    {
        $address = isset($config['address']) ? $config['address'] : null;
        if(!isset($address)) {
            return false;
        }
        $this->mode = 'ethernetServerWeb';
        $this->modeSettings['ethernetServerWeb']['address'] = $address;

        return true;
    }

    public function setPresets($presets)
    {
        if(!is_array($presets) ||
            count(array_diff_key($presets, $this->getPresetPacket())) ||
            count(array_diff_key($this->getPresetPacket(), $presets))
        ) {
            return false;
        }
        $bytes = array();
        for($i = 0; $i < 144; $i++) {
            $bytes[$i] = chr(255);
        }
        $bytes[0] = '#';
        $bytes[1] = chr(0);
        $bytes[2] = '#';
        $bytes[25] = '#';
        for($i = 0; $i < 22; $i++) {
            $pin_mode = $presets['pin_modes'][$i];
            $pin_output = $presets['pin_outputs'][$i];
            if(isset($pin_mode) && intval($pin_mode) >= 0 && intval($pin_mode) <= 2) {
                $bytes[3 + $i] = chr(intval($pin_mode));
                if((intval($pin_mode) == 1 && isset($pin_output) && intval($pin_output) >= 0 && intval($pin_output) <= 1) ||
                    (intval($pin_mode) == 2 && isset($pin_output) && intval($pin_output) >= 0 && intval($pin_output) <= 255)
                ) {
                    $bytes[26 + $i] = chr(intval($presets['pin_outputs'][$i]));
                }
            }
        }
        $bytes[48] = '#';
        $bytes[57] = '#';
        for($i = 0; $i < 2; $i++) {
            $pin_offset = ($i == 0) ? 49 : 53;
            $output_offset = ($i == 0) ? 58 : 90;
            $clock_pin = $presets['shift_registers'][$i]['clock_pin'];
            $data_pin = $presets['shift_registers'][$i]['data_pin'];
            $latch_pin = $presets['shift_registers'][$i]['latch_pin'];
            $enable_pin = $presets['shift_registers'][$i]['enable_pin'];
            $outputs = $presets['shift_registers'][$i]['outputs'];
            if(isset($clock_pin) && intval($clock_pin) >= 0 && intval($clock_pin) <= 21 &&
                isset($data_pin) && intval($data_pin) >= 0 && intval($data_pin) <= 21 &&
                isset($latch_pin) && intval($latch_pin) >= 0 && intval($latch_pin) <= 21
            ) {
                $bytes[$pin_offset + 0] = chr($clock_pin);
                $bytes[$pin_offset + 1] = chr($data_pin);
                $bytes[$pin_offset + 2] = chr($latch_pin);
                if(isset($enable_pin) && intval($enable_pin) >= 0 && intval($enable_pin) <= 21) {
                    $bytes[$pin_offset + 3] = chr($enable_pin);
                }
                for($j = 0; $j < 32; $j++) {
                    $bytes[$output_offset + $j] = (isset($outputs[$j]) && intval($outputs[$j]) >= 0 && intval($outputs[$j]) <= 255) ? chr(intval($outputs[$j])) : chr(0);
                }
            }
        }
        $bytes[122] = '#';
        if(isset($presets['serial'][0])) {
            $serial = null;
            switch($presets['serial'][0]) {
                case 300:
                    $serial = 0;
                    break;
                case 1200:
                    $serial = 1;
                    break;
                case 2400:
                    $serial = 2;
                    break;
                case 4800:
                    $serial = 3;
                    break;
                case 9600:
                    $serial = 4;
                    break;
                case 14400:
                    $serial = 5;
                    break;
                case 19200:
                    $serial = 6;
                    break;
                case 28800:
                    $serial = 7;
                    break;
                case 38400:
                    $serial = 8;
                    break;
                case 57600:
                    $serial = 9;
                    break;
                case 115200:
                    $serial = 10;
                    break;
            }
            if(isset($serial)) {
                $bytes[123] = chr($serial);
            }
        }
        $bytes[127] = '#';
        $bytes[134] = '#';
        for($i = 0; $i < 6; $i++) {
            $pin = $presets['servos'][$i]['pin'];
            $position = $presets['servos'][$i]['position'];
            if(isset($pin) && intval($pin) >= 0 && intval($pin) <= 21) {
                $bytes[128 + $i] = chr($pin);
                if(isset($position) && intval($position) >= 0 && intval($position) <= 180) {
                    $bytes[135 + $i] = chr($position);
                }
            }
        }
        $bytes[141] = '#';
        for($i = 0; $i < 1; $i++) {
            $define = $presets['wire'][$i]['define'];
            if(isset($define) && intval($define) >= 0 && intval($define) <= 1) {
                $bytes[142 + $i] = chr($define);
            }
        }
        $bytes[143] = '#';

        return $this->setEeprom(0, implode('', $bytes));
    }

    public function setPwmOutput($pin, $output)
    {
        if($pin > 255 || $output > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('22');
        $this->data[1] = $this->hexDecode('02');
        $this->data[2] = chr($pin);
        $this->data[3] = chr($output);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setSerial($port, $bytes)
    {
        $bytes = str_split($bytes);
        if($port > 255 || !is_array($bytes) || !count($bytes) || count($bytes) > 254) {
            return false;
        }
        foreach($bytes as $byte) {
            if(strlen($byte) > 1) {
                return false;
            }
        }
        $this->data[0] = $this->hexDecode('41');
        $this->data[1] = chr(1 + count($bytes));
        $this->data[2] = chr($port);
        $i = 0;
        foreach($bytes as $byte) {
            $this->data[3 + $i] = $byte;
            $i++;
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setServo($servo, $position)
    {
        if($servo > 255 || $position > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('51');
        $this->data[1] = $this->hexDecode('02');
        $this->data[2] = chr($servo);
        $this->data[3] = chr($position);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setShiftRegister($shift_register, $outputs)
    {
        if($shift_register > 255 || !is_array($outputs) || !count($outputs)) {
            return false;
        }
        foreach($outputs as $output) {
            if($output > 255) {
                return false;
            }
        }
        $this->data[0] = $this->hexDecode('31');
        $this->data[1] = chr(33);
        $this->data[2] = chr($shift_register);
        for($i = 0; $i < 32; $i++) {
            $this->data[3 + $i] = chr(isset($outputs[$i]) ? $outputs[$i] : 0);
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setStatusLed($count)
    {
        if($count > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('13');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($count);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setStatusLedPin($pin)
    {
        if($pin > 255) {
            return false;
        }
        $this->data[0] = $this->hexDecode('12');
        $this->data[1] = $this->hexDecode('01');
        $this->data[2] = chr($pin);
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;

        return $data_packet;
    }

    public function setWire($address, $bytes)
    {
        $bytes = str_split($bytes);
        if($address > 127 || !is_array($bytes) || !count($bytes) || count($bytes) > 254) {
            return false;
        }
        foreach($bytes as $byte) {
            if(strlen($byte) > 1) {
                return false;
            }
        }
        $this->data[0] = $this->hexDecode('71');
        $this->data[1] = chr(1 + count($bytes));
        $this->data[2] = chr($address);
        $i = 0;
        foreach($bytes as $byte) {
            $this->data[3 + $i] = $byte;
            $i++;
        }
        $start = microtime(true);
        if(!$this->process()) {
            return false;
        }
        $end = microtime(true);
        $data_packet = $this->getDataPacket();
        $data_packet['result'] = ord($this->data[0]);
        $data_packet['time'] = $end - $start;
        if(ord($this->data[1]) == 1) {
            $data_packet['values'][0] = ord($this->data[2]);
        }

        return $data_packet;
    }

}