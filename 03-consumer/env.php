<?php

$variables = [
	'DB_HOST'  		=> 'localhost',
	'DB_USERNAME' 	=> 'root',
	'DB_PASSWORD' 	=> '',
	'DB_NAME' 		=> 'distributed_system',
	'DB_PORT' 		=> '3306',
	'DB_SCHEMA' 	=> '',

	'GMAIL_SMTP' 		=> 'smtp.gmail.com',
	'GMAIL_PORT' 		=> 465,
	'GMAIL_SECURE' 		=> 'ssl',
	'GMAIL_ACCOUNT' 	=> 'familiatsilva@gmail.com',
	'GMAIL_PASSWORD' 	=> 'wuiipwofxhsrvkzf',
	'GMAIL_FROM' 		=> 'familiatsilva@gmail.com',
	'GMAIL_FROM_NAME'	=> 'Challenge - Distributed System',

	'QUEUE_PATH_PENDING' 	=> getcwd().'/queue/01-pending',
	'QUEUE_PATH_PROCESSING' => getcwd().'/queue/02-processing',
	'QUEUE_PATH_PROCESSED' 	=> getcwd().'/queue/03-processed',
];

foreach ($variables as $key => $value)
{
	putenv("$key=$value");
}