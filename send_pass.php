<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 'On');
    session_start();
    $email_pass = $_POST["email"];
    $token_pass = bin2hex(random_bytes(16));
    $token_hash_pass = hash("sha256", $token_pass);
    $expires = date ("Y-m-d H:i:s", time() + 3600);
    echo "xd";
    try {
    require_once "connect.php";
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    $sql = "UPDATE uzytkownicy SET reset_token_hash = ? WHERE email = ?";
    $stmt = $polaczenie->prepare($sql);
    $stmt->bind_param("sss", $token_hash_pass, $expiry, $email_pass);
    $stmt->execute();
    }  
    catch (Exception $e) {
        echo "Error". $e->getMessage(); 
    }
    // if ($polaczenie->affected_rows){
    //     require_once(__DIR__ . '/vendor/autoload.php');
    //     use Symfony\Component\Mailer\Transport;
    //     use Symfony\Component\Mailer\Mailer;
    //     use Symfony\Component\Mime\Email;
    //     try {
    //     $transport = Transport::fromDsn("smtp://2pinfo@mskk.pl:praktyka2024p2info@mail.mskk.pl:465");
    //     $mailer = new Mailer($transport);
    //     $email = (new Email())
    //             ->from("2pinfo@mskk.pl")
    //             ->to($email_to)
    //             ->subject("Resetowanie hasła")
    //             ->html('<p>Kliknij <a href="http://10.15.0.78/logowanie/reset_password.php?token='. $token_hash_pass .'">tutaj</a>, aby zresetować hasło</p>');
    //         $mailer->send($email);
    //         $_SESSION['sendmail']=true;
    //         $_SESSION['registered']=false;
    //         $polaczenie->close();
    //         header('Location: index.php');
    //     } 
    //     catch (Exception $e) {
    //         echo "Wystąpił błąd: " . $e->getMessage();
    //     }
    // }
?>