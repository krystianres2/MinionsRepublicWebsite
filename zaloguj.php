<?php

session_start();

if ((!isset($_POST['login'])) || (!isset($_POST['haslo']))) {
    header('Location: index.php');
    exit();
}

require_once "database.php";

// $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

// if ($polaczenie->connect_errno != 0) {
//     echo "Error: " . $polaczenie->connect_errno;
// } else {
$login = $_POST['login'];
// $haslo = $_POST['haslo'];

$login = htmlentities($login, ENT_QUOTES, "UTF-8");
$haslo = filter_input(INPUT_POST, 'haslo');

// $sql = "SELECT * FROM uzytkownicy WHERE user='$login'";
$userQuery = $db->prepare('SELECT * FROM uzytkownicy WHERE user = :login');
$userQuery->bindValue(':login', $login, PDO::PARAM_STR);
$userQuery->execute();

$user = $userQuery->fetch();

if ($user && password_verify($haslo, $user['pass'])) {
    $_SESSION['zalogowany'] = true;
    $_SESSION['id'] = $user['id'];
    $_SESSION['user'] = $user['user'];
    $_SESSION['drewno'] = $user['drewno'];
    $_SESSION['kamien'] = $user['kamien'];
    $_SESSION['zboze'] = $user['zboze'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['dnipremium'] = $user['dnipremium'];
    $_SESSION['image_url'] = $user['image_url'];

    unset($_SESSION['blad']);
    header('Location:gra.php');
} else {
    $_SESSION['blad'] = true;
    header('Location: index.php');
    exit();
}

?>