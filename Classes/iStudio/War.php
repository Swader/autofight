<?php

namespace iStudio;
use iStudio\Interfaces\BattleLogger;
use iStudio\Interfaces\Unit;

/**
 * War is the main event.
 * It contains armies, and logs the output of every turn.
 *
 * Class War
 * @package iStudio
 */
class War
{
    /** @var array */
    protected $aArmies = array();

    /** @var BattleLogger */
    protected $oLogger;

    /** @var int */
    protected $iTurns = 0;

    /**
     * Sets the logger responsible for the battle output
     * @param BattleLogger $oLogger
     * @return $this
     */
    public function setLogger(BattleLogger $oLogger)
    {
        $this->oLogger = $oLogger;
        return $this;
    }

    /**
     * Adds an Army into the War.
     * A War needs at least two armies, but can have more (chaos!!)
     * An Army can also have a label for more personalized battle text output.
     * If the label is omitted, the numeric index of the array is used.
     *
     * @param Army $oArmy
     * @internal param null|string $sLabel
     * @return $this
     */
    public function addArmy(Army $oArmy)
    {
        if ($oArmy->getLabel() === null) {
            $oArmy->setLabel($oArmy->generateRandomLabel());
        }
        $this->aArmies[] = $oArmy;
        return $this;
    }

    public function fight()
    {
        if (count($this->aArmies) < 2) {
            die("War. War never changes. ... And as such it needs at least 2 armies!");
        }

        while ($this->moreThanOneAliveArmy()) {
            $this->doTurn();
        }

        $this->oLogger->logOther($this->getSurvivingArmy()->getLabel() . ' wins!');
    }

    /**
     * Returns true if there's more than one army that can still fight.
     * @return bool
     */
    protected function moreThanOneAliveArmy()
    {
        $iAlive = 0;
        /** @var Army $oArmy */
        foreach ($this->aArmies as $oArmy) {
            $iAlive += (int)(bool)$oArmy->countAlive();
        }
        return $iAlive > 1;
    }

    /**
     * Returns the surviving army.
     * @return Army|null
     */
    protected function getSurvivingArmy()
    {
        /** @var Army $oArmy */
        foreach ($this->aArmies as $oArmy) {
            if ($oArmy->countAlive()) {
                return $oArmy;
            }
        }
        return null;
    }

    protected function doTurn()
    {
        /** @var Army $oArmy */
        /** @var Unit $oUnit */

        $this->iTurns++;

        /**
         * Randomize order of armies,
         * each turn a different one has the chance of going first
         */
        shuffle($this->aArmies);

        $this->oLogger->logOther('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $this->oLogger->logOther('Turn ' . $this->iTurns . ' begins.');

        foreach ($this->aArmies as $i => $oArmy) {
            $this->oLogger->logOther('Army ' . $oArmy->getLabel() . ' goes ' . ordinal($i + 1));
        }

        foreach ($this->aArmies as $oArmy) {
            // Units currently execute moves from first to last.
            // @todo implement unit initiative

            if ($this->moreThanOneAliveArmy()) {

                /** @var Army $oAttackedArmy */
                $oAttackedArmy = $this->findAttackableArmy($oArmy);
                $this->oLogger->logOther(
                    'Army "' . $oArmy->getLabel() . '" attacks "' . $oAttackedArmy->getLabel() . '".'
                );

                foreach ($oArmy->getUnits() as $oUnit) {
                    if ($oUnit->isAlive()) {
                        $aResults = $oUnit->act($oAttackedArmy);
                        if (!empty($aResults)) {
                            $aResults[] = '~';
                        }
                        $this->oLogger->logMultiple($aResults);
                    }
                }
            }
        }

        $this->oLogger->logOther('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');

    }

    /**
     * Returns a random army that is NOT the army in the $oArmy argument.
     * Only armies with still living units can be returned
     * @param Army $oArmy
     * @return mixed
     */
    protected function findAttackableArmy(Army $oArmy)
    {
        $aAttackable = array();
        /** @var Army $oAvailableArmy */
        foreach ($this->aArmies as $oAvailableArmy) {
            if ($oAvailableArmy->getLabel() != $oArmy->getLabel()
                && $oAvailableArmy->countAlive()
            ) {
                $aAttackable[] = $oAvailableArmy;
            }
        }
        if (isset($aAttackable[rand(0, count($aAttackable) - 1)])) {
            return $aAttackable[rand(0, count($aAttackable) - 1)];
        } else {
            die('Could not find any attackable army. Looks like ' . $oArmy->getLabel() . ' wins.');
        }
    }

}