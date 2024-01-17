<?php

namespace CMW\Exception\Shop\Payment;

use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use Exception;
use Throwable;


/**
 * Class: @ShopPaymentException
 * @package Shop
 * @author Teyir
 * @version 1.0
 */
class ShopPaymentException extends Exception
{
    /**
     * @param $message
     * @param int $code {@ShopPaymentExceptionType::type}
     * @param \Throwable|null $previous
     */
    public function __construct($message, int $code = ShopPaymentExceptionType::TEST, Throwable $previous = null)
    {
        Flash::send(Alert::ERROR, 'Erreur', $message);
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}