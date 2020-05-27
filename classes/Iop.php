<?php

class Iop extends Character
{
    private $color = 'red';

    public function setColor($color)
    {
        $this->color = $color;
    }
    public function getColor()
    {
        return $this->color;
    }

    public function hit(Character $characterToHit)
    {
        if($characterToHit->getId() === $this->getId()) {
            return self::$me;
        }
        elseif($characterToHit->getType() === 'sram') {
                $newDamages = $characterToHit->getDamages() + ($this->getStrength()) * 2;
                $characterToHit->setDamages($newDamages);
                $newLife = $characterToHit->getLife() - $newDamages;
                $characterToHit->setLife($newLife);
                if($characterToHit->getLife() <= 0) {
                    $this->gainExp(50);
                    return self::$isKilled;
                } else {
                    return self::$isHit;
                }
        } else {
            $newDamages = $characterToHit->getDamages() + $this->getStrength();
            $characterToHit->setDamages($newDamages);
            $newLife = $characterToHit->getLife() - $newDamages;
            $characterToHit->setLife($newLife);
            if($characterToHit->getLife() <= 0) {
                $this->gainExp(50);
                return self::$isKilled;
            } else {
                return self::$isHit;
            }            
        } 
    }
}