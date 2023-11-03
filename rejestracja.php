<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Successful validation flag
    $wszystko_OK = true;

    $nick = $_POST['nick'];

    if (strlen($nick) < 3 || strlen($nick) > 20) {
        $wszystko_OK = false;
        $_SESSION['e_nick'] = "Nick musi posiadać od 3 do 20 znaków!";
    }

    if (!ctype_alnum($nick)) {
        $wszystko_OK = false;
        $_SESSION['e_nick'] = "Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
    }

    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (filter_var($emailB, FILTER_VALIDATE_EMAIL) === false || $emailB !== $email) {
        $wszystko_OK = false;
        $_SESSION['e_email'] = "Podaj poprawny adres e-mail";
    }

    $haslo1 = $_POST['haslo1'];
    $haslo2 = $_POST['haslo2'];

    if (strlen($haslo1) < 8 || strlen($haslo1) > 20) {
        $wszystko_OK = false;
        $_SESSION['e_haslo'] = "Hasło musi posiadać od 8 do 20 znaków";
    }

    if ($haslo1 !== $haslo2) {
        $wszystko_OK = false;
        $_SESSION['e_haslo'] = "Hasła nie są identyczne";
    }

    $haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);

    if (!isset($_POST['regulamin'])) {
        $wszystko_OK = false;
        $_SESSION['e_regulamin'] = "Potwierdź akceptację regulaminu";
    }

    if (isset($_FILES['my_image'])) {
        $img_name = $_FILES['my_image']['name'];
        $img_size = $_FILES['my_image']['size'];
        $tmp_name = $_FILES['my_image']['tmp_name'];
        $error = $_FILES['my_image']['error'];

        if (empty($img_name)) {
            $_SESSION['e_image'] = "Please select an image";
            $wszystko_OK = false;
        } elseif ($error === 0) {
            if ($img_size > 500000) {
                $_SESSION['e_image'] = "Sorry, your file is too large.";
                $wszystko_OK = false;
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");
                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                    $img_upload_path = 'uploads/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                } else {
                    $_SESSION['e_image'] = "You can't upload files of this type";
                    $wszystko_OK = false;
                }
            }
        } else {
            $_SESSION['e_image'] = "Unknown error occurred!";
            $wszystko_OK = false;
        }
    } else {
        $_SESSION['e_image'] = "Please select an image";
        $wszystko_OK = false;
    }

    require_once "database.php"; // Assuming this file contains your PDO database connection.

    try {
        $userQuery = $db->prepare('SELECT id FROM uzytkownicy WHERE email = :email');
        $userQuery->bindValue(':email', $email, PDO::PARAM_STR);
        $userQuery->execute();
        $rezultat = $userQuery->fetch();

        if ($rezultat) {
            $wszystko_OK = false;
            $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail";
        }

        $userQuery = $db->prepare('SELECT id FROM uzytkownicy WHERE user = :nick');
        $userQuery->bindValue(':nick', $nick, PDO::PARAM_STR);
        $userQuery->execute();
        $rezultat = $userQuery->fetch();

        if ($rezultat) {
            $wszystko_OK = false;
            $_SESSION['e_nick'] = "Istnieje już gracz o takim nicku!";
        }

        if ($wszystko_OK) {
            $userQuery = $db->prepare('INSERT INTO uzytkownicy VALUES (NULL, :nick, :haslo_hash, :email, 100, 100, 100, NOW() + INTERVAL 14 DAY, :image_url)');
            $userQuery->bindValue(':nick', $nick, PDO::PARAM_STR);
            $userQuery->bindValue(':haslo_hash', $haslo_hash, PDO::PARAM_STR);
            $userQuery->bindValue(':email', $email, PDO::PARAM_STR);
            $userQuery->bindValue(':image_url', $new_img_name, PDO::PARAM_STR);
            $userQuery->execute();
            $_SESSION['udanarejestracja'] = true;
            header('Location: witamy.php');
            exit(); // Make sure to exit after a header redirect.
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
        echo '<br> Informacja developerska: ' . $e;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Osadnicy - załóż darmowe konto</title>
    <style>
        .error {
            color: red;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <form method="post" enctype="multipart/form-data">
        Nickname: <br> <input type="text" name="nick"><br>
        <?php
        if (isset($_SESSION['e_nick'])) {
            echo '<div class="error">' . $_SESSION['e_nick'] . '</div>';
            unset($_SESSION['e_nick']);
        }
        ?>

        E-mail: <br> <input type="text" name="email"><br>
        <?php
        if (isset($_SESSION['e_email'])) {
            echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
            unset($_SESSION['e_email']);
        }
        ?>
        Twoje hasło: <br> <input type="password" name="haslo1"><br>
        <?php
        if (isset($_SESSION['e_haslo'])) {
            echo '<div class="error">' . $_SESSION['e_haslo'] . '</div>';
            unset($_SESSION['e_haslo']);
        }
        ?>
        Powtórz hasło: <br> <input type="password" name="haslo2"><br>

        <label>
            <input type="checkbox" name="regulamin"> Akceptuję regulamin
        </label><br>
        <?php
        if (isset($_SESSION['e_regulamin'])) {
            echo '<div class="error">' . $_SESSION['e_regulamin'] . '</div>';
            unset($_SESSION['e_regulamin']);
        }
        ?>
        <label>
            Zdjęcie profilowe: <br><br>
            <input type="file" name="my_image" id="my_image" onchange="previewImage()">
        </label><br>
        <?php
        if (isset($_SESSION['e_image'])) {
            echo '<div class="error">' . $_SESSION['e_image'] . '</div>';
            unset($_SESSION['e_image']);
            unset($_FILES['my_image']); //TODO think about this
        }
        ?>
        <br><br>
        <!-- Image preview container -->
        <div id="imagePreview">
            <img src="#" alt="Image Preview" style="max-width: 100%; max-height: 300px; display: none;">
        </div>
        <br><br>
        <input type="submit" value="Zarejestruj się">
    </form>
    <script>
        // Function to display image preview
        function previewImage() {
            const fileInput = document.getElementById('my_image');
            const imagePreview = document.querySelector('#imagePreview img');

            if (fileInput.files && fileInput.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                imagePreview.style.display = 'none';
            }
        }
    </script>
</body>

</html>