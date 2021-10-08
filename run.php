<?php

declare(strict_types=1);

use HPT\Bootstrap;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Bootstrap.php';

$data = Bootstrap::boot()
    ->createContainer()
    ->getByType(\HPT\Dispatcher::class)
    ->run();
fwrite(STDOUT, $data);