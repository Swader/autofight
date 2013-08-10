<?php

namespace autofight\Interfaces;
use autofight\BattleResult;

/**
 * The Battle Logger is used to output the progress of the battle.
 *
 * Class BattleLogger
 * @package autofight\Interfaces
 */
interface BattleLogger
{

    const TYPE_HIT = 1;
    const TYPE_MISS = 2;
    const TYPE_DEATH = 3;
    const TYPE_MOVE = 4;
    const TYPE_INSANE = 5;

    /**
     * Logs a misc message
     * @param string $sMessage
     * @return BattleLogger
     */
    function logOther($sMessage);

    /**
     * Logs a battle result
     * @param BattleResult $oResult
     * @return $this
     */
    function logResult(BattleResult $oResult);

    /**
     * Logs an array of passed results, automatically picking the proper method
     * @param $aResults
     * @return $this
     */
    function logMultiple(array $aResults);
}