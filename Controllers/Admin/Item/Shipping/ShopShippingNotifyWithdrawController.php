<?php

namespace CMW\Controller\Shop\Admin\Item\Shipping;

use CMW\Controller\Shop\Admin\Notify\ShopNotifyController;
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
    public function sedMailWithInfo(UserEntity $user, ShopHistoryOrdersEntity $order): void
    {
        $globalName = LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting_title');

        $withdrawPoint = $order->getShippingMethod()->getShipping()->getWithdrawPoint();

        $titre = LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting');
        $message = LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.ready_to_withdraw');
        $use = LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.show_this');
        $address = LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.center');

        $htmlTemplate = <<<HTML
              <p>%MESSAGE%</p>
              <b>N°%ORDER_NUMBER%</b><br>
              <p>%USE%</p>
              <p>%CENTER%</p>
              <div style="text-align: left; margin-bottom: 15px">
                <p>%ADDRESS%</p>
                <p>%ADDRESS_LINE%</p>
                <p>%ADDRESS_PC% %ADDRESS_CITY%</p>
                <p>%ADDRESS_COUNTRY%</p>
              </div>
            HTML;

        $body = str_replace([
            '%MESSAGE%',
            '%ORDER_NUMBER%',
            '%USE%',
            '%CENTER%',
            '%ADDRESS%',
            '%ADDRESS_LINE%',
            '%ADDRESS_PC%',
            '%ADDRESS_CITY%',
            '%ADDRESS_COUNTRY%'],
            [
                $message,
                $order->getOrderNumber(),
                $use,
                $address,
                $withdrawPoint->getAddressLine(),
                $withdrawPoint->getAddressPostalCode(),
                $withdrawPoint->getAddressCity(),
                $withdrawPoint->getFormattedCountry()], $htmlTemplate);
        $object = Website::getWebsiteName() . " - " . $globalName ;
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            ShopNotifyController::getInstance()->notifyUser($user->getMail(), $globalName, $titre , $body);
            Flash::send(Alert::SUCCESS, 'Point de retrait', $user->getPseudo() . ' à été notifié que sa commande est prête pour le retrait dans le centre de dépôt ! vous pouvez valider cette étape !');
        } else {
            Flash::send(Alert::ERROR, 'Point de retrait','Nous n\'avons pas réussi à envoyer le mail au client ! Mais le colis et quand même prêt à être reitré. trouvez un autre moyen de le prévenir !');
        }
    }
}
