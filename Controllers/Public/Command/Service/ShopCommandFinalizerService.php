<?php

namespace CMW\Controller\Shop\Public\Command\Service;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Public\Command\Analyzer\ShopCartAnalyzer;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Manager\Filter\FilterManager;
use CMW\Utils\Redirect;

class ShopCommandFinalizerService extends AbstractController
{
    /**
     * @return void
     */
    public function finalize(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (!$user) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traiter la commande, veuillez vous connecter !');
            Redirect::redirectToHome();
        }

        $sessionId = session_id();

        if (!$sessionId) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de récupérer votre session !');
            Redirect::redirectToHome();
        }

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($user->getId(), $sessionId);

        // Vérifications pré-commande
        ShopCartValidationService::getInstance()->validateBeforeCommand($user->getId(),$sessionId,$cartContent);

        $tunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());

        if (!$tunnel) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traiter la commande !');
            Redirect::redirectToHome();
        }

        // Vérifie l’adresse
        $address = $tunnel->getShopDeliveryUserAddress();
        $requireAddress = !ShopCartAnalyzer::isOnlyVirtual($cartContent) || !ShopCommandService::getInstance()->isShopVirtualOnly();
        if ($requireAddress && (!$address || !$address->getId())) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traiter la commande, adresse introuvable !');
            Redirect::redirectToHome();
        }

        // Vérifie l’envoi (si besoin)
        $cartOnlyVirtual = ShopCartAnalyzer::isOnlyVirtual($cartContent);

        if (!$cartOnlyVirtual && !$tunnel->getShipping()?->getId()) {
            Flash::send(Alert::ERROR, 'Boutique', 'Cette méthode d\'envoi n\'existe plus !');
            ShopCommandTunnelModel::getInstance()->clearTunnel($user->getId());
            Redirect::redirectPreviousRoute();
        }

        // Vérifie la méthode de paiement choisie
        if (!isset($_POST['paymentName'])) {
            Flash::send(Alert::ERROR, 'Erreur', 'Merci de sélectionner une méthode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        $paymentName = FilterManager::filterInputStringPost('paymentName');
        ShopCommandTunnelModel::getInstance()->setPaymentName($user->getId(), $paymentName);

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName($paymentName);

        if (!$paymentMethod) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de trouver ce mode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        try {
            $paymentMethod->doPayment($cartContent, $user);
        } catch (ShopPaymentException $e) {
            Flash::send(Alert::ERROR, 'Erreur', "Erreur de paiement => $e");
            Redirect::redirectPreviousRoute();
        }
    }
}
