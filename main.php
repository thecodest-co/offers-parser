<?php
declare(strict_types=1);
require 'vendor/autoload.php';

use Brodaty\Application\ApplicationService;

$app = new ApplicationService();
$offers = $app->init();

file_put_contents('output.txt', json_encode($offers));