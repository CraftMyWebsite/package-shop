<?php

namespace CMW\Interface\Shop;

interface IGlobalConfig
{
    /**
     * @return string
     * @desc The name showed in shop add items
     * @example "Downloadable"
     */
    public function name(): string;

    /**
     * @return string
     * @desc The variable name defined automatically
     */
    public function varName(): string;

    /**
     * @return void
     * @desc Include the config widgets for set global variable
     * @example require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Global/reviews.config.inc.view.php";
     */
    public function includeGlobalConfigWidgets(): void;

}
