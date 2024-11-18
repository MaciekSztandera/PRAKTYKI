<?php 
$token = $_GET["token"];
$token_hash = hash("sha256", $token);
require_once "connect.php";
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$sql = "SELECT * FROM uzytkownicy WHERE reset_token_hash = ?";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user === null) {
    die("Błędny Token");
}
if (strtotime($user["reset_token_expires"]) <= time()) {
    die("Token się unieważnił");
}
echo "Poprawny token :D";
// https://youtu.be/R9bfts9ZFjs?si=CavNNY1KJjKKn50a&t=973
// Sprawdź pliki i zmień adresy z lokalhosta na 10.15.0.78
?>
