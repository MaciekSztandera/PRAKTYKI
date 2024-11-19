<?php
    session_start();
    $email_pass = $_POST["email"];
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expires = date ("Y-m-d H:i:s", time() + 60 * 30);
    require_once "connect.php";
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    $sql = "UPDATE uzytkownicy SET reset_token_hash = ?, reset_token_expires = ? WHERE email = ?";
    $stmt = $polaczenie->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expires, $email_pass);
    $stmt->execute();

    require_once(__DIR__ . '/vendor/autoload.php');
        use Symfony\Component\Mailer\Transport;
        use Symfony\Component\Mailer\Mailer;
        use Symfony\Component\Mime\Email;
    if ($polaczenie->affected_rows){
        try {
        $transport = Transport::fromDsn("smtp://2pinfo@mskk.pl:praktyka2024p2info@mail.mskk.pl:465");
        $mailer = new Mailer($transport);
        $email = (new Email())
                ->from("2pinfo@mskk.pl")
                ->to($email_pass)
                ->subject("Resetowanie hasła")
                ->html('<p>Kliknij <a href="10.15.0.78/logowanie/reset_pass.php?token='.$token.'">tutaj</a>, aby zresetować hasło</p>');
            $mailer->send($email);
            $_SESSION['sendmail'] = true;
            $_SESSION['pass_change'] = false;
            $polaczenie->close();
            header('Location: index.php');
        } 
        catch (Exception $e) {
            echo "Wystąpił błąd: " . $e->getMessage();
        }
    }
?>