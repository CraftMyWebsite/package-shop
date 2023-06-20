<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var CMW\Model\Shop\ShopCategories $categoryModel */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <span class="m-lg-auto">Articles</span></h3>
</div>

<section>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php foreach ($categoryModel->getCategories() as $category): ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile">Profile</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contact</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="#contact" aria-controls="contact" aria-selected="false"><i class="fa-solid fa-circle-plus text-success"></i> Ajouter</a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="card" style="border-radius: 0">sdqsdqsd</div>
        </div>
        <div class="tab-pane fade show" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            ss
        </div>
    </div>
