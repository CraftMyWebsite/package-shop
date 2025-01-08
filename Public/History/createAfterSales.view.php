<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $historyOrder */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

Website::setTitle("Boutique - Serve après ventes");
Website::setDescription('Déclarer un incident sur la commande ' . $historyOrder->getOrderNumber());

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center; margin-bottom: 1rem">Service après-vente</h3>
    <h4 style="font-size: 1.3rem; text-align: center">Déclarer un incident sur la commande N°<?=$historyOrder->getOrderNumber()?></h4>
    <div class="shop-grid-2-command-input-47875">
        <div class="shop-cart-card-45854" style="height: fit-content">
            <div>
                <p>Commandé le : <span style="color: #5a8cde"><?= $historyOrder->getCreated() ?></span></p>
                <p>Statut : <b><?= $historyOrder->getPublicStatus() ?></b></p>
                <?php if ($historyOrder->getShippingMethod()): ?>
                    <p>Éxpédition : <?= $historyOrder->getShippingMethod()->getName() ?> (<?= $historyOrder->getShippingMethod()->getPriceFormatted() ?>)</p>
                <?php endif; ?>
                <p>Total : <b><?= $historyOrder->getOrderTotalFormatted() ?></b> payé avec <?= $historyOrder->getPaymentMethod()->getName() ?> (<?= $historyOrder->getPaymentMethod()->getFeeFormatted() ?>)</p>
                <?php if ($historyOrder->getAppliedCartDiscount()): ?>
                    <p>Réduction appliquée : <b>-<?= $historyOrder->getAppliedCartDiscountTotalPriceFormatted() ?></b></p>
                <?php endif; ?>
            </div>
            <div>
                <?php foreach ($historyOrder->getOrderedItems() as $orderItem): ?>
                    <div style="display: flex; margin-bottom: 1rem" class="shop-item-ordered-456744">
                        <div style="width: 20%">
                            <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                                <img style="width: 8rem; height: 8rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Image de l'article">
                            <?php else: ?>
                                <img style="width: 8rem; height: 8rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Image de l'article">
                            <?php endif; ?>
                        </div>

                        <div style="width: 76%;">
                            <div>
                                <p><?= $orderItem->getName() ?></p>
                            </div>
                            <br>
                            <?php foreach ($historyOrder->getOrderedItemsVariantes($orderItem->getId()) as $variant): ?>
                                <p><?= $variant->getName() ?> : <?= $variant->getValue() ?></p>
                            <?php endforeach; ?>
                            <?php if ($orderItem->getDiscountName()): ?>
                                <p>Réduction appliquée : <b><?= $orderItem->getDiscountName() ?></b> (-<?= $orderItem->getPriceDiscountImpactFormatted() ?>)</p>
                                <p>Prix : <s><?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></s> <b><?= $orderItem->getTotalPriceAfterDiscountFormatted() ?></b> | Quantité : <?= $orderItem->getQuantity() ?></p>
                            <?php else: ?>
                                <p>Prix : <b> <?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></b> | Quantité : <?= $orderItem->getQuantity() ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="shop-cart-card-45854" style="height: fit-content">
            <form method="post" action="<?=$historyOrder->getOrderNumber()?>/create">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <label for="reason" class="shop-label-478541">Raison de la demande<span style="color: red">*</span> :</label>
                <select name="reason" id="reason" class="shop-input-45753">
                    <option value="0">Modification de commande</option>
                    <option value="1">Erreur de commande</option>
                    <option value="2">Produit défectueux</option>
                    <option value="3">Produit endommagé</option>
                    <option value="4">Produit manquant</option>
                    <option value="5">Retard de livraison</option>
                    <option value="6">Non-réception de la commande</option>
                    <option value="7">Problème de taille ou de spécifications</option>
                    <option value="8">Autres</option>
                </select>
                <div style="margin-top: 1rem">
                    <label for="content" class="shop-label-478541">Demande<span class="text-red-500">*</span> :</label>
                    <textarea style="min-height: 8rem" name="content" id="content" required minlength="50" class="shop-input-45753"></textarea>
                </div>
                <div style="display: flex; justify-content: center">
                    <button type="submit" class="shop-button-48751">Soumettre <i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</section>