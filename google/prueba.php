<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;



putenv('GOOGLE_APPLICATION_CREDENTIALS='.__DIR__.'/SBPA-8ff2e20bf066.json');

//UPLOAD OBJECT
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$projectId = 'sbpa-153705';
$bucketName = 'archivo-central-storage';
$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
$storage = new Google_Service_Storage($client);
$fileTmpName="tinker.jpg";
$file_name = "bodzin1.jpg";
$obj = new Google_Service_Storage_StorageObject();
$obj->setName($file_name);
$insert = $storage->objects->insert(
"archivo-central-storage",
$obj,
array('name' => $file_name, 'data' => file_get_contents($fileTmpName),'uploadType' => 'media')
);
print_r($insert);
?>