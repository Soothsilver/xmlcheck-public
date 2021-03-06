<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'asm\\utils\\' => array($baseDir . '/core/utils'),
    'asm\\plugin\\' => array($baseDir . '/core/plugin'),
    'asm\\db\\' => array($baseDir . '/core/db', $baseDir . '/core/db/expression', $baseDir . '/core/db/expression/predicate', $baseDir . '/core/db/expression/statement', $baseDir . '/core/db/expression/value', $baseDir . '/core/db/expression/wrapper', $baseDir . '/core/db/mysql'),
    'asm\\core\\' => array($baseDir . '/core/core', $baseDir . '/core/core/error', $baseDir . '/core/core/launcher', $baseDir . '/core/core/scripts', $baseDir . '/core/core/ui'),
    '' => array($baseDir . '/core/doctrine'),
);
