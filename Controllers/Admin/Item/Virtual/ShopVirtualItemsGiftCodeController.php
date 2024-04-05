<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MailController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;
use DateTime;


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
     * @throws \Exception
     */
    public function sedMailWithGiftCode(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        $giftCodePrefix = "GC_"; // TODO : var ?
        $code = $this->createCode($giftCodePrefix);
        $websiteName = Website::getWebsiteName();

        $symbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        if ($symbolIsAfter) {
            $amount = $item->getPrice() . $symbol;
        } else {
            $amount = $symbol . $item->getPrice();
        }

        $timestamp = time();
        $dateTime = date('Y-m-d H:i:s', $timestamp);
        $date = new DateTime($dateTime);
        $date->modify('+1 year');
        $endDateTime = $date->format('Y-m-d H:i:s');

        if (!ShopDiscountModel::getInstance()->createDiscount("Carte cadeau ".$amount,"",3,$dateTime,$endDateTime,1,0,null,$item->getPrice(),null,1,0,$code,0,0,0)){
            Flash::send(Alert::ERROR,'Erreur', "Impossible de créer la carte cadeau de ".$amount."!");
            MailController::getInstance()->sendMail($user->getMail(), $websiteName." - Carte cadeau de ". $amount, "Nous n'avons pas réussi à créer votre bon cadeau de".$amount.". Veuillez contacter l'administrateur du site web pour le prévenir !");
        }

        $formattedEndDate = CoreController::formatDate($endDateTime);
        $titre = "Félicitations !"; // TODO : var ?
        $message = "Vous avez reçu une carte cadeau d'une valeur de"; // TODO : var ?
        $use = "Utilisez ou partager ce code lors de votre prochain achat sur"; // TODO : var ?
        $timeLeft = "Ce code est valable jusqu'au ". $formattedEndDate; // TODO : var ?
        $url = "https://voyza.fr/shop"; // TODO : var ?

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
          <p>%MESSAGE% <strong>%AMOUNT%</strong>
          <div class="code">%CODE%</div><br>
          %USE% <a href="%URL%">%WEBSITENAME%</a>.<br>
          %TIME_LEFT%</p>
        </div>
        </body>
        </html>
        HTML;

        $body = str_replace(["%TITRE%", "%MESSAGE%", "%AMOUNT%", "%CODE%", "%URL%", "%WEBSITENAME%", "%USE%", "%TIME_LEFT%"],
            [$titre, $message, $amount, $code, $url, $websiteName, $use, $timeLeft], $htmlTemplate);
        $object = $websiteName." - Carte cadeau de ". $amount;
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
        return $code;
    }
}