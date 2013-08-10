<?php

namespace autofight;

use autofight\Abstracts\Unit as aUnit;
use autofight\Interfaces\BattleLogger;
use autofight\Interfaces\Unit as iUnit;

/**
 * The infantry unit is the smallest unit of any army.
 * One soldier with a rifle.
 *
 * Class Infantry
 * @package autofight
 */
class Infantry extends aUnit
{
    /** @var array */
    protected $aMessages = array(
        1 => array(
            'brings eternal shame to his family',
            'critically misses',
            'shoots at the sky',
            'shoots himself in the foot',
            'jams his rifle',
            'has a bullet explode in his rifle',
            'breaks his rifle in half',
            'hits himself in the head with recoil'
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

    /** @var array  */
    protected $aSuicideMessages = array(
        'grew tired of it all and decided to end it',
        'couldn\'t handle the killing',
        'didn\'t have the stomach for war',
        'gave up on life',
        'killed himself',
        'swallowed his own bullet',
        'sat on a grenade, pulled out the pin, and waited',
        'stepped on a land mine. On purpose'
    );

    /** @var array  */
    protected $aIdleMessages = array(
        'didn\'t feel like participating',
        'went to sleep',
        'was too depressed to hold the rifle',
        'sat down and looked the other way',
        'started crying',
        'decided to clean his rifle',
        'went to call his wife',
        'couldn\'t stop looking at his girlfriend\'s picture',
        'went to grab something to eat'
    );

    /** @var array  */
    protected $aFriendlyFireMessages = array(
        'went insane and attacked his own',
        'went crazy and aimed at his friend',
        'couldn\'t handle it and decided to attack the platoon leader',
        'became too depressed to aim at enemies, and chose friends instead',
        'decided to switch sides, for the time being',
        'went mad and switched sides temporarily'
    );

    /** @var int */
    protected static $rarity = 100;

    /** @var int */
    protected $iHealth = 100;

    /** @var int */
    protected $iMaxHealth = 100;

    /** @var int */
    protected $iAccuracy = 50;

    /** @var int */
    protected $iBlastRadius = 0;

    /** @var string */
    protected $sType = 'infantry';

    /** @var int */
    protected $iDamage = 10;

    /**
     * When a unit acts, he performs his default action.
     * A soldier will aim and fire, a tank might move and fire, a medic will heal, and so on.
     * @param Army $oAttackedArmy
     * @return array
     */
    public function act(Army $oAttackedArmy)
    {
        $aResults = array();
        $oResult = new BattleResult();
        $oResult->attacker = $this;
        $oResult->amount = 0;

        // Insanity
        if (rand(1, 1000000) == 1) {
            $oResult->type = BattleLogger::TYPE_INSANE;
            switch (rand(0, 2)) {
                case 0:
                    // Suicidal
                    $this->iHealth = 0;
                    $oResult->defender = $this;
                    $oResult->message = $this->getRandomElement($this->aSuicideMessages);
                    $oResult->amount = 1000;
                    break;
                case 1:
                    // Idle
                    $oResult->defender = $this;
                    $oResult->message = $this->getRandomElement($this->aIdleMessages);

                    break;
                case 2:
                    // Friendly fire
                    $aResults[] = $this.' '.$this->getRandomElement($this->aFriendlyFireMessages).'!';
                    $oAttackedUnit = $this->getArmy()->getRandomAliveUnit($this);
                    $aResults = array_merge($aResults, $this->shoot($oAttackedUnit));
                    break;
                default:
                    break;
            }
        } else {
            // No insanity, continue as planned
            $oAttackedUnit = $oAttackedArmy->getRandomAliveUnit();
            if ($oAttackedUnit) {
                return $this->shoot($oAttackedUnit);
            } else {
                return array();
            }
        }

        $aResults[] = $oResult;
        return $aResults;
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
                $aAdjacent = $this->getArmy()->getAdjacentUnits($this, 1);
                if (!empty($aAdjacent)) {
                    $oUnitToShootAt = $this->getRandomElement($aAdjacent);
                    if ($oUnitToShootAt) {
                        $aResults[] = $this.' aims at '.$oUnit.' but bullet strays towards '.$oUnitToShootAt.'!';
                        $aPostMerge = $this->shoot($oUnitToShootAt);
                    }
                }
            } else if ($iHitScore == 1) {
                // CRITICAL MISS
                switch (rand(0, 1)) {
                    case 0:
                        // Reduce accuracy by 10
                        $this->iAccuracy = ($this->iAccuracy < 11) ? 1 : ($this->iAccuracy - 10);
                        $sAddedMessage = $this.' has suffered a permanent reduction of accuracy!';
                        break;
                    case 1:
                        // Reduce health by 10
                        $this->iHealth = ($this->iHealth < 11) ? 1 : ($this->iHealth - 10);
                        $sAddedMessage = $this.' has suffered a permanent reduction of health!';
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
                $aResults[] = $this.' scored a critical hit!!';
            } else {
                $iAmount = $this->iDamage * $iHitScore / 100;
            }
        }
        $oResult->amount = $iAmount;

        $oUnit->decreaseHealth($iAmount);
        if (!$oUnit->isAlive()) {
            $oResult->message = 'kills';
            $oResult->type = BattleLogger::TYPE_DEATH;
        }

        $aResults[] = $oResult;
        if (isset($sAddedMessage)) {
            $aResults[] = $sAddedMessage;
        }
        $aPostMerge = (isset($aPostMerge)) ? $aPostMerge : array();
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