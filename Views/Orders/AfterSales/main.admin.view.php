<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity [] $afterSales */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\MailModel;

$title = 'Services après ventes';
$description = 'SAV';

?>
<h3><i class="fa-solid fa-headset"></i> Services après-ventes</h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

    <div class="card">
        <h6>En cours</h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>N° de commande</th>
                    <th>Raison</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale): ?>
                    <?php if ($afterSale->getStatus() !== 2): ?>
                        <tr>
                            <td><?= $afterSale->getAuthor()->getPseudo() ?></td>
                            <td><a class="link" href="orders/view/<?= $afterSale->getOrder()->getId() ?>">#<?= $afterSale->getOrder()->getOrderNumber() ?></a></td>
                            <td><?= $afterSale->getFormattedReason() ?></td>
                            <td><?= $afterSale->getFormattedStatus() ?></td>
                            <td><?= $afterSale->getCreated() ?></td>
                            <td class="text-center">
                                <a href="afterSales/manage/<?= $afterSale->getId() ?>">
                                    <i class="text-success fa-solid fa-headset"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6">
        <h6>Traité</h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>N° de commande</th>
                    <th>Raison</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale): ?>
                    <?php if ($afterSale->getStatus() === 2): ?>
                        <tr>
                            <td><?= $afterSale->getAuthor()->getPseudo() ?></td>
                            <td><a class="link" href="orders/view/<?= $afterSale->getOrder()->getId() ?>">#<?= $afterSale->getOrder()->getOrderNumber() ?></a></td>
                            <td><?= $afterSale->getFormattedReason() ?></td>
                            <td><?= $afterSale->getFormattedStatus() ?></td>
                            <td><?= $afterSale->getCreated() ?></td>
                            <td class="text-center">
                                <a href="afterSales/manage/<?= $afterSale->getId() ?>">
                                    <i class="text-info fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>