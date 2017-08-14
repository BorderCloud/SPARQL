<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class Curl
 *
 * @package BorderCloud\SPARQL
 */
class Curl
{
    /**
     * * Curl handler
     *
     * @var resource
     */
    private $_curlHandler;

    /**
     * Set debug to true in order to get useful output
     *
     * @var bool
     */
    private $_debug = false;

    /**
     * Curl constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->_debug = $debug;

        // initialize curl handle
        $this->_curlHandler = curl_init();

        // set various options

        // set error in case http return code bigger than 300
        curl_setopt($this->_curlHandler, CURLOPT_FAILONERROR, true);

        // allow redirects
        curl_setopt($this->_curlHandler, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($this->_curlHandler, CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * Set username/pass for basic http auth
     *
     * @param $username
     * @param $password
     */
    public function setCredentials($username, $password)
    {
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_curlHandler, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($this->_curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->_curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_curlHandler, CURLOPT_FOLLOWLOCATION, true);
    }

    /**
     * Set referrerUrl
     *
     * @param string $referrerUrl
     */
    public function setReferrer($referrerUrl)
    {
        curl_setopt($this->_curlHandler, CURLOPT_REFERER, $referrerUrl);
    }

    /**
     * Set client's userAgent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        curl_setopt($this->_curlHandler, CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * Set to receive output headers in all output functions
     *
     * @param bool true to include all response headers with output, false otherwise
     */
    public function includeResponseHeaders($value)
    {
        curl_setopt($this->_curlHandler, CURLOPT_HEADER, $value);
    }

    /**
     * Set proxy to use for each curl request
     *
     * @param string $proxy
     */
    public function setProxy($proxy)
    {
        curl_setopt($this->_curlHandler, CURLOPT_PROXY, $proxy);
    }

    /**
     * Send post data to target URL
     * return data returned from url or false if error occurred
     *
     * @param string $url
     * @param array $postData post data array ie. $foo['post_var_name'] = $value
     * @param array $arrayHeader header array of the HTTP request (default null)
     * @param string $ip address to bind (default null)
     * @param int $timeout in sec for complete curl operation (default 600)
     * @return bool|string data
     */
    public function sendPostData($url, $postData, $arrayHeader = null, $ip = null, $timeout = 600)
    {
        // set various curl options first
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }

        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        // set method to post
        curl_setopt($this->_curlHandler, CURLOPT_POST, true);

        // generate post string
        $post_array = array();
        if (! is_array($postData)) {
            return false;
        }
        foreach ($postData as $key => $value) {
            $post_array[] = urlencode($key) . "=" . urlencode($value);
        }

        $post_string = implode("&", $post_array);

        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
            echo "Post String: $post_string\n";
        }

        // set post string
        curl_setopt($this->_curlHandler, CURLOPT_POSTFIELDS, $post_string);

        // set header
        if ($arrayHeader != null)
            curl_setopt($this->_curlHandler, CURLOPT_HTTPHEADER, $arrayHeader);

        //Read response when httpcode > 400
        curl_setopt($this->_curlHandler, CURLOPT_FAILONERROR, 0);

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }

    /**
     * fetch data from target URL
     * return data returned from url or false if error occurred
     *
     * @param string $url
     * @param array $getData get data array ie. $foo['get_var_name'] = $value (default null)
     * @param string $ip address to bind (default null)
     * @param int $timeout in sec for complete curl operation (default 600)
     * @return bool|string data
     */
    public function fetchUrl($url, $getData = null, $ip = null, $timeout = 600)
    {
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }

        // generate get string
        $get_array = array();
        if (is_array($getData)) {
            foreach ($getData as $key => $value) {
                $get_array[] = urlencode($key) . "=" . urlencode($value);
            }
            $get_string = implode("&", $get_array);

            if ($this->_debug) {
                curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
                echo "GET String: $get_string\n";
            }
        } else {
            $get_string = null;
        }

        // set url to post to
        if (empty($get_string)) {
            curl_setopt($this->_curlHandler, CURLOPT_URL, $url);
        } else {
            curl_setopt($this->_curlHandler, CURLOPT_URL, $url . "?" . $get_string);
        }

        // set method to get
        curl_setopt($this->_curlHandler, CURLOPT_HTTPGET, true);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        //Read response when httpcode > 400
        curl_setopt($this->_curlHandler, CURLOPT_FAILONERROR, 0);

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }

    /**
     * Fetch data from target URL
     * and store it directly to file
     *
     * @param string $url
     * @param resource $fp resource value stream resource(ie. fopen)
     * @param string $ip address to bind (default null)
     * @param int $timeout in sec for complete curl operation (default 5)
     * @return bool true on success false otherwise
     */
    public function fetchIntoFile($url, $fp, $ip = null, $timeout = 5)
    {
        if ($this->_debug){
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }
        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // set method to get
        curl_setopt($this->_curlHandler, CURLOPT_HTTPGET, true);

        // store data into file rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_FILE, $fp);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        // and finally send curl request
        curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * Send multipart post data to the target URL
     * return data returned from url or false if error occurred
     * (contribution by Vule Nikolic, vule@dinke.net)
     *
     * @param string $url
     * @param array $postData post data array ie. $foo['post_var_name'] = $value
     * @param array $fileFieldArray contains file_field name = value - path pairs
     * @param string $ip address to bind (default null)
     * @param int $timeout in sec for complete curl operation (default 30 sec)
     * @return bool|string data
     */
    public function sendMultipartPostData($url, $postData, $fileFieldArray = array(), $ip = null, $timeout = 30)
    {
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }
        // curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        // set various curl options first

        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        // set method to post
        curl_setopt($this->_curlHandler, CURLOPT_POST, true);

        // disable Expect header
        // hack to make it working
        $headers = array(
            "Expect: "
        );
        curl_setopt($this->_curlHandler, CURLOPT_HTTPHEADER, $headers);

        // generate post string
        $postArray = array();
        $postStringArray = array();
        if (! is_array($postData)) {
            return false;
        }

        foreach ($postData as $key => $value) {
            $postArray[$key] = $value;
            $postStringArray[] = urlencode($key) . "=" . urlencode($value);
        }

        $post_string = implode("&", $postStringArray);

        if ($this->_debug) {
            echo "Post String: $post_string\n";
        }

        // set post string
        // curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_string);

        // set multipart form data - file array field-value pairs
        if (! empty($fileFieldArray)) {
            foreach ($fileFieldArray as $var_name => $var_value) {
                if (strpos(PHP_OS, "WIN") !== false)
                    $var_value = str_replace("/", "\\", $var_value); // win hack
                $fileFieldArray[$var_name] = "@" . $var_value;
            }
        }

        // set post data
        $result_post = array_merge($postArray, $fileFieldArray);
        curl_setopt($this->_curlHandler, CURLOPT_POSTFIELDS, $result_post);

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }

    /**
     * Set file location where cookie data will be stored and send on each new request
     *
     * @param string $cookie_file absolute path to cookie file (must be in writable dir)
     */
    public function storeCookies($cookie_file)
    {
        // use cookies on each request (cookies stored in $cookie_file)
        curl_setopt($this->_curlHandler, CURLOPT_COOKIEJAR, $cookie_file);
    }

    /**
     * Get last URL info
     * useful when original url was redirected to other location
     *
     * @return string url
     */
    public function getEffectiveUrl()
    {
        return curl_getinfo($this->_curlHandler, CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Get http response code
     *
     * @return int
     */
    public function getHttpResponseCode()
    {
        return curl_getinfo($this->_curlHandler, CURLINFO_HTTP_CODE);
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return curl_getinfo($this->_curlHandler);
    }

    /**
     * Return last error message and error number
     *
     * @return string error msg
     * @access public
     */
    public function getErrorMsg()
    {
        $err = "Error number: " . curl_errno($this->_curlHandler) . "\n";
        $err .= "Error message: " . curl_error($this->_curlHandler) . "\n";
        return $err;
    }

    /**
     * TODO
     *
     * @param $url
     * @param $headersData
     * @param $postData
     * @param $content
     * @param null $ip
     * @param int $timeout
     * @return bool|mixed
     */
    public function sendPostContent($url, $headersData, $postData, $content, $ip = null, $timeout = 10)
    {
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }
        // set various curl options first

        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($this->_curlHandler, CURLOPT_CUSTOMREQUEST, 'POST');

        // generate post string
        $post_array = array();

        if (count($postData) > 0) {
            foreach ($postData as $key => $value) {
                $post_array[] = urlencode($key) . "=" . urlencode($value);
            }
            $post_array[] = "data=" . urlencode($content);
            $post_string = implode("&", $post_array);
        } else {
            $post_string = $content;
        }

        if ($this->_debug) {
            echo "content String: $content\n";
            echo "header : " . print_r($headersData, true) . "\n";
            echo "Post String: $post_string\n";
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }

        // set header
        curl_setopt($this->_curlHandler, CURLOPT_HTTPHEADER, array_merge(array(
            'Content-Length: ' . strlen($post_string)
        ), $headersData));

        // set post string
        curl_setopt($this->_curlHandler, CURLOPT_POSTFIELDS, $post_string);

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }

    /**
     * TODO
     *
     * @param $url
     * @param $headersData
     * @param $putData
     * @param null $ip
     * @param int $timeout
     * @return bool|mixed
     */
    public function sendPutData($url, $headersData, $putData, $ip = null, $timeout = 10)
    {
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }
        // set various curl options first

        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        // set method to put

        curl_setopt($this->_curlHandler, CURLOPT_CUSTOMREQUEST, 'PUT');

        if ($this->_debug) {
            echo "data String: $putData\n";
            echo "header String: $headersData\n";
        }

        // set header
        curl_setopt($this->_curlHandler, CURLOPT_HTTPHEADER, array_merge(array(
            'Content-Length: ' . strlen($putData)
        ), $headersData));

        // set post string
        curl_setopt($this->_curlHandler, CURLOPT_POSTFIELDS, $putData);

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }

    /**
     * TODO
     *
     * @param $url
     * @param null $ip
     * @param int $timeout
     * @return bool|mixed
     */
    public function sendDelete($url, $ip = null, $timeout = 10)
    {
        if ($this->_debug) {
            curl_setopt($this->_curlHandler, CURLOPT_VERBOSE, true);
        }
        // set various curl options first

        // set url to post to
        curl_setopt($this->_curlHandler, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->_curlHandler, CURLOPT_RETURNTRANSFER, true);

        // bind to specific ip address if it is sent trough arguments
        if ($ip) {
            if ($this->_debug) {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->_curlHandler, CURLOPT_INTERFACE, $ip);
        }

        // set curl function timeout to $timeout
        curl_setopt($this->_curlHandler, CURLOPT_TIMEOUT, $timeout);

        // set method to put

        curl_setopt($this->_curlHandler, CURLOPT_CUSTOMREQUEST, "DELETE");

        // and finally send curl request
        $result = curl_exec($this->_curlHandler);

        if (curl_errno($this->_curlHandler)) {
            if ($this->_debug) {
                echo "Error occurred in Curl\n";
                echo "Error number: " . curl_errno($this->_curlHandler) . "\n";
                echo "Error message: " . curl_error($this->_curlHandler) . "\n";
            }
            return false;
        } else {
            return $result;
        }
    }
}
