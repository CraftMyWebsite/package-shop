<?php

namespace CMW\Controller\Shop\Admin\Item\Shipping;

use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity;
use CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersItemsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Shipping\ShopShippingRequirementModel;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Utils\Website;
use DateTime;

/**
 * Class: @ShopShippingNotifyWithdrawController
 * @package Shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopShippingNotifyWithdrawController extends AbstractController
{
    /**
     * @param ShopHistoryOrdersItemsEntity[] $items
     * @param UserEntity $user
     * @param ShopHistoryOrdersEntity $order
     */
    public function sedMailWithInfo(string $varName, array $items, UserEntity $user, ShopHistoryOrdersEntity $order): void
    {
        $requirement = ShopShippingRequirementModel::getInstance();

        $globalName = $requirement->getSetting($varName . '_global') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting_title');

        $withdrawPoint = $order->getShippingMethod()->getShipping()->getWithdrawPoint();

        $titre = $requirement->getSetting($varName . '_title_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting');
        $message = $requirement->getSetting($varName . '_text_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.ready_to_withdraw');
        $use = $requirement->getSetting($varName . '_use_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.show_this');
        $address = $requirement->getSetting($varName . '_address') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.center');

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
            <body style="background-color: %MAINBG%; padding-bottom: 3rem; padding-top: 3rem">

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
        $mainBG = $requirement->getSetting($varName . '_body_color') ?? '#214e7e';

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
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
            Flash::send(Alert::SUCCESS, 'Point de retrait', $user->getPseudo() . ' à été notifié que sa commande est prête pour le retrait dans le centre de dépôt ! vous pouvez valider cette étape !');
        } else {
            Flash::send(Alert::ERROR, 'Point de retrait','Nous n\'avons pas réussi à envoyer le mail au client ! Mais le colis et quand même prêt à être reitré. trouvez un autre moyen de le prévenir !');
        }
    }
}
