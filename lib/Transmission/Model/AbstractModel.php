<?php
namespace Transmission\Model;

use Transmission\Client;

/**
 * Base class for Transmission models
 *
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
abstract class AbstractModel implements ModelInterface
{
    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public static function getMapping()
    {
        return array();
    }
}
