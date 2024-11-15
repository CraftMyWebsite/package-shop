<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopRefundedSelfOrderEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Refunded-Self-Order-Event-CraftMyWebsite';
    }
}
