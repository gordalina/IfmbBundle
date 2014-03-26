<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Service;

class Client
{
    const DATE_FORMAT = 'd-m-Y H:i:s';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $sandbox;

    /**
     * @param string $endpoint
     * @param string $key
     * @param string $sandbox
     */
    public function __construct($endpoint, $sandbox)
    {
        $this->endpoint = $endpoint;
        $this->sandbox = $sandbox;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param  string           $entity
     * @param  string           $subEntity
     * @param  \DateTime|string $dateStart  optional
     * @param  \DateTime|string $dateEnd    optional
     * @param  string           $reference  optional
     * @param  string           $value      optional
     * @return array
     */
    public function getPayments(
        $entity,
        $subEntity,
        \DateTime $dateStart = null,
        \DateTime $dateEnd = null,
        $reference = null,
        $value = null
    ) {
        if ($this->key === null) {
            throw new \LogicException("Backoffice key must be set in configuration or in client instance");
        }

        if ($dateStart instanceof \DateTime === false) {
            $dateStart = new \DateTime($dateStart);
        }

        if ($dateEnd instanceof \DateTime === false) {
            $dateEnd = new \DateTime($dateEnd);
        }

        $dateStart = $dateStart->format(self::DATE_FORMAT);
        $dateEnd = $dateEnd->format(self::DATE_FORMAT);

        $query = array(
            'chavebackoffice' => $this->key,
            'entidade' => $entity,
            'subentidade' => $subEntity,
            'sandbox' => $this->sandbox ? 1 : 0
        );

        if ($dateStart instanceof \DateTime) {
            $query['dtHrInicio'] = $dateStart->format(self::DATE_FORMAT);
        }

        if ($dateEnd instanceof \DateTime) {
            $query['dtHrFim'] = $dateEnd->format(self::DATE_FORMAT);
        }

        if ($reference !== null) {
            $query['referencia'] = (string) $reference;
        }

        if ($value !== null) {
            $query['valor'] = (string) $value;
        }

        return $this->call(http_build_query($query));
    }

    /**
     * @param  string $query
     * @return array
     * @throws RuntimeException
     */
    protected function call($query)
    {
        $result = file_get_contents(sprintf("%s/GetPaymentsJson?%s", $this->endpoint, $query));

        if ($result === false) {
            throw new \RuntimeException('Could not call webservice (returned false)');
        }

        $data = json_decode($result, true);

        if (!$data) {
            throw new \RuntimeException('Could not parse response: ' . $result);
        }

        return $data;
    }
}
