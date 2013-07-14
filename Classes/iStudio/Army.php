<?php

namespace iStudio;
use iStudio\Interfaces\Unit;

/**
 * Class Army
 * @package iStudio
 */
class Army
{

    /** @var array */
    protected static $aUnitTypes = array();

    /** @var int */
    protected $iSize;

    /** @var array */
    protected $aUnits = array();

    /** @var string */
    protected $sLabel;

    /** @var array */
    protected $aAdjectives = ['Iron', 'Fuchsia', 'Red', 'Brave', 'Lonely', 'Bitter', 'Deadly', 'Black', 'Armored'];

    /** @var array */
    protected $aNouns = ['Scorpions', 'Hand', 'Death', 'Marauders', 'Itch', 'Scratch', 'Zeus', 'Hummer', 'Volcano'];


    /**
     * Adds a unit type to list of available units. Other units are cloned
     * from available types, ensuring a single dependency point.
     *
     * @param Unit $oUnit
     */
    public static function addUnitType(Unit $oUnit)
    {
        self::$aUnitTypes[$oUnit->getType()] = $oUnit;
    }

    /**
     * The constructor takes a size parameter which is used to auto generate
     * the roster of the army.
     * @see Army::buildArmy()
     * @param $iSize
     */
    public function __construct($iSize)
    {
        if (!is_numeric($iSize) || $iSize < 1) {
            die('Army construct param needs to be numeric and positive.
            "' . $iSize . '" is not.');
        }
        $this->setSize((int)$iSize);

        $this->buildArmy();
    }

    /**
     * Sets an army label, for legibility in battle output and personalization
     * @param $sLabel
     * @return $this|Army
     */
    public function setLabel($sLabel)
    {
        if (!is_string($sLabel) && !is_numeric($sLabel)) {
            die('A label must be a string or a number. "' . $sLabel . '" given.');
        }
        $this->sLabel = $sLabel;
        return $this;
    }

    /**
     * Returns the defined army label
     * @return string
     */
    public function getLabel()
    {
        return $this->sLabel;
    }

    /**
     * Builds an army according to size and unit rarity.
     * @return $this
     */
    protected function buildArmy()
    {
        $iRarityTotal = 0;
        $aRandomnessArray = array();
        /** @var Unit $oUnit */
        foreach (self::$aUnitTypes as $k => $oUnit) {
            $iRarityTotal += $oUnit->getRarity();
            $aRandomnessArray[$k] = $iRarityTotal;
        }

        for ($i = 1; $i <= $this->getSize(); $i++) {
            $iRand = rand(1, $iRarityTotal);
            foreach ($aRandomnessArray as $k => $iScore) {
                if ($iRand > $iScore) {
                    continue;
                } else if ($iRand < $iScore) {
                    $oUnit = clone self::$aUnitTypes[$k];
                    $oUnit->setArmy($this);
                    break;
                }
            }
            $iIndex = count($this->aUnits);
            $this->aUnits[$iIndex] = $oUnit->setIndex($iIndex);
        }
        return $this;
    }

    /**
     * Returns a random living unit from the army
     * @param \iStudio\Interfaces\Unit|null $oNotUnit
     * @return Unit
     */
    public function getRandomAliveUnit(Unit $oNotUnit = null)
    {
        $aLivingUnits = array();
        /** @var Unit $oUnit */
        foreach ($this->aUnits as $oUnit) {
            if ($oUnit->isAlive()) {
                if (!$oNotUnit || ($oNotUnit && $oNotUnit->getIndex() != $oUnit->getIndex())) {
                    $aLivingUnits[] = $oUnit;
                }
            }
        }
        $i = rand(0, count($aLivingUnits) - 1);
        return (isset($aLivingUnits[$i])) ? $aLivingUnits[$i] : null;
    }

    /**
     * Returns neighbor units in given range (radius).
     * For example, given a Unit X and 1 as range, will return unit
     * to the left of X AND to the right of X, if they exist (even if dead).
     * If you provide a side argument, only that side is returned.
     * So for Unit X, range 1, side left, only the ONE unit to the left of X is returned.
     *
     * @param Unit $oUnit
     * @param int $iRange
     * @param string $sSide
     * @return array
     */
    public function getAdjacentUnits(Unit $oUnit, $iRange = 1, $sSide = 'both')
    {
        $aAdjacent = array();
        while ($iRange > 0) {
            if ($sSide == 'both' || $sSide == 'left') {
                if (isset($this->aUnits[$oUnit->getIndex() - $iRange])) {
                    $aAdjacent[] = $this->aUnits[$oUnit->getIndex() - $iRange];
                }
            }
            if ($sSide == 'both' || $sSide == 'right') {
                if (isset($this->aUnits[$oUnit->getIndex() + $iRange])) {
                    $aAdjacent[] = $this->aUnits[$oUnit->getIndex() + $iRange];
                }
            }
            $iRange--;
        }
        return array_reverse($aAdjacent);
    }

    /**
     * Returns units
     * @return array
     */
    public function getUnits()
    {
        return $this->aUnits;
    }

    /**
     * The size is used to auto generate the army roster.
     * @see Army::buildArmy()
     * @param $iSize
     * @return $this
     */
    protected function setSize($iSize)
    {
        $this->iSize = $iSize;
        return $this;
    }

    /**
     * Returns defined army size.
     * @return int
     */
    public function getSize()
    {
        return $this->iSize;
    }

    /**
     * Counts number of remaining alive troops
     * @return int
     */
    public function countAlive()
    {
        $i = 0;
        /** @var Unit $oUnit */
        foreach ($this->aUnits as &$oUnit) {
            $i += (int)$oUnit->isAlive();
        }
        return $i;
    }

    /**
     * Generates a random army name. If there are no more random names to generate,
     * generates a random numeric ID in the range 1 - 1000
     * @return string
     */
    public function generateRandomLabel()
    {
        if (empty($this->aAdjectives) || empty($this->aNouns)) {
            return (string)rand(0, 1000);
        }

        $iAdjective = rand(0, count($this->aAdjectives) - 1);
        $iNoun = rand(0, count($this->aNouns) - 1);
        $sLabel = $this->aAdjectives[$iAdjective] . ' ' . $this->aNouns[$iNoun];

        /** Remove picked values and reset array keys */
        unset($this->aAdjectives[$iAdjective], $this->aNouns[$iNoun]);
        $this->aNouns = array_values($this->aNouns);
        $this->aAdjectives = array_values($this->aAdjectives);

        return $sLabel;
    }

}