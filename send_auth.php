<?php 
session_start();
require_once 'connect.php';
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "SELECT email FROM uzytkownicy WHERE user = ?";
$stmt = $polaczenie->prepare($sql);
$stmt -> bind_param("s", $_SESSION['login']);
$stmt -> bind_result($email_auth);
$stmt -> execute();
$stmt -> fetch();
$verification_code = str_pad(rand(0,999999), 6,'0', STR_PAD_LEFT);
$_SESSION['vericode'] = $verification_code;
require_once(__DIR__ . '/vendor/autoload.php');
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    try {
        $transport = Transport::fromDsn("smtps://emdokka@gazeta.pl:PASSWORD125.@smtp.gazeta.pl:465");
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from("emdokka@gazeta.pl")
            ->to($email_auth)
            ->subject("Logowanie - kod weryfikacyjny")
            ->html('<p>Twój kod weryfikacyjny: '.$_SESSION['vericode'].'</p>');
        $mailer->send($email);
        $polaczenie->close();
    }
    catch (Exception $e) {
        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
		// echo '<br />Informacja developerska: '.$e;
    }
    if (isset($_SESSION['verificate']) && $_SESSION['verificate']==false) {
        $_SESSION['verinfo'] = '<p class ="error"> Błędny kod weryfikacyjny! </p>';
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
<form action="check_auth.php" method="post" class="main">
    <p id="heading">Weryfikacja</p>
    <p>Podaj kod weryfikacyjny wysłany na Twoją pocztę email.</p>
    <?php 
        if(isset($_SESSION['verinfo'])) {
            echo $_SESSION['verinfo'];
            unset ($_SESSION['verinfo']);
        }
    ?>
        <div class="field">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-hash" viewBox="0 0 16 16"><path d="M8.39 12.648a1 1 0 0 0-.015.18c0 .305.21.508.5.508.266 0 .492-.172.555-.477l.554-2.703h1.204c.421 0 .617-.234.617-.547 0-.312-.188-.53-.617-.53h-.985l.516-2.524h1.265c.43 0 .618-.227.618-.547 0-.313-.188-.524-.618-.524h-1.046l.476-2.304a1 1 0 0 0 .016-.164.51.51 0 0 0-.516-.516.54.54 0 0 0-.539.43l-.523 2.554H7.617l.477-2.304c.008-.04.015-.118.015-.164a.51.51 0 0 0-.523-.516.54.54 0 0 0-.531.43L6.53 5.484H5.414c-.43 0-.617.22-.617.532s.187.539.617.539h.906l-.515 2.523H4.609c-.421 0-.609.219-.609.531s.188.547.61.547h.976l-.516 2.492c-.008.04-.015.125-.015.18 0 .305.21.508.5.508.265 0 .492-.172.554-.477l.555-2.703h2.242zm-1-6.109h2.266l-.515 2.563H6.859l.532-2.563z"/></svg>
        <input autocomplete="off" class="input-field" placeholder="Wpisz kod weryfikacyjny" type="text" name="verification">
        </div><br/>
    <button>Zatwierdź</button><br/>
</form>
</body>
</html>