<?php
    session_start();
    if(isset($_POST['email'])) {
        $login_attempt = true;
        $login = $_POST['login'];
        if ((strlen($login)<3) || (strlen($login)>20)) {
			$login_attempt=false;
			$_SESSION['e_login']="Login musi posiadać od 3 do 20 znaków!";
		}
        if (ctype_alnum($login)==false) {
            $login_attempt=false;
            $_SESSION['e_login']="Login musi składać się z tylko z liter i cyfr (bez polskich znaków)!";
        }
        if (ctype_alnum($login)==false) {
            $login_attempt=false;
            $_SESSION['e_login']="Login musi składać się z tylko z liter i cyfr (bez polskich znaków)!";
        }
        $email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$login_attempt=false;
			$_SESSION['e_email']="Podaj poprawny adres e-mail!";
		}
            $pass1 = $_POST['pass1'];
		    $pass2 = $_POST['pass2'];
        if ((strlen($pass1)<8) || (strlen($pass1)>20)) {
            $login_attempt=false;
			$_SESSION['e_pass']="Hasło musi posiadać od 8 do 20 znaków!";
        }
        if ($pass1!=$pass2) {
            $login_attempt=false;
			$_SESSION['e_pass']="Podane hasła nie są identyczne!";
        }
        $pass_hash = password_hash($pass1, PASSWORD_BCRYPT);
        $secret = "6LdJxXUqAAAAAP8UJjIMfCbimLncTig7Z7jKb890";
        $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $answer = json_decode($check);
        if ($answer->success==false) {
            $login_attempt=false;
            $_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
        }
        $_SESSION['fr_login'] = $login;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_pass1'] = $pass1;
		$_SESSION['fr_pass2'] = $pass2;
        require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0) {
				throw new Exception(mysqli_connect_errno());
			}
			else {
				$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE BINARY user = '$login' ");
				if (!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_loginow = $rezultat->num_rows;
				if($ile_takich_loginow>0)
				{
					$login_attempt=false;
					$_SESSION['e_login']="Istnieje już konto z podanym loginem.";
				}
                $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE BINARY email = '$email' ");
				if (!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$login_attempt=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
				}
				if ($login_attempt==true)
				{					
					if ($polaczenie->query("INSERT INTO uzytkownicy (user, email, pass) VALUES ('$login', '$email', '$pass_hash')")) {
						$_SESSION['email'] = $email;
                        $_SESSION['verification']= false;
                        header('Location: send_email.php');
					}
					else {
						throw new Exception($polaczenie->error);
					}
				}
				$polaczenie->close();
			}
		}
		catch(Exception $e) {
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <!-- Skrypt może nie zadziałać przy innym adresie niż 10.15.0.139 lub localhost/127.0.0.1 -->
</head>
<body>
<form method="post" class="main">
    <p id="heading">Rejestracja</p>
    <!-- LOGIN -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>    
    <input autocomplete="off" class="input-field" placeholder="Login" type="text" name="login">
    </div>
    <?php if(isset($_SESSION['e_login'])) {
        echo '<div class="error">'.$_SESSION['e_login'].'</div>';
        unset($_SESSION['e_login']);
    } 
    ?>
    <!-- EMAIL -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-at" viewBox="0 0 16 16"><path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914"/></svg>
    <input autocomplete="off" class="input-field" placeholder="Email" type="text" name="email">
    </div>
    <?php if(isset($_SESSION['e_email'])) {
        echo '<div class="error">'.$_SESSION['e_email'].'</div>';
        unset($_SESSION['e_email']); } 
    ?>
    <!-- HASŁO -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>    
    <input autocomplete="off" class="input-field" placeholder="Hasło" type="password" name="pass1">
    </div>
    <?php if(isset($_SESSION['e_pass'])) {
        echo '<div class="error">'.$_SESSION['e_pass'].'</div>';
        unset($_SESSION['e_pass']); } 
    ?>
    <!-- POWTÓRZ HASŁO -->
    <div class="field">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>    
    <input autocomplete="off" class="input-field" placeholder="Powtórz Hasło" type="password" name="pass2">
    </div><br>
    <div class="g-recaptcha" data-sitekey="6LdJxXUqAAAAAK3ZXVloDwnal58cnUm3JA2-vuL8" data-action="LOGIN"></div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
        const recaptcha = document.querySelector('.g-recaptcha');
        recaptcha.setAttribute("data-theme", "dark");
        });
    </script>
    <?php if(isset($_SESSION['e_bot'])) {
        echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
        unset ($_SESSION['e_bot']); } 
    ?>
    <button>Zarejestruj się</button><br/><br/>
    <p>Masz już konto? <a href="index.php">Zaloguj się</a></p>
</form>
</body>
</html>