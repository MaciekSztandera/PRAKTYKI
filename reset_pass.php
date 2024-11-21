<?php
session_start();
$token = $_GET["token"];
$token_hash = hash("sha256", $token);
require_once "connect.php";
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "SELECT * FROM uzytkownicy WHERE reset_token_hash = ?";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user === null) {
    die("Błędny Token");
}
if (strtotime($user["reset_token_expires"]) <= time()) {
    die("Token się unieważnił");
}
if (isset($_POST['password1'])) {
    $pass1 = $_POST['password1'];
    $pass2 = $_POST['password2'];
        if ((strlen($pass1)<8) || (strlen($pass1)>20)) {
			$_SESSION['e_pass']="Hasło musi posiadać od 8 do 20 znaków!";
        }
        if ($pass1!=$pass2) {
			$_SESSION['e_pass']="Podane hasła nie są identyczne!";
        } 
        else {
        $password = password_hash($pass1, PASSWORD_BCRYPT);

        $update_sql = "UPDATE uzytkownicy SET pass = ?, reset_token_hash = NULL, reset_token_expires = NULL WHERE ID = ?";
        $update_stmt = $polaczenie->prepare($update_sql);
        $update_stmt->bind_param("si", $password, $user['ID']);

        if ($update_stmt->execute()) {
            $_SESSION['pass_change'] = true;
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['e_pass'] = "Nie udało się zmienić hasła. Spróbuj ponownie.";
        }
    }
}
$polaczenie->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<form method="post" class="main">
    <p id="heading">Wpisz nowe hasło</p>
    <!-- HASŁO -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>    
    <input autocomplete="off" class="input-field" placeholder="Hasło" type="password" name="password1">
    </div>
    <?php if(isset($_SESSION['e_pass'])) {
        echo '<div class="error">'.$_SESSION['e_pass'].'</div>';
        unset($_SESSION['e_pass']); } 
    ?>
    <!-- POWTÓRZ HASŁO -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>    
    <input autocomplete="off" class="input-field" placeholder="Powtórz Hasło" type="password" name="password2">
    </div>
    <?php if(isset($_SESSION['e_pass'])) {
        echo '<div class="error">'.$_SESSION['e_pass'].'</div>';
        unset($_SESSION['e_pass']); } 
    ?>
    <button>Zapisz hasło</button><br/>
</form>
</body>
</html>
