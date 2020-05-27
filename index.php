<?php
//create session and include the partial to get our classes when called on the index
include('config/autoload.php');
session_start();

if (isset($_GET['disconnect']))
{
  session_destroy();
  header('Location: .');
  exit();
}

//create a new PDO and connect to my db
$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=tp-rpg;charset=utf8', 
    'root', 
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

//create a new manager
$charactersManager = new CharactersManager($pdo);

//if session chara still exists, get it
if (isset($_SESSION['sessionCharacter']) && !is_null($_SESSION['sessionCharacter'])) 
{
  $chara = $charactersManager->get($_SESSION['sessionCharacter']);
}

//if session opponent still exists, get it
if (isset($_SESSION["sessionOpponent"]) && !is_null($_SESSION['sessionOpponent'])) 
{
  $characterToHit = $charactersManager->get($_SESSION['sessionOpponent']);
}


//if user want to create a new character
if(isset($_POST["createCharacter"]) && !empty($_POST["newCharacterName"])) {

    switch($_POST["characterClass"]) 
    {
        case 'iop':
        $chara = new Iop(["name" => $_POST["newCharacterName"]]);
        break;
    
        case 'sram':
        $chara = new Sram(["name" => $_POST["newCharacterName"]]);
        break;
    
        case 'cra':
        $chara = new Cra(["name" => $_POST["newCharacterName"]]);
        break;
    }

    //if the chosen name isn't valid, delete the new character
    if(!$chara->validName()) {
        unset($chara);
        $message = "Please choose a name.</br>";
    } 
    //else if character already exists, delete the new character
    elseif($charactersManager->doExists($chara->getName())) {
        unset($chara);
        $message = "This name is already used.</br>";
    }
    //store character name in a session var and manager create a new row in the table
    else {
        $_SESSION["sessionCharacter"] = $_POST["newCharacterName"];
        $charactersManager->create($chara);
        $message = "Your character has been created!</br>";
    }
}

//if user wants to select a character
if (isset($_POST['chooseCharacter']) && !empty($_POST['characterName']))
{
  if ($charactersManager->doExists($_POST['characterName']))
  {
    $chara = $charactersManager->get($_POST['characterName']);
    $_SESSION["sessionCharacter"] = $_POST["characterName"];
  }
  else
  {
    $message = 'This character does not exist.';
  }
}

//if user wants to choose an opponent
if (isset($_POST['chooseOpponent']))
{
  if (!isset($chara))
  {
    $message = 'Please create or choose a character to play.';
  }
  
  else
  {
    if (!$charactersManager->doExists($_POST['chooseOpponent']))
    {
      $message = 'The character you\'re trying to hit does not exist.';
    }
    
    else
    {
      $characterToHit = $charactersManager->get($_POST['chooseOpponent']);
      $_SESSION["sessionOpponent"] = $_POST["chooseOpponent"];
    }
  }
}

//if user wants to hit the opponent
if(isset($_GET['hit'])) {
    $result = $chara->hit($characterToHit);

    switch ($result)
    {
    case Character::$me :
        $message = $chara->getName()." se frappe dans sa confusion!</br>";
        break;
    
    case Character::$isHit :
        $message = $chara->getName()." hit ".$characterToHit->getName()." for ".$characterToHit->getDamages()." damages!</br>
        Carefull ".$characterToHit->getName().", you only have ".($characterToHit->getLife())."hp left!</br>";
        
        $charactersManager->update($chara);
        $charactersManager->update($characterToHit);
        break;
    
    case Character::$isKilled :
        $message = "You have killed ".$characterToHit->getName()." !</br>";
        
        $charactersManager->update($chara);
        $charactersManager->delete($characterToHit);
        unset($_SESSION['sessionOpponent']);
        unset($characterToHit);
        break;
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TP : RPG combat</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  </head>

  <body>

  <h4 class="text-center">Total characters created : <?= $charactersManager->count() ?></h4>

</br>

      <div class="container-fluid">
          <div class="row">
            <div class="col-4">
                <form action='' method='POST'>
                    <div>
                        <h2>You can create a new character</h2>
                        <label for='newCharacterName'>Your new character's name :</label>
                        <input type='text' name='newCharacterName' id='newCharacterName' required>
                        <label for="pet-select">Choose a class:</label>
                        <select name="characterClass" id="characterClass">
                            <option value="iop">iop</option>
                            <option value="sram">sram</option>
                            <option value="cra">cra</option>
                        </select>
                    </div>
                    <div>
                        <input type='submit' name='createCharacter' value='Create'>
                    </div>
                </form>
            </div>

            <div class="col-4">
                <form action='' method='POST'>
                    <div>
                        <h2>Or you can choose a character</h2>
                        <label for='characterName'>Your character's name :</label>
                        <input type='text' name='characterName' id='characterName' required>
                    </div>
                    <div>
                        <input type='submit' name='chooseCharacter' value='Choose'>
                    </div>
                </form>
            </div>

            <div class="col-4">
                <form action='' method='POST'>
                    <div>
                        <h2>Choose an opponent</h2>
                        <label for='chooseOpponent'>Your opponent's name :</label>
                        <input type='text' name='chooseOpponent' id='chooseOpponent' required>
                    </div>
                    <div>
                        <input type='submit' value='Fight!'>
                    </div>
                </form>
            </div>

          </div>
      </div>

</br>

    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <?php 
                    if(isset($_SESSION["sessionCharacter"]) && !is_null($_SESSION["sessionCharacter"])) {
                        echo ("<div class='card' style='background-color:".$chara->getColor().";width: 18rem;'>
                                    <div class='card-body text-center'>
                                        <h5 class='card-title'>Now playing as : ".$chara->getName()."</h5>
                                        <p class='card-text'>Lvl : ".$chara->getLvl()."</p>
                                        <p class='card-text'>Xp : ".$chara->getXp()."</p>
                                        <p class='card-text'>Strength : ".$chara->getStrength()."</p>
                                        <p class='card-text'>Life : ".$chara->getLife()."</p>
                                        <p class='card-text'>Damages taken : ".$chara->getDamages()."</p></br>
                                        <a href='index.php?disconnect'class='btn btn-light'>Disconnect</a>
                                    </div>
                            </div>");
                    }
                ?>
            </div>

            <div class="col-4">
                <?php 
                    if(isset($message)) {
                        echo ("<p>Info : ".$message."</p>");
                    }
                ?>
            </div>

            <div class="col-4">
                <?php 
                    if(isset($characterToHit) && !empty($characterToHit)) {
                        echo ("<div class='card' style='background-color:".$characterToHit->getColor().";width: 18rem;'>
                                    <div class='card-body text-center'>
                                        <h5 class='card-title'>Now fighting vs : ".$characterToHit->getName()."</h5>
                                        <p class='card-text'>Lvl : ".$characterToHit->getLvl()."</p>
                                        <p class='card-text'>Xp : ".$characterToHit->getXp()."</p>
                                        <p class='card-text'>Strength : ".$characterToHit->getStrength()."</p>
                                        <p class='card-text'>Life : ".$characterToHit->getLife()."</p>
                                        <p class='card-text'>Damages taken : ".$characterToHit->getDamages()."</p></br>
                                        <a href='index.php?hit'class='btn btn-light'>Hit hard!</a>
                                    </div>
                            </div>");
                    }
                ?>
            </div>
        </div>
    </div>

<?php

include 'combat.php';

//fonction Greg
  function prettyArray(array $nested_arrays): void
    {
        foreach ($nested_arrays as $key => $value) {
            if (gettype($value) !== 'array') {
                echo ('<li class="dump">' . $key . ' : '
                    . $value . '</li>');
            } else {
                echo ('<ul class="dump">' . $key);
                prettyArray($value);
                echo ('</ul>');
            }
        }
    }

  function prettyDump(array $nested_arrays): void
  {
      foreach ($nested_arrays as $key => $value) {
          switch (gettype($value)) {
              case 'array':
                  /* ignore same level recursion */
                  if ($nested_arrays !== $value) {
                      echo ('<details><summary style="color : tomato;'
                          . 'font-weight : bold;">'
                          . $key . '<span style="color : steelblue;'
                          . 'font-weight : 100;"> ('
                          . count($value) . ')</span>'
                          . '</summary><ul style="font-size: 0.75rem;'
                          . 'background-color: ghostwhite">');
                      prettyDump($value);
                      echo ('</ul></details>');
                  }
                  break;
              case 'object':
                  echo ('<details><summary style="color : tomato;'
                      . 'font-weight : bold;">'
                      . $key . '<span style="color : steelblue;'
                      . 'font-weight : 100;"> ('
                      . gettype($value) . ' : ' . get_class($value) . ')</span>'
                      . '</summary><ul style="font-size: 0.75rem;'
                      . 'background-color: ghostwhite">');
                  prettyDump(get_object_vars($value));
                  echo ' <details open><summary style="font-weight : bold;'
                      . 'color : plum">(methods)</summary><pre>';
                  prettyArray(get_class_methods($value));
                  echo '</details></pre>';
                  echo '</li></ul></details>';
                  break;
              case 'callable':
              case 'iterable':
              case 'resource':
                  /* not supported yet */
                  break;
              default:
                  /* scalar and NULL */
                  echo ('<li style="margin-left: 2rem;color: teal;'
                      . 'background-color: white">'
                      . '<span style="color : steelblue;font-weight : bold;">'
                      . $key . '</span> : '
                      . ($value ?? '<span style="font-weight : bold;'
                          . 'color : violet">NULL<span/>')
                      . '</li>');
                  break;
          }
      }
  }
  prettyDump($GLOBALS);
?>
    
  </body>
</html>