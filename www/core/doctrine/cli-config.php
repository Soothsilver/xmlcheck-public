<?php
require_once __DIR__ . "/../../vendor/autoload.php";
\asm\core\Config::init(__DIR__ . "/../config.ini", __DIR__ . "/../internal.ini");

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(\asm\core\Repositories::getEntityManager());