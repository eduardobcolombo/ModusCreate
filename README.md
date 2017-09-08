# Modus Create PHP API Development Assignment

This is a simple application developed with Slim Framework 3 Skeleton.
This application is an API that is to be used the [NHTSA NCAP 5 Star Safety Ratings API](https://one.nhtsa.gov/webapi/Default.aspx?SafetyRatings/API/5). 

## Install the Application

Requirements:
You MUST be installed on your environment the follow :
- Git
- Composer
- PHP 7.0

Run this command from the directory in which you want to install your the application.

    git clone https://github.com/eduardobcolombo/ModusCreate.git backendAPI

Replace `[backendAPI]` with the desired directory name for your new application. You'll want to:

You MUST need to run the below command to install the libraries

    composer install

To run the application in development, you can also run this command. 

	composer start
	
Or 

    php -S 0.0.0.0:8080 -t public public/index.php
    
    
Run this command to run the test suite

	composer test
	
Or 

     vendor/phpunit/phpunit/phpunit
    
    
For deploy you can use any webserver, running the PHP 7.0 or later.
This application does not use any database or environment configurations.

If you discover a security vulnerability within this application, 
please send an e-mail to Eduardo de Brito Colombo at eduardobcolombo at gmail dot com.
