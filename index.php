<?php

include_once($_SERVER['DOCUMENT_ROOT']. '/streamr/streamr.class.php');

$streamrClient = new Streamr('STREAMR_PRIVATE_KEY', 'STREAMR_STREAM_ID');

$response = $streamrClient->publishData(array(
    'hello' => 'world'
));

print_r($response);
