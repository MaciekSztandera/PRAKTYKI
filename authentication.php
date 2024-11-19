<?php 
session_start();
$kod = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$login = $_SESSION['login'];
require_once 'connect.php';
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "UPDATE uzytkownicy SET auth = ? WHERE user = ?";
$stmt = $polaczenie->prepare($sql);
$stmt -> bind_param("ss", $kod, $login);
$stmt -> execute();

$update_sql = "SELECT email FROM uzytkownicy WHERE user = ?";
$update_stmt = $polaczenie->prepare($update_sql);
$update_stmt->bind_param("s", $login);
$update_stmt->execute();
$result = $update_stmt->get_result();
$user = $result->fetch_assoc();
$email_auth = $user['email'];
echo "1 ".$kod. "<br/>";
echo "2 ".$login. "<br/>";
echo "3 ".$email_auth. "<br/>";

?>