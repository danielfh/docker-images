<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$console = new \Symfony\Component\Console\Application();

$console->add(new \Danielfh\PhpStormConsole\Command\Run());
$console->add(new \Danielfh\PhpStormConsole\Command\Help());
$console->add(new \Danielfh\PhpStormConsole\Command\UpgradePhpStorm());

if (getenv('PHPSTORM_USER') !== false) {
    $console->setDefaultCommand('run');
} else {
    $console->setDefaultCommand('help');
}

return $console;