<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Controller\Core\MailController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Utils\Website;


/**
 * Class: @ShopVirtualItemsGiftCodeController
 * @package Shop
 * @author Zomblard
 * @version 1.0
 */
class ShopVirtualItemsGiftCodeController extends AbstractController
{
    /**
     * @param ShopItemEntity $item
     * @param UserEntity $user
     */
    public function sedMailWithGiftCode(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        $giftCodePrefix = "GC_"; // TODO : var ?
        $titre = "Félicitations !"; // TODO : var ?
        $message = "Vous avez reçu une carte cadeau d'une valeur de"; // TODO : var ?
        $use = "Utilisez ce code lors de votre prochain achat sur"; // TODO : var ?
        $url = "https://voyza.fr/shop"; // TODO : var ?
        $amount = $item->getPrice();
        $websiteName = Website::getWebsiteName();

        $code = $this->createCode($giftCodePrefix);

        /*TODO : Create discount like :
        INSERT INTO `cmw_shops_discount` (`shop_discount_id`, `shop_discount_name`, `shop_discount_description`, `shop_discount_linked`, `shop_discount_start_date`, `shop_discount_end_date`, `shop_discount_default_uses`, `shop_discount_uses_left`, `shop_discount_percent`, `shop_discount_price`, `shop_discount_use_multiple_per_users`, `shop_discount_status`, `shop_discount_test`, `shop_discount_code`, `shop_discount_default_active`, `shop_discount_users_need_purchase_before_use`, `shop_discount_quantity_impacted`, `shop_discount_created_at`, `shop_discount_updated_at`) VALUES (NULL, 'Carte cadeau 50€', '', '0', NOW(), '2024-06-14 13:40:13', NULL, '1', NULL, '50', NULL, '1', NULL, 'gc_3292454', '0', NULL, '0', current_timestamp(), current_timestamp());
        */

        $htmlTemplate = <<<HTML
        <html>
        <head>
        <style>
          .gift-card {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
          }
        
          .gift-card h1 {
            color: #333;
          }
        
          .gift-card p {
            color: #666;
          }
        
          .code {
            font-size: 18px;
            color: #007bff;
            margin: 20px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            display: inline-block;
          }
        </style>
        </head>
        <body>
        
        <div class="gift-card">
          <h1>%TITRE%</h1>
          <p>%MESSAGE% <strong>%AMOUNT%€</strong></p>
          <div class="code">%CODE%</div>
          <p>%USE% <a target="_blank" href="%URL%">%WEBSITENAME%</a>.</p>
        </div>
        
        </body>
        </html>
        HTML;

        $body = str_replace(["%TITRE%", "%MESSAGE%", "%AMOUNT%", "%CODE%", "%URL%", "%WEBSITENAME%", "%USE%"],
            [$titre, $message, $amount, $code, $url, $websiteName, $use], $htmlTemplate);
        $object = $websiteName." - Carte cadeau de ". $amount."€";
        MailController::getInstance()->sendMail($user->getMail(), $object, $body);
    }

    private function createCode($giftCodePrefix):string
    {
        $dateComponents = getdate();
        $random1 = rand(0, 9);
        $random2 = rand(0, 9);
        $yearLastTwoDigits = $dateComponents['year'] % 100;
        $code = sprintf(
            "%s%d%d%d%d%d%d%d",
            $giftCodePrefix,
            $random1,
            $dateComponents['mday'],
            $dateComponents['mon'],
            $random2,
            $dateComponents['minutes'],
            $dateComponents['hours'],
            $yearLastTwoDigits,
        );
    }
}