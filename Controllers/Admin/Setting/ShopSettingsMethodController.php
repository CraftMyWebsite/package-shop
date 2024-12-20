<?php

namespace CMW\Controller\Shop\Admin\Setting;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopSettingsMethodController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopSettingsMethodController extends AbstractController
{
    #[Link('/settings/methods', Link::GET, [], '/cmw-admin/shop')]
    private function shopSettings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config');

        $globalConfigMethod = ShopItemsController::getInstance()->getGlobalConfigMethods();

        View::createAdminView('Shop', 'Settings/method')
            ->addVariableList(['globalConfigMethod' => $globalConfigMethod])
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/settings/methods', Link::POST, [], '/cmw-admin/shop', secure: true)]
    private function shopVirtualItemGlobalSettingsPost(): void
    {
        header('Content-Type: application/json');

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config.edit');

        try {
            $newCsrfTokenId = bin2hex(random_bytes(8));
            $newCsrfToken = SecurityManager::getInstance()->getCSRFToken($newCsrfTokenId);

            $settings = $_POST;

            unset($settings['security-csrf-token'], $settings['security-csrf-token-id'], $settings['honeyInput']);

            $errors = [];
            $updatedSettings = [];

            foreach ($settings as $key => $value) {
                if (preg_match('/^methodVarName-(\d+)$/', $key, $matches)) {
                    // Récupérer la valeur de `methodVarName-X`
                    $methodVarName = FilterManager::filterData($value, 50);
                    continue; // Passer à l'itération suivante
                }

                // Filtrer et associer chaque paramètre à son `methodVarName`
                $key = FilterManager::filterData($key, 50);
                $value = FilterManager::filterData($value, 255);

                if (!empty($methodVarName)) {
                    if (ShopSettingsModel::getInstance()->updateOrInsertGlobalSetting($key, $value, $methodVarName)) {
                        $updatedSettings[$methodVarName][$key] = $value;
                    } else {
                        $errors[] = "Impossible de mettre à jour $key pour $methodVarName";
                    }
                }
            }

            // Construire la réponse JSON
            $response = [
                'success' => empty($errors),
                'errors' => $errors,
                'updated_settings' => $updatedSettings,
                'new_csrf_token' => $newCsrfToken,
                'new_csrf_token_id' => $newCsrfTokenId,
            ];

            echo json_encode($response, JSON_THROW_ON_ERROR);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
            exit;
        }
    }

    #[NoReturn] #[Link('/settings/methods/reset/:methodVarName', Link::GET, [], '/cmw-admin/shop')]
    private function shopResetDefaultMethodSettings($methodVarName): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config.edit');

        $method = ShopItemsController::getInstance()->getGlobalConfigMethodsByVarName($methodVarName);

        if (ShopSettingsModel::getInstance()->resetGlobalSetting($methodVarName)) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Paramètre de ' . $method->name() . ' réinitialiser !');
        } else {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de réinitialiser ' . $method->name());
        }

        Redirect::redirectPreviousRoute();
    }
}
