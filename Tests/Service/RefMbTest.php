<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Service\Test;

use Gordalina\Bundle\IfmbBundle\Service\RefMb;

class RefMbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validReferencesProvider
     *
     * @param  integer $entity
     * @param  integer $subEntity
     * @param  integer $order
     * @param  integer $value
     * @param  array   $results
     * @return null
     */
    public function testSimple($entity, $subEntity, $order, $value, array $results)
    {
        $ref = new RefMb();
        $this->assertSame($results, $ref->generate($order, $value, $entity, $subEntity));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Entity "123" must be 5 digits in length
     */
    public function testInvalidEntity()
    {
        $ref = new RefMb();
        $ref->generate(123, 123, 123, 123);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Sub-entity "1" must be 3 digits in length
     */
    public function testInvalidSubEntity()
    {
        $ref = new RefMb();
        $ref->generate(123, 123, 12312, 1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Value "0.99" must be at least 1,00 EUR
     */
    public function testInvalidLowerBoundValue()
    {
        $ref = new RefMb();
        $ref->generate(123, 0.99, 12312, 123);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Value "1000000.00" must be at most 999 999,99 EUR
     */
    public function testInvalidHigherBoundValue()
    {
        $ref = new RefMb();
        $ref->generate(123, 1000000, 12312, 123);
    }

    public function validReferencesProvider()
    {
        return array(
            array(12312, 123, 123, 123, array(
                'entity' => '12312',
                'reference' => '123 012 397',
                'value' => '123,00'
            )),

            array(12312, 123, 123, 200, array(
                'entity' => '12312',
                'reference' => '123 012 383',
                'value' => '200,00'
            )),

            array(11604, 666, 5, 100, array(
                'entity' => '11604',
                'reference' => '666 000 540',
                'value' => '100,00'
            )),

            array(11604, 666, 5000512312, 100, array(
                'entity' => '11604',
                'reference' => '666 231 202',
                'value' => '100,00'
            )),
        );
    }
}
