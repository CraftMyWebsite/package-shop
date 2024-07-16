<?php

/* @var int $numberOrderThisMonth */
/* @var int $refundedOrderThisMonth */


$title = "Statistiques";
$description = "Stats stats stats";

?>

<h3><i class="fa-solid fa-chart-pie"></i> Statistiques</h3>

<p>Commande ce mois : <?= $numberOrderThisMonth ?></p>
<p>Commande remboursée ce mois : <?= $refundedOrderThisMonth ?></p>

Nombre d'articles en vente :<br>
Nombre d'articles archivé :<br>
Nombre d'article dans des paniers :<br>
Argent créer depuis le debut :<br>
Argent ce mois-ci :<br>
Argent perdu commande remboursé :<br>
Argent en attente : <br>