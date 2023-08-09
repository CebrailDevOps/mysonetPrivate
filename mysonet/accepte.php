<?php
if (isset($_GET['ref_demande']) AND isset($_GET['ip_add']) AND isset($_GET['token'])) {
    $ref_demande = $_GET['ref_demande'];
    $ip_add = $_GET['ip_add'];
    $token = $_GET['token'];

    // Assurez-vous de vérifier le chemin d'accès au fichier et de le modifier si nécessaire
    $file_path = '/home/inspectorsonet/demandes_envoyees';

    if (file_exists($file_path)) {
        // Lire le fichier dans un tableau
        $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // Créer un tableau pour stocker les lignes que nous voulons conserver
        $new_lines = [];

        // Parcourir chaque ligne du fichier
        foreach ($lines as $line) {
            // Diviser la ligne pour obtenir les détails de la demande
            list($ref_demande2, $pseudo_demande) = explode(';', $line);

            if ($ref_demande != $ref_demande2) {
                // Si la ligne ne correspond pas à ref_demande, ajoutez-la au nouveau tableau
                $new_lines[] = $line;
            } else {
                include 'db.php';
                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    // Préparer la requête SQL pour insérer le nouvel ami dans la base de données
                    $stmt = $conn->prepare("INSERT INTO mes_amis (pseudo, ip_add, token) VALUES (:pseudo, :ip_add, :token)");
                    $stmt->bindParam(':pseudo', $pseudo_demande);
                    $stmt->bindParam(':ip_add', $ip_add);
                    $stmt->bindParam(':token', $token);
                    // Exécuter la requête SQL
                    $stmt->execute();
                    $stmt2 = $conn->prepare("SELECT token FROM login");
                    $stmt2->execute();
                    $mon_token = $stmt2->fetchColumn();
                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                    exit(); // Arrêter l'exécution si la connexion échoue
                }
            }
        }
        // Écrire le nouveau tableau dans le fichier
        file_put_contents($file_path, implode(PHP_EOL, $new_lines));
        header('Location: http://mysonet.online/reponse.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add.'&token='.$mon_token);
    }
}
?>