<?php
session_start();

if (!isset($_SESSION['pseudo']) || empty($_POST['contenu'])) {
    header("Location: mes_publications.php");
    exit();
}

include 'db.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pseudo = $_SESSION['pseudo'];
    $contenu = $_POST['contenu'];
    $date_publication = date('Y-m-d H:i:s'); // Vous pouvez ajuster le format de la date si nécessaire

    $stmt = $conn->prepare("INSERT INTO mes_postes (contenu) VALUES (:contenu)");
    $stmt->execute([':contenu' => $contenu]);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

header("Location: mes_publications.php");
?>