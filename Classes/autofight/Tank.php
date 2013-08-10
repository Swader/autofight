<?php

namespace autofight;

use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\Unit as iUnit;
use autofight\Interfaces\BattleLogger;

/**
 * The Tank is a heavily armored vehicle with a large blast radius.
 *
 * Class Tank
 * @package autofight
 */
class Tank extends aUnit
{

    /** @var array */
    protected $aMessages = array(
        1 => array(
            'brings eternal shame to his family',
            'critically misses',
            'shoots at the sky',
            'has a projectile explode in the pipe',
            'breaks the tank tracks',
            'drops the projectile on the driver\'s head while reloading',
            'dents the turret',
            'cracks the turret on a tree'
        ),
        20 => array(
            'misses badly',
            'fails miserably',
            'shoots like a blind five year old'
        ),
        40 => array(
            'shoots clumsily',
            'misses by a yardstick',
            'appears to be seeing double'
        ),
        50 => array(
            'shoots too low',
            'shoots too high',
            'shoots the ground'
        ),
        60 => array(
            'slightly wounds',
            'grazes',
            'pokes'
        ),
        80 => array(
            'wounds',
            'hits',
            'pierces'
        ),
        99 => array(
            'hits well',
            'hits hard',
            'badly wounds'
        ),
        100 => array(
            'critically hits',
            'pulverizes',
            'destroys',
            'obliterates',
            'critically wounds'
        )
    );

    /** @var int */
    protected static $rarity = 10;

    /** @var int */
    protected $iHealth = 500;

    /** @var int */
    protected $iMaxHealth = 500;

    /** @var int */
    protected $iAccuracy = 35;

    /** @var int */
    protected $iBlastRadius = 3;

    /** @var int */
    protected $iDamage = 50;

    /** @var string */
    protected $sType = 'tank';

    /**
     * @param Army $oAttackedArmy
     * @return array
     */
    public function act(Army $oAttackedArmy)
    {
        if ($oAttackedArmy->countAlive()) {
            return $this->shoot($oAttackedArmy->getRandomAliveUnit());
        }
        return array();
    }


    /**
     * Shoots at a given unit. Hit or miss depends on accuracy and other
     * factors. Can shoot at self and commit suicide with a 100% success rate.
     * @param \autofight\Interfaces\Unit $oUnit
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

        $oResult->type = ($bHit) ? BattleLogger::TYPE_HIT : BattleLogger::TYPE_MISS;
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
                        $aResults[] = $this.' aims at '.$oUnit.' but projectile strays towards '.$oUnitToShootAt.'!';
                        $aPostMerge = $this->shoot($oUnitToShootAt);
                    }
                }
            } else if ($iHitScore == 1) {
                // CRITICAL MISS
                switch (rand(0, 1)) {
                    case 0:
                        // Reduce accuracy by 10
                        $this->iAccuracy = ($this->iAccuracy < 11) ? 1 : ($this->iAccuracy - 10);
                        $sAddedMessage = $this . ' has suffered a permanent reduction of accuracy!';
                        break;
                    case 1:
                        // Reduce health by 10
                        $this->iHealth = ($this->iHealth < 11) ? 1 : ($this->iHealth - 10);
                        $sAddedMessage = $this . ' has suffered a permanent reduction of health!';
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
                $aResults[] = $this . ' scored a critical hit!!';
            } else {
                $iAmount = $this->iDamage * $iHitScore / 100;
            }

            /**
             * SHRAPNEL implementation
             */
            $aAdjacent = $oUnit->getArmy()->getAdjacentUnits($oUnit, $this->getBlastRadius());
            $aPostMerge[] = 'Splash Damage!';
            /** @var \autofight\Interfaces\Unit $oAdjacentUnit */
            foreach ($aAdjacent as $oAdjacentUnit) {
                if ($oAdjacentUnit->isAlive()) {
                    $iAmountToReduce = round($iAmount * ($this->getBlastRadius() - abs($oUnit->getIndex()-$oAdjacentUnit->getIndex())) / ($this->getBlastRadius()*2) + 1, 2);
                    $oAdjacentUnit->decreaseHealth($iAmountToReduce);
                    if ($oAdjacentUnit->isAlive()) {
                        $aPostMerge[] = $oAdjacentUnit.' was hit by shrapnel for '.$iAmountToReduce.' damage.';
                    } else {
                        $aPostMerge[] = $oAdjacentUnit.' was hit by shrapnel for '.$iAmountToReduce.' damage and perished.';
                    }
                } else {
                    $aPostMerge[] = 'The corpse of '.$oAdjacentUnit.' is mutilated by shrapnel.';
                }
            }

        }

        $oUnit->decreaseHealth($iAmount);
        if (!$oUnit->isAlive()) {
            $oResult->message = 'kills';
            $oResult->type = BattleLogger::TYPE_DEATH;
        }
        $oResult->amount = $iAmount;

        $aResults[] = $oResult;
        if (isset($sAddedMessage)) {
            $aResults[] = $sAddedMessage;
        }
        $aResults = array_merge($aResults, $aPostMerge);
        return $aResults;
    }

    /**
     * Rarity is the chance of getting this unit in a random draw of units.
     * A bigger number means more chance to appear.
     * @return int
     */
    static function getRarity()
    {
        return self::$rarity;
    }

}