<?php
    session_start();
    $auth_token = bin2hex(random_bytes(16));
    $auth_token_hash = hash("sha256", $auth_token);
    require_once "connect.php";
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    $sql = "UPDATE uzytkownicy SET activation_token = ? WHERE email = ?";
    $stmt = $polaczenie->prepare($sql);
    $stmt -> bind_param("ss", $auth_token_hash, $_SESSION['email']);
    $stmt->execute();           
    $stmt->close();
    require_once(__DIR__ . '/vendor/autoload.php');
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    try {
        $transport = Transport::fromDsn("smtps://emdokka@gazeta.pl:PASSWORD125.@smtp.gazeta.pl:465");
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from("emdokka@gazeta.pl")
            ->to($_SESSION['email'])
            ->subject("Potwierdź maila")
            ->html('<p>Kliknij <a href="10.15.0.78/logowanie/activation_email.php?token='.$auth_token.'">tutaj</a>, aby potwierdzić maila</p>');
        $mailer->send($email);
        $_SESSION['sendmail'] = true;
        $_SESSION['registered'] = false;
        $polaczenie->close();
        header('Location: index.php');
    } 
    catch (Exception $e) {
        echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
    }
?>