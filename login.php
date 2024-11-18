<?php
    session_start();
    if((!isset($_POST['login'])) || (!isset($_POST['pass'])))
    {
        header('Location: index.php');
        exit();
    }
    require_once "connect.php";
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    if($polaczenie->connect_errno!=0)
    {
        echo "ERROR: ".$polaczenie->connect_errno;
    }
    else {
        $login = $_POST['login'];
        $pass = $_POST['pass'];
        $login = htmlentities($login, ENT_QUOTES, "UTF-8");
        
        if ($rezultat = @$polaczenie->execute_query("SELECT * FROM uzytkownicy WHERE BINARY user=?", [$login]))
        {
            $ilu_userow = $rezultat->num_rows;
            if($ilu_userow>0)
            {
                $wiersz = $rezultat->fetch_assoc();
                if (password_verify($pass, $wiersz['pass']))
                {
                    $_SESSION['logged']= true;
                    $_SESSION['ID'] = $wiersz['ID'];
                    $_SESSION['user'] = $wiersz['user'];
                    unset($_SESSION['err']);
                    $rezultat->close();
                    header('Location: main_page.php');
                }
                else {
                $_SESSION['err'] = '<span style="color: red; font-size: 20px;">Nieprawidłowy login lub hasło!</span>';
                header('Location: index.php');
                }
            }
            else {
                $_SESSION['err'] = '<span style="color: red; font-size: 20px;">Nieprawidłowy login lub hasło!</span>';
                header('Location: index.php');
            }
        }
        $polaczenie->close();
    }
?>