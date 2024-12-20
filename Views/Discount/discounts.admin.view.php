<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

$title = '';
$description = '';

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $upcomingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');

?>
<div class="page-title">
    <h3><i class="fa-solid fa-tag"></i> Promotions</h3>
    <a href="discounts/add" type="button" class="btn-primary">Nouvelle promotion</a>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

<div class="card">
        <h6>Promotions en cours</h6>
    <div class="table-container">
        <table class="table" id="table1">
            <thead>
            <tr>
                <th>Nom</th>
                <th>CODE</th>
                <th>Lié à</th>
                <th>Impacte</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Utilisation</th>
                <th class="text-center">Gérer</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ongoingDiscounts as $discount): ?>
                <tr>
                    <td><?= $discount->getName() ?></td>
                    <td><?= !empty($discount->getCode()) ? $discount->getCode() : "S'applique automatiquement" ?></td>
                    <td><?= $discount->getLinkedFormatted() ?></td>
                    <td>
                        <?php if ($discount->getPrice()): ?>
                            <?= $discount->getPrice() ?> <?= $symbol ?>
                        <?php else: ?>
                            <?= $discount->getPercentage() ?> %
                        <?php endif; ?>
                    </td>
                    <td><?= $discount->getStartDateFormatted() ?></td>
                    <td><?= $discount->getDuration() ?></td>
                    <td><b><?= $discount->getCurrentUses() ?? '0' ?>/<?= $discount->getMaxUses() ?? '∞' ?></b></td>
                    <td class="space-x-2 text-center">
                        <a href="discounts/edit/<?= $discount->getId() ?>" title="Modifier">
                            <i class="text-info fa-solid fa-pen-to-square"></i>
                        </a>
                        <button data-modal-toggle="modal-report-<?= $discount->getId() ?>" title="Reporter">
                            <i class="text-warning fa-solid fa-forward"></i>
                        </button>
                        <button data-modal-toggle="modal-disable-<?= $discount->getId() ?>" title="Désactiver">
                            <i class="text-warning fa-solid fa-ban"></i>
                        </button>
                        <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                            <i class="text-danger fa-solid fa-trash"></i>
                        </button>
                        <!--Report-->
                        <div id="modal-report-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-warning">
                                    <h6>Report de <?= $discount->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-report-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <form action="discounts/report" method="post">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="modal-body">
                                    <div>
                                        <input hidden="" name="id" value="<?= $discount->getId() ?>">
                                        <label for="startDate">Date de début :</label>
                                        <div class="input-group">
                                            <i class="fa-regular fa-clock"></i>
                                            <input type="datetime-local" id="startDate" step="1" name="startDate" value="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn-warning">Reporter</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!--Désactivation-->
                        <div id="modal-disable-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-warning">
                                    <h6>Désactivation de <?= $discount->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-disable-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    Une fois désactivé, vous ne pourrez plus utiliser cette promotion. voyez la comme une suppression archivée.
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/stop/<?= $discount->getId() ?>" type="button" class="btn-warning">Désactiver</a>
                                </div>
                            </div>
                        </div>
                        <!--SUPPRESSION-->
                        <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6>Suppression de <?= $discount->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    Cette suppression est definitive.
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2 mt-6">
    <div class="card">
            <h6>Promotions à venir</h6>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>CODE</th>
                    <th>Impacte</th>
                    <th>Lié à</th>
                    <th>Commence dans</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($upcomingDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= !empty($discount->getCode()) ? $discount->getCode() : "S'applique automatiquement" ?></td>
                        <td>
                            <?php if ($discount->getPrice()): ?>
                                <?= $discount->getPrice() ?> <?= $symbol ?>
                            <?php else: ?>
                                <?= $discount->getPercentage() ?> %
                            <?php endif; ?>
                        </td>
                        <td><?= $discount->getLinkedFormatted() ?></td>
                        <td><?= $discount->getDuration() ?></td>
                        <td class="space-x-2">
                            <a title="Activer maintenant" href="discounts/start/<?= $discount->getId() ?>">
                                <i class="text-success fa-solid fa-rocket"></i>
                            </a>
                            <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </button>
                            <!--SUPPRESSION-->
                            <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header-danger">
                                        <h6>Suppression de <?= $discount->getName() ?></h6>
                                        <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        Cette suppression est definitive.
                                    </div>
                                    <div class="modal-footer">
                                        <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger">Supprimer</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h6>Promotions passées</h6>
        <div class="table-container">
            <table class="table" id="table3">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>CODE</th>
                    <th>Impacte</th>
                    <th>Lié à</th>
                    <th>Utilisation</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pastDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= !empty($discount->getCode()) ? $discount->getCode() : "S'applique automatiquement" ?></td>
                        <td>
                            <?php if ($discount->getPrice()): ?>
                                <?= $discount->getPrice() ?> <?= $symbol ?>
                            <?php else: ?>
                                <?= $discount->getPercentage() ?> %
                            <?php endif; ?>
                        </td>
                        <td><?= $discount->getLinkedFormatted() ?></td>
                        <td><b><?= $discount->getCurrentUses() ?? '0' ?>/<?= $discount->getMaxUses() ?? '∞' ?></b></td>
                        <td>
                            <a title="Supprimé" href="discounts/delete/<?= $discount->getId() ?>">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>