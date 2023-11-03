<?php

session_start();

if (!isset($_SESSION['zalogowany'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php

    echo "<p>Witaj " . $_SESSION['user'] . '! [<a href="logout.php">Wyloguj się!</a>]</p>';
    echo "<p><b>Drewno</b>: " . $_SESSION['drewno'];
    echo " | <b>Kamień</b>: " . $_SESSION['kamien'];
    echo " | <b>Zboże</b>: " . $_SESSION['zboze'] . "</p>";

    echo "<p><b>E-mail</b>: " . $_SESSION['email'];
    echo "<br /><b>Data wygaśnięcia premium</b>: " . $_SESSION['dnipremium'] . "</p>";
    echo '<img src="uploads/' . $_SESSION['image_url'] . '" width=200px height=200px>';
    echo "<br><br>";
    $dataczas = new DateTime();
    echo "Data i czas serwera: " . $dataczas->format('Y-m-d H:i:s') . "<br>";

    $koniec = DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['dnipremium']);

    $roznica = $dataczas->diff($koniec);

    if ($dataczas < $koniec) {
        echo "Pozostało premium: " . $roznica->format('%y lat, %m mies, %d dni %h godz, %i min, %s sek');
    } else {
        echo "Premium nieaktywne od: " . $roznica->format('%y lat, %m mies, %d dni %h godz, %i min, %s sek');
    }


    // echo time() . "<br>";
    // echo date('Y-m-d H:i:s') . "<br>";
    
    // $dataczas = new DateTime();
    // echo $dataczas->format('Y-m-d H:i:s');
    
    // $dzien = 26;
    // $miesac = 7;
    // $rok = 1875;
    
    // if (checkdate($miesac, $dzien, $rok)) {
    //     echo "<br>Poprawna data!";
    // }
    


    ?>

</body>

</html>