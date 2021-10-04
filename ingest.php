<?php

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
if(!is_array($data)){
    echo 'Data not in JSON format';
}

echo $decoded->endpoint->method;
echo "\n";
echo $decoded->endpoint->url;
echo "\n";

$data = $decoded->data;
echo $data;

// Cycle over data objects
foreach($data as $item) {
    var_dump($item);
    echo $item->mascot;
    echo "\n";
}

?>
