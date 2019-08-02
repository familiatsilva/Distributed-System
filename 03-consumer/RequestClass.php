<?php
include_once('env.php');

// Esta classe aceita apenas requisições POST
class RequestClass
{
	private $data = null;

	public function __construct()
	{
		if($_SERVER["REQUEST_METHOD"] == 'OPTIONS')
		{
			$this->enableCors();
		}
		else
		{
			$this->data = null;

			if(sizeof($_REQUEST) > 0)
			{
				$this->data = filter_var_array($_REQUEST, FILTER_SANITIZE_STRING);
			}
		}		
	}

	public function enableCors()
	{
		header('Access-Control-Allow-Origin: ' . getenv('WEBSERVER'));
		header('Access-Control-Allow-Methods: POST, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, access-control-allow-origin');

		exit;	
	}

	public function get($name)
	{
		if($this->data != null && isset($this->data[$name]))
		{
			return $this->data[$name];
		}
	}	

	public function all()
	{
		return $this->data;
	}

	public function response($httpCode, $data)
	{
		http_response_code($httpCode);
		header('Access-Control-Allow-Origin: *');
		header('Content-type: application/json');
		echo json_encode($data); 		
		exit;
	}

}