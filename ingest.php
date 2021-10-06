<?php
require '/home/kafka/kafka/vendor/autoload.php';
use Kafka\Protocol;
use Kafka\Socket;
date_default_timezone_set('UTC');

// Verify that HTTP request method is POST
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    echo 'Request Method must be POST';
}

// Get content type from HTTP request header
$content_type = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

// Verify content type is 'application/json'
if(strcasecmp($content_type, 'application/json') != 0){
    echo 'Content Header must be application/json';
}

// Grab HTTP request payload
$json = file_get_contents('php://input');

// Convert request payload to PHP array
$decoded = json_decode($json);

// Verify request payload is was valid JSON
if(!is_array($decoded)){
    echo 'Data not in JSON format';
}

// /Set up Kafka Producer 
$config = \Kafka\ProducerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setBrokerVersion('1.0.0');
$config->setRequiredAck(1);
$config->setIsAsyn(false);
$config->setProduceInterval(500);
$producer = new \Kafka\Producer();

// Send data postback object for each object in $data
// Add endpoint method, url and timestamp
foreach($data as $item) {
    var_dump($item);
    $now_time = date("Y.m.d H:i:s");
    $item->endpoint_method = $decoded->endpoint->method;
    $item->endpoint_url = $decoded->endpoint->url;
    $item->start_time = $now_time;
    
    $producer->send([
        [
            'topic' => 'postback.delivery',
            'value' => json_encode($item),
            'key' => '',
        ],
    ]);
}

?>
