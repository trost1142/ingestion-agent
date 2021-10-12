# ingestion-agent
PHP Postback Deliver Ingestion Agent

Services used: apache2, php5

Port: 80

Sample request (curl):

curl -XPOST http://127.0.0.1/ingest.php -H 'Content-Type: application/json' -d '{"endpoint":{"method":"GET", "url":"http://sample_domain_endpoint.com/data?title={mascot}&image={location}&foo={bar}"},"data":[{"mascot":"Gopher", "location":"https://blog.golang.org/gopher/gopher.png"},{"mascot": "Puppy","location": "https://blog.golang.org/puppy/puppy.png"}]}'


Ingestion Agent Workflow:
1. Grab HTTP requst payload
2. Decode request payload (must be JSON)
3. Create Kafka Producer
4. For Each JSON object found in the "data" section (see Sample Request):
   - Create timestamp with milliseconds accuracy and in UTC timezone
   - Add "data" section, "endpoint_method", "endpoint_url", and "start_time" key:value pairs to JSON object
   - Send object through Kafka producer
   
Object Consruction:
-------------------------
 - "Data" -> JSON object will hold all values from original HTTP request JSON object.
 - "endpoint_method" -> Holds the value from endpoint.method from the original HTTP request JSON object
 - "endpoint_url" -> Holds the value from endpoint.url from the original HTTP request JSON object
 - "start_time" -> Holds the datetime of when each postback object is processed and sent through the Kafka producer. This value is used to calculate total delivery time and response times in the delivery-agent. 


Troubleshooting:
------------------------

Agent file location: /var/www/html/ingest.php

Access log file location: /var/log/apache2/access.log
Error log file location: /var/log/apache2/error.log

To view data that has been pushed Kafka, run the following:
  /etc/kafka/kafka/bin/kafka-console-consumer.sh --bootstrap-server localhost:9092 --topic postback.delivery --from-beginning
  
  If you do not see your data here, this likely means that parsing failed for the data. 
  Please verify that your HTTP request payload is in JSON format (Possible resource: jsonlint.com)
  Also look at errors in error log file above. 


Common Errors:
-----------------------
FileNotFound: /etc/kafka/kafka/vendor/autoload.php

This error typically happens if the supporting composer package has not been installed. 
To fix this error, do the following:
  cd /etc/kafka/kafka/
  php composer.phar require nmred/kafka-php


