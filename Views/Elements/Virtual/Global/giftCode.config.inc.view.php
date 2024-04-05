<?php
/* @var string $varName */

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Utils\Website;

?>

<label for="<?=$varName?>object">Objet du mail :</label>
<input value="ss"
       placeholder="Boutique <?= Website::getWebsiteName() ?>"
       type="text"
       name="<?=$varName?>object"
       id="<?=$varName?>object"
       class="form-control"
       required
       >