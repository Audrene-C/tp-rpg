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
        $req = $this->pdo->prepare('INSERT INTO characters(name) VALUES(:name)');
        $req->execute(array(
            'name' => $character->getName()
            ));

        $character->hydrate([
            'id' => $this->pdo->lastInsertId()
        ]);
    }

    public function doExists(String $info)
    {
        $req = $this->pdo->prepare('SELECT COUNT(*) FROM characters WHERE name = :name');
        $req->execute([':name' => $info]);
        
        return (bool) $req->fetchColumn();  
    }

    public function count()
    {
        return $this->pdo->query('SELECT COUNT(*) FROM characters')->fetchColumn();
    }

    // public function update()
    // {
        
    // }

    // public function delete()
    // {
        
    // }

    // public function get()
    // {
        
    // }


    // public function getList()
    // {
        
    // }

    
    
    // public function all()
    // {
    //     $charactersStatement = $this->pdo->query('SELECT * FROM characters');
        
    //     $charactersRow = $charactersStatement->fetchAll();
    //     $characters = [];
        
    //     foreach($charactersRow as $characterRow) {
    //         $characters[] = new Character($characterRow);
    //     }
        
    //     return $characters;
    // }
      
}