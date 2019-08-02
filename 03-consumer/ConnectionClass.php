<?php
include_once('env.php');

// Esta classe aceita apenas requisições POST
class ConnectionClass
{
	private $conn = null;

	public function __construct()
	{
	    if (!$this->conn = mysqli_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME')))
	    {
	        throw new Exception('Unable to connect');
	    }
	}

	public function select($query)
	{
		$result = $this->conn->query($query);

		$this->close();

		return $result;
	}

	public function close()
	{
		$this->conn->close();
	}

}
