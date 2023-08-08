<?php
session_start();

if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

try {
    // Créer une connexion
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des demandes d'ami
    $stmt = $conn->prepare("SELECT * FROM demandes_recues WHERE statut = 'répondre' ORDER BY date_demande DESC");
    $stmt->execute();

    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header"><?php echo $_SESSION['pseudo'] ?> - MySoNet.Online</div>
<div class="navbar">
    <a href="postes_amis.php">Publications des amis</a>
    <a href="mes_publications.php">Mes publications</a>
</div>
<div class="container">
    <h1>Notifications</h1>
    <?php
    foreach($demandes as $demande){
        echo "<div class='demande-ami'>";
        echo "<p>Demande d'ami de " . $demande["demandeur"]. "</p>";
        echo "<form method='post' action='accepter_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='accepter'>✅</button>";
        echo "</form>";
        echo "<form method='post' action='refuser_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='refuser'>❌</button>";
        echo "</form></div>";
    }
    ?>
</div>
</body>
</html>
