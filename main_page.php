<?php
    session_start();
    if(!isset($_SESSION['logged']))
    {
        header('Location: index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Zalogowano</title>
</head>
<body>
<div class="main">
    <?php
    echo '<p id="heading">Zalogowano poprawnie.</p>';    
    echo '<p>Witaj, '.$_SESSION['user'].'!<br/><br/>'; 
    echo '<a href="logout.php"><button type="button" class="button2">Wyloguj się</button></a>';
    ?>
</div>
</body>
</html>