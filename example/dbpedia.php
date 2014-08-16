<?php

require_once('../Endpoint.php');

    $endpoint ="http://dbpedia.org/sparql";
    $sp_readonly = new Endpoint($endpoint);
 $q = "select *  where {?x ?y ?z.} LIMIT 5";
 $rows = $sp_readonly->query($q, 'rows');
 $err = $sp_readonly->getErrors();
 if ($err) {
      print_r($err);
      throw new Exception(print_r($err,true));
    }

 foreach($rows["result"]["variables"] as $variable){
        printf("%-20.20s",$variable);
        echo '|';
 }
 echo "\n";

 foreach ($rows["result"]["rows"] as $row){
        foreach($rows["result"]["variables"] as $variable){
                printf("%-20.20s",$row[$variable]);
        echo '|';
        }
        echo "\n";
 }
?>

