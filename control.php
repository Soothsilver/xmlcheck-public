<?php
if (!isset($argc)) { die("This is a command-line utility."); }
if ($argc >= 2 && $argv[1] === 'doctrine')
{
    chdir("www/core/doctrine");
    $arguments = $argv;
    array_shift($arguments);
    array_shift($arguments);
    passthru(realpath("../../vendor/bin/doctrine") . ' ' . implode(' ', $arguments));
    die;
}
echo "Invalid argument.";