<?php
//create session and include the partial to get our classes when called on the index
include('config/autoload.php');
session_start();

//create a new PDO and connect to my db
$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=tp-rpg;charset=utf8', 
    'root', 
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

//create a new manager
$charactersManager = new CharactersManager($pdo);

// Si la session perso existe, on restaure l'objet
// if (isset($_SESSION['sessionCharacter'])) 
// {
//   $chara = $_SESSION['sessionCharacter'];
// }

//if user want to login
if(isset($_POST["login"]) && !empty($_POST["loginNickname"])) {
    //store nickname of current user in a session var
    $_SESSION["sessionNickname"] = $_POST["loginNickname"];
}

if(!empty($_SESSION["sessionNickname"])) {
    //if user want to create a new character
    if(isset($_POST["createCharacter"]) && !empty($_POST["characterName"])) {
        //store character name in a session var
        $_SESSION["sessionCharacter"] = $_POST["characterName"];
        //create new character
        $chara = new Character(["name" => $_POST["characterName"]]);

        //if the chosen name isn't valid, delete the new character
        // if(!$chara->valideName()) {
        //     unset($chara);
        //     $message = "Character's name shouldn't have any special symbols, and have at least 3 letters.</br>";
        // } 
        //else if character already exists, delete the new character
        if($charactersManager->doExists($chara->getName())) {
            unset($chara);
            $message = "This name is already used.</br>";
        }
        //manager create a new row in the table
        else {
            $charactersManager->create($chara);
            $message = "Your character has been created!</br>";
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
prettyDump($_POST);
prettyDump($_SESSION);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TP : RPG combat</title>
    <meta charset="utf-8" />
  </head>

  <body>
        <div>
            <form action='' method='POST' id='login-form'>
                <div>    
                    <div>
                        <label for='loginNickname'>Nickname :</label>
                        <input type='text' name='loginNickname' id='loginNickname' required>
                    </div>
                </div>
                <div>
                    <input type='submit' name='login' value='Login'>
                </div>
            </form>
        </div>

        <div>
            <form action='' method='POST' id='create-character'>
                <div>
                    <label for='characterName'>Your character's name :</label>
                    <input type='text' name='characterName' id='characterName' required>
                </div>
                <div>
                    <input type='submit' name='createCharacter' value='Create'>
                </div>
            </form>
        </div>
    <p>Total characters created : <?= $charactersManager->count() ?></p>
<?php
if (isset($message))
  echo ("<p>".$message."</p>");

  if (isset($_SESSION["sessionNickname"]))
  echo ("<p>Logged as : ".$_SESSION["sessionNickname"]."</p>");
?>
    
  </body>
</html>
