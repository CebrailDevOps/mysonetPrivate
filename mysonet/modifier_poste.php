<?php
session_start();

if (!isset($_SESSION['pseudo']) || !isset($_POST['poste_id'])) {
    header("Location: index.php");
    exit();
}

$poste_id = $_POST['poste_id'];

include 'db.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT contenu FROM mes_postes WHERE ID = :poste_id");
    $stmt->bindParam(':poste_id', $poste_id);
    $stmt->execute();

    $poste_contenu = $stmt->fetchColumn();

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier mon poste - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header"><?php echo $_SESSION['pseudo'] ?> - MySoNet.Online</div>
<div class="navbar">
    <a href="postes_amis.php">Publications des amis</a>
    <a href="mes_publications.php">Mes publications</a>
</div>
<div class="container">
    <h1>Modifier mon poste</h1>
    <form action="sauvegarder_modification.php" method="post">
        <input type="hidden" name="poste_id" value="<?php echo $poste_id; ?>" />
        <textarea name="contenu"><?php echo $poste_contenu; ?></textarea>
        <input type="submit" value="Sauvegarder" />
    </form>
</div>
</body>
</html>