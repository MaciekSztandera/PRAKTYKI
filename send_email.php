<?php
    session_start();
    $email_to = $_SESSION["email"];
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    require_once "connect.php";
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    $sql = "UPDATE uzytkownicy SET activation_token = ? WHERE email = ?";
    $stmt = $polaczenie->prepare($sql);
    $stmt -> bind_param("ss", $token_hash, $email_to);
    $stmt->execute();           
    $stmt->close();

    require_once(__DIR__ . '/vendor/autoload.php');
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    try {
       $transport = Transport::fromDsn("smtp://2pinfo@mskk.pl:praktyka2024p2info@mail.mskk.pl:465");
       $mailer = new Mailer($transport);
       $email = (new Email())
            ->from("2pinfo@mskk.pl")
            ->to($email_to)
            ->subject("Potwierdź maila")
            ->html('<p>Kliknij <a href="http://10.15.0.78/logowanie/activation_email.php?token='.$token_hash.'">tutaj</a>, aby potwierdzić maila</p>');
        $mailer->send($email);
        $_SESSION['sendmail'] = true;
        $_SESSION['registered'] = false;
        $polaczenie->close();
        header('Location: index.php');
    } 
    catch (Exception $e) {
        echo "Wystąpił błąd: " . $e->getMessage();
    }
?>