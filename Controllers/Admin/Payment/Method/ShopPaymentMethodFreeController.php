<?php

namespace CMW\Controller\Shop\Admin\Payment\Method;

use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Package\AbstractController;

/**
 * Class: @ShopPaymentMethodFreeController
 * @package Shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopPaymentMethodFreeController extends AbstractController
{
    public function sendFreePayment(): void
    {
        Emitter::send(ShopPaymentCompleteEvent::class, []);
    }
}
