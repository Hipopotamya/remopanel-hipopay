<?php

require('../vendor/autoload.php');

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$connections = require('../brain/database/config.php');

foreach ($connections as $connection => $settings) {
    $capsule->addConnection($settings, $connection);
}

$capsule->setAsGlobal();

try {
    /** @var Illuminate\Database\Connection $panelDb */
    $panelDb = $capsule->connection('panel');

    /** @var Illuminate\Database\Connection $accountDb */
    $accountDb = $capsule->connection('account');

    /** @var Illuminate\Database\Connection $logDb */
    $logDb = $capsule->connection('log');

    /** @var Illuminate\Database\Connection $shardDb */
    $shardDb = $capsule->connection('shard');
} catch (Exception $e) {
    die('Veritabanı bağlantı ayarları hatalı!');
}