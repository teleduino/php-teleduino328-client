<?php
/*
 * https://www.teleduino.org
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

include_once(__DIR__.'/_config.php');

if(file_exists(__DIR__.'/../vendor/autoload.php')) {
    include_once(__DIR__.'/../vendor/autoload.php');
} else if(file_exists(__DIR__.'/../../../../vendor/autoload.php')) {
    include_once(__DIR__.'/../../../../vendor/autoload.php');
} else {
    die("Unable to find autoload.php\n");
}