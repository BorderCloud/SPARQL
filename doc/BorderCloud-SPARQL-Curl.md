BorderCloud\SPARQL\Curl
===============

Class Curl




* Class name: Curl
* Namespace: BorderCloud\SPARQL





Properties
----------


### $_curlHandler

    private resource $_curlHandler

* Curl handler



* Visibility: **private**


### $_debug

    private boolean $_debug = false

Set debug to true in order to get useful output



* Visibility: **private**


Methods
-------


### __construct

    mixed BorderCloud\SPARQL\Curl::__construct(boolean $debug)

Curl constructor.



* Visibility: **public**


#### Arguments
* $debug **boolean**



### setCredentials

    mixed BorderCloud\SPARQL\Curl::setCredentials($username, $password)

Set username/pass for basic http auth



* Visibility: **public**


#### Arguments
* $username **mixed**
* $password **mixed**



### setReferrer

    mixed BorderCloud\SPARQL\Curl::setReferrer(string $referrerUrl)

Set referrerUrl



* Visibility: **public**


#### Arguments
* $referrerUrl **string**



### setUserAgent

    mixed BorderCloud\SPARQL\Curl::setUserAgent(string $userAgent)

Set client's userAgent



* Visibility: **public**


#### Arguments
* $userAgent **string**



### includeResponseHeaders

    mixed BorderCloud\SPARQL\Curl::includeResponseHeaders(boolean $value)

Set to receive output headers in all output functions



* Visibility: **public**


#### Arguments
* $value **boolean** - &lt;p&gt;true to include all response headers with output, false otherwise&lt;/p&gt;



### setProxy

    mixed BorderCloud\SPARQL\Curl::setProxy(string $proxy)

Set proxy to use for each curl request



* Visibility: **public**


#### Arguments
* $proxy **string**



### sendPostData

    boolean|string BorderCloud\SPARQL\Curl::sendPostData(string $url, array $postData, array $arrayHeader, string $ip, integer $timeout)

Send post data to target URL
return data returned from url or false if error occurred



* Visibility: **public**


#### Arguments
* $url **string**
* $postData **array** - &lt;p&gt;post data array ie. $foo[&#039;post_var_name&#039;] = $value&lt;/p&gt;
* $arrayHeader **array** - &lt;p&gt;header array of the HTTP request (default null)&lt;/p&gt;
* $ip **string** - &lt;p&gt;address to bind (default null)&lt;/p&gt;
* $timeout **integer** - &lt;p&gt;in sec for complete curl operation (default 600)&lt;/p&gt;



### fetchUrl

    boolean|string BorderCloud\SPARQL\Curl::fetchUrl(string $url, array $getData, string $ip, integer $timeout)

fetch data from target URL
return data returned from url or false if error occurred



* Visibility: **public**


#### Arguments
* $url **string**
* $getData **array** - &lt;p&gt;get data array ie. $foo[&#039;get_var_name&#039;] = $value (default null)&lt;/p&gt;
* $ip **string** - &lt;p&gt;address to bind (default null)&lt;/p&gt;
* $timeout **integer** - &lt;p&gt;in sec for complete curl operation (default 600)&lt;/p&gt;



### fetchIntoFile

    boolean BorderCloud\SPARQL\Curl::fetchIntoFile(string $url, resource $fp, string $ip, integer $timeout)

Fetch data from target URL
and store it directly to file



* Visibility: **public**


#### Arguments
* $url **string**
* $fp **resource** - &lt;p&gt;resource value stream resource(ie. fopen)&lt;/p&gt;
* $ip **string** - &lt;p&gt;address to bind (default null)&lt;/p&gt;
* $timeout **integer** - &lt;p&gt;in sec for complete curl operation (default 5)&lt;/p&gt;



### sendMultipartPostData

    boolean|string BorderCloud\SPARQL\Curl::sendMultipartPostData(string $url, array $postData, array $fileFieldArray, string $ip, integer $timeout)

Send multipart post data to the target URL
return data returned from url or false if error occurred
(contribution by Vule Nikolic, vule@dinke.net)



* Visibility: **public**


#### Arguments
* $url **string**
* $postData **array** - &lt;p&gt;post data array ie. $foo[&#039;post_var_name&#039;] = $value&lt;/p&gt;
* $fileFieldArray **array** - &lt;p&gt;contains file_field name = value - path pairs&lt;/p&gt;
* $ip **string** - &lt;p&gt;address to bind (default null)&lt;/p&gt;
* $timeout **integer** - &lt;p&gt;in sec for complete curl operation (default 30 sec)&lt;/p&gt;



### storeCookies

    mixed BorderCloud\SPARQL\Curl::storeCookies(string $cookie_file)

Set file location where cookie data will be stored and send on each new request



* Visibility: **public**


#### Arguments
* $cookie_file **string** - &lt;p&gt;absolute path to cookie file (must be in writable dir)&lt;/p&gt;



### getEffectiveUrl

    string BorderCloud\SPARQL\Curl::getEffectiveUrl()

Get last URL info
useful when original url was redirected to other location



* Visibility: **public**




### getHttpResponseCode

    integer BorderCloud\SPARQL\Curl::getHttpResponseCode()

Get http response code



* Visibility: **public**




### getInfo

    mixed BorderCloud\SPARQL\Curl::getInfo()





* Visibility: **public**




### getErrorMsg

    string BorderCloud\SPARQL\Curl::getErrorMsg()

Return last error message and error number



* Visibility: **public**




### sendPostContent

    boolean|mixed BorderCloud\SPARQL\Curl::sendPostContent($url, $headersData, $postData, $content, null $ip, integer $timeout)

TODO



* Visibility: **public**


#### Arguments
* $url **mixed**
* $headersData **mixed**
* $postData **mixed**
* $content **mixed**
* $ip **null**
* $timeout **integer**



### sendPutData

    boolean|mixed BorderCloud\SPARQL\Curl::sendPutData($url, $headersData, $putData, null $ip, integer $timeout)

TODO



* Visibility: **public**


#### Arguments
* $url **mixed**
* $headersData **mixed**
* $putData **mixed**
* $ip **null**
* $timeout **integer**



### sendDelete

    boolean|mixed BorderCloud\SPARQL\Curl::sendDelete($url, null $ip, integer $timeout)

TODO



* Visibility: **public**


#### Arguments
* $url **mixed**
* $ip **null**
* $timeout **integer**


