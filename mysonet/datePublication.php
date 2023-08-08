<?php
function tempsDepuisPublication($datePublication) {
    $date1 = new DateTime($datePublication);
    $date2 = new DateTime(); // Date et heure actuelles
    $diff = $date1->diff($date2);

    $secondes = $diff->s + ($diff->i * 60) + ($diff->h * 60 * 60) + ($diff->d * 24 * 60 * 60);
    $minutes = floor($secondes / 60);
    $heures = floor($minutes / 60);
    $jours = floor($heures / 24);
    $semaines = floor($jours / 7);
    $mois = $diff->m + ($diff->y * 12);
    $ans = $diff->y;

    if ($secondes < 60) {
        return "il y a " . $secondes . "s";
    } elseif ($minutes < 60) {
        return "il y a " . $minutes . "min";
    } elseif ($heures < 24) {
        return "il y a " . $heures . "h";
    } elseif ($jours < 7) {
        return "il y a " . $jours . "j";
    } elseif ($jours < 30) {
        return "il y a " . $semaines . "sem";
    } elseif ($mois < 12) {
        return "il y a " . $mois . " mois";
    } else {
        return "il y a " . ($ans == 1 ? "1 an" : $ans . " ans");
    }
}
?>