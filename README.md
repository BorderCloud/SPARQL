[![Build Status](https://travis-ci.org/BorderCloud/SPARQL.svg?branch=master)](https://travis-ci.org/BorderCloud/SPARQL)


## Lib Sparql 1.1 HTTP Client

Very simple SparqlClient for PHP.

Thanks to [contributors](https://github.com/BorderCloud/SPARQL/graphs/contributors).

### Installation

This project assumes you have composer installed. Simply add new dependency via Composer:
```sh
composer require bordercloud/sparql
```

To your composer.json, and then you can simply install with:
```sh
composer install
```

### Test the lib with a php script : query

You can test your first query sparql with DBPEDIA via a command line :
```sh
./query -r -e http://dbpedia.org/sparql -f ./example/queryReadDBpedia.rq
```

And the doc of this script with virtuoso, 4store, Allegrograph, Fuseki and Sesame :
```
USAGE : query [-r|-w][-e URL|--endpointQueryAndUpdate=URL]
		[--file=FILE|-f FILE]
        [-v|-verbose]

    -r                                  READ ONLY
    -w                                  WRITE ONLY
    -e, --endpointQueryAndUpdate=URL    Put url of endpoint to do query or
                                        update :
                                            URL/sparql/?query=...
                                            URL/update/?update=... (POST)
    -q, --endpointQueryOnly=URL         Put url of endpoint to do query :
                                            URL?query=...
    -u, --endpointUpdateOnly=URL        Put url of endpoint to do query :
                                            URL?update=... (POST)
    --nameParameterQuery=PARAMETER      Change the name of parameter in
                                        the request http to read.
                                        (by default : query)
    --nameParameterUpdate=PARAMETER     Change the name of parameter in
                                        the request http to write.
                                        (by default : update)
    -f,--file=File                      File of the query.
    -t, --typeOutput=TYPE               Type of response: table,txt,csv,tsv,ttl,srx,srj
                                        (by default : table)

    -l, --login=LOGIN                  Server login
    -p, --password=PASSWORD            Server password

    -v, --verbose                       Mode verbose
    -d, --debug                         Mode debug

EXAMPLE : Virtuoso
./query -w -e http://localhost/tests/ -f ./example/queryWrite1.rq

./query -r -e http://localhost/tests/ -f ./example/queryRead1.rq

EXAMPLE : 4Store
./query -w -e http://localhost/ -f ./example/queryWrite1.rq

./query -r -e http://localhost/ -f ./example/queryRead1.rq

EXAMPLE : Sesame
./query -w -q http://localhost/openrdf-sesame/repositories/tests \
 -u http://localhost/openrdf-sesame/repositories/tests/statements \
-f ./example/queryWrite1.rq

./query -r -q http://localhost/openrdf-sesame/repositories/tests \
 -u http://localhost/openrdf-sesame/repositories/tests/statements \
-f ./example/queryRead1.rq

EXAMPLE : Fuseki
./query -w -q http://localhost/tests/query \
-u http://localhost/tests/update \
-f ./example/queryWrite1.rq

./query -r -q http://localhost/tests/query \
-u http://localhost/tests/update \
-f ./example/queryRead1.rq

EXAMPLE : Allegrograph
./query -w -q http://localhost/repositories/tests \
-u http://localhost/repositories/tests \
--nameParameterUpdate=query \
-f ./example/queryWrite1.rq

./query -r -q http://localhost/repositories/tests \
-u http://localhost/repositories/tests \
--nameParameterUpdate=query \
-f ./example/queryRead1.rq
```

### Examples
Send a simple query to Wikidata :
```php
<?php
use BorderCloud\SPARQL\SparqlClient;

require_once ('../vendor/autoload.php');

$endpoint = "https://query.wikidata.org/sparql";
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
//$sc->setMethodHTTPRead("GET");
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
```

Send a simple query to DBpedia :
```php
<?php
use BorderCloud\SPARQL\SparqlClient;

require_once ('../vendor/autoload.php');

$endpoint = "http://dbpedia.org/sparql";
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
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
```

Send a simple query via an endpoint sparql-auth (with OpenLink Virtuoso Open-Source Edition) :
```php
<?php
use BorderCloud\SPARQL\SparqlClient;

require_once ('../vendor/autoload.php');

$endpoint = "https://example.com/sparql-auth";
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
//$sc->setEndpointWrite($endpoint);
$sc->setLogin("login");
$sc->setPassword("password");

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
```

###  Documentation API

* [SparqlClient](doc/BorderCloud-SPARQL-SparqlClient.md)
* [API](doc/ApiIndex.md)

### Copy Sources and tests

TODO !!
```sh
git clone http://github.com/BorderCloud/SPARQL.git
```

### Contact

If you have remarks, questions, or suggestions, please send them to
karima.rafes@bordercloud.com

### Release-Notes

* V2.0.5
** Compatibility : PHP 7.1 and psr-4
** Rename the class Endpoint to SparqlClient and simplify the constructor. You can set the endpoints  only by their setters.
** Rename several functions (PHP Lint)
** Update PHPDoc
** Add the function SparqlClient->getLastErreur() : can read the SPARQL syntax error directly, if the pattern of error exists (Add the pattern of Wikidata and Virtuoso)
** Move files and add tests + phpunit.xml. SparqlClient is coverage to 82% for the moment (coverage with Virtuoso and Wikidata).
** Enable Travis in GitHub

* V1.2.1.0 Add fix for Wikidata and other

* V1.1.0.0 version SPARQL.Pro lib PHP by Karima Rafes <karima.rafes@bordercloud.com>

### license

SPARQL.Pro lib PHP (c)2014 by Karima Rafes - BorderCloud

SPARQL.Pro lib PHP is licensed under a
Creative Commons Attribution-ShareAlike 4.0 International License.

You should have received a copy of the license along with this
work. If not, see <http://creativecommons.org/licenses/by-sa/4.0/>.

### Compile DOC

```sh
php vendor/phpdocumentor/phpdocumentor/bin/phpdoc --directory=src --template="xml"
vendor/evert/phpdoc-md/bin/phpdocmd ./output/structure.xml doc
```

### Git...
```sh
git pull
git push
git tag -a 2.0.1@dev -m "version dev"
git push --tags
```


