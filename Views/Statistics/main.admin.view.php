<?php


/* @var int $totalOrders */
/* @var int $refundedOrder */
/* @var int $limit */
/* @var int $gainTotal */
/* @var \CMW\Entity\Shop\Statistics\ShopBestBuyerEntity [] $bestsBuyersThisMonth */
/* @var \CMW\Entity\Shop\Statistics\ShopBestBuyerEntity [] $bestsBuyers */
/* @var \CMW\Entity\Shop\Statistics\ShopBestSellerEntity [] $bestsSeller */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $symbol */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $symbolIsAfter */
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
/* @var array $monthlyGainsAndLossesLastYear*/
/* @var array $monthlyOrderCurrentCompletedAndLossesLastYear*/
/* @var int $perfOrderDiff */
/* @var float $perfOrderPercent*/
/* @var float $perfRevenuePercent*/
/* @var float $perfRevenueDiff*/
/* @var float $refundRate*/
/* @var string $averageOrderProcessingTime*/
/* @var float $averageOrderValue*/

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

<div class="grid-2">
    <div class="card">
        <h6>Gain et pertes par mois</h6>
        <small>Sur année -1</small>
        <div id="chartGainLoss"></div>
    </div>
    <div class="card">
        <h6>Commandes par mois</h6>
        <small>Sur année -1</small>
        <div id="chartCommands"></div>
    </div>
</div>

<hr>

<h5>Performance</h5>
<div class="flex gap-6">
    <div class="card text-center">
        <div style="background: #3398cf" class="icon-background mx-auto">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Commandes</b><br>
            <small>Par rapport au mois dernier</small>
            <h5 class="font-extrabold mb-0" style="color: <?= $perfOrderPercent >= 0 ? 'green' : 'red' ?>">
                <?= $perfOrderPercent > 0 ? '+' : '' ?> <?= $perfOrderPercent ?> %
            </h5>
            <p><?= $perfOrderDiff > 0 ? '+' : '' ?> <?= $perfOrderDiff ?> commande(s)</p>
        </div>
    </div>
    <div class="card text-center">
        <div style="background: #0ab312" class="icon-background mx-auto">
            <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Revenues</b><br>
            <small>Par rapport au mois dernier</small>
            <h5 class="font-extrabold mb-0" style="color: <?= $perfRevenuePercent >= 0 ? 'green' : 'red' ?>">
                <?= $perfRevenuePercent > 0 ? '+' : '' ?> <?= $perfRevenuePercent ?> %</h5>
            <p><?= $perfRevenuePercent > 0 ? '+' : '' ?> <?= $symbolIsAfter ? $perfRevenueDiff . ' ' . $symbol : $symbol . ' ' . $perfRevenueDiff?></p>
        </div>
    </div>
    <div class="card">
        <div style="background: #da2a2a" class="icon-background mx-auto">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Taux de remboursement </b><br>
            <small>Nombre de commandes remboursées par rapport au nombre total de commandes passé. (Garder cette valeur la plus basse possible !)</small>
            <h5 class="font-extrabold mb-0" style="color: <?= $refundRate > 0 ? 'red' : 'green' ?>"><?= $refundRate ?> %</h5>
        </div>
    </div>
</div>
<div class="flex gap-6 mt-6">
    <div class="card text-center">
        <div style="background: #d1ce22" class="icon-background mx-auto">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Temp de traitement moyen</b><br>
            <small>Du moment où l'utilisateur commande jusqu'à ce que la commande soit entièrement finalisé</small>
            <h5 class="font-extrabold mb-0">
                <?= $averageOrderProcessingTime ?>
            </h5>
        </div>
    </div>
    <div class="card text-center">
        <div style="background: #226ed1" class="icon-background mx-auto">
            <i class="fa-solid fa-scale-balanced"></i>
        </div>
        <div class="text-center">
            <b class="text-muted font-semibold">Revenues moyen</b><br>
            <small>Par commandes</small>
            <h5 class="font-extrabold mb-0"><?= $symbolIsAfter ? $averageOrderValue . ' ' . $symbol : $symbol . ' ' . $averageOrderValue?></h5>
        </div>
    </div>
</div>
<hr>

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
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
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
    function getMonths() {
        const monthNames = <?= json_encode(json_decode(LangManager::translate('core.months.list')), JSON_THROW_ON_ERROR); ?>;
        const today = new Date();
        let months = [];

        for (let i = 0; i < 12; i++) {
            let month = new Date(today.getFullYear(), today.getMonth() - i, 1);
            months.unshift(monthNames[month.getMonth()]);
        }
        return months;
    }

    const monthlyData = <?= json_encode($monthlyGainsAndLossesLastYear, JSON_THROW_ON_ERROR) ?>;
    const monthlyData2 = <?= json_encode($monthlyOrderCurrentCompletedAndLossesLastYear, JSON_THROW_ON_ERROR) ?>;
    const gains = Object.values(monthlyData['gains'] ?? []);
    const losses = Object.values(monthlyData['losses'] ?? []);
    const completed = Object.values(monthlyData2['monthly_completed'] ?? []);
    const current = Object.values(monthlyData2['monthly_current'] ?? []);
    const commandsLosses = Object.values(monthlyData2['monthly_losses'] ?? []);

    const areaOptions1 = {
        chart: {
            type: 'area'
        },
        series: [
            {
                name: <?php if ($symbolIsAfter): ?>'Gains (<?= $symbol ?>)' <?php else: ?>'(<?= $symbol ?>) Gains'<?php endif; ?>,
                data: gains
            },
            {
                name: <?php if ($symbolIsAfter): ?>'Pertes (<?= $symbol ?>)' <?php else: ?>'(<?= $symbol ?>) Pertes'<?php endif; ?>,
                data: losses
            }
        ],
        xaxis: {
            categories: getMonths()
        },
        yaxis: {
            title: {
                text: <?php if ($symbolIsAfter): ?>'Montant (<?= $symbol ?>)' <?php else: ?>'(<?= $symbol ?>) Montant'<?php endif; ?>
            },
            labels: {
                formatter: function(value) {
                    return value.toFixed(2); // Limite à 2 décimales
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(value) {
                return <?php if ($symbolIsAfter): ?>value.toFixed(2) + ' <?= $symbol ?>' <?php else: ?>'<?= $symbol ?> ' + value.toFixed(2)<?php endif; ?>;
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value.toFixed(2); // Limite à 2 décimales dans les tooltips
                }
            }
        },
        colors: ['#4CAF50', '#FF0000'],
    };

    const areaChart1 = new ApexCharts(document.querySelector("#chartGainLoss"), areaOptions1);
    areaChart1.render();

    const areaOptions2 = {
        chart: {
            type: 'area'
        },
        series: [
            {
                name: 'Commandes Complétées',
                data: completed
            },
            {
                name: 'Commandes en Cours',
                data: current
            },
            {
                name: 'Commandes Perdues',
                data: commandsLosses
            }
        ],
        xaxis: {
            categories: getMonths()
        },
        yaxis: {
            title: {
                text: 'Nombre de Commandes'
            },
            labels: {
                formatter: function(value) {
                    return value.toFixed(0); // Limite à 0 décimales pour le nombre de commandes
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(value) {
                return value.toFixed(0); // Limite à 0 décimales
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value.toFixed(0); // Limite à 0 décimales dans les tooltips
                }
            }
        },
        colors: ['#4c6faf', '#FFC107', '#FF0000'],
    };

    const areaChart2 = new ApexCharts(document.querySelector("#chartCommands"), areaOptions2);
    areaChart2.render();
</script>

