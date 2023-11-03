<?php

session_start();

if(isset($_SESSION['zalogowany'])&&($_SESSION['zalogowany']==true)){
    header('Location: gra.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osadnicy - gra przeglądarkowa</title>
</head>

<body>
    Jakiś cytat cos tam cos tam
    <br><br>
    <a href="rejestracja.php">Rejestracja - załóż darmowe konto!</a><br><br>

    <form action="zaloguj.php" method="post">

        Login: <br> <input type="text" name="login"><br>
        Hasło: <br> <input type="password" name="haslo"><br><br>
        <input type="submit" value="Zaloguj się">
    </form>

    <?php
    if(isset($_SESSION['blad'])){
        echo '<p>Niepoprawny login lub hasło!</p>';
        unset($_SESSION['blad']);
    }
    ?>

</body>

</html>