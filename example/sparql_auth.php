<?php
declare(strict_types=1);

use BorderCloud\SPARQL\SparqlClient;

// git clone https://github.com/BorderCloud/SPARQL
// ./query -r -e https://example.com/sparql-auth -f ./example/queryRead1.rq -l login -p password -v

require_once ('../vendor/autoload.php');

$endpoint = "https://example.com/sparql-auth";
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
//$sc->setEndpointWrite($endpoint);
$sc->setLogin("login");
$sc->setPassword("password");

$q = "select *  where {?x ?y ?z.} LIMIT 5";
$rows = $sc->query($q, 'rows');
$err = $sc->getErrors();
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
