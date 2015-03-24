<?php

namespace autofight;

use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\BattleLogger;
use autofight\Interfaces\Unit as iUnit;

class Priest extends aUnit
{
/** @var array */
protected $aMessages = array(
    1 => array(
        'started crying',
        'took a break',
    ),
    20 => array(
        'misses badly',
        'doesn\'t even touch',
    ),
    40 => array(
        'misses by a yardstick',
        'heals a little',
    ),
    50 => array(
        'makes it',
        'seems to remember what to do',
    ),
    60 => array(
        'touches',
        'waves a hand at',
    ),
    80 => array(
        'blesses',
        'sends his grace',
    ),
    99 => array(
        'hugs',
        'holds hand',
    ),
    100 => array(
        'sends God',
        'kisses',
    ),
);

/** @var int */
protected $iHealth = 200;

/** @var int */
protected $iMaxHealth = 200;

/** @var int */
protected $iAccuracy = 30;

/** @var int */
protected $iRadius = 0;

/** @var string */
protected $sType = 'priest';

/** @var int */
protected $iDamage = 10;

/** @var int */
protected static $rarity = 30;

/**
 * Rarity is the chance of getting this unit in a random draw of units.
 * A bigger number means more chance to appear.
 *
 * @return int
 */
public static function getRarity()
{
    return self::$rarity;
}

/**
 * @param Army $oAttackedArmy
 *
 * @return array
 */
public function act(Army $oAttackedArmy)
{
    $oAttackedArmy = $this->getArmy();
    $oUnit = $oAttackedArmy->getRandomAliveUnit($this);

    if ($oUnit) {
        return $this->shoot($oUnit);
    }

    return array();
}

/**
 * Shoots at a given unit. Hit or miss depends on accuracy and other
 * factors. Can shoot at self and commit suicide with a 100% success rate.
 *
 * @param \autofight\Interfaces\Unit $oUnit
 *
 * @return mixed
 */
public function shoot(iUnit $oUnit)
{
    $aResults = array();
    $oResult = new BattleResult();
    $oResult->attacker = $this;
    $oResult->defender = $oUnit;

    $aPostMerge = array();

    // Calculate hit or miss.
    $iHitScore = rand(1, 100);
    $bHit = $iHitScore >= $this->iAccuracy;

    $oResult->type = ($bHit) ? BattleLogger::TYPE_HEAL : BattleLogger::TYPE_NOTHEAL;
    $oResult->message = $this->determineMessage($iHitScore);

    $fPercentageOfAccuracy = $iHitScore / $this->iAccuracy * 100;
    if (!$bHit) {
        $iAmount = 0;
        // MISS
        if ($fPercentageOfAccuracy > 50 && $fPercentageOfAccuracy < 60) {
            /*
            If the hit score was between 50% and 60% of accuracy
            there's a chance the adjacent trooper was hit.
            */
            $aAdjacent = $this->getArmy()->getAdjacentUnits($this, 2);
            if (!empty($aAdjacent)) {
                $oUnitToShootAt = $this->getRandomElement($aAdjacent);
                if ($oUnitToShootAt) {
                    $aResults[] = $this.' aims at '.$oUnit.' but his power strays towards '.$oUnitToShootAt.'!';
                    $aPostMerge = $this->shoot($oUnitToShootAt);
                }
            }
        } elseif ($iHitScore == 1) {
            // CRITICAL MISS
            switch (rand(0, 1)) {
                case 0:
                    // Reduce accuracy by 10
                    $this->iAccuracy = ($this->iAccuracy < 11) ? 1 : ($this->iAccuracy - 10);
                    $sAddedMessage = $this.' was punished by God for not using his powers right!';
                    break;
                case 1:
                    // Reduce health by 10
                    $this->iHealth = ($this->iHealth < 11) ? 1 : ($this->iHealth - 10);
                    $sAddedMessage = $this.' was punished by God for not using his powers right!';
                    break;
                default:
                    break;
            }
        }
    } else {
        // HIT
        if ($iHitScore == 100) {
            // CRITICAL HIT
            $iAmount = $this->iDamage * 5;
            $aResults[] = $this.' is playing Jesus!!';
        } else {
            $iAmount = $this->iDamage * $iHitScore / 100;
        }
    }

    $oUnit->increaseHealth($iAmount);
    $oResult->amount = $iAmount;

    $aResults[] = $oResult;
    if (isset($sAddedMessage)) {
        $aResults[] = $sAddedMessage;
    }
    $aResults = array_merge($aResults, $aPostMerge);

    return $aResults;
}
}
