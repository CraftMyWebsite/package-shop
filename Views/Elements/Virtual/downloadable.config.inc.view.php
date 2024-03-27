<?php

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;

?>

    <div class="row">
        <div class="form-group">
            <label for="downloadable_text">texte :</label>
            <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getSetting('downloadable_text') ?>"
                   placeholder="SECRET_KEY"
                   type="text"
                   name="downloadable_text"
                   id="downloadable_text"
                   class="form-control"
                   >
        </div>
    </div>