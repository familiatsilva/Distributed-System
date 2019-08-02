<?php

$variables = [
    'WEBSERVER_URL' => 'http://webserver.distributed.system:8080',
    'CONSUMER_URL' => 'http://consumer.distributed.system:8082',
];

foreach ($variables as $key => $value)
{
    putenv("$key=$value");
}