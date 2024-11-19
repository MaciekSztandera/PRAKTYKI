<?php
    session_start();
    
	if (isset($_SESSION['fr_login'])) unset($_SESSION['fr_login']);
    if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_pass1'])) unset($_SESSION['fr_pass1']);
	if (isset($_SESSION['fr_pass2'])) unset($_SESSION['fr_pass2']);	
	if (isset($_SESSION['e_login'])) unset($_SESSION['e_logink']);
    if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_pass'])) unset($_SESSION['e_pass']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
    if ((isset($_SESSION['logged'])) && ($_SESSION['logged']==true)) {
        header('Location: main_page.php');
        exit();
    }
    if (isset($_SESSION['registered']) && $_SESSION['registered']) {
        $_SESSION['reginfo'] = '<p class ="notification"> Pomyślnie zarejestrowano konto! </p>';
    }
    if (isset($_SESSION['verification']) && $_SESSION['verification']) {
        $_SESSION['verinfo'] = '<p class ="notification"> Nie można zalogować, potwierdź swojego maila! </p>';
    }
    if ((isset($_SESSION['sendmail'])) && ($_SESSION['sendmail']==true)) {
        $_SESSION['mailinfo'] = '<p class ="notification"> Sprawdź skrzynkę pocztową. </p>';
    }
    if ((isset($_SESSION['pass_change'])) && ($_SESSION['pass_change']==true)) {
        $_SESSION['passinfo'] = '<p class ="notification"> Pomyślnie zmieniono hasło. </p>';
    }
    if ((isset($_SESSION['auth'])) && ($_SESSION['auth']==true)) {
        $_SESSION['authinfo'] = '<p class ="notification"> Błąd weryfikacji! Spróbuj ponownie. </p>';
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<form action="login.php" method="post" class="main">
    <p id="heading">Logowanie</p>
    <?php 
    if(isset($_SESSION['reginfo'])) echo $_SESSION['reginfo'];
    if(isset($_SESSION['mailinfo'])) echo $_SESSION['mailinfo'];
    if(isset($_SESSION['passinfo'])) echo $_SESSION['passinfo'];
    if(isset($_SESSION['verinfo'])) echo $_SESSION['verinfo'];
    if(isset($_SESSION['authinfo'])) echo $_SESSION['authinfo'];
    ?>

    <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg>    
        <input autocomplete="off" class="input-field" type="text" placeholder="Login" name="login">
    </div>
    <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path></svg>
        <input autocomplete="off" class="input-field" type="password" placeholder="Hasło" name="pass">
    </div>
        <?php if(isset($_SESSION['err'])) echo $_SESSION['err'];?>
        <a href="forgot_pass.php">Zapomniałem hasła</a><br/>
        <button>Zaloguj się</button><br/><br/>
        <p>Nie masz konta? <a href="registration.php">Utwórz je tutaj</a></p>
</form>
<?php session_unset(); ?>
</body>
</html>