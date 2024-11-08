<?php

/* @var int $numberOrderThisMonth */
/* @var int $totalOrders */
/* @var int $refundedOrder */
/* @var int $refundedOrderThisMonth */
/* @var int $limit */
/* @var int $gainTotal */
/* @var \CMW\Entity\Shop\Statistics\ShopBestBuyerEntity [] $bestsBuyersThisMonth */
/* @var \CMW\Entity\Shop\Statistics\ShopBestBuyerEntity [] $bestsBuyers */
/* @var \CMW\Entity\Shop\Statistics\ShopBestSellerEntity [] $bestsSeller */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $symbol */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $symbolIsAfter */
/* @var int $gainThisMonth */
/* @var int $lostThisMonth */
/* @var int $lostTotal */
/* @var int $activeItems */
/* @var int $draftItems */
/* @var int $archivedItems */
/* @var int $itemInCart */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */
/* @var array $gains*/
/* @var array $losses*/

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = 'Statistiques';
$description = 'Stats stats stats';

?>

<style>
    .icon-background {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-background i {
        color: white; /* Couleur de l'icône */
        font-size: 20px; /* Taille de l'icône */
    }

</style>

<h3><i class="fa-solid fa-chart-pie"></i> Statistiques</h3>

<h5>Depuis l'ouverture du Shop</h5>
<div class="flex gap-6">
    <div class="card text-center">
        <div style="background: #3398cf" class="icon-background mx-auto">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Commandes total</b>
            <h5 class="font-extrabold mb-0"><?= $totalOrders ?></h5>
        </div>
    </div>
    <div class="card text-center">
        <div style="background: #da2a2a" class="icon-background mx-auto">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Commandes remboursées</b>
            <h5 class="font-extrabold mb-0"><?= $refundedOrder ?></h5>
        </div>
    </div>
    <div class="card">
        <div style="background: #0ab312" class="icon-background mx-auto">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Gains total</b>
            <h5 style="color: green" class="font-extrabold mb-0">+ <?= $symbolIsAfter ? $gainTotal . ' ' . $symbol : $symbol . ' ' . $gainTotal?></h5>
        </div>
    </div>
    <div class="card">
        <div style="background: #950808" class="icon-background mx-auto">
            <i class="fa-solid fa-arrow-trend-down"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Perte total</b>
            <h5 style="color: red" class="font-extrabold mb-0">- <?= $symbolIsAfter ? $lostTotal . ' ' . $symbol : $symbol . ' ' . $lostTotal?></h5>
        </div>
    </div>
</div>

<hr>

<h5>Du mois en cours</h5>
<div class="flex gap-6">
    <div class="card text-center">
            <div style="background: #5e9dbf" class="icon-background mx-auto">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="text-center">
                <b class="text-muted font-semibold">Commandes total</b>
                <h5 class="font-extrabold mb-0"><?= $numberOrderThisMonth ?></h5>
            </div>
    </div>
    <div class="card text-center">
            <div style="background: #c14444" class="icon-background mx-auto">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="text-center">
                <b class="text-muted font-semibold">Commandes remboursées</b>
                <h5 class="font-extrabold mb-0"><?= $refundedOrderThisMonth ?></h5>
            </div>
    </div>
    <div class="card">
        <div style="background: #5ebf64" class="icon-background mx-auto">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Gains ce mois</b>
            <h5 style="color: green" class="font-extrabold mb-0">+ <?= $symbolIsAfter ? $gainThisMonth . ' ' . $symbol : $symbol . ' ' . $gainThisMonth?></h5>
        </div>
    </div>
    <div class="card">
        <div style="background: #9f1515" class="icon-background mx-auto">
            <i class="fa-solid fa-arrow-trend-down"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Perte ce mois</b>
            <h5 style="color: red" class="font-extrabold mb-0">- <?= $symbolIsAfter ? $lostThisMonth . ' ' . $symbol : $symbol . ' ' . $lostThisMonth?></h5>
        </div>
    </div>
</div>

<hr>

<h5>Articles</h5>
<div class="flex gap-6">
    <div class="card">
        <div style="background: #34b527" class="icon-background mx-auto">
            <i class="fa-solid fa-cart-plus"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Articles dans des paniers</b>
            <h5 class="font-extrabold mb-0"><?= $itemInCart ?></h5>
        </div>
    </div>
    <div class="card text-center">
        <div style="background: #23cfc9" class="icon-background mx-auto">
            <i class="fa-solid fa-shop"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Articles en vente</b>
            <h5 class="font-extrabold mb-0"><?= $activeItems ?></h5>
        </div>
    </div>
    <div class="card text-center">
        <div style="background: #c6aa20" class="icon-background mx-auto">
            <i class="fa-solid fa-box-archive"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Articles archivés</b>
            <h5 class="font-extrabold mb-0"><?= $archivedItems ?></h5>
        </div>
    </div>
    <div class="card">
        <div style="background: #614612" class="icon-background mx-auto">
            <i class="fa-solid fa-compass-drafting"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Article en brouillon</b>
            <h5 class="font-extrabold mb-0"><?= $draftItems ?></h5>
        </div>
    </div>
</div>

<hr>

<div class="card mt-6">
    <h5>Les 5 meilleures ventes</h5>
    <small>Inclus les articles archivés !</small>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th style="max-width: 4rem;" class="text-center">Ventes</th>
                <th>Images</th>
                <th>Nom</th>
                <?php if ($allowReviews): ?>
                    <th class="text-center">Avis</th>
                <?php endif; ?>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th class="text-center">En panier</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bestsSeller as $bestSeller): ?>
                <tr>
                    <td style="max-width: 2rem;" class="text-center"><h4><?= $bestSeller->getSales() ?></h4></td>
                    <td class="text-center" style="width: 6rem; height: 6rem;">
                        <?php $getImagesItem = $imagesItem->getShopImagesByItem($bestSeller->getItem()->getId());
                        $v = 0;
                        foreach ($getImagesItem as $countImage) {
                            $v++;
                        } ?>
                        <?php if ($getImagesItem): ?>
                            <?php if ($v !== 1): ?>
                                <div class="slider-container relative w-full max-w-2xl mx-auto" data-height="80px">
                                    <?php foreach ($getImagesItem as $imagesUrl): ?>
                                        <img src="<?= $imagesUrl->getImageUrl() ?>" alt="..">
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <?php foreach ($imagesItem->getShopImagesByItem($bestSeller->getItem()->getId()) as $imageUrl): ?>
                                    <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                                         src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block"
                                         alt="..."/>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                                 src="<?= $defaultImage ?>" class="p-2 d-block"
                                 alt="..."/>
                        <?php endif; ?>
                    </td>
                    <td style="width: fit-content;">
                        <h6><?= mb_strimwidth($bestSeller->getItem()->getName(), 0, 30, '...') ?></h6>
                    </td>
                    <?php if ($allowReviews): ?>
                        <td class="text-center">
                            <a href="items/review/<?= $bestSeller->getItem()->getId() ?>">
                                <?= $review->countTotalRatingByItemId($bestSeller->getItem()->getId()) ?> avis<br>
                                <?= $review->getStars($bestSeller->getItem()->getId()) ?>
                            </a>
                        </td>
                    <?php endif; ?>
                    <td style="max-width: 5rem;">
                        <?= mb_strimwidth($bestSeller->getItem()->getShortDescription(), 0, 60, '...') ?>
                    </td>
                    <td style="width: fit-content;">
                        <a class="link" data-bs-toggle="tooltip" title="Consulter cette catégorie" target="_blank" href="<?= $bestSeller->getItem()->getCategory()->getCatLink() ?>"><?= $bestSeller->getItem()->getCategory()->getName() ?></a>
                    </td>
                    <td>
                        <b><?= $bestSeller->getItem()->getPriceFormatted() ?></b>
                    </td>
                    <td>
                        <?= $bestSeller->getItem()->getFormattedStock() ?>
                    </td>
                    <td class="text-center">
                        <?= $bestSeller->getItem()->getQuantityInCart() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<hr>

<!--TODO Finish him-->
<div class="card">
    <div id="chartGlobal"></div>
</div>


<div class="card mt-6">
    <div class="flex justify-between">
        <h5>Top <?= $limit ?> des meilleur acheteur</h5>
        <button data-modal-toggle="modal" class="btn-primary-sm" type="button">Changer le nombre de top</button>
    </div>
        <!--MODAL-->
        <div id="modal" class="modal-container">
            <div class="modal">
                <div class="modal-header">
                    <h6>Changer le nombre de top</h6>
                    <button type="button" data-modal-hide="modal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="modal-body">
                        <label for="default-input">Nombre affiché :</label>
                        <input value="<?= $limit ?>" name="limit" type="number" required id="default-input" class="input" placeholder="3">
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal" type="button" class="btn-danger">Fermer</button>
                        <button type="submit" class="btn-primary">Sauvegarder</button>
                    </div>
                </form>

            </div>
        </div>

     <div class="grid-2">
         <div>
             <h6>Top depuis la création</h6>
             <div class="table-container">
                 <table>
                     <thead>
                     <tr>
                         <th class="text-center">Rang</th>
                         <th>Utilisateur</th>
                         <th class="text-center">Montant</th>
                     </tr>
                     </thead>
                     <tbody>
                     <?php $i=1; foreach ($bestsBuyers as $bestBuyer): ?>
                     <tr>
                         <td class="text-center"><?= $i ?></td>
                         <td>
                             <div class="avatar-text">
                                 <img class="avatar-rounded" src="<?= $bestBuyer->getUserImage() ?>" alt="">
                                 <div>
                                     <b><?= $bestBuyer->getUserPseudo() ?></b>
                                 </div>
                             </div>
                         </td>
                         <td class="text-center"><?= $bestBuyer->getFormattedSpent() ?></td>
                     </tr>
                     </tbody>
                     <?php $i++; endforeach; ?>
                 </table>
             </div>
         </div>
         <div>
             <h6>Top du mois</h6>
             <div class="table-container">
                 <table>
                     <thead>
                     <tr>
                         <th class="text-center">Rang</th>
                         <th>Utilisateur</th>
                         <th class="text-center">Montant</th>
                     </tr>
                     </thead>
                     <tbody>
                     <?php $i=1; foreach ($bestsBuyersThisMonth as $bestBuyer): ?>
                     <tr>
                         <td class="text-center"><?= $i ?></td>
                         <td>
                             <div class="avatar-text">
                                 <img class="avatar-rounded" src="<?= $bestBuyer->getUserImage() ?>" alt="">
                                 <div>
                                     <b><?= $bestBuyer->getUserPseudo() ?></b>
                                 </div>
                             </div>
                         </td>
                         <td class="text-center"><?= $bestBuyer->getFormattedSpent() ?></td>
                     </tr>
                     </tbody>
                     <?php $i++; endforeach; ?>
                 </table>
             </div>
         </div>
     </div>
</div>


<script>
    function getLast3Months() {

        const monthNames = <?= LangManager::translate('core.months.list') ?>

        const today = new Date();
        let toReturn = [];

        for (let i = 0; i < 3; i++) {
            toReturn.push(monthNames[(today.getMonth() - i)]);
        }
        return toReturn.reverse();
    }

    const gains = <?= json_encode($gains, JSON_THROW_ON_ERROR) ?>;
    const losses = <?= json_encode($losses, JSON_THROW_ON_ERROR) ?>;

    const areaOptions = {
        chart: {
            type: 'area'
        },
        series: [
            {
                name: 'Gains',
                data: gains
            },
            {
                name: 'Pertes',
                data: losses
            }
        ],
        xaxis: {
            categories: getLast3Months()
        }
    };

    const areaChart = new ApexCharts(document.querySelector("#chartGlobal"), areaOptions);
    areaChart.render();
</script>



