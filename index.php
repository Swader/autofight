<?php

/** Autoloader helps us avoid requires and includes */
require_once 'autoload.php';
/** Utility methods help us with some common operations */
require_once 'utility_methods.php';

/** Use helps us avoid long class names */
use autofight\Army;

/** Check if we got the required params */
/** @var int $iArmy1 */
$iArmy1 = (PHP_SAPI == 'cli')
    ? (isset($argv[1]) ? $argv[1] : 0)
    : ((isset($_GET['army1'])) ? (int)$_GET['army1'] : 0);
/** @var int $iArmy2 */
$iArmy2 = (PHP_SAPI == 'cli')
    ? (isset($argv[2]) ? $argv[2] : 0)
    : ((isset($_GET['army2'])) ? (int)$_GET['army2'] : 0);

if (!$iArmy1 || !$iArmy2) {
    $sMsg = 'Two parameters are expected - army1 and army2.
    Cannot continue without both. ';
    switch (PHP_SAPI) {
        case 'cli':
            $sMsg .= 'Maybe try this (each number represents the';
            $sMsg .= ' size of one army): index.php 50 50' . PHP_EOL;
            break;
        default:
            $sMsg .= '<br />Maybe try this link:
            <a href="/?army1=50&army2=50" >Army 1 = Army 2 = 50</a>';
            break;
    }
    die ($sMsg);
}

/**
 * Register available unit types
 */
Army::addUnitType(new \autofight\Infantry());
Army::addUnitType(new \autofight\Tank());

/**
 * Build armies
 */
$oArmy1 = new Army($iArmy1);
$oArmy2 = new Army($iArmy2);

$oWar = new \autofight\War();

/**
 * Register appropriate logger, depending on context
 */
$oWar->setLogger(
    PHP_SAPI == 'cli'
        ? new \autofight\Loggers\LoggerCli()
        : new \autofight\Loggers\LoggerWeb()
);

/**
 * Start the war
 */
//$oWar->addArmy($oArmy1->setLabel('Blue'))->addArmy($oArmy2->setLabel('Red'));
$oWar->addArmy($oArmy1)->addArmy($oArmy2);
$oWar->fight();