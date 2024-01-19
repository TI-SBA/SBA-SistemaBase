<?php
    require_once("google-api/autoload.php");
    //require_once("google-api/src/Google/Client.php");
    //require_once("google-api/src/Google/Service/Storage.php");
    //require_once("google-api/src/Google/Http/MediaFileUpload.php");
    session_start();
    /**
        * Connect to Google Cloud Storage API
    */
    $client = new Google_Client();
    $client->setApplicationName("ApplicationName");

    // $stored_access_token - your cached oauth access token 
    if( $stored_access_token ) {
        $client->setAccessToken( $stored_access_token );
    }

    $credential = new Google_Auth_AssertionCredentials(
    "email-sdfaskjrsd@developer.gserviceaccount.com",
    array('https://www.googleapis.com/auth/devstorage.read_write'),
    file_get_contents("pathtokey/mykeyhere-7845b2eb92c9.p12")
    );

    $client->setAssertionCredentials($credential);
    if($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($credential);
        // Cache the access token however you choose, getting the access token with $client->getAccessToken()
    }

    $storage = new Google_Service_Storage($client);



    if (isset($_GET['code'])) {
        if (strval($_SESSION['state']) !== strval($_GET['state'])) {
            die('The session state did not match.');
        }


        $client->authenticate($_GET['code']);
        $_SESSION['token'] = $client->getAccessToken();
        header('Location: ' . $redirect);
    }
    if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
    }
    if ($client->getAccessToken()) {


            $sfilename = "mfile.zip"; //filename here
            $obj = new Google_Service_Storage_StorageObject();

            $obj->setName($sfilename);
            $obj->setBucket("myBucketS"); //bucket name here


            $filen = "pathtofile/uploadthis.zip";

            $mimetype = mime_content_type($filen);


            $chunkSizeBytes = 1 * 1024 * 1024;
            $client->setDefer(true);
            $status = false;

            $filetoupload = array('name' => $sfilename, 'uploadType' => 'resumable');

            $request = $storage->objects->insert("myBucketS",$obj,$filetoupload);

            $media = new Google_Http_MediaFileUpload($client, $request, $mimetype, null, true, $chunkSizeBytes);
            $media->setFileSize(filesize($filen));
            $handle = fopen($filen, "rb");

            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            $result = false;
            if($status != false) {
                $result = $status;
            }

            fclose($handle);
            // Reset to the client to execute requests immediately in the future.
            $client->setDefer(false);

        } else {
        // If the user hasn't authorized the app, initiate the OAuth flow
        $state = mt_rand();
        $client->setState($state);
        $_SESSION['state'] = $state;
        $authUrl = $client->createAuthUrl();
    }
    $_SESSION['token'] = $client->getAccessToken();

    print_r($status);


?>