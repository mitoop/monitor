<?php

// PHP>=8.0.0
require '../vendor/autoload.php';

use Mitoop\Monitor;

$monitor = new Monitor();

while (true) {
    echo "Hi\n";
    sleep(2);

    $monitor->stopIfNecessary(stoppingCallback: fn () => '脚本结束');
}
