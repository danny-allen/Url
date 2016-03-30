<?php

namespace Dao;

use Exception;


/**
 * Url
 *
 * Set/retrieve the URL and split it into its components.
 */
class Url {

	/**
	 * $_manual
	 *
	 * Whether we are manually setting the URL to parse.
	 * 
	 * @var boolean
	 */
	private $_manual = true;


	/**
	 * $_ports
	 *
	 * The ports we dont want to append to the url.
	 * 
	 * @var array
	 */
	private $_ports = array(443, 80);


	/**
	 * $_acceptedComponents
	 *
	 * The URL components supported by this class.
	 * 
	 * @var array
	 */
	private $_acceptedComponents = array(
		'path',
		'scheme',
		'port',
		'host',
		'query'
	);



	/**
	 * __construct
	 *
	 * Set the URL, either manually, or with user passed value.
	 * 
	 * @param string 	$url 	The URL to parse.
	 */
	public function __construct($url = null){

		//find url to use
		$this->url = (!$url)? $this->_setUrlFromCurrentLoad() : $url;

		//get the components
		$this->components = parse_url($this->url);
	}


	/**
	 * get
	 *
	 * Returns the URL in its entirety.
	 *
	 * Needs data type checking - component must be a string.
	 * 
	 * @return 	string 	The URL.
	 */
	public function get($component = null) {

		//if we're not after a component, return the full URL.
		if(!$component){
			return $this->url;
		}

		//check for component support
		if(!in_array($component, $this->_acceptedComponents)){
			throw new Exception('The URL component "' . $component . '" is either invalid or not supported.');
		}

		//return the component if it exists
		return (isset($this->components[$component]))? $this->components[$component] : null;
	}


	/**
	 * _setUrlFromCurrentLoad
	 *
	 * Finds the URL from the current page load.
	 */
	private function _setUrlFromCurrentLoad() {

		//loading url manually from current page load
		$this->_manual = false;

		//set protocol
		$url  = ($this->isSecure()) ? 'https://' :  'http://';

		//add the server name
		$url .= $_SERVER["SERVER_NAME"];

		//maybe add the port number
		$url .= (!in_array($_SERVER["SERVER_PORT"], $this->_ports)) ? ":".$_SERVER["SERVER_PORT"] : "";

		//add the request uri
		$url .= $_SERVER["REQUEST_URI"];

		//return the url
		return $url;
	}


	/**
	 * isSecure
	 *
	 * Checks to see if the pages was loaded securly.
	 *
	 * Note: this will not work on a manually entered URL.
	 * 
	 * @return boolean Whether the current load is using HTTPS.
	 */
	public function isSecure() {

		//check to see if the url was added manually
		if($this->_manual) {
			throw new Exception('Cannot check a manually entered URL for SSL. Please see documentation.');
		}

		//return whether we're using HTTPS or not
  		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}
}