[Settings]
 - Pure Javascript
 - PHP 7.2
 - MariaDB 10.3 (Driver)
 - Composer
 - Swift_Message - Submitted by GMAIL
 - Curl

[Running the project]

To make the project work just follow the steps below

1 - Add the following entries to the computer's hosts
127.0.0.1 webserver.distributed.system
127.0.0.1 producer.distributed.system
127.0.0.1 consumer.distributed.system

2 - Restore the DUMP of the database inside the 04-database_dump folder

3 - Run composer to install Swift_Message

4 - Change Database and GMAIL variables from ENV files inside the 02-producer and 03-consumer folders

5 - Access the project folder and execute the following commands
php.exe -S webserver.distributed.system:8080 -t 01-webserver
php.exe -S producer.distributed.system:8081 -t 02-producer
php.exe -S consumer.distributed.system:8082 -t 03-consumer

6 - Access the following address: http://webserver.distributed.system:8080

7 - To process the queue just run the following command or add in linux cron or Windows ScheduleTask
curl http://consumer.distributed.system:8082/?action=processQueue

[TO-DO]
 - Add single tests
 - Refactor code to use market frameworks such as Laravel for backend and Vue.js for frontend
 - Refactor code to inject notification classes (WhatsApp, Telegram, SMS and other email classes)
 - Comment all code
 - Abstract persistence layer

[Comments]
 - The whole project was made from 0 based on the test creation sent by email.
 - The only library I used was from Swift to work with GMAIL
 - I have been working since 2013 using frameworks such as Symfony, Zend, CakePHP and 4 years ago working as Laravel. I work from the beginning of my career with support and support and so I need to understand how code works without the need for Framework.