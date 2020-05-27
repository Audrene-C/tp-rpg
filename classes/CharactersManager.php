<?php

class CharactersManager
{
    private $pdo;

    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function __construct(PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function create(Character $character)
    {
        $req = $this->pdo->prepare('INSERT INTO characters(name, type) VALUES(:name, :type)');
        $req->execute(array(
            'name' => $character->getName(),
            'type' => $character->getType()
            ));

        $character->hydrate([
            'id' => $this->pdo->lastInsertId()
        ]);
    }

    public function update(Character $character)
    {
        $req = $this->pdo->prepare('UPDATE characters SET lvl = :lvl, xp = :xp, strength = :strength, life = :life, damages = :damages WHERE id = :id');
        $req->execute(array(
            ':lvl' => $character->getLvl(),
            ':xp' => $character->getXp(),
            ':strength' => $character->getStrength(),
            ':life' => $character->getLife(),
            ':damages' => $character->getDamages(),
            ':id' => $character->getId()
            )); 
    }

    public function get($info)
    {
        if(is_int($info)) {
            $req = $this->pdo->query('SELECT * FROM characters WHERE id = '.$info);
            $data = $req->fetch(PDO::FETCH_ASSOC);
        } else {
            $req = $this->pdo->prepare('SELECT * FROM characters WHERE name = :name');
            $req->execute([':name' => $info]);
            $data = $req->fetch(PDO::FETCH_ASSOC);
        }

        switch ($data['type']) {
            case 'iop' : return new Iop($data);
            case 'sram' : return new Sram($data);
            case 'cra' : return new Cra($data);
        }
    }

    public function doExists(String $info)
    {
        if(is_int($info)) {
            return (bool) $this->pdo->query('SELECT COUNT(*) FROM characters WHERE id = '.$info)->fetchColumn();
        } else {
            $req = $this->pdo->prepare('SELECT COUNT(*) FROM characters WHERE name = :name');
            $req->execute([':name' => $info]);
            
            return (bool) $req->fetchColumn();
        }  
    }

    public function count()
    {
        return $this->pdo->query('SELECT COUNT(*) FROM characters')->fetchColumn();
    }

    public function delete(Character $character)
    {
        $this->pdo->exec('DELETE FROM characters WHERE id = '.$character->getId());
    }
  

    public function getList($name)
    {
        $characters = [];
    
    $req = $this->pdo->prepare('SELECT * FROM characters WHERE name <> :name ORDER BY name');
    $req->execute([':name' => $name]);
    
    while ($data = $req->fetch(PDO::FETCH_ASSOC))
    {
      switch($data['type']) {
          case 'iop' : $characters = new Iop($data); break;
          case 'sram' : $characters = new Sram($data); break;
          case 'cra' : $characters = new Cra($data); break;
      }
    }
    
    return $characters;
    }
      
}