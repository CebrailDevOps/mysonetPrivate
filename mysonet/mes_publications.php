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

    $pseudo = $_SESSION['pseudo'];

    // Récupération des posts de l'utilisateur actuellement connecté
    $stmt = $conn->prepare("SELECT * FROM mes_postes ORDER BY date_publication DESC");
    $stmt->execute();

    $mes_postes = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Assurez-vous de vérifier le chemin d'accès au fichier et de le modifier si nécessaire
$file_path = '/home/inspectorsonet/demandes_en_attente';

if (file_exists($file_path)) {
    // Lire le fichier dans un tableau
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Parcourir chaque ligne du fichier
    foreach ($lines as $line) {
        // Diviser la ligne pour obtenir les détails de la demande
        list($ref_demande, $pseudo_demandeur, $ip_demandeur, $date_et_heure) = explode(';', $line);

        // Préparer la requête SQL pour insérer la demande dans la base de données
        $stmt2 = $conn->prepare("INSERT INTO demandes_recues (ref_demande, demandeur, ip_demandeur, date_demande)
                                VALUES (:ref_demande, :demandeur, :ip_demandeur, :date_demande)");

        // Exécuter la requête SQL
        $stmt2->execute([
            ':ref_demande' => $ref_demande,
            ':demandeur' => $pseudo_demandeur,
            ':ip_demandeur' => $ip_demandeur,
            ':date_demande' => $date_et_heure
        ]);
    }

    // Une fois que toutes les demandes sont traitées, vous pouvez vider le fichier
    file_put_contents($file_path, '');
}

// Récupération du nombre de demandes d'ami non traitées
$stmt3 = $conn->query("SELECT COUNT(*) FROM demandes_recues WHERE statut = 'répondre'");
$demandes_ami = $stmt3->fetchColumn();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Mes publications - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header"><?php echo $_SESSION['pseudo'] ?> - MySoNet.Online</div>

<div class="navbar">
    <a href="postes_amis.php">Publications des amis</a>
    <a href="postes_amis.php">Mes publications</a>
    <?php if ($demandes_ami > 0) {
        echo '<a href="notif.php" class="notif-link"><span class="notif-icon">🔔</span><span class="notif-count">'.$demandes_ami.'</span></a>';
    } ?>
</div>

<div class="container">
    <h1>Mes publications</h1>
    <!-- Formulaire pour créer un nouveau poste -->
    <form action="creer_poste.php" method="post">
        <textarea name="contenu" placeholder="Quoi de neuf ?" rows="4"></textarea>
        <br>
        <input type="submit" value="Publier">
    </form>

    <?php
    include 'datePublication.php';
    foreach($mes_postes as $poste) {
        echo "<div class='poste'>";
        echo "<form action='modifier_poste.php' method='post' style='display:inline;'>
            <input type='hidden' name='poste_id' value='" . $poste["ID"] . "' />
            <input type='submit' value='📝' class='edit-button' />
        </form>";
        echo "<form action='supprimer_poste.php' method='post' style='display:inline;'>
                <input type='hidden' name='poste_id' value='" . $poste["ID"] . "' />
                <input type='submit' value='❌' class='delete-button' />
            </form></p>";
        echo "<p><strong>" . tempsDepuisPublication($poste["date_publication"]) . "</strong>: " . $poste["contenu"]. "</p>";
        echo "</div>";
    }
    ?>
</div>
</body>
</html>