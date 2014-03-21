<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Gordalina\Bundle\IfmbBundle\Model\PaymentNotification;

class PaymentNotificationEvent extends Event
{
    /**
     * @var PaymentNotification
     */
    protected $paymentNotification;

    /**
     * @param PaymentNotification $paymentNotification
     */
    public function __construct(PaymentNotification $paymentNotification)
    {
        $this->paymentNotification = $paymentNotification;
    }

    /**
     * @return PaymentNotification
     */
    public function getPaymentNotification()
    {
        return $this->paymentNotification;
    }
}
