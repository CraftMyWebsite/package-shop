<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopPaymentCancelEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Payment-Cancel-Event-CraftMyWebsite';
    }
}
