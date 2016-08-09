Endpoint
===============

Sparql HTTP Client for SPARQL1.1&#039;s Endpoint

You can send a query to any endpoint sparql
and read the result in an array.

Example : send a simple query to DBpedia
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

For the different server, you can use the property setEndpointQuery,
setEndpointUpdate,setNameParameterQueryRead or setNameParameterQueryWrite.

EXAMPLE to config : Virtuoso
```php
$sp_readonly = new Endpoint("http://localhost/tests/",$modeRead,$modeDebug);
```

EXAMPLE to config : 4Store
```php
$sp_readonly = new Endpoint("http://localhost/",$modeRead,$modeDebug);
```

EXAMPLE to config : Sesame
```php
$sp_readonly = new Endpoint("",$modeRead,$modeDebug);
$sp_readonly->setEndpointQuery("http://localhost/openrdf-sesame/repositories/tests");
$sp_readonly->setEndpointUpdate("http://localhost/openrdf-sesame/repositories/tests/statements");
```

EXAMPLE to config : Fuseki
```php
$sp_readonly = new Endpoint("",$modeRead,$modeDebug);
$sp_readonly->setEndpointQuery("http://localhost/tests/query");
$sp_readonly->setEndpointUpdate("http://localhost/tests/update");
```

EXAMPLE to config : Allegrograph
```php
$sp_readonly = new Endpoint("",$modeRead,$modeDebug);
$sp_readonly->setEndpointQuery("http://localhost/repositories/tests");
$sp_readonly->setEndpointUpdate("http://localhost/repositories/tests");
$sp_readonly->setNameParameterQueryWrite("query");
```

 With a query ASK, you can use the parameter 'raw'
 in the function query and read directly the result true or false.

Example : send a query ASK with the parameter raw
```php
<?php
   $q = "PREFIX a: <http://example.com/test/a/>
           PREFIX b: <http://example.com/test/b/>
           ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
   $res = $sp_readonly->query($q, 'raw');
   $err = $sp_readonly->getErrors();
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
    $sp_write = new Endpoint($MyEndPointSparql,$MyCode,$MyGraph);
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
    $sp_write = new Endpoint($MyEndPointSparql,$MyCode,$MyGraph);

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
```

 You can change the format of the response with the function
 QueryRead and QueryUpdate.


* Class name: Endpoint
* Namespace: 
* Parent class: [Base](Base.md)





Properties
----------


### $_endpoint_root

```
private string $_endpoint_root
```

Root of the URL Endpoint



* Visibility: **private**


### $_endpoint

```
private string $_endpoint
```

URL of Endpoint to read



* Visibility: **private**


### $_endpoint_write

```
private string $_endpoint_write
```

URL  sparql to write



* Visibility: **private**


### $_debug

```
private string $_debug
```

in the constructor set debug to true in order to get usefull output



* Visibility: **private**


### $_readOnly

```
private string $_readOnly
```

in the constructor set the right to write or not in the store



* Visibility: **private**


### $_proxy_host

```
private string $_proxy_host
```

in the constructor set the proxy_host if necessary



* Visibility: **private**


### $_proxy_port

```
private integer $_proxy_port
```

in the constructor set the proxy_port if necessary



* Visibility: **private**


### $_parserSparqlResult

```
private \ParserSparqlResult $_parserSparqlResult
```

Parser of XML result



* Visibility: **private**


### $_nameParameterQueryRead

```
private string $_nameParameterQueryRead
```

Name of parameter HTTP to send a query SPARQL to read data.



* Visibility: **private**


### $_nameParameterQueryWrite

```
private string $_nameParameterQueryWrite
```

Name of parameter HTTP to send a query SPARQL to write data.



* Visibility: **private**


### $_MethodHTTPRead

```
private string $_MethodHTTPRead
```

Method HTTP to send a query SPARQL to read data.



* Visibility: **private**


### $_MethodHTTPWrite

```
private mixed $_MethodHTTPWrite
```





* Visibility: **private**


### $_login

```
private mixed $_login
```





* Visibility: **private**


### $_password

```
private mixed $_password
```





* Visibility: **private**


### $_errors

```
private mixed $_errors
```





* Visibility: **private**


### $_max_errors

```
private mixed $_max_errors
```





* Visibility: **private**


Methods
-------


### \Endpoint::__construct()

```
mixed Endpoint::\Endpoint::__construct()(string $endpoint, boolean $readOnly, boolean $debug, string $proxy_host, string $proxy_port)
```

Constructor of Graph



* Visibility: **public**

#### Arguments

* $endpoint **string** - &lt;p&gt;: url of endpoint, example : &lt;a href=&quot;http://lod.bordercloud.com/sparql&quot;&gt;http://lod.bordercloud.com/sparql&lt;/a&gt;&lt;/p&gt;
* $readOnly **boolean** - &lt;p&gt;: true by default, if you allow the function query to write in the database&lt;/p&gt;
* $debug **boolean** - &lt;p&gt;: false by default, set debug to true in order to get usefull output&lt;/p&gt;
* $proxy_host **string** - &lt;p&gt;: null by default, IP of your proxy&lt;/p&gt;
* $proxy_port **string** - &lt;p&gt;: null by default, port of your proxy&lt;/p&gt;



### \Endpoint::setMethodHTTPRead()

```
mixed Endpoint::\Endpoint::setMethodHTTPRead()(string $method)
```

Set the method HTTP to read



* Visibility: **public**

#### Arguments

* $method **string** - &lt;p&gt;: HTTP method (GET or POST) for reading data (by default is POST)&lt;/p&gt;



### \Endpoint::setMethodHTTPWrite()

```
mixed Endpoint::\Endpoint::setMethodHTTPWrite()(string $method)
```

Set the method HTTP to write



* Visibility: **public**

#### Arguments

* $method **string** - &lt;p&gt;: HTTP method (GET or POST) for writing data (by default is POST)&lt;/p&gt;



### \Endpoint::setEndpointQuery()

```
mixed Endpoint::\Endpoint::setEndpointQuery()(string $url)
```

Set the url to read



* Visibility: **public**

#### Arguments

* $url **string** - &lt;p&gt;: endpoint&#039;s url to read&lt;/p&gt;



### \Endpoint::getEndpointQuery()

```
string Endpoint::\Endpoint::getEndpointQuery()()
```

Get the url to read



* Visibility: **public**



### \Endpoint::setEndpointUpdate()

```
mixed Endpoint::\Endpoint::setEndpointUpdate()(string $url)
```

Set the url to write



* Visibility: **public**

#### Arguments

* $url **string** - &lt;p&gt;: endpoint&#039;s url to write&lt;/p&gt;



### \Endpoint::getEndpointUpdate()

```
string Endpoint::\Endpoint::getEndpointUpdate()()
```

Get the url to write



* Visibility: **public**



### \Endpoint::setNameParameterQueryWrite()

```
mixed Endpoint::\Endpoint::setNameParameterQueryWrite()(string $name)
```

Set the parameter in the query to write



* Visibility: **public**

#### Arguments

* $name **string** - &lt;p&gt;: name of parameter&lt;/p&gt;



### \Endpoint::getNameParameterQueryWrite()

```
string Endpoint::\Endpoint::getNameParameterQueryWrite()()
```

Get the parameter in the query to write



* Visibility: **public**



### \Endpoint::setNameParameterQueryRead()

```
mixed Endpoint::\Endpoint::setNameParameterQueryRead()(string $name)
```

Set the parameter in the query to read



* Visibility: **public**

#### Arguments

* $name **string** - &lt;p&gt;: name of parameter&lt;/p&gt;



### \Endpoint::getNameParameterQueryRead()

```
string Endpoint::\Endpoint::getNameParameterQueryRead()()
```

Get the parameter in the query to read



* Visibility: **public**



### \Endpoint::setLogin()

```
mixed Endpoint::\Endpoint::setLogin()(string $login)
```

Set the server login



* Visibility: **public**

#### Arguments

* $login **string** - &lt;p&gt;: server login&lt;/p&gt;



### \Endpoint::getLogin()

```
string Endpoint::\Endpoint::getLogin()()
```

Get the server login



* Visibility: **public**



### \Endpoint::setPassword()

```
mixed Endpoint::\Endpoint::setPassword()(string $password)
```

Set the server password



* Visibility: **public**

#### Arguments

* $password **string** - &lt;p&gt;: server password&lt;/p&gt;



### \Endpoint::getPassword()

```
string Endpoint::\Endpoint::getPassword()()
```

Get the server login



* Visibility: **public**



### \Endpoint::check()

```
boolean Endpoint::\Endpoint::check()()
```

Check if the server is up.



* Visibility: **public**



### \Endpoint::query()

```
array|boolean Endpoint::\Endpoint::query()(string $q, string $result_format)
```

This function parse a SPARQL query, send the query and parse the SPARQL result in a array.

You can custom the result with the parameter $result_format :
<ul>
<li>rows to return array of results
<li>row to return array of first result
<li>raw to return boolean for request ask, insert and delete
</ul>

* Visibility: **public**

#### Arguments

* $q **string** - &lt;p&gt;: Query SPARQL&lt;/p&gt;
* $result_format **string** - &lt;p&gt;: Optional,  rows, row or raw&lt;/p&gt;



### \Endpoint::queryRead()

```
string Endpoint::\Endpoint::queryRead()(string $query, string $typeOutput)
```

Send a request SPARQL of type select or ask to endpoint directly and output the response
of server. If you want parse the result of this function, it's better and simpler
to use the function query().



* Visibility: **public**

#### Arguments

* $query **string** - &lt;p&gt;: Query Sparql&lt;/p&gt;
* $typeOutput **string** - &lt;p&gt;by default &quot;application/sparql-results+xml&quot;,&lt;/p&gt;



### \Endpoint::queryUpdate()

```
boolean Endpoint::\Endpoint::queryUpdate()(string $query, string $typeOutput)
```

Send a request SPARQL of type insert data or delete data to endpoint directly.

<ul>
<li>Example insert : PREFIX ex: <http://example.com/> INSERT DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
<li>Example delete : PREFIX ex: <http://example.com/> DELETE DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
</ul>

* Visibility: **public**

#### Arguments

* $query **string** - &lt;p&gt;: Query Sparql of type insert data or delete data only&lt;/p&gt;
* $typeOutput **string** - &lt;p&gt;by default &quot;application/sparql-results+xml&quot;,&lt;/p&gt;



### \Endpoint::mtime()

```
mixed Endpoint::\Endpoint::mtime()()
```





* Visibility: **public**
* This method is **static**.



### \Endpoint::errorLog()

```
mixed Endpoint::\Endpoint::errorLog()(string $query, $data, string $endPoint, \number $httpcode, string $response)
```

write error for human



* Visibility: **private**

#### Arguments

* $query **string**
* $data **mixed**
* $endPoint **string**
* $httpcode **number**
* $response **string**



### \Endpoint::debugLog()

```
mixed Endpoint::\Endpoint::debugLog()(\unknown_type $query, \unknown_type $endPoint, \unknown_type $httpcode, \unknown_type $response)
```

Print infos



* Visibility: **private**

#### Arguments

* $query **unknown_type**
* $endPoint **unknown_type**
* $httpcode **unknown_type**
* $response **unknown_type**



### \Endpoint::initCurl()

```
\an Endpoint::\Endpoint::initCurl()()
```

Init an object Curl in function of proxy.



* Visibility: **private**



### \Base::__construct()

```
mixed Endpoint::\Base::__construct()()
```





* Visibility: **public**



### \Base::AddError()

```
mixed Endpoint::\Base::AddError()($error)
```





* Visibility: **public**

#### Arguments

* $error **mixed**



### \Base::GetErrors()

```
array Endpoint::\Base::GetErrors()()
```

Give the errors



* Visibility: **public**



### \Base::ResetErrors()

```
mixed Endpoint::\Base::ResetErrors()()
```





* Visibility: **public**


