<?php

/* @var \CMW\Entity\Shop\ShopCategoryEntity $category */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Catégorie";
$description = "Ajouter une sous-catégorie";
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span
            class="m-lg-auto">Ajout d'une sous catégorie dans <?= $category->getName() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form class="card" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="card-body">
                <h6>Nom<span style="color: red">*</span> :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="name" required
                           placeholder="Vêtement">
                    <div class="form-control-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <h6>Icon :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="icon"
                           placeholder="fa-solid fa-shirt">
                    <div class="form-control-icon">
                        <i class="fas fa-icons"></i>
                    </div>
                    <small class="form-text">Retrouvez la liste des icones sur le
                        site de <a href="https://fontawesome.com/search?o=r&m=free"
                                   target="_blank">FontAwesome.com</a></small>
                </div>
                <h6>Déscription :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="description"
                           placeholder="Des vêtements">
                    <div class="form-control-icon">
                        <i class="fas fa-paragraph"></i>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check"></i>
                        <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>