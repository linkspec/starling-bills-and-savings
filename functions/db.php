<?php

// Check if we have a DB connection
$db = new mysqli($dbhost,$dbuser,$dpass,$dbname);

if ($db -> connect_errno) {
    echo "There was an error - Please enable debuggins for more info <br>";
    if($debug) echo "Failed to connect to MySQL ". $db -> connect_error;
    exit();
}

?>