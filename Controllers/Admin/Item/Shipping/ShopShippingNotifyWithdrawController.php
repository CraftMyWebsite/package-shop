<?php

namespace CMW\Controller\Shop\Admin\Item\Shipping;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Manager\Mail\MailManager;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Shipping\ShopShippingRequirementModel;
use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;
use DateTime;

/**
 * Class: @ShopShippingNotifyWithdrawController
 * @package Shop
 * @author Zomblard
 * @version 1.0
 */
class ShopShippingNotifyWithdrawController extends AbstractController
{
    /**
     * @param ShopHistoryOrdersItemsEntity[] $items
     * @param UserEntity $user
     * @param ShopHistoryOrdersEntity $order
     * @throws \Exception
     */
    public function sedMailWithInfo(string $varName, array $items, UserEntity $user, ShopHistoryOrdersEntity $order): void
    {
        $requirement = ShopShippingRequirementModel::getInstance();

        $globalName = $requirement->getSetting($varName . '_global') ?? 'Colis en attente de retrait';

        $withdrawPoint = $order->getShippingMethod()->getShipping()->getWithdrawPoint();

        $titre = $requirement->getSetting($varName . '_title_mail') ?? 'Retrait en attente';
        $message = $requirement->getSetting($varName . '_text_mail') ?? "Votre commande est prête à être récupérer dans notre centre !";
        $use = $requirement->getSetting($varName . '_use_mail') ?? 'Présenter ce mail pour retirer votre colis !';
        $address = $requirement->getSetting($varName . '_address') ?? 'Adresse du centre :';

        $htmlTemplate = <<<HTML
            <html>
            <head>
            <style>
              .gift-card {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: %CARDBG%;
                border: 1px solid #ddd;
                border-radius: 10px;
                text-align: center;
              }

              .gift-card h1 {
                color: %TITLECOLOR%;
              }

              .gift-card p {
                color: %TEXTCOLOR%;
              }

              .code {
                font-size: 18px;
                color: %CODETEXT%;
                margin: 20px 0;
                padding: 10px;
                background-color: %CODEBG%;
                border-radius: 5px;
                display: inline-block;
              }
            </style>
            </head>
            <body style="background-color: %MAINBG%">

            <div class="gift-card">
              <h1>%TITRE%</h1>
              <p>%MESSAGE%</p>
              <div class="code">#%CODE%</div><br>
              <div style="text-align: left; margin-bottom: 15px">
                            <p>%ADDRESS%</p>
                            <p>%ADDRESS_LINE%</p>
                            <p>%ADDRESS_PC% %ADDRESS_CITY%</p>
                            <p>%ADDRESS_COUNTRY%</p>
                        </div>
              <p>%USE%</p>
            </div>
            </body>
            </html>
            HTML;

        $cardBG = $requirement->getSetting($varName . '_card_color') ?? '#f8f9fa';
        $titleColor = $requirement->getSetting($varName . '_color_title') ?? '#2f2f2f';
        $textColor = $requirement->getSetting($varName . '_color_p') ?? '#656565';
        $codeText = $requirement->getSetting($varName . '_code_color') ?? '#007bff';
        $codeBG = $requirement->getSetting($varName . '_code_bg_color') ?? '#e9ecef';
        $mainBG = $requirement->getSetting($varName . '_body_color') ?? '#ffffff';

        $body = str_replace([
            '%TITRE%',
            '%MESSAGE%',
            '%CODE%',
            '%USE%',
            '%ADDRESS%',
            '%ADDRESS_LINE%',
            '%ADDRESS_PC%',
            '%ADDRESS_CITY%',
            '%ADDRESS_COUNTRY%',
            '%MAINBG%',
            '%CODEBG%',
            '%CODETEXT%',
            '%TEXTCOLOR%',
            '%TITLECOLOR%',
            '%CARDBG%'],
            [
                $titre,
                $message,
                $order->getOrderNumber(),
                $use,
                $address,
                $withdrawPoint->getAddressLine(),
                $withdrawPoint->getAddressPostalCode(),
                $withdrawPoint->getAddressCity(),
                $withdrawPoint->getFormattedCountry(),
                $mainBG,
                $codeBG,
                $codeText,
                $textColor,
                $titleColor,
                $cardBG], $htmlTemplate);
        $object = Website::getWebsiteName() . " - " . $globalName ;
        MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
    }
}
