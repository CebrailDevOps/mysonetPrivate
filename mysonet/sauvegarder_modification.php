<?php
session_start();

if (!isset($_SESSION['pseudo']) || !isset($_POST['poste_id']) || !isset($_POST['contenu'])) {
    header("Location: index.php");
    exit();
}

$poste_id = $_POST['poste_id'];
$contenu = $_POST['contenu'];

include 'db.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("UPDATE mes_postes SET contenu = :contenu WHERE ID = :poste_id");
    $stmt->bindParam(':contenu', $contenu);
    $stmt->bindParam(':poste_id', $poste_id);
    $stmt->execute();

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

header("Location: mes_publications.php");
?>
