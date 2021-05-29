<?php

namespace ZerosDev\KirimWA;

use Exception, InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
	/**
     * @var string API baseurl
     */
	private $baseurl;


	/**
     * @var object GuzzleHttp\Client new instance of Guzzle HTTP Client
     */
	private $client;


	/**
     * @var array Current selected sender
     */
	private $sender;


	/**
     * @var string Error message
     */
	private $error;


	/**
     * @var object|null Response from KirimWA
     */
	private $response;


	/**
     * @param string|null $senderNumber Select sender
     * 
     * @param boolean $randomIfNotExists Choose whether sender should be picked randomly or not if selected sender does not exists
     */
	public function __construct(string $senderNumber = null, bool $randomIfNotExists = false)
	{
		$this->loadConfig();

		$this->useSender($senderNumber, $randomIfNotExists);
	}


	/**
     * Load configuration
     */
	private function loadConfig()
	{
		if( Config::$configLoaded === false ) {
			$configFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'config.php';
			Config::load($configFile);
		}
	}


	/**
     * Initialize/re-initialize Guzzle HTTP Client instance & property
     */
	private function init()
	{
		$host = $this->sender['host'];

		if( substr($host, 0, 1) === ':' ) {
			if( empty(Config::$default_host) ) {
				throw new InvalidArgumentException('default_host is empty');
			}
			$host = rtrim(Config::$default_host, '/').rtrim($host, '/');
		}

		$this->baseurl = $host.'/api/';
		$this->client = new GuzzleClient([
			'base_uri'	=> $this->baseurl,
			'headers'	=> [
				'Authorization' => 'Bearer '.$this->sender['key']
			],
			'http_errors' => false,
			'allow_redirects' => false,
			'timeout'	=> 15,
			'connect_timeout' => 5
		]);
	}


	/**
	 * @param string $senderNumber Define selected sender
	 * 
	 * @param boolean $randomIfNotExists Choose whether sender should be picked randomly or not if selected sender does not exists
	 * 
     * Pick sender
     * 
     * @return ZerosDev\KirimWA\Client
     */
	public function useSender(string $senderNumber = null, bool $randomIfNotExists = false)
	{
		if( !is_array(Config::$senders) || @count(Config::$senders) == 0 ) {
			throw new InvalidArgumentException('Cannot load senders or sender list is not configured properly');
		}

		if( !empty($senderNumber) && isset(Config::$senders["$senderNumber"]) ) {
			$this->sender = Config::$senders["$senderNumber"];
		}
		elseif( empty($senderNumber) || $randomIfNotExists === true ) {
			$keys = array_keys(Config::$senders);

			if( count($keys) > 1 ) {

				shuffle($keys);

				if( !empty($this->sender) ) {

					// make sure new selected sender is different from current sender
					foreach( $keys as $k ) {
						if( $k != $this->sender['number'] ) {
							$key = $k;
							break;
						}
					}
				}
				else {
					$key = $keys[0];
				}
			}
			else {
				$key = $keys[0];
			}

			$this->sender = array_merge(['number' => $key], Config::$senders[$key]);
		}
		else {
			throw new InvalidArgumentException('Cannot use sender or sender list is not configured properly');
		}

		if( empty($this->sender) ) {
			throw new InvalidArgumentException('Cannot use sender');
		}

		/**
		 * After changing sender, we need to reinitialize Guzzle HTTP client with the new sender
		 * */
		$this->init();

		return $this;
	}


	/**
	 * Alias of useSender()
     */
	public function changeSender(string $senderNumber = null, bool $randomIfNotExists = false)
	{
		return $this->useSender($senderNumber, $randomIfNotExists);
	}


	/**
     * @param string $phone Destination number
     * 
     * @param string $message Message to be sent
     *
     * @return boolean true|false
     */
	public function sendText(string $phone, string $message)
	{
		/**
		 * Reset error & response
		 * */
		$this->reset();

		/**
		 * Replace 08/+62 with 62
		 * */
		if( substr($phone, 0, 2) === '08' ) {
			$phone = '62'.substr($phone, 1);
		}
		elseif( substr($phone, 0, 1) === '+' ) {
			$phone = substr($phone, 1);
		}

		try
		{
			$endpoint = 'message';

			$result = $this->client->request('POST', $endpoint, [
				'form_params' => [
					'phone'	=> $phone,
					'message' => $message,
					'type'	=> 'text'
				]
			])
			->getBody()
			->getContents();

			if( empty($result) ) {
				throw new Exception("Empty response from: {$this->baseurl}{$endpoint}");
			}

			$result = json_decode($result);

			if( json_last_error() !== JSON_ERROR_NONE ) {
				throw new InvalidArgumentException("Invalid JSON from response");
			}

			if( isset($result->code) && $result->code > 299 ) {
				throw new Exception('['.$result->code.'] '.$result->message);
			}

			$this->response = $result;
		}
		catch(Exception $e) {
		    $this->error = $e->getMessage();
		}

		return $this;
	}


	/**
     * Reset state
     */
	private function reset()
	{
		$this->response = null;
		$this->error = null;
	}


	/**
     * Get current selected sender
     * 
     * @return array|null
     */
	public function sender()
	{
		return $this->sender;
	}


	/**
     * Get current response data
     * 
     * @return object|null
     */
	public function response()
	{
		return $this->response;
	}


	/**
     * Get current error message
     * 
     * @return string|null
     */
	public function error()
	{
		return !empty($this->error) ? $this->error : null;
	}


	/**
     * Check if there is an error
     * 
     * @return boolean
     */
	public function hasError()
	{
		return !empty($this->error) ? true : false;
	}


	/**
     * Get class instance
     * 
     * @return ZerosDev\KirimWA\Client
     */
	public function instance()
	{
		return $this;
	}
}