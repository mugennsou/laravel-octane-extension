<?php

declare(strict_types=1);

$config = new Mugennsou\CodeStyle\Config();

$config->getFinder()
    ->in(__DIR__ . '/src');

return $config;
