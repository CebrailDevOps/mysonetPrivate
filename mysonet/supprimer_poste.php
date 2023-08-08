<?php
session_start();

if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['poste_id'])) {
    $poste_id = $_POST['poste_id'];

    include 'db.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifiez que l'utilisateur supprime bien son propre poste
        $pseudo = $_SESSION['pseudo'];
        $stmt = $conn->prepare("DELETE FROM mes_postes WHERE ID = :poste_id");
        $stmt->bindParam(':poste_id', $poste_id);
        $stmt->execute();

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    header("Location: mes_publications.php");
}
?>