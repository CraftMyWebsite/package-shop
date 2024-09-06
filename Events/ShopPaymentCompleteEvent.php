<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopPaymentCompleteEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Payment-Complete-Event-CraftMyWebsite';
    }
}
