<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class PaymentNotification
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $terminal;

    /**
     * @var string
     */
    protected $key;

    /**
     * @param string $entity
     * @param string $reference
     * @param string $value
     * @param mixed  $date
     * @param string $terminal
     * @param string $key
     */
    public function __construct($entity, $reference, $value, $date, $terminal, $key)
    {
        $this->entity = $entity;
        $this->reference = $reference;
        $this->value = $value;
        $this->date = $date;
        $this->terminal = $terminal;
        $this->key = $key;

        if ($this->date instanceof \DateTime === false && $this->date !== null) {
            $this->date = \DateTime::createFromFormat('d-m-Y H:i:s', $this->date);
        }
    }

    /**
     * @param  Request $request
     * @return PaymentNotification
     */
    public static function fromRequest(Request $request)
    {
        return new static(
            $request->query->get('entidade'),
            $request->query->get('referencia'),
            $request->query->get('valor'),
            $request->query->get('datahorapag', null),
            $request->query->get('terminal', null),
            $request->query->get('chave')
        );
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param  string $key
     * @return boolean
     */
    public function matchesKey($key)
    {
        return $this->key === $key;
    }
}
