BorderCloud\SPARQL\SparqlClient
===============

Sparql HTTP Client for SPARQL1.1&#039;s Endpoint

You can send a query to any endpoint sparql
and read the result in an array.

Example : send a simple query to DBpedia
```php
<?php

use BorderCloud\SPARQL\SparqlClient;

$endpoint ="http://dbpedia.org/";
$sp_readonly = new SparqlClient($endpoint);
$q = "select * where {?x ?y ?z.} LIMIT 5";
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

For the different server, you can use the property setEndpointQuery,
setEndpointUpdate,setNameParameterQueryRead or setNameParameterQueryWrite.

EXAMPLE to config : Virtuoso
```php
$sc_readonly = new SparqlClient("http://localhost/tests/",$modeRead,$modeDebug);
```

EXAMPLE to config : Sesame
```php
$sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
$sc_readonly->setEndpointQuery("http://localhost/openrdf-sesame/repositories/tests");
$sc_readonly->setEndpointUpdate("http://localhost/openrdf-sesame/repositories/tests/statements");
```

EXAMPLE to config : Fuseki
```php
$sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
$sc_readonly->setEndpointQuery("http://localhost/tests/query");
$sc_readonly->setEndpointUpdate("http://localhost/tests/update");
```

EXAMPLE to config : Allegrograph
```php
$sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
$sc_readonly->setEndpointQuery("http://localhost/repositories/tests");
$sc_readonly->setEndpointUpdate("http://localhost/repositories/tests");
$sc_readonly->setNameParameterQueryWrite("query");
```

With a query ASK, you can use the parameter 'raw'
in the function query and read directly the result true or false.

Example : send a query ASK with the parameter raw
```php
<?php
$q = "PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
$res = $sc_readonly->query($q);
$err = $sc_readonly->getErrors();
if ($err) {
     print_r($err);
     throw new Exception(print_r($err,true));
}
var_dump($res);
?>
```

You can insert data also with SPARQL and the function query in your graphs.
The BorderCloud's service can host your graphs ( http://www.bordercloud.com ).
You can choose your graph's name and Bordercloud will give you a code.
With 3 parameters, you are alone to update your graph.

Example : send a query Insert
```php
$sp_write = new SparqlClient($MyEndPointSparql,$MyCode,$MyGraph);
echo "\nInsert :";
$q = "
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
INSERT DATA {
GRAPH <".$MyGraph."> {
a:A b:Name \"Test1\" .
a:A b:Name \"Test2\" .
a:A b:Name \"Test3\" .
}}";
$res = $sp_write->query($q,'raw');
$err = $sp_write->getErrors();
if ($err) {
     print_r($err);
     throw new Exception(print_r($err,true));
}
var_dump($res);
```

Example : send a query Delete
```php
$sp_write = new SparqlClient($MyEndPointSparql,$MyCode,$MyGraph);

echo "\nDelete :";
$q = "
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
DELETE DATA {
GRAPH <".$MyGraph."> {
a:A b:Name \"Test2\" .
}}";

$res = $sp_write->query($q,'raw');
$err = $sp_write->getErrors();
if ($err) {
     print_r($err);
     throw new Exception(print_r($err,true));
}
var_dump($res);

You can change the format of the response with the function
queryRead and queryUpdate.


* Class name: SparqlClient
* Namespace: BorderCloud\SPARQL
* Parent class: [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)





Properties
----------


### $_endpointRead

    private string $_endpointRead

URL of SPARQL endpoint to read



* Visibility: **private**


### $_endpointWrite

    private string $_endpointWrite

URL of SPARQL endpoint to write



* Visibility: **private**


### $_debug

    private boolean $_debug

in the constructor set debug to true in order to get useful output



* Visibility: **private**


### $_proxyHost

    private string $_proxyHost

in the constructor set the proxy_host if necessary



* Visibility: **private**


### $_proxyPort

    private integer $_proxyPort

in the constructor set the proxy_port if necessary



* Visibility: **private**


### $_parserSparqlResult

    private \BorderCloud\SPARQL\ParserSparqlResult $_parserSparqlResult

Parser of XML result



* Visibility: **private**


### $_nameParameterQueryRead

    private string $_nameParameterQueryRead

Name of parameter HTTP to send a query SPARQL to read data.



* Visibility: **private**


### $_nameParameterQueryWrite

    private string $_nameParameterQueryWrite

Name of parameter HTTP to send a query SPARQL to write data.



* Visibility: **private**


### $_methodHTTPRead

    private string $_methodHTTPRead

Method HTTP to send a query SPARQL to read data : GET or POST



* Visibility: **private**


### $_methodHTTPWrite

    private string $_methodHTTPWrite

Method HTTP to send a query SPARQL to read data : GET or POST



* Visibility: **private**


### $_login

    private string $_login

SPARQL service login



* Visibility: **private**


### $_password

    private string $_password

SPARQL service password



* Visibility: **private**


### $_lastError

    private string $_lastError





* Visibility: **private**


### $_errors

    private array $_errors

TODO



* Visibility: **private**


### $_maxErrors

    private integer $_maxErrors

TODO



* Visibility: **private**


Methods
-------


### __construct

    mixed BorderCloud\SPARQL\Base::__construct()

TODO

Base constructor.

* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)




### getProxyHost

    string BorderCloud\SPARQL\SparqlClient::getProxyHost()





* Visibility: **public**




### setProxyHost

    mixed BorderCloud\SPARQL\SparqlClient::setProxyHost(string $proxyHost)





* Visibility: **public**


#### Arguments
* $proxyHost **string**



### getProxyPort

    integer BorderCloud\SPARQL\SparqlClient::getProxyPort()





* Visibility: **public**




### setProxyPort

    mixed BorderCloud\SPARQL\SparqlClient::setProxyPort(integer $proxyPort)





* Visibility: **public**


#### Arguments
* $proxyPort **integer**



### setMethodHTTPRead

    mixed BorderCloud\SPARQL\SparqlClient::setMethodHTTPRead(string $method)

Set the method HTTP to read



* Visibility: **public**


#### Arguments
* $method **string** - &lt;p&gt;: HTTP method (GET or POST) for reading data (by default is POST)&lt;/p&gt;



### getMethodHTTPRead

    mixed BorderCloud\SPARQL\SparqlClient::getMethodHTTPRead()

Get the method HTTP to read



* Visibility: **public**




### setMethodHTTPWrite

    mixed BorderCloud\SPARQL\SparqlClient::setMethodHTTPWrite(string $method)

Set the method HTTP to write



* Visibility: **public**


#### Arguments
* $method **string** - &lt;p&gt;: HTTP method (GET or POST) for writing data (by default is POST)&lt;/p&gt;



### getMethodHTTPWrite

    mixed BorderCloud\SPARQL\SparqlClient::getMethodHTTPWrite()

Get the method HTTP to write



* Visibility: **public**




### setEndpointRead

    mixed BorderCloud\SPARQL\SparqlClient::setEndpointRead(string $url)

Set the url to read



* Visibility: **public**


#### Arguments
* $url **string** - &lt;p&gt;: endpoint&#039;s url to read&lt;/p&gt;



### getEndpointRead

    string BorderCloud\SPARQL\SparqlClient::getEndpointRead()

Get the url to read



* Visibility: **public**




### setEndpointWrite

    mixed BorderCloud\SPARQL\SparqlClient::setEndpointWrite(string $url)

Set the url to write



* Visibility: **public**


#### Arguments
* $url **string** - &lt;p&gt;: endpoint&#039;s url to write&lt;/p&gt;



### getEndpointWrite

    string BorderCloud\SPARQL\SparqlClient::getEndpointWrite()

Get the url to write



* Visibility: **public**




### setNameParameterQueryWrite

    mixed BorderCloud\SPARQL\SparqlClient::setNameParameterQueryWrite(string $name)

Set the parameter in the query to write



* Visibility: **public**


#### Arguments
* $name **string** - &lt;p&gt;: name of parameter&lt;/p&gt;



### getNameParameterQueryWrite

    string BorderCloud\SPARQL\SparqlClient::getNameParameterQueryWrite()

Get the parameter in the query to write



* Visibility: **public**




### setNameParameterQueryRead

    mixed BorderCloud\SPARQL\SparqlClient::setNameParameterQueryRead(string $name)

Set the parameter in the query to read



* Visibility: **public**


#### Arguments
* $name **string** - &lt;p&gt;: name of parameter&lt;/p&gt;



### getNameParameterQueryRead

    string BorderCloud\SPARQL\SparqlClient::getNameParameterQueryRead()

Get the parameter in the query to read



* Visibility: **public**




### setLogin

    mixed BorderCloud\SPARQL\SparqlClient::setLogin(string $login)

Set the server login



* Visibility: **public**


#### Arguments
* $login **string** - &lt;p&gt;: server login&lt;/p&gt;



### getLogin

    string BorderCloud\SPARQL\SparqlClient::getLogin()

Get the server login



* Visibility: **public**




### setPassword

    mixed BorderCloud\SPARQL\SparqlClient::setPassword(string $password)

Set the server password



* Visibility: **public**


#### Arguments
* $password **string** - &lt;p&gt;: server password&lt;/p&gt;



### getPassword

    string BorderCloud\SPARQL\SparqlClient::getPassword()

Get the server login



* Visibility: **public**




### checkEndpointRead

    boolean BorderCloud\SPARQL\SparqlClient::checkEndpointRead()

Check if the SPARQL endpoint for reading is up.



* Visibility: **public**




### checkEndpointWrite

    boolean BorderCloud\SPARQL\SparqlClient::checkEndpointWrite()

Check if the SPARQL endpoint for writing is up.



* Visibility: **public**




### query

    array|boolean BorderCloud\SPARQL\SparqlClient::query(string $q, string $result_format)

This function parse a SPARQL query, send the query and parse the SPARQL result in a array.

You can custom the result with the parameter $result_format :
<ul>
<li>rows to return array of results
<li>row to return array of first result
<li>raw to return bool for request ask, insert and delete
</ul>

* Visibility: **public**


#### Arguments
* $q **string** - &lt;p&gt;: Query SPARQL&lt;/p&gt;
* $result_format **string** - &lt;p&gt;: Optional, rows, row or raw&lt;/p&gt;



### queryRead

    string BorderCloud\SPARQL\SparqlClient::queryRead(string $query, string $typeOutput)

Send a request SPARQL of type select or ask to endpoint directly and output the response
of server.

If you want parse the result of this function, it's better and simpler
to use the function query().

* Visibility: **public**


#### Arguments
* $query **string** - &lt;p&gt;: Query Sparql&lt;/p&gt;
* $typeOutput **string** - &lt;p&gt;by default &quot;application/sparql-results+xml&quot;,&lt;/p&gt;



### queryUpdate

    boolean BorderCloud\SPARQL\SparqlClient::queryUpdate(string $query, string $typeOutput)

Send a request SPARQL of type insert data or delete data to endpoint directly.

<ul>
<li>Example insert : PREFIX ex: <http://example.com/> INSERT DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
<li>Example delete : PREFIX ex: <http://example.com/> DELETE DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
</ul>

* Visibility: **public**


#### Arguments
* $query **string** - &lt;p&gt;: Query Sparql of type insert data or delete data only&lt;/p&gt;
* $typeOutput **string** - &lt;p&gt;by default &quot;application/sparql-results+xml&quot;,&lt;/p&gt;



### getLastError

    string BorderCloud\SPARQL\SparqlClient::getLastError()

Get last message for Sparql editor, ie print only the error syntax message.

Message supported for the moment :
 - Wikidata
 - Virtuoso 7

* Visibility: **public**




### mtime

    float BorderCloud\SPARQL\SparqlClient::mtime()

TODO



* Visibility: **public**
* This method is **static**.




### errorLog

    string BorderCloud\SPARQL\SparqlClient::errorLog(string $query, $data, string $endPoint, integer $httpcode, string $response)

write error for human
TODO :logs ? https://github.com/Seldaek/monolog



* Visibility: **private**


#### Arguments
* $query **string**
* $data **mixed**
* $endPoint **string**
* $httpcode **integer**
* $response **string**



### debugLog

    mixed BorderCloud\SPARQL\SparqlClient::debugLog(string $query, string $endPoint, string $httpcode, string $response)

Print logs
TODO : change logs



* Visibility: **private**


#### Arguments
* $query **string**
* $endPoint **string**
* $httpcode **string**
* $response **string**



### initCurl

    \BorderCloud\SPARQL\Curl BorderCloud\SPARQL\SparqlClient::initCurl()

Init an object Curl in function of proxy.



* Visibility: **private**




### addError

    boolean BorderCloud\SPARQL\Base::addError($error)

TODO



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)


#### Arguments
* $error **mixed**



### getErrors

    array BorderCloud\SPARQL\Base::getErrors()

Give the errors



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)




### resetErrors

    mixed BorderCloud\SPARQL\Base::resetErrors()

TODO



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)



