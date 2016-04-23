<?php
namespace phuong17889\transmission\models;

use Exception;
use phuong17889\transmission\core\AbstractModel;
use phuong17889\transmission\helpers\PropertyMapper;
use phuong17889\transmission\helpers\ResponseValidator;

/**
 * @author Ramon Kleiss <ramon@cubilon.nl>
 */
class Torrent extends AbstractModel {

	/**
	 * @var integer
	 */
	const STATUS_STOPPED = 0;

	/**
	 * @var integer
	 */
	const STATUS_CHECK_WAIT = 1;

	/**
	 * @var integer
	 */
	const STATUS_CHECK = 2;

	/**
	 * @var integer
	 */
	const STATUS_DOWNLOAD_WAIT = 3;

	/**
	 * @var integer
	 */
	const STATUS_DOWNLOAD = 4;

	/**
	 * @var integer
	 */
	const STATUS_SEED_WAIT = 5;

	/**
	 * @var integer
	 */
	const STATUS_SEED = 6;

	const STATUS      = [
		'Stopped',
		'Wait to check',
		'Checking',
		'Wait to download',
		'Downloading',
		'Wait to seed',
		'Seeding',
	];

	/**
	 * @const float Default http timeout
	 */
	const timeout = 30;

	/**
	 * @var array List of error occured
	 */
	protected static $errors = [];

	public           $info;

	public           $comment;

	public           $httpseeds;

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $eta;

	/**
	 * @var integer
	 */
	protected $size;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var Status
	 */
	protected $status;

	/**
	 * @var boolean
	 */
	protected $finished;

	/**
	 * @var integer
	 */
	protected $startDate;

	/**
	 * @var integer
	 */
	protected $uploadRate;

	/**
	 * @var integer
	 */
	protected $downloadRate;

	/**
	 * @var integer
	 */
	protected $peersConnected;

	/**
	 * @var double
	 */
	protected $percentDone;

	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * @var array
	 */
	protected $peers = array();

	/**
	 * @var array
	 */
	protected $trackers = array();

	/**
	 * @var array
	 */
	protected $trackerStats = array();

	/**
	 * @var double
	 */
	protected $uploadRatio;

	/**
	 * @var string
	 */
	protected $downloadDir;

	/**
	 * @var integer
	 */
	protected $downloadedEver;

	/**
	 * @var integer
	 */
	protected $uploadedEver;

	/**
	 * @param integer $id
	 */
	public function setId($id) {
		$this->id = (integer) $id;
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param integer $eta
	 */
	public function setEta($eta) {
		$this->eta = (integer) $eta;
	}

	/**
	 * @return integer
	 */
	public function getEta() {
		return $this->eta;
	}

	/**
	 * @param integer $size
	 */
	public function setSize($size) {
		$this->size = (integer) $size;
	}

	/**
	 * @return integer
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = (string) $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $hash
	 */
	public function setHash($hash) {
		$this->hash = (string) $hash;
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}

	/**
	 * @param integer|Status $status
	 */
	public function setStatus($status) {
		$this->status = new Status($status);
	}

	/**
	 * @return integer
	 */
	public function getStatus() {
		return $this->status->getValue();
	}

	/**
	 * @param boolean $finished
	 */
	public function setFinished($finished) {
		$this->finished = (boolean) $finished;
	}

	/**
	 * @return boolean
	 */
	public function isFinished() {
		return ($this->finished || (int) $this->getPercentDone() == 100);
	}

	/**
	 * @var integer $startDate
	 */
	public function setStartDate($startDate) {
		$this->startDate = (integer) $startDate;
	}

	/**
	 * @return integer
	 */
	public function getStartDate() {
		return $this->startDate;
	}

	/**
	 * @var integer $rate
	 */
	public function setUploadRate($rate) {
		$this->uploadRate = (integer) $rate;
	}

	/**
	 * @return integer
	 */
	public function getUploadRate() {
		return $this->uploadRate;
	}

	/**
	 * @param integer $rate
	 */
	public function setDownloadRate($rate) {
		$this->downloadRate = (integer) $rate;
	}

	/**
	 * @param integer $peersConnected
	 */
	public function setPeersConnected($peersConnected) {
		$this->peersConnected = (integer) $peersConnected;
	}

	/**
	 * @return integer
	 */
	public function getPeersConnected() {
		return $this->peersConnected;
	}

	/**
	 * @return integer
	 */
	public function getDownloadRate() {
		return $this->downloadRate;
	}

	/**
	 * @param double $done
	 */
	public function setPercentDone($done) {
		$this->percentDone = (double) $done;
	}

	/**
	 * @return double
	 */
	public function getPercentDone() {
		return $this->percentDone * 100.0;
	}

	/**
	 * @param array $files
	 */
	public function setFiles(array $files) {
		$this->files = array_map(function($file) {
			return PropertyMapper::map(new File(), $file);
		}, $files);
	}

	/**
	 * @return array
	 */
	public function getFiles() {
		return $this->files;
	}

	/**
	 * @param array $peers
	 */
	public function setPeers(array $peers) {
		$this->peers = array_map(function($peer) {
			return PropertyMapper::map(new Peer(), $peer);
		}, $peers);
	}

	/**
	 * @return array
	 */
	public function getPeers() {
		return $this->peers;
	}

	/**
	 * @param array $trackerStats
	 */
	public function setTrackerStats(array $trackerStats) {
		$this->trackerStats = array_map(function($trackerStats) {
			return PropertyMapper::map(new TrackerStats(), $trackerStats);
		}, $trackerStats);
	}

	/**
	 * @return array
	 */
	public function getTrackerStats() {
		return $this->trackerStats;
	}

	/**
	 * @param array $trackers
	 */
	public function setTrackers(array $trackers) {
		$this->trackers = array_map(function($tracker) {
			return PropertyMapper::map(new Tracker(), $tracker);
		}, $trackers);
	}

	/**
	 * @return array
	 */
	public function getTrackers() {
		return $this->trackers;
	}

	/**
	 * @param double $ratio
	 */
	public function setUploadRatio($ratio) {
		$this->uploadRatio = (double) $ratio;
	}

	/**
	 * @return double
	 */
	public function getUploadRatio() {
		return $this->uploadRatio;
	}

	/**
	 * @return boolean
	 */
	public function isStopped() {
		return $this->status->isStopped();
	}

	/**
	 * @return boolean
	 */
	public function isChecking() {
		return $this->status->isChecking();
	}

	/**
	 * @return boolean
	 */
	public function isDownloading() {
		return $this->status->isDownloading();
	}

	/**
	 * @return boolean
	 */
	public function isSeeding() {
		return $this->status->isSeeding();
	}

	/**
	 * @return string
	 */
	public function getDownloadDir() {
		return $this->downloadDir;
	}

	/**
	 * @param string $downloadDir
	 */
	public function setDownloadDir($downloadDir) {
		$this->downloadDir = $downloadDir;
	}

	/**
	 * @return int
	 */
	public function getDownloadedEver() {
		return $this->downloadedEver;
	}

	/**
	 * @param int $downloadedEver
	 */
	public function setDownloadedEver($downloadedEver) {
		$this->downloadedEver = $downloadedEver;
	}

	/**
	 * @return int
	 */
	public function getUploadedEver() {
		return $this->uploadedEver;
	}

	/**
	 * @param int $uploadedEver
	 */
	public function setUploadedEver($uploadedEver) {
		$this->uploadedEver = $uploadedEver;
	}

	/**
	 */
	public function stop() {
		$this->call('torrent-stop', array('ids' => array($this->getId())));
	}

	/**
	 * @param boolean $now
	 */
	public function start($now = false) {
		$this->call($now ? 'torrent-start-now' : 'torrent-start', array('ids' => array($this->getId())));
	}

	/**
	 */
	public function verify() {
		$this->call('torrent-verify', array('ids' => array($this->getId())));
	}

	/**
	 */
	public function reAnnounce() {
		$this->call('torrent-reannounce', array('ids' => array($this->getId())));
	}

	/**
	 * @param boolean $localData
	 */
	public function remove($localData = false) {
		$arguments = array('ids' => array($this->getId()));
		if ($localData) {
			$arguments['delete-local-data'] = true;
		}
		$this->call('torrent-remove', $arguments);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getMapping() {
		return array(
			'id'             => 'id',
			'eta'            => 'eta',
			'sizeWhenDone'   => 'size',
			'name'           => 'name',
			'status'         => 'status',
			'isFinished'     => 'finished',
			'rateUpload'     => 'uploadRate',
			'rateDownload'   => 'downloadRate',
			'percentDone'    => 'percentDone',
			'files'          => 'files',
			'peers'          => 'peers',
			'peersConnected' => 'peersConnected',
			'trackers'       => 'trackers',
			'trackerStats'   => 'trackerStats',
			'startDate'      => 'startDate',
			'uploadRatio'    => 'uploadRatio',
			'hashString'     => 'hash',
			'downloadDir'    => 'downloadDir',
			'downloadedEver' => 'downloadedEver',
			'uploadedEver'   => 'uploadedEver',
		);
	}

	/**
	 * @param null  $data
	 * @param array $meta
	 * @param int   $piece_length
	 *
	 * @return mixed|Torrent
	 */
	public static function newInstance($data = null, $meta = [], $piece_length = 256) {
		$torrent = new Torrent();
		if (is_null($data)) {
			return null;
		}
		if ($piece_length < 32 || $piece_length > 4096) {
			return self::set_error(new Exception('Invalid piece length, must be between 32 and 4096'));
		}
		if (is_string($meta)) {
			$meta = ['announce' => $meta];
		}
		if ($torrent->build($data, $piece_length * 1024)) {
			$torrent->touch();
		} else {
			$meta = array_merge($meta, self::decode($data));
		}
		foreach ($meta as $key => $value) {
			$torrent->$key = $value;
		}
		return $torrent;
	}

	/** Convert the current Torrent instance in torrent format
	 * @return string encoded torrent data
	 */
	public function __toString() {
		return $this->encode($this);
	}

	/** Return last error message
	 * @return string|boolean last error message or false if none
	 */
	public function error() {
		return empty(self::$errors) ? false : self::$errors[0]->getMessage();
	}

	/** Return Errors
	 * @return array|boolean error list or false if none
	 */
	public function errors() {
		return empty(self::$errors) ? false : self::$errors;
	}

	/**** Getters and setters ****/
	/** Getter and setter of torrent announce url / list
	 * If the argument is a string, announce url is added to announce list (or set as announce if announce is not set)
	 * If the argument is an array/object, set announce url (with first url) and list (if array has more than one url),
	 * tiered list supported If the argument is false announce url & list are unset
	 *
	 * @param null|false|string|array announce url / list, reset all if false (optional, if omitted it's a getter)
	 *
	 * @return string|array|null announce url / list or null if not set
	 */
	public function announce($announce = null) {
		if (is_null($announce)) {
			return !isset($this->{'announce-list'}) ? isset($this->announce) ? $this->announce : null : $this->{'announce-list'};
		}
		$this->touch();
		if (is_string($announce) && isset($this->announce)) {
			return $this->{'announce-list'} = self::announce_list(isset($this->{'announce-list'}) ? $this->{'announce-list'} : $this->announce, $announce);
		}
		unset($this->{'announce-list'});
		if (is_array($announce) || is_object($announce)) {
			if (($this->announce = self::first_announce($announce)) && count($announce) > 1) {
				return $this->{'announce-list'} = self::announce_list($announce);
			} else {
				return $this->announce;
			}
		}
		if (!isset($this->announce) && $announce) {
			return $this->announce = (string) $announce;
		}
		unset($this->announce);
		return true;
	}

	/** Getter and setter of torrent comment
	 *
	 * @param null|string comment (optional, if omitted it's a getter)
	 *
	 * @return string|null comment or null if not set
	 */
	public function comment($comment = null) {
		return is_null($comment) ? isset($this->comment) ? $this->comment : null : $this->touch($this->comment = (string) $comment);
	}

	/** Getter and setter of torrent name
	 *
	 * @param null|string name (optional, if omitted it's a getter)
	 *
	 * @return string|null name or null if not set
	 */
	public function name($name = null) {
		return is_null($name) ? isset($this->info['name']) ? $this->info['name'] : null : $this->touch($this->info['name'] = (string) $name);
	}

	/** Getter and setter of private flag
	 *
	 * @param null|boolean is private or not (optional, if omitted it's a getter)
	 *
	 * @return boolean private flag
	 */
	public function is_private($private = null) {
		return is_null($private) ? !empty($this->info['private']) : $this->touch($this->info['private'] = $private ? 1 : 0);
	}

	/** Getter and setter of webseed(s) url list ( GetRight implementation )
	 *
	 * @param null|string|array webseed or webseeds mirror list (optional, if omitted it's a getter)
	 *
	 * @return string|array|null webseed(s) or null if not set
	 */
	public function url_list($urls = null) {
		return is_null($urls) ? isset($this->{'url-list'}) ? $this->{'url-list'} : null : $this->touch($this->{'url-list'} = is_string($urls) ? $urls : (array) $urls);
	}

	/** Getter and setter of httpseed(s) url list ( Bittornado implementation )
	 *
	 * @param null|string|array httpseed or httpseeds mirror list (optional, if omitted it's a getter)
	 *
	 * @return array|null httpseed(s) or null if not set
	 */
	public function httpseeds($urls = null) {
		return is_null($urls) ? isset($this->httpseeds) ? $this->httpseeds : null : $this->touch($this->httpseeds = (array) $urls);
	}

	/**** Analyze BitTorrent ****/
	/** Get piece length
	 * @return integer piece length or null if not set
	 */
	public function piece_length() {
		return isset($this->info['piece length']) ? $this->info['piece length'] : null;
	}

	/** Compute hash info
	 * @return string hash info or null if info not set
	 */
	public function hash_info() {
		return isset($this->info) ? sha1(self::encode($this->info)) : null;
	}

	/** List torrent content
	 *
	 * @param integer|null size precision (optional, if omitted returns sizes in bytes)
	 *
	 * @return array file(s) and size(s) list, files as keys and sizes as values
	 */
	public function content($precision = null) {
		$files = array();
		if (isset($this->info['files']) && is_array($this->info['files'])) {
			foreach ($this->info['files'] as $file) {
				$files[self::path($file['path'], $this->info['name'])] = $precision ? self::format($file['length'], $precision) : $file['length'];
			}
		} elseif (isset($this->info['name'])) {
			$files[$this->info['name']] = $precision ? self::format($this->info['length'], $precision) : $this->info['length'];
		}
		return $files;
	}

	/** List torrent content pieces and offset(s)
	 * @return array file(s) and pieces/offset(s) list, file(s) as keys and pieces/offset(s) as values
	 */
	public function offset() {
		$files = array();
		$size  = 0;
		if (isset($this->info['files']) && is_array($this->info['files'])) {
			foreach ($this->info['files'] as $file) {
				$files[self::path($file['path'], $this->info['name'])] = array(
					'startpiece' => floor($size / $this->info['piece length']),
					'offset'     => fmod($size, $this->info['piece length']),
					'size'       => $size += $file['length'],
					'endpiece'   => floor($size / $this->info['piece length']),
				);
			}
		} elseif (isset($this->info['name'])) {
			$files[$this->info['name']] = array(
				'startpiece' => 0,
				'offset'     => 0,
				'size'       => $this->info['length'],
				'endpiece'   => floor($this->info['length'] / $this->info['piece length']),
			);
		}
		return $files;
	}

	/** Sum torrent content size
	 *
	 * @param integer|null size precision (optional, if omitted returns size in bytes)
	 *
	 * @return integer|string file(s) size
	 */
	public function size($precision = null) {
		$size = 0;
		if (isset($this->info['files']) && is_array($this->info['files'])) {
			foreach ($this->info['files'] as $file) {
				$size += $file['length'];
			}
		} elseif (isset($this->info['name'])) {
			$size = $this->info['length'];
		}
		return is_null($precision) ? $size : self::format($size, $precision);
	}

	/** Request torrent statistics from scrape page USING CURL!!
	 *
	 * @param string|array $announce         or scrape page url (optional, to request an alternative tracker BUT
	 *                                       requirered for static call)
	 * @param string       $hash_info        torrent hash info (optional, requirered ONLY for static call)
	 * @param int          $timeout          read timeout in seconds (optional, default to self::timeout 30s)
	 *
	 * @return array tracker torrent statistics
	 */
	/* static */
	public function scrape($announce = null, $hash_info = null, $timeout = self::timeout) {
		$packed_hash = urlencode(pack('H*', $hash_info ? $hash_info : $this->hash_info()));
		$handles     = $scrape = array();
		if (!function_exists('curl_multi_init')) {
			return self::set_error(new Exception('Install CURL with "curl_multi_init" enabled'));
		}
		$curl = curl_multi_init();
		foreach ((array) ($announce ? $announce : $this->announce()) as $tier) {
			foreach ((array) $tier as $tracker) {
				$tracker = str_ireplace(array(
					'udp://',
					'/announce',
					':80/',
				), array(
					'http://',
					'/scrape',
					'/',
				), $tracker);
				if (isset($handles[$tracker])) {
					continue;
				}
				$handles[$tracker] = curl_init($tracker . '?info_hash=' . $packed_hash);
				curl_setopt($handles[$tracker], CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handles[$tracker], CURLOPT_TIMEOUT, $timeout);
				curl_multi_add_handle($curl, $handles[$tracker]);
			}
		}
		do {
			while (($state = curl_multi_exec($curl, $running)) == CURLM_CALL_MULTI_PERFORM) {
				;
			}
			if ($state != CURLM_OK) {
				continue;
			}
			while ($done = curl_multi_info_read($curl)) {
				$info    = curl_getinfo($done['handle']);
				$tracker = array_shift(explode('?', $info['url'], 2));
				if (empty($info['http_code'])) {
					$scrape[$tracker] = self::set_error(new Exception('Tracker request timeout (' . $timeout . 's)'), true);
				} elseif ($info['http_code'] != 200) {
					$scrape[$tracker] = self::set_error(new Exception('Tracker request failed (' . $info['http_code'] . ' code)'), true);
				}
				$stats = self::decode_data(curl_multi_getcontent($done['handle']));
				curl_multi_remove_handle($curl, $done['handle']);
				$scrape[$tracker] = empty($stats['files']) ? self::set_error(new Exception('Empty scrape data'), true) : array_shift($stats['files']) + (empty($stats['flags']) ? array() : $stats['flags']);
			}
		} while ($running);
		curl_multi_close($curl);
		return $scrape;
	}

	/**** Save and Send ****/
	/** Save torrent file to disk
	 *
	 * @param null|string name of the file (optional)
	 *
	 * @return boolean file has been saved or not
	 */
	public function save($filename = null) {
		return file_put_contents(is_null($filename) ? $this->info['name'] . '.torrent' : $filename, $this->encode($this));
	}

	/** Send torrent file to client
	 *
	 * @param null|string name of the file (optional)
	 *
	 * @return void script exit
	 */
	public function send($filename = null) {
		$data = $this->encode($this);
		header('Content-type: application/x-bittorrent');
		header('Content-Length: ' . strlen($data));
		header('Content-Disposition: attachment; filename="' . (is_null($filename) ? $this->info['name'] . '.torrent' : $filename) . '"');
		exit($data);
	}

	/**** Encode BitTorrent ****/
	/** Encode torrent data
	 *
	 * @param $mixed mixed to encode
	 *
	 * @return string torrent encoded data
	 */
	public static function encode($mixed) {
		switch (gettype($mixed)) {
			case 'integer':
			case 'double':
				return self::encode_integer($mixed);
			case 'object':
				return (array) $mixed;
			case 'array':
				return self::encode_array($mixed);
			default:
				return self::encode_string((string) $mixed);
		}
	}

	/** Encode torrent string
	 *
	 * @param string string to encode
	 *
	 * @return string encoded string
	 */
	static private function encode_string($string) {
		return strlen($string) . ':' . $string;
	}

	/** Encode torrent integer
	 *
	 * @param integer integer to encode
	 *
	 * @return string encoded integer
	 */
	static private function encode_integer($integer) {
		return 'i' . $integer . 'e';
	}

	/** Encode torrent dictionary or list
	 *
	 * @param array array to encode
	 *
	 * @return string encoded dictionary or list
	 */
	static private function encode_array($array) {
		if (self::is_list($array)) {
			$return = 'l';
			foreach ($array as $value) {
				$return .= self::encode($value);
			}
		} else {
			ksort($array, SORT_STRING);
			$return = 'd';
			foreach ($array as $key => $value) {
				$return .= self::encode(strval($key)) . self::encode($value);
			}
		}
		return $return . 'e';
	}

	/**** Decode BitTorrent ****/
	/** Decode torrent data or file
	 *
	 * @param string $string or file path to decode
	 *
	 * @return array decoded torrent data
	 */
	protected static function decode($string) {
		$data = is_file($string) || self::url_exists($string) ? self::file_get_contents($string) : $string;
		return (array) self::decode_data($data);
	}

	/** Decode torrent data
	 *
	 * @param string $data data to decode
	 *
	 * @return array decoded torrent data
	 */
	static private function decode_data(& $data) {
		switch (self::char($data)) {
			case 'i':
				$data = substr($data, 1);
				return self::decode_integer($data);
			case 'l':
				$data = substr($data, 1);
				return self::decode_list($data);
			case 'd':
				$data = substr($data, 1);
				return self::decode_dictionary($data);
			default:
				return self::decode_string($data);
		}
	}

	/** Decode torrent dictionary
	 *
	 * @param string $data data to decode
	 *
	 * @return array decoded dictionary
	 */
	static private function decode_dictionary(& $data) {
		$dictionary = array();
		$previous   = null;
		while (($char = self::char($data)) != 'e') {
			if ($char === false) {
				return self::set_error(new Exception('Unterminated dictionary'));
			}
			if (!ctype_digit($char)) {
				return self::set_error(new Exception('Invalid dictionary key'));
			}
			$key = self::decode_string($data);
			if (isset($dictionary[$key])) {
				return self::set_error(new Exception('Duplicate dictionary key'));
			}
			if ($key < $previous) {
				return self::set_error(new Exception('Missorted dictionary key'));
			}
			$dictionary[$key] = self::decode_data($data);
			$previous         = $key;
		}
		$data = substr($data, 1);
		return $dictionary;
	}

	/** Decode torrent list
	 *
	 * @param string $data data to decode
	 *
	 * @return array decoded list
	 */
	static private function decode_list(& $data) {
		$list = array();
		while (($char = self::char($data)) != 'e') {
			if ($char === false) {
				return self::set_error(new Exception('Unterminated list'));
			}
			$list[] = self::decode_data($data);
		}
		$data = substr($data, 1);
		return $list;
	}

	/** Decode torrent string
	 *
	 * @param string $data data to decode
	 *
	 * @return string decoded string
	 */
	static private function decode_string(& $data) {
		if (self::char($data) === '0' && substr($data, 1, 1) != ':') {
			self::set_error(new Exception('Invalid string length, leading zero'));
		}
		if (!$colon = @strpos($data, ':')) {
			return self::set_error(new Exception('Invalid string length, colon not found'));
		}
		$length = intval(substr($data, 0, $colon));
		if ($length + $colon + 1 > strlen($data)) {
			return self::set_error(new Exception('Invalid string, input too short for string length'));
		}
		$string = substr($data, $colon + 1, $length);
		$data   = substr($data, $colon + $length + 1);
		return $string;
	}

	/** Decode torrent integer
	 *
	 * @param string $data data to decode
	 *
	 * @return integer decoded integer
	 */
	static private function decode_integer(& $data) {
		$start = 0;
		$end   = strpos($data, 'e');
		if ($end === 0) {
			self::set_error(new Exception('Empty integer'));
		}
		if (self::char($data) == '-') {
			$start ++;
		}
		if (substr($data, $start, 1) == '0' && ($start != 0 || $end > $start + 1)) {
			self::set_error(new Exception('Leading zero in integer'));
		}
		if (!ctype_digit(substr($data, $start, $end))) {
			self::set_error(new Exception('Non-digit characters in integer'));
		}
		$integer = substr($data, 0, $end);
		$data    = substr($data, $end + 1);
		return $integer;
	}

	/**** Internal Helpers ****/
	/** Build torrent info
	 *
	 * @param string|array $data         source  folder/file(s) path
	 * @param integer      $piece_length piece length
	 *
	 * @return array|boolean torrent info or false if data isn't folder/file(s)
	 */
	protected function build($data, $piece_length) {
		if (is_null($data)) {
			return false;
		} elseif (is_array($data) && self::is_list($data)) {
			return $this->info = $this->files($data, $piece_length);
		} elseif (is_dir($data)) {
			return $this->info = $this->folder($data, $piece_length);
		} elseif ((is_file($data) || self::url_exists($data)) && !self::is_torrent($data)) {
			return $this->info = $this->file($data, $piece_length);
		} else {
			return false;
		}
	}

	/** Set torrent creator and creation date
	 *
	 * @param mixed $void param
	 *
	 * @return mixed param
	 */
	protected function touch($void = null) {
		$this->{'created by'}    = 'Torrent PHP Class - Adrien Gibrat';
		$this->{'creation date'} = time();
		return $void;
	}

	/** Add an error to errors stack
	 *
	 * @param Exception $exception error to add
	 * @param boolean   $message   return error message or not (optional, default to false)
	 *
	 * @return boolean|string return false or error message if requested
	 */
	protected static function set_error($exception, $message = false) {
		return (array_unshift(self::$errors, $exception) && $message) ? $exception->getMessage() : false;
	}

	/** Build announce list
	 *
	 * @param string|array announce url / list
	 * @param string|array announce url / list to add (optionnal)
	 *
	 * @return array announce list (array of arrays)
	 */
	protected static function announce_list($announce, $merge = array()) {
		return array_map(create_function('$a', 'return (array) $a;'), array_merge((array) $announce, (array) $merge));
	}

	/** Get the first announce url in a list
	 *
	 * @param array $announce announce list (array of arrays if tiered trackers)
	 *
	 * @return string first announce url
	 */
	protected static function first_announce($announce) {
		while (is_array($announce)) {
			$announce = reset($announce);
		}
		return $announce;
	}

	/** Helper to pack data hash
	 *
	 * @param string $data data
	 *
	 * @return string packed data hash
	 */
	protected static function pack(& $data) {
		return pack('H*', sha1($data)) . ($data = null);
	}

	/** Helper to build file path
	 *
	 * @param array  $path   file path
	 * @param string $folder base folder
	 *
	 * @return string real file path
	 */
	protected static function path($path, $folder) {
		array_unshift($path, $folder);
		return join(DIRECTORY_SEPARATOR, $path);
	}

	/** Helper to test if an array is a list
	 *
	 * @param array array to test
	 *
	 * @return boolean is the array a list or not
	 */
	protected static function is_list($array) {
		foreach (array_keys($array) as $key) {
			if (!is_int($key)) {
				return false;
			}
		}
		return true;
	}

	/** Build pieces depending on piece length from a file handler
	 *
	 * @param boolean|resource $handle       file handle
	 * @param integer          $piece_length piece length
	 * @param boolean          $last         is last piece
	 *
	 * @return string pieces
	 */
	private function pieces($handle, $piece_length, $last = true) {
		static $piece, $length;
		if (empty($length)) {
			$length = $piece_length;
		}
		$pieces = null;
		while (!feof($handle)) {
			if (($length = strlen($piece .= fread($handle, $length))) == $piece_length) {
				$pieces .= self::pack($piece);
			} elseif (($length = $piece_length - $length) < 0) {
				return self::set_error(new Exception('Invalid piece length!'));
			}
		}
		fclose($handle);
		return $pieces . ($last && $piece ? self::pack($piece) : null);
	}

	/** Build torrent info from single file
	 *
	 * @param string  $file         file path
	 * @param integer $piece_length piece length
	 *
	 * @return array torrent info
	 */
	private function file($file, $piece_length) {
		if (!$handle = self::fopen($file, $size = self::filesize($file))) {
			return self::set_error(new Exception('Failed to open file: "' . $file . '"'));
		}
		return array(
			'length'       => $size,
			'name'         => end(explode(DIRECTORY_SEPARATOR, $file)),
			'piece length' => $piece_length,
			'pieces'       => $this->pieces($handle, $piece_length),
		);
	}

	/** Build torrent info from files
	 *
	 * @param array   $files        file list
	 * @param integer $piece_length piece length
	 *
	 * @return array torrent info
	 */
	private function files($files, $piece_length) {
		$files = array_map('realpath', $files);
		sort($files);
		usort($files, create_function('$a,$b', 'return strrpos($a,DIRECTORY_SEPARATOR)-strrpos($b,DIRECTORY_SEPARATOR);'));
		$path       = explode(DIRECTORY_SEPARATOR, dirname(realpath(current($files))));
		$pieces     = null;
		$info_files = array();
		$count      = count($files) - 1;
		foreach ($files as $i => $file) {
			if ($path != array_intersect_assoc($file_path = explode(DIRECTORY_SEPARATOR, $file), $path)) {
				self::set_error(new Exception('Files must be in the same folder: "' . $file . '" discarded'));
			}
			if (!$handle = self::fopen($file, $filesize = self::filesize($file))) {
				self::set_error(new Exception('Failed to open file: "' . $file . '" discarded'));
			}
			$pieces .= $this->pieces($handle, $piece_length, $count == $i);
			$info_files[] = array(
				'length' => $filesize,
				'path'   => array_diff($file_path, $path),
			);
		}
		return array(
			'files'        => $info_files,
			'name'         => end($path),
			'piece length' => $piece_length,
			'pieces'       => $pieces,
		);
	}

	/** Build torrent info from folder content
	 *
	 * @param string  $dir          folder path
	 * @param integer $piece_length piece length
	 *
	 * @return array torrent info
	 */
	private function folder($dir, $piece_length) {
		return $this->files(self::scandir($dir), $piece_length);
	}

	/** Helper to return the first char of encoded data
	 *
	 * @param string $data encoded data
	 *
	 * @return string|boolean first char of encoded data or false if empty data
	 */
	static private function char($data) {
		return empty($data) ? false : substr($data, 0, 1);
	}

	/**** Public Helpers ****/
	/** Helper to format size in bytes to human readable
	 *
	 * @param integer $size      size in bytes
	 * @param integer $precision precision after coma
	 *
	 * @return string formated size in appropriate unit
	 */
	public static function format($size, $precision = 2) {
		$units = array(
			'octets',
			'Ko',
			'Mo',
			'Go',
			'To',
		);
		while (($next = next($units)) && $size > 1024) {
			$size /= 1024;
		}
		return round($size, $precision) . ' ' . ($next ? prev($units) : end($units));
	}

	/** Helper to return filesize (even bigger than 2Gb -linux only- and distant files size)
	 *
	 * @param string $file file path
	 *
	 * @return double|boolean filesize or false if error
	 */
	public static function filesize($file) {
		if (is_file($file)) {
			return (double) sprintf('%u', @filesize($file));
		} else if ($content_length = preg_grep($pattern = '#^Content-Length:\s+(\d+)$#i', (array) @get_headers($file))) {
			return (int) preg_replace($pattern, '$1', reset($content_length));
		}
		return null;
	}

	/** Helper to open file to read (even bigger than 2Gb, linux only)
	 *
	 * @param string         $file file path
	 * @param integer|double $size file size (optional)
	 *
	 * @return resource|boolean file handle or false if error
	 */
	public static function fopen($file, $size = null) {
		if ((is_null($size) ? self::filesize($file) : $size) <= 2 * pow(1024, 3)) {
			return fopen($file, 'r');
		} elseif (PHP_OS != 'Linux') {
			return self::set_error(new Exception('File size is greater than 2GB. This is only supported under Linux'));
		} elseif (!is_readable($file)) {
			return false;
		} else {
			return popen('cat ' . escapeshellarg(realpath($file)), 'r');
		}
	}

	/** Helper to scan directories files and sub directories recursivly
	 *
	 * @param string $dir directory path
	 *
	 * @return array directory content list
	 */
	public static function scandir($dir) {
		$paths = array();
		foreach (scandir($dir) as $item) {
			if ($item != '.' && $item != '..') {
				if (is_dir($path = realpath($dir . DIRECTORY_SEPARATOR . $item))) {
					$paths = array_merge(self::scandir($path), $paths);
				} else {
					$paths[] = $path;
				}
			}
		}
		return $paths;
	}

	/** Helper to check if url exists
	 *
	 * @param string $url url to check
	 *
	 * @return boolean does the url exist or not
	 */
	public static function url_exists($url) {
		return preg_match('#^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$#i', $url) ? (bool) preg_grep('#^HTTP/.*\s(200|304)\s#', (array) @get_headers($url)) : false;
	}

	/** Helper to check if a file is a torrent
	 *
	 * @param string $file    file location
	 * @param int    $timeout http timeout (optional, default to self::timeout 30s)
	 *
	 * @return boolean is the file a torrent or not
	 */
	public static function is_torrent($file, $timeout = self::timeout) {
		return self::file_get_contents($file, $timeout, 0, 11) === 'd8:announce';
	}

	/** Helper to get (distant) file content
	 *
	 * @param string  $file    file location
	 * @param int     $timeout http timeout (optional, default to self::timeout 30s)
	 * @param integer $offset  starting offset (optional, default to null)
	 * @param integer $length  content length (optional, default to null)
	 *
	 * @return string|boolean file content or false if error
	 */
	public static function file_get_contents($file, $timeout = self::timeout, $offset = null, $length = null) {
		if (is_file($file) || ini_get('allow_url_fopen')) {
			$context = !is_file($file) && $timeout ? stream_context_create(array('http' => array('timeout' => $timeout))) : null;
			return !is_null($offset) ? $length ? @file_get_contents($file, false, $context, $offset, $length) : @file_get_contents($file, false, $context, $offset) : @file_get_contents($file, false, $context);
		} elseif (!function_exists('curl_init')) {
			return self::set_error(new Exception('Install CURL or enable "allow_url_fopen"'));
		}
		$handle = curl_init($file);
		if ($timeout) {
			curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
		}
		if ($offset || $length) {
			curl_setopt($handle, CURLOPT_RANGE, $offset . '-' . ($length ? $offset + $length - 1 : null));
		}
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($handle);
		$size    = curl_getinfo($handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		curl_close($handle);
		return ($offset && $size == - 1) || ($length && $length != $size) ? $length ? substr($content, $offset, $length) : substr($content, $offset) : $content;
	}

	/**
	 * @param string $method
	 * @param array  $arguments
	 */
	protected function call($method, $arguments) {
		if (!($client = $this->getClient())) {
			return;
		}
		ResponseValidator::validate($method, $client->call($method, $arguments));
	}
}
