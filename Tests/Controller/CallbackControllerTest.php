<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Tests\Controller;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Gordalina\Bundle\IfmbBundle\IfmbEvents;
use Gordalina\Bundle\IfmbBundle\Controller\CallbackController;

class CallbackControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testValidResponse()
    {
        $controller = new CallbackController();
        $controller->setContainer($this->getContainer());

        $response = $controller->indexAction(new Request(array(
            'chave' => '0000-0000-0000-0000',
            'referencia' => '12345',
            'entidade' => '123',
            'valor' => '1.23',
        )));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testEventDispatched()
    {
        $ev = new EventDispatcher();
        $ev->addListener(IfmbEvents::PAYMENT_NOTIFICATION, array($this, 'eventTestEventDispatched'));

        $controller = new CallbackController();
        $controller->setContainer($this->getContainer(array(
            'event_dispatcher' => $ev
        )));

        $response = $controller->indexAction(new Request(array(
            'chave' => '0000-0000-0000-0000',
            'referencia' => '12345',
            'entidade' => '123',
            'valor' => '1.23',
            'datahorapag' => '20-12-2013 23:53:52',
            'terminal' => 'ATM'
        )));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function eventTestEventDispatched($event)
    {
        $this->assertInstanceOf('Gordalina\Bundle\IfmbBundle\Event\PaymentNotificationEvent', $event);

        $entity = $event->getPaymentNotification();

        $this->assertSame('123', $entity->getEntity());
        $this->assertSame('12345', $entity->getReference());
        $this->assertSame('1.23', $entity->getValue());
        $this->assertEquals(new \DateTime('2013-12-20 23:53:52'), $entity->getDate());
        $this->assertSame('ATM', $entity->getTerminal());
    }

    public function testUnauthorizedKey()
    {
        $controller = new CallbackController();
        $controller->setContainer($this->getContainer());

        $response = $controller->indexAction(new Request(array(
            'chave' => '1111-1111-1111-1111',
            'referencia' => '12345',
            'entidade' => '123',
            'valor' => '1.23',
        )));

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testExceptionHandling()
    {
        $ev = new EventDispatcher();
        $ev->addListener(IfmbEvents::PAYMENT_NOTIFICATION, function () {
            throw new \Exception('Test');
        });

        $controller = new CallbackController();
        $controller->setContainer($this->getContainer(array(
            'event_dispatcher' => $ev
        )));

        $response = $controller->indexAction(new Request(array(
            'chave' => '0000-0000-0000-0000',
            'referencia' => '12345',
            'entidade' => '123',
            'valor' => '1.23',
            'datahorapag' => '20-12-2013 23:53:52',
            'terminal' => 'ATM'
        )));

        $this->assertSame(500, $response->getStatusCode());
    }

    protected function getContainer(array $services = array())
    {
        $container = new Container();
        $container->setParameter('gordalina_ifmb.anti_phishing_key', '0000-0000-0000-0000');

        $services = array_merge(array(
            'event_dispatcher' => $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher'),
            'logger' => $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')->disableOriginalConstructor()->getMock(),
        ), $services);

        foreach ($services as $id => $service) {
            $container->set($id, $service);
        }

        return $container;
    }
}
