<?php
    // Commencer la session
    session_start();

    // Connexion à la base de données
    include 'db.php';
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer toutes les demandes en attente de reconnexion
    $stmt = $pdo->prepare("SELECT demandeur, ip_demandeur, ref_demande FROM demandes_recues WHERE statut = 'En attente de reconnexion'");
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Parcourir toutes les demandes
    foreach($demandes as $demande) {
        // Pinger l'IP avec un délai d'attente de 2 secondes
        exec("ping -c 1 -W 2 " . escapeshellarg($demande['ip_demandeur']), $output, $result);

        // Si le ping est OK
        if ($result == 0) {
            $stmt2 = $pdo->prepare("SELECT token FROM login LIMIT 1");
            $stmt2->execute(); // Ajoutez cette ligne pour exécuter la requête
            $token = $stmt2->fetchColumn();

            $ip_add = shell_exec("cat /home/inspectorsonet/mysonetPrivate");

            $url = 'http://'.$demande['ip_demandeur'].'/accepte.php?ref_demande='.$demande['ref_demande'].'&ip_add='. $ip_add.'&token='. $token;

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Suivre les redirections
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Si vous ne souhaitez pas récupérer la réponse

            $result = curl_exec($ch);

            if ($result === false) {
                die('Erreur: ' . curl_error($ch));
            }

            curl_close($ch);
        }
    }
?>
