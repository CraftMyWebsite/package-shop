<?php

$title = "Paiements";
$description = "Gérez les méthodes de paiements";

/* @var $methods \CMW\Interface\Shop\IPaymentMethod[] */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cash-register"></i> <span class="m-lg-auto">Paiements</span></h3>
</div>
<section class="list-group-navigation">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Liste des méthodes de paiements</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-4">
                                <div class="list-group" role="tablist">
                                    <?php $i = 1; foreach ($methods as $method): ?>
                                        <a class="list-group-item list-group-item-action <?= $i === 1 ? 'active' : '' ?>" id="list-settings-list"
                                           data-bs-toggle="list" href="#method-<?= $method->name() ?>"
                                           role="tab" aria-selected="<?= $i === 1 ? 'true' : 'false' ?>">
                                            <?= $method->name() ?>
                                        </a>
                                    <?php ++$i; endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 mt-1">
                                <div class="tab-content text-justify" id="nav-tabContent">
                                    <?php $i = 1; foreach ($methods as $method): ?>
                                        <div class="tab-pane <?= $i === 1 ? 'active show' : '' ?>"
                                             id="method-<?= $method->name() ?>" role="tabpanel"
                                             aria-labelledby="list-settings-list">
                                            <?php $method->includeConfigWidgets() ?>
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
