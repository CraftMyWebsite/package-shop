<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\ShopCategoriesModel;

$title = "";
$description = "";

/* @var CMW\Model\Shop\ShopCategoriesModel $categories */
/* @var CMW\Model\Shop\ShopItemsModel $items */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <span class="m-lg-auto">Articles</span></h3>
</div>

<section>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php $i = 0;foreach ($categories as $cat): ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?php if ($i === 0): ?>active<?php endif; ?>" id="cat_<?= $cat->getId() ?>_1"
                   data-bs-toggle="tab" href="#cat_<?= $cat->getId() ?>" role="tab"
                   aria-controls="<?= $cat->getId() ?>-tab"
                   aria-selected="<?php if ($i === 0): ?>true<?php else: ?>false<?php endif; ?>"><?= $cat->getName() ?></a>
            </li>
            <?php $i++; endforeach; ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link" type="button" data-bs-toggle="modal" data-bs-target="#add-cat"><i class="fa-solid fa-circle-plus text-success"></i> Ajouter</a>
        </li>
    </ul>
</section>
<section class="tab-content" id="myTabContent">
    <?php $i = 0;
    foreach ($categories as $category): ?>
        <div class="tab-pane fade <?php if ($i === 0): ?>show active<?php endif; ?>" id="cat_<?= $category->getId() ?>"
             role="tabpanel" aria-labelledby="cat_<?= $category->getId() ?>_1">
            <div class="card" style="border-radius: 0">
                <?php foreach ($items as $item): ?>
                    <p><?= $item->getId() ?></p>
                <p><?= $item->getName() ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php $i++; endforeach; ?>
</section>

<!--
----MODAL AJOUT ----
-->
<div class="modal fade text-left" id="add-cat" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="myModalLabel160">Nouveau prefix</h5>
            </div>
            <div class="modal-body">
                <form id="add_cat" action="items/add_cat" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-12 col-lg-6 mt-2">
                            <h6>Nom :</h6>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-12 col-lg-6 mt-2">
                            <h6>Description :</h6>
                            <input type="text" class="form-control" name="description" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                </button>
                <button form="add_cat" type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>

            </div>
        </div>
    </div>
</div>