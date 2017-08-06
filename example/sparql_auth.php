<?php
use BorderCloud\SPARQL\Endpoint;

// git clone https://github.com/BorderCloud/SPARQL
// ./query -r -e https://example.com/sparql-auth -f ./example/queryRead1.rq -l login -p password -v

require_once ('../vendor/autoload.php');

$endpoint = "https://example.com/sparql-auth";
$sp_ReadAndWrite = new Endpoint($endpoint, false);

$sp_ReadAndWrite->setLogin("login");
$sp_ReadAndWrite->setPassword("password");

$q = "select *  where {?x ?y ?z.} LIMIT 5";
$rows = $sp_ReadAndWrite->query($q, 'rows');
$err = $sp_ReadAndWrite->getErrors();
if ($err) {
    print_r($err);
    throw new Exception(print_r($err, true));
}

foreach ($rows["result"]["variables"] as $variable) {
    printf("%-20.20s", $variable);
    echo '|';
}
echo "\n";

foreach ($rows["result"]["rows"] as $row) {
    foreach ($rows["result"]["variables"] as $variable) {
        printf("%-20.20s", $row[$variable]);
        echo '|';
    }
    echo "\n";
}
