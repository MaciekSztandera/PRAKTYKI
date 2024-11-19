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
    exit;
} else {
    $update_sql = "UPDATE uzytkownicy SET active_account='y' WHERE ID = ?";
    $update_stmt = $polaczenie->prepare($update_sql);
    $update_stmt->bind_param("i", $user['ID']);
    $update_stmt->execute();
    $result = $update_stmt->get_result();
    echo $result;
}
$_SESSION['registered']=true;
header('Location: index.php');
$polaczenie->close();
?>