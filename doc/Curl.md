Curl
===============






* Class name: Curl
* Namespace: 





Properties
----------


### $ch

```
public resource $ch
```

Curl handler



* Visibility: **public**


### $debug

```
public string $debug = false
```

set debug to true in order to get usefull output



* Visibility: **public**


### $error_msg

```
public string $error_msg
```

Contain last error message if error occured



* Visibility: **public**


Methods
-------


### \Curl::Curl()

```
mixed Curl::\Curl::Curl()($debug)
```

Curl_HTTP_Client constructor



* Visibility: **public**

#### Arguments

* $debug **mixed**



### \Curl::set_credentials()

```
mixed Curl::\Curl::set_credentials()($username, $password)
```

Set username/pass for basic http auth



* Visibility: **public**

#### Arguments

* $username **mixed**
* $password **mixed**



### \Curl::set_referrer()

```
mixed Curl::\Curl::set_referrer()($referrer_url)
```

Set referrer



* Visibility: **public**

#### Arguments

* $referrer_url **mixed**



### \Curl::set_user_agent()

```
mixed Curl::\Curl::set_user_agent()($useragent)
```

Set client's useragent



* Visibility: **public**

#### Arguments

* $useragent **mixed**



### \Curl::include_response_headers()

```
mixed Curl::\Curl::include_response_headers()($value)
```

Set to receive output headers in all output functions



* Visibility: **public**

#### Arguments

* $value **mixed**



### \Curl::set_proxy()

```
mixed Curl::\Curl::set_proxy()($proxy)
```

Set proxy to use for each curl request



* Visibility: **public**

#### Arguments

* $proxy **mixed**



### \Curl::send_post_data()

```
string Curl::\Curl::send_post_data()($url, $postdata, $arrayHeader, $ip, $timeout)
```

Send post data to target URL
return data returned from url or false if error occured



* Visibility: **public**

#### Arguments

* $url **mixed**
* $postdata **mixed**
* $arrayHeader **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::fetch_url()

```
string Curl::\Curl::fetch_url()($url, $getdata, $ip, $timeout)
```

fetch data from target URL
return data returned from url or false if error occured



* Visibility: **public**

#### Arguments

* $url **mixed**
* $getdata **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::fetch_into_file()

```
boolean Curl::\Curl::fetch_into_file()($url, $fp, $ip, $timeout)
```

Fetch data from target URL
and store it directly to file



* Visibility: **public**

#### Arguments

* $url **mixed**
* $fp **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::send_multipart_post_data()

```
string Curl::\Curl::send_multipart_post_data()($url, $postdata, $file_field_array, $ip, $timeout)
```

Send multipart post data to the target URL
return data returned from url or false if error occured
(contribution by vule nikolic, vule@dinke.net)



* Visibility: **public**

#### Arguments

* $url **mixed**
* $postdata **mixed**
* $file_field_array **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::store_cookies()

```
mixed Curl::\Curl::store_cookies()($cookie_file)
```

Set file location where cookie data will be stored and send on each new request



* Visibility: **public**

#### Arguments

* $cookie_file **mixed**



### \Curl::get_effective_url()

```
string Curl::\Curl::get_effective_url()()
```

Get last URL info
usefull when original url was redirected to other location



* Visibility: **public**



### \Curl::get_http_response_code()

```
integer Curl::\Curl::get_http_response_code()()
```

Get http response code



* Visibility: **public**



### \Curl::get_info()

```
mixed Curl::\Curl::get_info()()
```





* Visibility: **public**



### \Curl::get_error_msg()

```
string Curl::\Curl::get_error_msg()()
```

Return last error message and error number



* Visibility: **public**



### \Curl::send_post_content()

```
mixed Curl::\Curl::send_post_content()($url, $headersdata, $postdata, $content, $ip, $timeout)
```





* Visibility: **public**

#### Arguments

* $url **mixed**
* $headersdata **mixed**
* $postdata **mixed**
* $content **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::send_put_data()

```
mixed Curl::\Curl::send_put_data()($url, $headersdata, $putdata, $ip, $timeout)
```





* Visibility: **public**

#### Arguments

* $url **mixed**
* $headersdata **mixed**
* $putdata **mixed**
* $ip **mixed**
* $timeout **mixed**



### \Curl::send_delete()

```
mixed Curl::\Curl::send_delete()($url, $ip, $timeout)
```





* Visibility: **public**

#### Arguments

* $url **mixed**
* $ip **mixed**
* $timeout **mixed**


