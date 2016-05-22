<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$howToCommand = new \Danielfh\ConsoleApp\Command\HowTo();

$app->add($howToCommand);
$app->setDefaultCommand($howToCommand->getName());

$app->run();