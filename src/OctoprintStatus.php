<?php

namespace Kchimbo\OctoprintStatus;

class OctoprintStatus
{
	/**
	 * The Octoprint IP Address
	 * @var string
	 */
	protected $ip;
	/**
	 * The OctoPrint Port
	 * @var int
	 */
	protected $port;

	/**
	 * The OctoPrint API Key.
	 * @var string
	 */
	protected $api;

	/**
	 * Instantiate a new OctoprintStatus instance.
	 * @param string
	 * @param int
	 * @param string
	 */
	public function __construct($ip, $port, $api)
	{
		$this->ip = $ip;
		$this->port = $port;
		$this->api = $api;
	}

	/**
	 * Get the status of the printer. Some fields are empty is nothing is currently printing.
	 * @return array
	 */
	public function getStatus() 
	{
		$printer = 	$this->makeRequest('printer');
		$temperature = $printer['temperature'];

		$job = 		$this->makeRequest('job');
		$progress	= $job['progress'];

		return [
			'status' 	=> $printer['state']['text'],
			'tbed'		=> $temperature['bed']['actual'],
			'thotend'	=> $temperature['tool0']['actual'],
			'job'		=> $job['job']['file']['name'],
			'print_time'		=> $progress['printTime'],
			'print_time_left'	=> $progress['printTimeLeft'],
			'progress'			=> $progress['completion']
			];
	
	}

	/**
	 * @param  string the resource to get information from
	 * @param  array  extra parameters for the resource (i.e history, limit)
	 * @return json
	 */
	public function makeRequest($resource, $parameters = [])
	{
		$params = http_build_query($parameters, '', '&');

		$endpoint = "http://{$this->ip}:{$this->port}/api/{$resource}?{$params}&apikey={$this->api}";

		$r = file_get_contents($endpoint);

		return json_decode($r, true);
	}
}
