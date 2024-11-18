<?php 
session_start();
$token = $_GET['token'];
$token_hash = hash("sha256", $token);
require_once "connect.php";
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "SELECT * FROM uzytkownicy WHERE activation_token = ?";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user === null) {
    echo "Nieprawidłowy token";
}
$_SESSION['registered']=true;
header('Location: index.php');
$polaczenie->close();
?>