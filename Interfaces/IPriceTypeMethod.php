<?php

namespace CMW\Interface\Shop;

interface IPriceTypeMethod
{
    /**
     * @return string
     * @desc The name of the payment method
     * @example "Money"
     */
    public function name(): string;

    /**
     * @return string
     * @desc The variable name
     * @example "money"
     */
    public function varName(): string;
}
