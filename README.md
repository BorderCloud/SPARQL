# Lib Sparql 1.1 HTTP Client 

## Example : send a simple query to DBpedia
```php
<?php

require_once('bordercloud/Endpoint.php');

    $endpoint ="http://dbpedia.org/";
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
```

##  Documentation API 
[API](doc/Endpoint.md)

##  Install 
TODO !!

##  Copy Sources and tests 
TODO !!
git clone http://github.com/BorderCloud/SPARQL.git

##  Howto TESTS 
TODO !!

##  Contact 

If you have remarks, questions, or suggestions, please send them to
karima.rafes@bordercloud.com

## Release-Notes 

* V0.1.0.0 version SPARQL.Pro lib PHP by Karima Rafes <karima.rafes@bordercloud.com>

##  license 
SPARQL.Pro lib PHP (c)2014 by Karima Rafes - BorderCloud

SPARQL.Pro lib PHP is licensed under a
Creative Commons Attribution-ShareAlike 4.0 International License.

You should have received a copy of the license along with this
work. If not, see <http://creativecommons.org/licenses/by-sa/4.0/>. 

## Compile DOC 
php ../vendor/phpdocumentor/phpdocumentor/bin/phpdoc.php -d . --template="xml"
../vendor/evert/phpdoc-md/bin/phpdocmd ./output/structure.xml doc


