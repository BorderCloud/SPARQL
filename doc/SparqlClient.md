# BorderCloud\SPARQL\SparqlClient  

Sparql HTTP Client for SPARQL1.1's Endpoint

You can send a query to any endpoint sparql
and read the result in an array.

Example : send a simple query to DBpedia
```php
<?php

use BorderCloud\SPARQL\SparqlClient;

$endpoint = "http://dbpedia.org/sparql";
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
$q = "select * where {?x ?y ?z.} LIMIT 5";
$rows = $sc->query($q, 'rows');
$err = $sc->getErrors();
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
$sc = new SparqlClient();
$sc->setEndpointRead($endpoint);
$sc->setEndpointWrite($endpoint);
```

EXAMPLE to config : Sesame
```php
$sc = new SparqlClient();
$sc->setEndpointRead("http://localhost/openrdf-sesame/repositories/tests");
$sc->setEndpointWrite("http://localhost/openrdf-sesame/repositories/tests/statements");
```

EXAMPLE to config : Fuseki
```php
$sc = new SparqlClient();
$sc->setEndpointRead("http://localhost/tests/query");
$sc->setEndpointWrite("http://localhost/tests/update");
```

EXAMPLE to config : Allegrograph
```php
$sc = new SparqlClient();
$sc->setEndpointRead("http://localhost/repositories/tests");
$sc->setEndpointWrite("http://localhost/repositories/tests");
$sc->setNameParameterQueryWrite("query");
```

With a query ASK, you can use the parameter 'raw'
in the function query and read directly the result true or false.

Example : send a query ASK with the parameter raw
```php
<?php
$q = "PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
$res = $sc->query($q);
$err = $sc->getErrors();
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
$sc = new SparqlClient();
$sc->setEndpointRead($MyEndPointSparql);
$sc->setEndpointWrite($MyEndPointSparql);
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
$res = $sc->query($q,'raw');
$err = $sc->getErrors();
if ($err) {
     print_r($err);
     throw new Exception(print_r($err,true));
}
var_dump($res);
```

Example : send a query Delete
```php
$sc = new SparqlClient();
$sc->setEndpointRead($MyEndPointSparql);
$sc->setEndpointWrite($MyEndPointSparql);

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
queryRead and queryUpdate.  



## Extend:

BorderCloud\SPARQL\Base

## Methods

| Name | Description |
|------|-------------|
|[checkEndpointRead](#sparqlclientcheckendpointread)|Check if the SPARQL endpoint for reading is up.|
|[checkEndpointWrite](#sparqlclientcheckendpointwrite)|Check if the SPARQL endpoint for writing is up.|
|[getEndpointRead](#sparqlclientgetendpointread)|Get the url to read|
|[getEndpointWrite](#sparqlclientgetendpointwrite)|Get the url to write|
|[getLastError](#sparqlclientgetlasterror)|Get last message for Sparql editor, ie print only the error syntax message.|
|[getLogin](#sparqlclientgetlogin)|Get the server login|
|[getMethodHTTPRead](#sparqlclientgetmethodhttpread)|Get the method HTTP to read|
|[getMethodHTTPWrite](#sparqlclientgetmethodhttpwrite)|Get the method HTTP to write|
|[getNameParameterQueryRead](#sparqlclientgetnameparameterqueryread)|Get the parameter in the query to read|
|[getNameParameterQueryWrite](#sparqlclientgetnameparameterquerywrite)|Get the parameter in the query to write|
|[getPassword](#sparqlclientgetpassword)|Get the server login|
|[getProxyHost](#sparqlclientgetproxyhost)|Get the proxy address|
|[getProxyPort](#sparqlclientgetproxyport)|Get the proxy port|
|[mtime](#sparqlclientmtime)|TODO|
|[query](#sparqlclientquery)|This function parse a SPARQL query, send the query and parse the SPARQL result in a array.|
|[queryRead](#sparqlclientqueryread)|Send a request SPARQL of type select or ask to endpoint directly and output the response
of server.|
|[queryUpdate](#sparqlclientqueryupdate)|Send a request SPARQL of type insert data or delete data to endpoint directly.|
|[setEndpointRead](#sparqlclientsetendpointread)|Set the url to read|
|[setEndpointWrite](#sparqlclientsetendpointwrite)|Set the url to write|
|[setLogin](#sparqlclientsetlogin)|Set the server login|
|[setMethodHTTPRead](#sparqlclientsetmethodhttpread)|Set the method HTTP to read|
|[setMethodHTTPWrite](#sparqlclientsetmethodhttpwrite)|Set the method HTTP to write|
|[setNameParameterQueryRead](#sparqlclientsetnameparameterqueryread)|Set the parameter in the query to read|
|[setNameParameterQueryWrite](#sparqlclientsetnameparameterquerywrite)|Set the parameter in the query to write|
|[setPassword](#sparqlclientsetpassword)|Set the server password|
|[setProxyHost](#sparqlclientsetproxyhost)|Set the proxy address|
|[setProxyPort](#sparqlclientsetproxyport)|Set the proxy port|

## Inherited methods

| Name | Description |
|------|-------------|
|__construct|TODO|
|addError|TODO|
|getErrors|Give the errors|
|resetErrors|TODO|



### SparqlClient::checkEndpointRead  

**Description**

```php
public checkEndpointRead (void)
```

Check if the SPARQL endpoint for reading is up. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`bool`

> true if the service is up.


<hr />


### SparqlClient::checkEndpointWrite  

**Description**

```php
public checkEndpointWrite (void)
```

Check if the SPARQL endpoint for writing is up. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`bool`

> true if the service is up.


<hr />


### SparqlClient::getEndpointRead  

**Description**

```php
public getEndpointRead (void)
```

Get the url to read 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $url : endpoint's url to read


<hr />


### SparqlClient::getEndpointWrite  

**Description**

```php
public getEndpointWrite (void)
```

Get the url to write 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $url : endpoint's url to write


<hr />


### SparqlClient::getLastError  

**Description**

```php
public getLastError (void)
```

Get last message for Sparql editor, ie print only the error syntax message. 

Message supported for the moment :  
- Wikidata  
- Virtuoso 7 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### SparqlClient::getLogin  

**Description**

```php
public getLogin (void)
```

Get the server login 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $login : server login


<hr />


### SparqlClient::getMethodHTTPRead  

**Description**

```php
public getMethodHTTPRead (void)
```

Get the method HTTP to read 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### SparqlClient::getMethodHTTPWrite  

**Description**

```php
public getMethodHTTPWrite (void)
```

Get the method HTTP to write 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`void`


<hr />


### SparqlClient::getNameParameterQueryRead  

**Description**

```php
public getNameParameterQueryRead (void)
```

Get the parameter in the query to read 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $name : name of parameter


<hr />


### SparqlClient::getNameParameterQueryWrite  

**Description**

```php
public getNameParameterQueryWrite (void)
```

Get the parameter in the query to write 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $name : name of parameter


<hr />


### SparqlClient::getPassword  

**Description**

```php
public getPassword (void)
```

Get the server login 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> $password : server password


<hr />


### SparqlClient::getProxyHost  

**Description**

```php
public getProxyHost (void)
```

Get the proxy address 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### SparqlClient::getProxyPort  

**Description**

```php
public getProxyPort (void)
```

Get the proxy port 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`int`




<hr />


### SparqlClient::mtime  

**Description**

```php
public static mtime (void)
```

TODO 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`float`




<hr />


### SparqlClient::query  

**Description**

```php
public query (string $q, string $result_format, int $timeout)
```

This function parse a SPARQL query, send the query and parse the SPARQL result in a array. 

You can custom the result with the parameter $result_format :  
<ul>  
<li>rows to return array of results  
<li>row to return array of first result  
<li>raw to return bool for request ask, insert and delete  
</ul> 

**Parameters**

* `(string) $q`
: : Query SPARQL  
* `(string) $result_format`
: : Optional, rows, row or raw  
* `(int) $timeout`
: : Optional, time in seconds for complete query (default 600)  

**Return Values**

`array|bool`

> in function of parameter $result_format


<hr />


### SparqlClient::queryRead  

**Description**

```php
public queryRead (string $query, string $typeOutput, int $timeout)
```

Send a request SPARQL of type select or ask to endpoint directly and output the response
of server. 

If you want parse the result of this function, it's better and simpler  
to use the function query(). 

**Parameters**

* `(string) $query`
: : Query Sparql  
* `(string) $typeOutput`
: by default "application/sparql-results+xml",  
* `(int) $timeout`
: : Optional, time in seconds for complete query (default 600)  

**Return Values**

`string`

> response of server or false if error (to do getErrors())


<hr />


### SparqlClient::queryUpdate  

**Description**

```php
public queryUpdate (string $query, string $typeOutput, int $timeout)
```

Send a request SPARQL of type insert data or delete data to endpoint directly. 

Example insert :  
```sparql  
PREFIX ex: <http://example.com/>  
INSERT DATA {  
     GRAPH <http://mygraph> {  
         ex:a ex:p 12 .  
     }  
}  
```  
Example delete :  
```sparql  
PREFIX ex: <http://example.com/>  
DELETE DATA {  
     GRAPH <http://mygraph> {  
         ex:a ex:p 12 .  
     }  
}  
``` 

**Parameters**

* `(string) $query`
: : Query Sparql of type insert data or delete data only  
* `(string) $typeOutput`
: by default "application/sparql-results+xml",  
* `(int) $timeout`
: : Optional, time in seconds for complete query (default 600)  

**Return Values**

`bool`

> true if it did or false if error (to do getErrors())


<hr />


### SparqlClient::setEndpointRead  

**Description**

```php
public setEndpointRead (string $url)
```

Set the url to read 

 

**Parameters**

* `(string) $url`
: : endpoint's url to read  

**Return Values**

`void`


<hr />


### SparqlClient::setEndpointWrite  

**Description**

```php
public setEndpointWrite (string $url)
```

Set the url to write 

 

**Parameters**

* `(string) $url`
: : endpoint's url to write  

**Return Values**

`void`


<hr />


### SparqlClient::setLogin  

**Description**

```php
public setLogin (string $login)
```

Set the server login 

 

**Parameters**

* `(string) $login`
: : server login  

**Return Values**

`void`


<hr />


### SparqlClient::setMethodHTTPRead  

**Description**

```php
public setMethodHTTPRead (string $method)
```

Set the method HTTP to read 

 

**Parameters**

* `(string) $method`
: : HTTP method (GET or POST) for reading data (by default is POST)  

**Return Values**

`void`


<hr />


### SparqlClient::setMethodHTTPWrite  

**Description**

```php
public setMethodHTTPWrite (string $method)
```

Set the method HTTP to write 

 

**Parameters**

* `(string) $method`
: : HTTP method (GET or POST) for writing data (by default is POST)  

**Return Values**

`void`


<hr />


### SparqlClient::setNameParameterQueryRead  

**Description**

```php
public setNameParameterQueryRead (string $name)
```

Set the parameter in the query to read 

 

**Parameters**

* `(string) $name`
: : name of parameter  

**Return Values**

`void`


<hr />


### SparqlClient::setNameParameterQueryWrite  

**Description**

```php
public setNameParameterQueryWrite (string $name)
```

Set the parameter in the query to write 

 

**Parameters**

* `(string) $name`
: : name of parameter  

**Return Values**

`void`


<hr />


### SparqlClient::setPassword  

**Description**

```php
public setPassword (string $password)
```

Set the server password 

 

**Parameters**

* `(string) $password`
: : server password  

**Return Values**

`void`


<hr />


### SparqlClient::setProxyHost  

**Description**

```php
public setProxyHost (string $proxyHost)
```

Set the proxy address 

 

**Parameters**

* `(string) $proxyHost`

**Return Values**

`void`


<hr />


### SparqlClient::setProxyPort  

**Description**

```php
public setProxyPort (int $proxyPort)
```

Set the proxy port 

 

**Parameters**

* `(int) $proxyPort`

**Return Values**

`void`


<hr />

