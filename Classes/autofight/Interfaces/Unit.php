<?php

namespace autofight\Interfaces;
use autofight\Army;

/**
 * A Unit is the basic element of an Army.
 *
 * Class Unit
 * @package autofight\Interfaces
 */
interface Unit
{

    /**
     * Returns number of remaining hit points
     * @return int
     */
    function getHealth();

    /**
     * Increases number of hit points by given value, or by 1 if no value
     * given.
     * @param null|int $iIncrease
     * @return mixed
     */
    function increaseHealth($iIncrease = null);

    /**
     * Decreases number of hit points by given value, or by 1 if no value
     * given.
     * @param null $iDecrease
     * @return mixed
     */
    function decreaseHealth($iDecrease = null);

    /**
     * Returns accuracy in percentage. 100 should always be hit, 1 should
     * always be miss.
     * @return int
     */
    function getAccuracy();

    /**
     * Performs action against Attacked Army
     * @param \autofight\Army $oAttackedArmy
     * @return array
     */
    function act(Army $oAttackedArmy);

    /**
     * Returns maximum number of surrounding units that can be affected by
     * a single shot from this Unit.
     * For example, a tank would have a radius of 5, whereas an infantry
     * Unit's rifle would only have a radius of 1 (single target).
     * @return int
     */
    function getRadius();

    /**
     * Returns true if the Unit is still alive, false otherwise
     * @return bool
     */
    function isAlive();

    /**
     * Returns type of unit, e.g. "infantry", "tank", etc.
     * @return string
     */
    function getType();

    /**
     * Returns the maximum amount of damage a hit from this Unit can cause
     * @return int
     */
    function getDamage();

    /**
     * Rarity is the chance of getting this unit in a random draw of units.
     * A bigger number means more chance to appear.
     * @return int
     */
    static function getRarity();

    /**
     * Sets army for unit when unit is added to army.
     * Enables easier chaining.
     *
     * @param Army $oArmy
     * @return $this
     */
    function setArmy(Army $oArmy);

    /**
     * Returns the unit's army
     * @return Army
     */
    function getArmy();

    /**
     * Sets the Unit's position in the army. Automatic. Ranging from 1 - army size.
     * @param int $iIndex
     * @return $this
     */
    function setIndex($iIndex);

    /**
     * Returns the Unit's position in the army
     * @return int
     */
    function getIndex();

    /**
     * Echoes the unit in a readable format
     * @return string
     */
    function __toString();

}