<?php
// Handles inbound starling webhook events

require('../config.php');
require('../functions/db.php');

// Get the POST body
$json = file_get_contents('php://input');


// Fetch the provided signature
foreach (getallheaders() as $name => $value) {
    if($name == 'X-Hook-Signature')
    {
        $signature = $value;
    }
}

// Verify the signature matches one of our configured keys
$verified = false;
foreach($webhookPublicKeys as $key)
{
    // Convert the key string into a public key
    $pubkey_pem = "-----BEGIN PUBLIC KEY-----\n$key\n-----END PUBLIC KEY-----";
    $publicKey = openssl_pkey_get_public($pubkey_pem);
    if(!$publicKey)
    {
        syslog(LOG_WARNING, "feed-item: FAILED TO GENERATE PUBLIC KEY");
    }

    if(openssl_verify($json, base64_decode($signature), $publicKey, 'sha512WithRSAEncryption'))
    {
        $verified = true;
    }
}

// Check the message verified agasint one of our keys. If not, exit
if(!$verified)
{
    syslog(LOG_WARNING, "feed-item: Unable to verify agasint any key");
    exit();
}


// Log the raw json into the database
$now = time();
$stmt = $db->prepare("INSERT INTO logs(date, logstring) VALUES (?, ?)");
$stmt->bind_param("is", $now, $json);
$stmt->execute();

// Convert it into an array for processing
$payload = json_decode($json, true);


file_put_contents('./filename.txt', print_r($payload, true));
?>