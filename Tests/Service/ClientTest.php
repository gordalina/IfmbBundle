<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Tests\Service;

use Gordalina\Bundle\IfmbBundle\Service\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testDateFormat()
    {
        $this->assertEquals(
            new \DateTime('2013-12-20 23:53:53'),
            \DateTime::createFromFormat(Client::DATE_FORMAT, '20-12-2013 23:53:53')
        );
    }

    public function testGetPayments()
    {
        $client = $this->getMockBuilder('Gordalina\Bundle\IfmbBundle\Service\Client')
            ->setConstructorArgs(array('endpoint', 'key', true))
            ->setMethods(array('call'))
            ->getMock();

        $client->expects($this->once())
               ->method('call')
               ->will($this->returnCallback(array($this, 'callbackTestGetPayments')));

        $response = $client->getPayments(
            '12345',
            '123',
            \DateTime::createFromFormat(Client::DATE_FORMAT, '20-12-2013 23:53:52'),
            \DateTime::createFromFormat(Client::DATE_FORMAT, '20-12-2013 23:53:52'),
            '999999999',
            '10.25'
        );

        $this->assertTrue(is_array($response));
        $this->assertCount(1, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertSame('ok', $response['code']);
    }

    public function callbackTestGetPayments($query)
    {
        $this->assertSame('chavebackoffice=key&entidade=12345&subentidade=123&sandbox=1&referencia=999999999&valor=10.25', $query);
        return array('code' => 'ok');
    }
}
