<?php
require_once 'vendor/autoload.php';
include_once('env.php');
include_once('RequestClass.php');
include_once('ConnectionClass.php');

class AppClass
{
	//Metodo que recebe a requisição, verifica a existencia de uma ação e aciona a mesma
	public function __construct(RequestClass $request = null)
	{
		//Verifica se existe uma ação na requisição e se existe um metodo correspondente
		if(!$request->get('action') || !method_exists($this, $request->get('action')))
		{
			$request->response(503,array('msg'=>'Action Undefined.'));
		}

		//Recupera a ação
		$action = $request->get('action');

		//Executa o metodo passando os parametros
		$this->$action($request);
	}

	public function getUser($request)
	{
		if(!$username = $request->get('username'))
		{
			$request->response(422,array('msg'=>'Username is required'));
		}

		try{
			$connection = new ConnectionClass();	

			$username = $request->get('username');

			$result = $connection->select("SELECT * FROM ".getenv('DB_SCHEMA')."users WHERE username = '$username';");

			if($result->num_rows > 0)
			{
				$request->response(422,array(
					'error' => false,
					'data' => $result->fetch_assoc()
				));
			}
			else
			{
				$request->response(422,array(
					'error' => true,
					'data' => 'This user is not registered.'
				));
			}
		}
		catch(Exception $e)
		{
			$request->response(422,array('msg'=>'Unable to connect to database.'));
		}
	}

	public function addQueue($request)
	{
		if(!$queue = $request->get('queue'))
		{
			$request->response(422,array('msg'=>'Queue is required'));
		}

		$content = $queue . '|' . $request->get('data');
		$fileName = tempnam(getenv('QUEUE_PATH_PENDING'), $queue);

		$fp = fopen($fileName,"wb");
		if (!is_resource($fp))
		{ 
			$request->response(422,array(
				'error' => true,
				'data' => 'Command not added to queue'
			));
		}

		fwrite($fp,$content);

		$request->response(200,array(
			'error' => false,
			'data' => 'Command added to queue'
		));

		fclose($fp);
	}

	public function processQueue($request)
	{
		if(!file_exists('queue.lock'))
		{
			touch('queue.lock');
			try{
				if ($handle = opendir(getenv('QUEUE_PATH_PENDING')))
				{
				    while (false !== ($entry = readdir($handle)))
				    {
				        if ($entry != "." && $entry != "..")
				        {
				            rename(getenv('QUEUE_PATH_PENDING').DIRECTORY_SEPARATOR.$entry , getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry );

							$myfile = fopen(getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry, "r");
							$content = fread($myfile,filesize(getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry));
							fclose($myfile);

							$action = explode('|',$content);

							$error = false;

							switch($action[0])
							{
								case 'recoveryPassword': 
									$jsonText = $action[1];
									$decodedText = html_entity_decode($jsonText);
									$myArray = json_decode($decodedText, true);
									if(!$this->sendEmail($myArray['email'],'Password Recovery','Your password is: '.$myArray['password'])){
										$error = true;
									}
							}

							if($error)
							{
								rename(getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry , getenv('QUEUE_PATH_PENDING').DIRECTORY_SEPARATOR.$entry );
								error_log('Could not process queue: '.getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry);
							}
							else
							{
								rename(getenv('QUEUE_PATH_PROCESSING').DIRECTORY_SEPARATOR.$entry , getenv('QUEUE_PATH_PROCESSED').DIRECTORY_SEPARATOR.$entry );	
							}
				        }
				    }

				    closedir($handle);
				}
			}
			finally
			{
			    unlink('queue.lock');
			}			
		}
	}

	public function sendEmail($to, $subject, $body)
	{
		try
		{
			$transport = (new Swift_SmtpTransport(getenv('GMAIL_SMTP'), getenv('GMAIL_PORT'), getenv('GMAIL_SECURE')))
			->setUsername(getenv('GMAIL_ACCOUNT'))
			->setPassword(getenv('GMAIL_PASSWORD'));

			$mailer = new Swift_Mailer($transport);

			$message = (new Swift_Message($subject))
		  	->setFrom([getenv('GMAIL_FROM') => getenv('GMAIL_FROM_NAME')])
		  	->setTo($to)
		  	->setBody($body);

			return $mailer->send($message);
		}
		catch (Swift_TransportException $Ste)
		{
			return null;
		}
	}
}

new AppClass(new RequestClass($_POST));