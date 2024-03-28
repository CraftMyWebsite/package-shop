<?php

$title = "Paiements";
$description = "Gérez les méthodes de paiements";

/* @var $methods \CMW\Interface\Shop\IPaymentMethod[] */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cash-register"></i> <span class="m-lg-auto">Moyens de paiements</span></h3>
</div>
<section class="list-group-navigation">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-2">
                                <div class="list-group" role="tablist">
                                    <?php $i = 1; foreach ($methods as $method): ?>
                                        <a class="list-group-item list-group-item-action <?= $i === 1 ? 'active' : '' ?>" id="list-settings-list"
                                           data-bs-toggle="list" href="#method-<?= $method->varName() ?>"
                                           role="tab" aria-selected="<?= $i === 1 ? 'true' : 'false' ?>">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?= $method->faIcon("fa-xl") ?> <?= $method->name() ?>
                                                </div>
                                                <div>
                                                    <?php if ($method->isActive()): ?>
                                                        <span class="text-success"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Paiement atif." class="fa-solid fa-circle-check"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-warning"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Paiement incatif." class="fa-solid fa-circle-xmark"></i></span>
                                                    <?php endif;?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php ++$i; endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <div class="tab-content text-justify" id="nav-tabContent">
                                    <?php $i = 1; foreach ($methods as $method): ?>
                                        <div class="tab-pane <?= $i === 1 ? 'active show' : '' ?>"
                                             id="method-<?= $method->varName() ?>" role="tabpanel"
                                             aria-labelledby="list-settings-list">
                                            <section>
                                                <div class="card-in-card">
                                                    <?php if ($method->varName() == "free"): ?>
                                                    <div class="card-body">
                                                        <p>Vous ne pouvez pas modifier cette méthode de paiement, car elle est obligatoire si vous vendez des articles gratuits.<br>
                                                        Aucune inquiètude cette méthode de paiement est totalement autonome, elle ne sera disponible que si tout le contenu du panier est égale à 0.
                                                        </p>
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="card-body">
                                                        <div class="">
                                                            <h4><?= $method->faIcon("fa-xl") ?> Configuration des paiements avec <?= $method->name() ?></h4>
                                                            <?php if ($method->isActive()): ?>
                                                                <a href="payments/disable/<?= $method->varName() ?>" class="btn btn-danger btn-sm me-2">Désactiver <?= $method->name() ?></a>
                                                            <?php else: ?>
                                                                <a href="payments/enable/<?= $method->varName() ?>" class="btn btn-success btn-sm me-2">Activer <?= $method->name() ?></a>
                                                            <?php endif;?>
                                                            <?php if ($method->dashboardURL()) : ?>
                                                                <a href="<?= $method->dashboardURL() ?>" target="_blank" class="btn btn-primary btn-sm me-2">Panel de gestion <?= $method->name() ?></a>
                                                            <?php endif;?>
                                                            <?php if ($method->documentationURL()) : ?>
                                                            <a href="<?= $method->documentationURL() ?>" target="_blank" class="btn btn-primary btn-sm">Documentations</a>
                                                            <?php endif;?>
                                                        </div>
                                                        <?php $method->includeConfigWidgets() ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </section>
                                        </div>
                                    <?php ++$i; endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
