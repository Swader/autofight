<?php

namespace autofight\Abstracts;
use autofight\Army;

/**
 * Abstract class for basic common method inheritance
 *
 * Class Unit
 * @package autofight\Abstracts
 */
abstract class Unit implements \autofight\Interfaces\Unit
{

    /** @var array */
    protected $aMessages = array();

    /** @var int */
    protected $iHealth;

    /** @var int */
    protected $iMaxHealth;

    /** @var int */
    protected $iAccuracy;

    /** @var int */
    protected $iBlastRadius;

    /** @var string */
    protected $sType;

    /** @var int */
    protected $iDamage;

    /** @var Army */
    protected $oArmy;

    /** @var int */
    protected $iIndex;

    /**
     * Returns accuracy in percentage. 100 should always be hit, 1 should
     * always be miss.
     * @return int
     */
    function getAccuracy()
    {
        return $this->iAccuracy;
    }

    /**
     * Returns number of remaining hit points
     * @return int
     */
    function getHealth()
    {
        return $this->iHealth;
    }

    /**
     * Increases number of hit points by given value, or by 1 if no value
     * given.
     * @param null|int $iIncrease
     * @return mixed
     */
    function increaseHealth($iIncrease = null)
    {
        $this->iHealth += ($iIncrease === null) ? 1 : abs((int)$iIncrease);
        return $this;
    }

    /**
     * Decreases number of hit points by given value, or by 1 if no value
     * given.
     * @param null $iDecrease
     * @return mixed
     */
    function decreaseHealth($iDecrease = null)
    {
        $this->iHealth -= ($iDecrease === null) ? 1 : abs((int)$iDecrease);
        return $this;
    }

    /**
     * Returns true if the Unit is still alive, false otherwise
     * @return bool
     */
    function isAlive()
    {
        return !($this->iHealth <= 0);
    }

    /**
     * Returns maximum number of surrounding units that can be affected by
     * a single shot from this Unit.
     * For example, a tank would have a radius of 3, which means 3 from each
     * side of original target (total 6), whereas an infantry Unit's rifle would only
     * have a radius of 0 (single target).
     * @return int
     */
    function getBlastRadius()
    {
        return $this->iBlastRadius;
    }

    /**
     * Returns type of unit, e.g. "infantry", "tank", etc.
     * @return string
     */
    function getType()
    {
        return $this->sType;
    }

    /**
     * Returns the maximum amount of damage a hit from this Unit can cause
     * @return int
     */
    function getDamage()
    {
        return $this->iDamage;
    }

    /**
     * Sets army for unit when unit is added to army.
     * Enables easier chaining.
     *
     * @param Army $oArmy
     * @return $this
     */
    function setArmy(Army $oArmy)
    {
        $this->oArmy = $oArmy;
        return $this;
    }

    /**
     * Returns the unit's army
     * @return Army
     */
    function getArmy()
    {
        return $this->oArmy;
    }

    /**
     * Sets the Unit's position in the army. Automatic. Ranging from 1 - army size.
     * @param int $iIndex
     * @return $this
     */
    function setIndex($iIndex)
    {
        $this->iIndex = (int)$iIndex;
        return $this;
    }

    /**
     * Returns the Unit's position in the army
     * @return int
     */
    function getIndex()
    {
        return $this->iIndex;
    }

    /**
     * Returns a random message from the messages property, depending
     * on hit score.
     * @param $iHitScore
     * @return mixed
     */
    function determineMessage($iHitScore) {
        foreach ($this->aMessages as $iScore => $aMessages) {
            if ($iHitScore > $iScore) {
                continue;
            } else {
                return $aMessages[rand(0, count($aMessages) - 1)];
            }
        }
        return '-->';
    }

    /**
     * Returns a random array element
     * @param array $aArray
     * @return mixed
     */
    protected function getRandomElement(array $aArray) {
        if (!empty($aArray)) {
            return $aArray[rand(0, count($aArray) - 1)];
        }
        return null;
    }

    /**
     * Returns the name of the unit, tagged with index and army name
     * @return string
     */
    function __toString() {
        return ucfirst($this->getType()).' unit '.$this->getIndex().' ('.$this->getArmy()->getLabel().')';
    }
}