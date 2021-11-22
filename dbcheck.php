<?php

// Checks the database structure is as expected and corrects if not (For installs and upgrades)

// Check we have a config file
if(!file_exists('config.php'))
{
    die('config.php not found. Please copy config.php.sample to config.php and enter data appropriate to your enviroment');
}

require('config.php');
require('functions/db.php');

// Set the target version we want installed
$latestVersion = '2';


if ($result = $db->query("SHOW TABLES LIKE 'systeminfo'")) {
    // This is a fresh install - set the version to '0'
    
    if($result->num_rows == 0) {
        $currentVersion = '0';
    }
    else
    {
        // Get the current version
        $result = $db->query("SELECT value FROM systeminfo WHERE name='version'");
        while ($row = $result->fetch_object()){
            $currentVersion = $row->value;
        }
    }
}

echo "Detected version: ";
print_r( $currentVersion);
$db->begin_transaction();

while ($currentVersion < $latestVersion)
{
    
    // VERSION 0 (FIRST INSTALL)
    if($currentVersion == '0')
    {
        // Create system table
        runQuery("CREATE TABLE systeminfo(
            id int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name varchar(255),
            value varchar(255)
            )");
       
        // Insert the current version

        runQuery("INSERT INTO systeminfo
            ( name, value )
                VALUES
            ( 'version', '1' )
        ");
        // Set the version we are up to
        $currentVersion = '1';
    }

    // VERSION 1
    if($currentVersion == '1')
    {
         // Create logs table
         runQuery("CREATE TABLE logs(
            id int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            date int(32),
            logstring TEXT
            )");



        // Set the version we are up to
        $currentVersion = '2';
    }
    
}


if(!empty($errorArray))
{
    echo "There was an error and the upgrade has been cancelled";
    $db->rollback();
    $print_r($errorArray);
}
else
{
    $db->commit();
}




// Runs the requested query and returns true or false if the task was successful
function runQuery($query)
{
    global $errorArray;
    global $db;

    $result = $db->query($query);
    print_r($db->error);
    print_r("<br>");
    if($db->error)
    {
        $errorArray[] = $db->error;
        return false;
    }
    return true;
}


?>