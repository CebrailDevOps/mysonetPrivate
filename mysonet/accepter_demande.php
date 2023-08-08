<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['ref_demande'])) {
    $ref_demande = $_POST['ref_demande'];
    $pseudo = $_SESSION['pseudo'];

    // connexion à la base de données
    include 'db.php';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Modifier la demande d'ami
        $stmt = $conn->prepare("UPDATE demandes_recues SET statut = 'En attente de reconnexion' WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        
        $stmt2 = $conn->prepare("SELECT ip_demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt2->bindParam(':ref_demande', $ref_demande);
        $stmt2->execute();
        $ip_demandeur = $stmt2->fetchColumn();

        // Vérifiez si l'IP est valide en envoyant un ping
        exec("ping -c 1 -W 2 " . $ip_demandeur, $output, $result);

        if ($result != 0) { ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Demande acceptée - MySoNet.Online</title>
                <link rel="stylesheet" href="styles.css">
            </head>
            <body>
            <div class="header"><?php echo $_SESSION['pseudo'] ?> - MySoNet.Online</div>
            <div class="navbar">
                <a href="postes_amis.php">Publications des amis</a>
                <a href="mes_publications.php">Mes publications</a>
                <?php if ($demandes_ami > 0) {
                        echo '<a href="notif.php" class="notif-link"><span class="notif-icon">🔔</span><span class="notif-count">'.$demandes_ami.'</span></a>';
                } ?>
            </div>
            <div class="container">
                <p>Vous avez accepté la demande d'ami.<br>Vous pourrez voir ses publications quand son serveur sera en ligne.
                <br>Vous allez être redirigé...</p>
                <script>setTimeout(function(){window.location.href = 'notif.php';}, 5000);</script>
            </div>
            </body>
            </html>
        <?php }
        else {
            $stmt3 = $conn->prepare("SELECT token FROM login WHERE pseudo = :pseudo");
            $stmt3->bindParam(':pseudo', $pseudo);
            $stmt3->execute();
            $token = $stmt3->fetchColumn();
            
            $ip_add_full = shell_exec("hostname -I");
            $ip_add_array = explode(' ', $ip_add_full);
            $ip_add = $ip_add_array[0]; // Prend la première adresse IP
            header('Location: http://'.$ip_demandeur.'/accepte.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add.'&token='. $token);
            exit();
        }
        
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>