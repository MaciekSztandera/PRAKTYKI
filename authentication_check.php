<?php
session_start();
$kod1 = $_GET['kod'];
require_once "connect.php";
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "SELECT * FROM uzytkownicy WHERE auth = ?";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param("s", $kod);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo "1. ".$kod."<br>";
var_dump($user);
// if ($user === null) {
//     $_SESSION['auth'] = true;
//     header('Location: index.php');
//     exit;
// }
// else {
//     $_SESSION['auth'] = false;
//     $_SESSION['logged'] = true;
//     header('Location: main_page.php');
// }
?>