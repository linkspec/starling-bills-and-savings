<?php

// Check we have a config file
if(!file_exists('config.php'))
{
    die('config.php not found. Please copy config.php.sample to config.php and enter data appropriate to your enviroment');
}

require('config.php');
require('functions/db.php');




?>


