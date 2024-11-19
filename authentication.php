<?php 
session_start();
$kod = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$kod_hash = hash("sha256", $kod);
$login = $_SESSION['login'];
require_once 'connect.php';
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "UPDATE uzytkownicy SET auth = ? WHERE user = ?";
$stmt = $polaczenie->prepare($sql);
$stmt -> bind_param("ss", $kod_hash, $login);
$stmt -> execute();

$update_sql = "SELECT email FROM uzytkownicy WHERE user = ?";
$update_stmt = $polaczenie->prepare($update_sql);
$update_stmt->bind_param("s", $login);
$update_stmt->execute();
$result = $update_stmt->get_result();
$user = $result->fetch_assoc();
$email_auth = $user['email'];

require_once(__DIR__ . '/vendor/autoload.php');
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    try {
       $transport = Transport::fromDsn("smtp://2pinfo@mskk.pl:praktyka2024p2info@mail.mskk.pl:465");
       $mailer = new Mailer($transport);
       $email = (new Email())
            ->from("2pinfo@mskk.pl")
            ->to($email_auth)
            ->subject("Kod weryfikacyjny")
            ->html('<p>Twój kod weryfikacyjny: '.$kod_hash.'</p>');
        $mailer->send($email);
        $polaczenie->close();
    } 
    catch (Exception $e) {
        echo "Wystąpił błąd: " . $e->getMessage();
    }  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Weryfikacja</title>
</head>
<body>
<form action="authentication_check.php" method="post" class="main">
    <p id="heading">Weryfikacja</p>
    <p>Aby się zalogować proszę wpisać kod wysłany na Twoją pocztę mailową.</p>
    <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path></svg>
        <input autocomplete="off" class="input-field" type="text" placeholder="Kod" name="kod">
    </div><br/>
    <button>Wyślij</button><br/>
    <a href="index.php">Powrót do logowania</a><br/>
</form>
</body>
</html>