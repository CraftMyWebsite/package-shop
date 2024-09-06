<?php

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Utils\Website;

/* @var ?int $itemId */
/* @var string $varName */
?>

<!---TODO Rework--->

<label for="<?= $varName ?>object">Objet du mail :</label>
<input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getSetting($varName . 'object', $itemId) ?>"
       placeholder="Boutique <?= Website::getWebsiteName() ?>"
       type="text"
       name="<?= $varName ?>object"
       id="<?= $varName ?>object"
       class="input"
       required
>

<label for="<?= $varName ?>text">Texte du mail :</label>
<input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getSetting($varName . 'text', $itemId) ?>"
       placeholder="Merci pour votre achat"
       type="text"
       name="<?= $varName ?>text"
       id="<?= $varName ?>text"
       class="input"
       required
>