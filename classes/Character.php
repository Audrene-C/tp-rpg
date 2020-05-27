<?php

class Character
{

//properties
    protected $id;
    protected $name;
    protected $lvl;
    protected $xp;
    protected $strength;
    protected $life;
    protected $damages;
    protected $type;
    public static $me = 1;
    public static $isHit = 2;
    public static $isKilled = 3;
    protected static $xpNeededToLvlUp = 100;

//get and setter ID
    public function setId($id)
    {
        $id = (int) $id;
        
        if ($id > 0) {
            $this->id = (int)$id;
        }
    }
    public function getId()
    {
        return $this->id;
    }

//get and setter name
    public function setName(String $name)
    {      
        $this->name = htmlspecialchars($name);
    }
    public function getName()
    {
        return ($this->name);
    }

//get and setter lvl
    public function setLvl(Int $lvl)
    {
        if ($lvl > 0 && $lvl <= 100) {
            $this->lvl = $lvl;
        } else {
            throw new Error("Lvl is min. 1 and max. 100.");
        }
    }
    public function getLvl()
    {
        return $this->lvl;
    }

//get and setter xp
    public function setXp(Int $xp)
    {
        $this->xp = $xp;
    }
    public function getXp()
    {
        return $this->xp;
    }

//get and setter strength
    public function setStrength(Int $strength)
    {
        if ($strength > 0 && $strength <= 100) {
            $this->strength = $strength;
        } else {
            throw new Error("Strength is min. 1 and max. 100.");
        }
    }
    public function getStrength()
    {
        return $this->strength;
    }

//get and setter life
    public function setLife(Int $life)
    {
        $this->life = $life;     
    }
    public function getLife()
    {
        return $this->life;
    }

//get and setter damages
    public function setDamages(Int $damages)
    {
        if ($damages >= 0) {
            $this->damages = $damages;
        } else {
            throw new Error("That's not how you heal people...");
        }   
    }
    public function getDamages()
    {
        return $this->damages;
    }

//get and setter type
    public function setType($type)
    {
        
        $this->type = $type;
    }    
    public function getType()
    {
        return $this->type;
    }

//return xp needed to lvl up
    public static function getXpNeededToLvlUp()
    {
        return self::$xpNeededToLvlUp;
    }

//hydrate from an array
    public function hydrate(array $characterRow)
  {
    foreach ($characterRow as $key => $value)
    {
      $method = 'set'.ucfirst($key);
      
      if (method_exists($this, $method))
      {
        $this->$method($value);
      }
    }
  }

//construct with hydrate function
  public function __construct(array $characterRow)
  {
    $this->hydrate($characterRow);
    $this->type = strtolower(static::class);
  }

//this is what my characters can do
    public function speak()
    {
        echo ($this->getName()." : I'm ".$this->getName()."!</br>");
    }

    public function lvlUp()
    {
        $newLvl = $this->getLvl() + 1;
        $this->setLvl($newLvl);
        $newStrength = $this->getStrength() + 5;
        $this->setStrength($newStrength);
        $newLife = $this->getLife() + 10;
        $this->setLife($newLife);
        echo ("Lvl up! (strength +5, life +10) You are now lvl ".$this->getLvl().", congrats!</br>");
        $this->setXp(0);
    }

    public function gainExp(Int $exp)
    {
        if ($exp > 0) {
            $newXp = $this->getXp() + $exp;
            $this->setXp($newXp);
            echo ($this->getName()." : You gained $exp xp.</br>");
            if($this->getXp() >= self::$xpNeededToLvlUp) {
                $this->lvlUp();
        }
        echo ((Character::getXpNeededToLvlUp() - $this->getXp())." xp to go before level up!</br>");
        } else {
            throw new Error("You can't lose exp.");
        }
    }

    public function hit(Character $characterToHit)
    {
        if($characterToHit->getId() === $this->getId()) {
            return self::$me;
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

//check if name is not an emtpy string
    public function validName()
    {
        return !empty($this->name);
    }
}