<?php

namespace iStudio;
use iStudio\Interfaces\Unit;

/**
 * The BattleResult class contains information on the latest
 * outcome of a unit to unit attack
 *
 * message can be something like "pulverises" or "critically misses".
 * amount can be any floating point number.
 * type can be an integer corresponding to BattleLogger type constants.
 *
 * Class BattleResult
 * @package iStudio
 */
class BattleResult {
    /** @var Unit */
    public $attacker;

    /** @var Unit */
    public $defender;

    /** @var string */
    public $message;

    /** @var float */
    public $amount;

    /** @var int */
    public $type;
}