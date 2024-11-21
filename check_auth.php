<?php 
    session_start();
    if ($_POST['verification'] === $_SESSION['vericode']) {
        $_SESSION['logged']= true;
        header('Location: main_page.php');
    }
    else {
        $_SESSION['verificate'] = false;
        header('Location: send_auth.php');
    }
?>