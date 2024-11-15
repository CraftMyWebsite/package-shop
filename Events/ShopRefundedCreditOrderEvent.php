<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopRefundedCreditOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Refunded-Credit-Order-Event-CraftMyWebsite';
    }
}
