<?php

class Character
{
    private $id;
    private $name;
    private $lvl = 1;
    private $xp = 0;
    private static $xpNeededToLvlUp = 100;
    private $strength = 20;
    private $life = 100;
    private $damages = 0;

    public function valideName()
    {
        return !empty($this->name);
    }

  public function __construct(array $characterRow)
  {
    $this->hydrate($characterRow);
  }
  
  public function hydrate(array $characterRow)
  {
    if(!empty($id)) {
        $this->setId($characterRow["id"]);
    }     

    $this->setName($characterRow["name"]);

    if(!empty($lvl)) {
        $this->setLvl($characterRow["lvl"]);
    }
    
    if(!empty($xp)) {
        $this->setXp($characterRow["xp"]);
    }
    
    if(!empty($strength)) {
        $this->setStrength($characterRow["strength"]);
    }
    
    if(!empty($life)) {
        $this->setLife($characterRow["life"]);
    }
    
    if(!empty($damages)) {
        $this->setDamages($characterRow["damages"]);
    }
  }

    public function setId($id)
    {
        // On convertit l'argument en nombre entier.
        // Si c'en était déjà un, rien ne changera.
        $id = (int) $id;
        
        // On vérifie ensuite si ce nombre est bien strictement positif.
        if ($id > 0) {
            $this->id = (int)$id;
        }
    }
    public function getId()
    {
        return $this->id;
    }

    public function setName(String $name)
    {
        if (strlen($name) > 2 && strlen($name) <= 30) {
            $this->name = htmlspecialchars($name);
        } else {
            throw new Error("Name should be min. 3 letters and max. 30 letters long.");
        }
    }
    public function getName()
    {
        return ucfirst($this->name);
    }

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

    public function setXp(Int $xp)
    {
        $this->xp = $xp;
    }
    public function getXp()
    {
        return $this->xp;
    }

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

    public function setLife(Int $life)
    {
        if ($life >= 0) {
            $this->life = $life;
        } else {
            throw new Error("You can't be more dead than dead, you know. No zombies in this game.");
        }      
    }
    public function getLife()
    {
        return $this->life;
    }

    public function setDamages(Int $damages)
    {
        if ($damages > 0) {
            $this->damages = $damages;
        } else {
            throw new Error("That's not how you heal people...");
        }   
    }
    public function getDamages()
    {
        return $this->damages;
    }

    public static function getXpNeededToLvlUp()
    {
        return self::$xpNeededToLvlUp;
    }

    public function speak()
    {
        echo ($this->getName()." : I'm a ".$this->getName()."!</br>");
    }

    public function lvlUp()
    {
        $this->lvl += 1; //getLvl ou setLvl marche pas
        echo ("Lvl up! You are now lvl ".$this->getLvl().", congrats!</br>");
        $this->setXp(0);
    }

    public function gainExp(Int $exp)
    {
        if ($exp > 0) {
            $this->xp += $exp;//getXp ou setXp marche pas
            echo ($this->getName()." : You gained $exp xp.</br>");
            if($this->getXp() === self::$xpNeededToLvlUp) {
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
            echo ($this->getName()." se frappe dans sa confusion!</br>");
        } else {
            $characterToHit->damages += $this->getStrength();
            echo ($this->getName()." hit ".$characterToHit->getName()." for ".$characterToHit->getDamages()." damages!</br>
                Carefull ".$characterToHit->getName().", you only have ".($characterToHit->getLife() - $characterToHit->getDamages())."hp left!</br>");
        }
    }
}