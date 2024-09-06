<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = '';
$description = '';

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-truck"></i> <span class="m-lg-auto">Livraisons</span></h3>
</div>

<div class="grid-4">
    <div class="card">
        <h6>Réglages</h6>
        <label for="depotDist">Distance du dépôt (km) :</label>
        <input class="input" name="depotDist" id="depotDist" value="" placeholder="20">
        <small>Determine le rayon d'affichage des dépot disponible entre l'adresse de l'utilisateur et l'adresse des dépôt</small>
    </div>
    <div class="card col-span-3">
        <div class="tab-menu">
            <ul class="tab-horizontal" data-tabs-toggle="#tab-content-1">
                <li>
                    <button data-tabs-target="#tab1" role="tab">Éxpedition</button>
                </li>
                <li>
                    <button data-tabs-target="#tab2" role="tab">Click and collect (Dépôt)</button>
                </li>
            </ul>
        </div>
        <div id="tab-content-1">
            <div class="tab-content" id="tab1">
                <h6>Tab1</h6>
            </div>
            <div class="tab-content" id="tab2">
                <h6>Tab2</h6>
            </div>
        </div>
    </div>
</div>


Add :<br>
Condition de déclenchement :<br>
Si le total du panier dépasse max_total_cart_price alors on ne l'affiche pas<br>
min_total_cart_price est le seuil minimal de déclenchement de cette méthode d'envoie<br>
Si le total du panier est inférieur à min_total_cart_price alors on ne l'affiche pas<br><br>

Dans le details modal explique en cas de condition de declanchement ce qui se passe concrétement (si la panier depasse X alors il se passerais tel choses ...)