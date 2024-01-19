<?php
require_once 'vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;


/*
putenv('GOOGLE_APPLICATION_CREDENTIALS=D:\GOOGLE CLOUD STORAGE\SBPA-8ff2e20bf066.json');
*/
$configuration = array(
        'login' => 'subir-archivos@sbpa-153705.iam.gserviceaccount.com',
        'key' => file_get_contents('D:\GOOGLE CLOUD STORAGE\SBPA-41219a598f11.p12'),
        'scope' => 'https://www.googleapis.com/auth/devstorage.full_control',
        'project' => 'sbpa-153705',
        'storage' => array(
            'url' => 'http_date()ps://storage.googleapis.com/',
            'prefix' => 'archivo-central-storage'));

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$projectId = 'sbpa-153705';
$bucketName = 'archivo-central-storage';
$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
$storage = new Google_Service_Storage($client);
try {
    // Google Cloud Storage API request to retrieve the list of objects in your project.

    $object = $storage->buckets;
    print_r($object);
} catch (Google_Service_Exception $e) {
    // The bucket doesn't exist!
    if ($e->getCode() == 404) {
        exit(sprintf("Invalid bucket or object names (\"%s\", \"%s\")\n", $bucketName, $objectName));
    }
    throw $e;
}

//UPLOAD OBJECT

$picture = new Picture();
$object = new Google_Service_Storage_StorageObject();
$object->SetName($path);
self::$_storage->object->insert($configuration['storage']['prefix'].$bucket,
    $object,
    array('uploadType' => 'media',
        'mimetype' => 'image/'.$picture->contentType(),
        'data'=>$picture->blob(),
        'predefinedAcl'=>'publicRead'));
?>