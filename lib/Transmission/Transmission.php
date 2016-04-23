<?php
namespace Transmission;

use RuntimeException;
use Transmission\Model\Torrent;
use Transmission\Model\Session;
use Transmission\Util\PropertyMapper;
use Transmission\Util\ResponseValidator;

/**
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
class Transmission
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ResponseValidator
     */
    protected $validator;

    /**
     * @var PropertyMapper
     */
    protected $mapper;

    /**
     * Constructor
     *
     * @param string  $host
     * @param integer $port
     * @param string  $path
     */
    public function __construct($host = null, $port = null, $path = null)
    {
        $this->setClient(new Client($host, $port, $path));
        $this->setMapper(new PropertyMapper());
        $this->setValidator(new ResponseValidator());
    }

    /**
     * Get all the torrents in the download queue
     *
     * @return array
     */
    public function all()
    {
        $response = $this->getClient()->call(
            'torrent-get',
            array('fields' => array_keys(Torrent::getMapping()))
        );

        $torrents = array();

        foreach ($this->getValidator()->validate('torrent-get', $response) as $t) {
            $torrents[] = $this->getMapper()->map(
                new Torrent($this->getClient()),
                $t
            );
        }

        return $torrents;
    }

    /**
     * Get a specific torrent from the download queue
     *
     * @param integer $id
     * @return Torrent
     * @throws RuntimeException
     */
    public function get($id)
    {
        $response = $this->getClient()->call(
            'torrent-get',
            array(
                'fields' => array_keys(Torrent::getMapping()),
                'ids'    => array($id)
            )
        );

        $torrent = null;

        foreach ($this->getValidator()->validate('torrent-get', $response) as $t) {
            $torrent = $this->getMapper()->map(
                new Torrent($this->getClient()),
                $t
            );
        }

        if (!$torrent instanceof Torrent) {
            throw new RuntimeException(
                sprintf("Torrent with ID %s not found", $id)
            );
        }

        return $torrent;
    }

    /**
     * Get the Transmission session
     * 
     * @return Session
     */
    public function getSession(){
        $response = $this->getClient()->call(
            'session-get',
            array()
        );

        return $this->getMapper()->map(
            new Session($this->getClient()),
            $this->getValidator()->validate('session-get', $response)
        );
    }

    /**
     * Add a torrent to the download queue
     *
     * @param string  $torrent
     * @param boolean $metainfo
     * @return Torrent
     */
    public function add($torrent, $metainfo = false)
    {
        $response = $this->getClient()->call(
            'torrent-add',
            array($metainfo ? 'metainfo' : 'filename' => $torrent)
        );

        return $this->getMapper()->map(
            new Torrent($this->getClient()),
            $this->getValidator()->validate('torrent-add', $response)
        );
    }

    /**
     * Set the client used to connect to Transmission
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the client used to connect to Transmission
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the hostname of the Transmission server
     *
     * @param string $host
     */
    public function setHost($host)
    {
        return $this->getClient()->setHost($host);
    }

    /**
     * Get the hostname of the Transmission server
     *
     * @return string
     */
    public function getHost()
    {
        return $this->getClient()->getHost();
    }

    /**
     * Set the port the Transmission server is listening on
     *
     * @param integer $port
     */
    public function setPort($port)
    {
        return $this->getClient()->setPort($port);
    }

    /**
     * Get the port the Transmission server is listening on
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->getClient()->getPort();
    }

    /**
     * Set the mapper used to map responses from Transmission to models
     *
     * @param PropertyMapper $mapper
     */
    public function setMapper(PropertyMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Get the mapper used to map responses from Transmission to models
     *
     * @return PropertyMapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Set the validator used to validate Transmission responses
     *
     * @param ResponseValidator $validator
     */
    public function setValidator(ResponseValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validator used to validate Transmission responses
     *
     * @return ResponseValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
