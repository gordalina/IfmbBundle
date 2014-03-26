<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Gordalina\Bundle\IfmbBundle\Model\PaymentNotification;
use Gordalina\Bundle\IfmbBundle\Event\PaymentNotificationEvent;
use Gordalina\Bundle\IfmbBundle\IfmbEvents;

class CallbackController extends Controller
{
    /**
     * @param  Request  $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($this->container->hasParameter('gordalina_ifmb.anti_phishing_key')) {
            $givenKey = $request->query->get('chave');
            $allowedKey = $this->container->getParameter('gordalina_ifmb.anti_phishing_key');

            if (strcmp($givenKey, $allowedKey) !== 0) {
                return Response::create('', Response::HTTP_UNAUTHORIZED);
            }
        }

        $paymentNotification = PaymentNotification::fromRequest($request);

        try {
            $this->get('event_dispatcher')->dispatch(
                IfmbEvents::PAYMENT_NOTIFICATION,
                new PaymentNotificationEvent($paymentNotification)
            );

            $this->get('logger')->notice(
                'Processed ifmb payment',
                array(
                    'model' => array(
                        'entity' => $paymentNotification->getEntity(),
                        'reference' => $paymentNotification->getReference(),
                        'value' => $paymentNotification->getValue(),
                        'date' => $paymentNotification->getDate(),
                        'terminal' => $paymentNotification->getTerminal(),
                    )
                )
            );

            $status = Response::HTTP_OK;
        } catch (\Exception $e) {
            $this->get('logger')->critical(
                'Exception ocurred when processing a payment notification (IfmbBundle)',
                array(
                    'stacktrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                    'model' => array(
                        'entity' => $paymentNotification->getEntity(),
                        'reference' => $paymentNotification->getReference(),
                        'value' => $paymentNotification->getValue(),
                        'date' => $paymentNotification->getDate(),
                        'terminal' => $paymentNotification->getTerminal(),
                    )
                )
            );

            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return Response::create('', $status);
    }
}
