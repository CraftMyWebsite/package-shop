<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopEditCatEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Edit-Cat-Event-CraftMyWebsite';
    }
}
