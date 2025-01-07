<?php

use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity[] $historyOrders */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

Website::setTitle("Boutique - Historique d'achat");
Website::setDescription('Consultation de vos achats');

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center; margin-bottom: 1rem">Historique d'achat</h3>
        <?php foreach ($historyOrders as $order): ?>
        <div class="shop-cart-card-45854" style="margin-bottom: 1.6rem">
                <div>
                    <div style="display: flex; justify-content: space-between">
                        <div>
                            <h4 style="font-weight: bolder; font-size: 1.6rem">N°<?= $order->getOrderNumber() ?></h4>
                        </div>
                        <div>Commandé le : <span style="color: #5a8cde"><?= $order->getCreated() ?></span></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p>Statut : <b><?= $order->getPublicStatus() ?></b></p>
                            <?php if ($order->getShippingMethod()): ?>
                                <p>Éxpédition : <?= $order->getShippingMethod()->getName() ?> (<?= $order->getShippingMethod()->getPriceFormatted() ?>)</p>
                            <?php endif; ?>
                            <p>Total : <b><?= $order->getOrderTotalFormatted() ?></b> payé avec <?= $order->getPaymentMethod()->getName() ?> (<?= $order->getPaymentMethod()->getFeeFormatted() ?>)</p>
                            <?php if ($order->getAppliedCartDiscount()): ?>
                                    <p>Réduction appliquée : <b>-<?= $order->getAppliedCartDiscountTotalPriceFormatted() ?></b></p>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; flex-direction: column">
                            <?php if (!empty($order->getShippingLink()) && $order->getStatusCode() === 2): ?>
                            <div>
                                <a href="<?= $order->getShippingLink() ?>" target="_blank" class="shop-link-48725">Suivre le colis</a>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($order->getInvoiceLink())): ?>
                                <div>
                                    <a href="<?= $order->getInvoiceLink() ?>" target="_blank" class="shop-link-48725">Télécharger ma facture</a>
                                </div>
                            <?php endif; ?>
                            <div style="text-align: end">
                                <a class="shop-link-48725" href="history/afterSales/request/<?= $order->getOrderNumber() ?>">Service après vente</a>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 1rem">
                        <?php foreach ($order->getOrderedItems() as $orderItem): ?>
                        <div style="display: flex; margin-bottom: 1rem" class="shop-item-ordered-456744">
                                <div style="width: 10%">
                                    <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                                        <img class="mx-auto" style="width: 8rem; height: 8rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Image de l'article">
                                    <?php else: ?>
                                        <img class="mx-auto" style="width: 8rem; height: 8rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Image de l'article">
                                    <?php endif; ?>
                                </div>

                            <div style="width: 97%;">
                                <div style="display: flex; justify-content: space-between; align-items: center">
                                    <p style="font-weight: bolder"><?= $orderItem->getName() ?></p>
                                    <div><a href="<?= $orderItem->getItem()?->getItemLink() ?>" class="shop-link-48725">Acheter à nouveau</a></div>
                                </div>
                                <br>
                                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $variant): ?>
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
        </div>
        <?php endforeach; ?>
</section>