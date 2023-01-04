<?php

// PHP>=8.0.0
require '../vendor/autoload.php';

use Mitoop\Monitor;

Monitor::macro('getTimestampOfLastQueueRestart', static fn () => getTimestampOfLastQueueRestart());

$monitor = new Monitor(3, 200);

[$startTime, $jobsProcessed] = [time(), 0];

$lastRestart = getTimestampOfLastQueueRestart();

while (true) {
    echo "Hi\n";
    $jobsProcessed++;
    sleep(2);

    $monitor->stopIfNecessary($startTime, $jobsProcessed, $lastRestart, fn () => '脚本结束');
}

function getTimestampOfLastQueueRestart()
{
    $redis = new \Redis();

    $redis->connect('127.0.0.1');

    $redis->auth('mitoop');

    return $redis->get('mitoop:queue:restart');
}
