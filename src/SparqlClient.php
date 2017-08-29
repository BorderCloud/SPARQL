<?php
declare(strict_types=1);

/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;
/**
 * Sparql HTTP Client for SPARQL1.1's Endpoint
 *
 * You can send a query to any endpoint sparql
 * and read the result in an array.
 *
 * Example : send a simple query to DBpedia
 * ```php
 * <?php
 *
 * use BorderCloud\SPARQL\SparqlClient;
 *
 * $endpoint ="http://dbpedia.org/";
 * $sp_readonly = new SparqlClient($endpoint);
 * $q = "select * where {?x ?y ?z.} LIMIT 5";
 * $rows = $sp_readonly->query($q, 'rows');
 * $err = $sp_readonly->getErrors();
 * if ($err) {
 *      print_r($err);
 *      throw new Exception(print_r($err,true));
 * }
 *
 * foreach($rows["result"]["variables"] as $variable){
 *      printf("%-20.20s",$variable);
 *      echo '|';
 * }
 * echo "\n";
 *
 * foreach ($rows["result"]["rows"] as $row){
 *      foreach($rows["result"]["variables"] as $variable){
 *          printf("%-20.20s",$row[$variable]);
 *          echo '|';
 *      }
 *      echo "\n";
 * }
 * ?>
 * ```
 *
 * For the different server, you can use the property setEndpointQuery,
 * setEndpointUpdate,setNameParameterQueryRead or setNameParameterQueryWrite.
 *
 * EXAMPLE to config : Virtuoso
 * ```php
 * $sc_readonly = new SparqlClient("http://localhost/tests/",$modeRead,$modeDebug);
 * ```
 *
 * EXAMPLE to config : Sesame
 * ```php
 * $sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
 * $sc_readonly->setEndpointQuery("http://localhost/openrdf-sesame/repositories/tests");
 * $sc_readonly->setEndpointUpdate("http://localhost/openrdf-sesame/repositories/tests/statements");
 * ```
 *
 * EXAMPLE to config : Fuseki
 * ```php
 * $sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
 * $sc_readonly->setEndpointQuery("http://localhost/tests/query");
 * $sc_readonly->setEndpointUpdate("http://localhost/tests/update");
 * ```
 *
 * EXAMPLE to config : Allegrograph
 * ```php
 * $sc_readonly = new SparqlClient("",$modeRead,$modeDebug);
 * $sc_readonly->setEndpointQuery("http://localhost/repositories/tests");
 * $sc_readonly->setEndpointUpdate("http://localhost/repositories/tests");
 * $sc_readonly->setNameParameterQueryWrite("query");
 * ```
 *
 * With a query ASK, you can use the parameter 'raw'
 * in the function query and read directly the result true or false.
 *
 * Example : send a query ASK with the parameter raw
 * ```php
 * <?php
 * $q = "PREFIX a: <http://example.com/test/a/>
 * PREFIX b: <http://example.com/test/b/>
 * ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
 * $res = $sc_readonly->query($q);
 * $err = $sc_readonly->getErrors();
 * if ($err) {
 *      print_r($err);
 *      throw new Exception(print_r($err,true));
 * }
 * var_dump($res);
 * ?>
 * ```
 *
 * You can insert data also with SPARQL and the function query in your graphs.
 * The BorderCloud's service can host your graphs ( http://www.bordercloud.com ).
 * You can choose your graph's name and Bordercloud will give you a code.
 * With 3 parameters, you are alone to update your graph.
 *
 * Example : send a query Insert
 * ```php
 * $sp_write = new SparqlClient($MyEndPointSparql,$MyCode,$MyGraph);
 * echo "\nInsert :";
 * $q = "
 * PREFIX a: <http://example.com/test/a/>
 * PREFIX b: <http://example.com/test/b/>
 * INSERT DATA {
 * GRAPH <".$MyGraph."> {
 * a:A b:Name \"Test1\" .
 * a:A b:Name \"Test2\" .
 * a:A b:Name \"Test3\" .
 * }}";
 * $res = $sp_write->query($q,'raw');
 * $err = $sp_write->getErrors();
 * if ($err) {
 *      print_r($err);
 *      throw new Exception(print_r($err,true));
 * }
 * var_dump($res);
 * ```
 *
 * Example : send a query Delete
 * ```php
 * $sp_write = new SparqlClient($MyEndPointSparql,$MyCode,$MyGraph);
 *
 * echo "\nDelete :";
 * $q = "
 * PREFIX a: <http://example.com/test/a/>
 * PREFIX b: <http://example.com/test/b/>
 * DELETE DATA {
 * GRAPH <".$MyGraph."> {
 * a:A b:Name \"Test2\" .
 * }}";
 *
 * $res = $sp_write->query($q,'raw');
 * $err = $sp_write->getErrors();
 * if ($err) {
 *      print_r($err);
 *      throw new Exception(print_r($err,true));
 * }
 * var_dump($res);
 *
 * You can change the format of the response with the function
 * queryRead and queryUpdate.
 */
final class SparqlClient extends Base
{
    /**
     * URL of SPARQL endpoint to read
     *
     * @access private
     * @var string
     */
    private $_endpointRead;

    /**
     * URL of SPARQL endpoint to write
     *
     * @access private
     * @var string
     */
    private $_endpointWrite;

    /**
     * in the constructor set debug to true in order to get useful output
     *
     * @access private
     * @var bool
     */
    private $_debug;

    /**
     * in the constructor set the proxy_host if necessary
     *
     * @access private
     * @var string
     */
    private $_proxyHost;

    /**
     * in the constructor set the proxy_port if necessary
     *
     * @access private
     * @var int
     */
    private $_proxyPort;

    /**
     * Parser of XML result
     *
     * @var ParserSparqlResult
     */
    private $_parserSparqlResult;

    /**
     * Name of parameter HTTP to send a query SPARQL to read data.
     *
     * @var string
     */
    private $_nameParameterQueryRead;

    /**
     * Name of parameter HTTP to send a query SPARQL to write data.
     *
     * @var string
     */
    private $_nameParameterQueryWrite;

    /**
     * Method HTTP to send a query SPARQL to read data : GET or POST
     *
     * @var string
     */
    private $_methodHTTPRead;

    /**
     * Method HTTP to send a query SPARQL to read data : GET or POST
     *
     * @var string
     */
    private $_methodHTTPWrite;

    /**
     * SPARQL service login
     *
     * @var string
     */
    private $_login;

    /**
     * SPARQL service password
     *
     * @var string
     */
    private $_password;

    /**
     * @var string
     */
    private $_lastError;

    /**
     * Constructor of SparqlClient
     *
     * @param bool $debug
     *            : false by default, set debug to true in order to get useful output
     * @param string $proxyHost
     *            : null by default, IP of your proxy
     * @param string $proxyPort
     *            : null by default, port of your proxy
     * @access public
     */
    public function __construct($debug = false, $proxyHost = null, $proxyPort = null)
    {
        parent::__construct();

        $this->_debug = $debug;
        $this->_proxyHost = $proxyHost;
        $this->_proxyPort = $proxyPort;

        $this->_methodHTTPRead = "POST";
        $this->_methodHTTPWrite = "POST";
        $this->_nameParameterQueryRead = "query";
        $this->_nameParameterQueryWrite = "update";

        // init parser
        $this->_parserSparqlResult = new ParserSparqlResult();

        $this->_lastError = "";
    }

    /**
     * @return string
     */
    public function getProxyHost(): string
    {
        return $this->_proxyHost;
    }

    /**
     * @param string $proxyHost
     */
    public function setProxyHost(string $proxyHost)
    {
        $this->_proxyHost = $proxyHost;
    }

    /**
     * @return int
     */
    public function getProxyPort(): int
    {
        return $this->_proxyPort;
    }

    /**
     * @param int $proxyPort
     */
    public function setProxyPort(int $proxyPort)
    {
        $this->_proxyPort = $proxyPort;
    }

    /**
     * Set the method HTTP to read
     *
     * @param string $method
     *            : HTTP method (GET or POST) for reading data (by default is POST)
     * @access public
     */
    public function setMethodHTTPRead($method)
    {
        $this->_methodHTTPRead = $method;
    }

    /**
     * Get the method HTTP to read
     */
    public function getMethodHTTPRead()
    {
        return $this->_methodHTTPRead;
    }


    /**
     * Set the method HTTP to write
     *
     * @param string $method
     *            : HTTP method (GET or POST) for writing data (by default is POST)
     * @access public
     */
    public function setMethodHTTPWrite($method)
    {
        $this->_methodHTTPWrite = $method;
    }

    /**
     * Get the method HTTP to write
     */
    public function getMethodHTTPWrite()
    {
        return $this->_methodHTTPWrite;
    }


    /**
     * Set the url to read
     *
     * @param string $url
     *            : endpoint's url to read
     * @access public
     */
    public function setEndpointRead($url)
    {
        // FIX for Wikidata
        if ($url == "https://query.wikidata.org/sparql") {
            $this->_methodHTTPRead = "GET";
        }
        $this->_endpointRead = $url;
    }

    /**
     * Get the url to read
     *
     * @return string $url : endpoint's url to read
     * @access public
     */
    public function getEndpointRead()
    {
        return $this->_endpointRead;
    }

    /**
     * Set the url to write
     *
     * @param string $url
     *            : endpoint's url to write
     * @access public
     */
    public function setEndpointWrite($url)
    {
        $this->_endpointWrite = $url;
    }

    /**
     * Get the url to write
     *
     * @return string $url : endpoint's url to write
     * @access public
     */
    public function getEndpointWrite()
    {
        return $this->_endpointWrite;
    }

    /**
     * Set the parameter in the query to write
     *
     * @param string $name
     *            : name of parameter
     * @access public
     */
    public function setNameParameterQueryWrite($name)
    {
        $this->_nameParameterQueryWrite = $name;
    }

    /**
     * Get the parameter in the query to write
     *
     * @return string $name : name of parameter
     * @access public
     */
    public function getNameParameterQueryWrite()
    {
        return $this->_nameParameterQueryWrite;
    }

    /**
     * Set the parameter in the query to read
     *
     * @param string $name
     *            : name of parameter
     * @access public
     */
    public function setNameParameterQueryRead($name)
    {
        $this->_nameParameterQueryRead = $name;
    }

    /**
     * Get the parameter in the query to read
     *
     * @return string $name : name of parameter
     * @access public
     */
    public function getNameParameterQueryRead()
    {
        return $this->_nameParameterQueryRead;
    }

    /**
     * Set the server login
     *
     * @param string $login
     *            : server login
     * @access public
     */
    public function setLogin($login)
    {
        $this->_login = $login;
    }

    /**
     * Get the server login
     *
     * @return string $login : server login
     * @access public
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * Set the server password
     *
     * @param string $password
     *            : server password
     * @access public
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * Get the server login
     *
     * @return string $password : server password
     * @access public
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Check if the SPARQL endpoint for reading is up.
     *
     * @return bool true if the service is up.
     * @access public
     */
    public function checkEndpointRead()
    {
        return Network::ping($this->_endpointRead) != - 1;
    }

    /**
     * Check if the SPARQL endpoint for writing is up.
     *
     * @return bool true if the service is up.
     * @access public
     */
    public function checkEndpointWrite()
    {
        return Network::ping($this->_endpointWrite) != - 1;
    }

    /**
     * This function parse a SPARQL query, send the query and parse the SPARQL result in a array.
     * You can custom the result with the parameter $result_format :
     * <ul>
     * <li>rows to return array of results
     * <li>row to return array of first result
     * <li>raw to return bool for request ask, insert and delete
     * </ul>
     *
     * @param string $q
     *            : Query SPARQL
     * @param string $result_format
     *            : Optional, rows, row or raw
     * @return array|bool in function of parameter $result_format
     * @access public
     */
    public function query($q, $result_format = 'rows')
    {
        $t1 = SparqlClient::mtime();
        $result = null;
        switch ($result_format) {
            case "json":
                $response = $this->queryRead($q, "application/sparql-results+json");
                $result = json_decode($response);
                break;
            case "row":
            case "raw":
            default: // rows
                $response = "";
                if (preg_match("/(INSERT|DELETE|CLEAR|LOAD)/i", $q)) {
                    $response = $this->queryUpdate($q);
                } else {
                    $response = $this->queryRead($q);
                }
                if(! empty($response)){
                    $parser = $this->_parserSparqlResult->getParser();
                    $success = xml_parse($parser, $response, true);
                    $result = $this->_parserSparqlResult->getResult();
                    if (! $success) { // if(! array_key_exists("result",$result)){
                        $message = "Error parsing XML result:" . xml_error_string(xml_get_error_code($parser)) . ' Response : ' . $response . "\n";
                        $error = $this->errorLog($q, null, $this->_endpointRead, 200, $message);
                        $this->addError($error);
                        return false;
                    }
                }else{
                    $result = array();
                }
        }
        $result['query_time'] = SparqlClient::mtime() - $t1;
        if(array_key_exists('boolean',$result)) { // ASK query
            return $result["boolean"];
        }else{ // other
            switch ($result_format) {
                case "row":
                    return $result["result"]["rows"][0];
                case "raw":
                     return $result["result"]["rows"][0][$result["result"]["variables"][0]];
                case "json":
                default: // rows
                    return $result;
            }
        }
    }

    /**
     * Send a request SPARQL of type select or ask to endpoint directly and output the response
     * of server.
     * If you want parse the result of this function, it's better and simpler
     * to use the function query().
     *
     * @param string $query
     *            : Query Sparql
     * @param string $typeOutput
     *            by default "application/sparql-results+xml",
     * @return string response of server or false if error (to do getErrors())
     * @access public
     */
    public function queryRead($query, $typeOutput = "application/sparql-results+xml")
    {
        $client = $this->initCurl();
        $sUri = $this->_endpointRead;
        $response = "";

        if ($typeOutput == null) {
            $data = array(
                $this->_nameParameterQueryRead => $query
            );
            if ($this->_methodHTTPRead == "POST") {
                $response = $client->sendPostData($sUri, $data);
            } else {
                $response = $client->fetchUrl($sUri, $data); // fix for wikidata
            }
        } else {
            $data = array(
                $this->_nameParameterQueryRead => $query,
                "output" => Mimetype::getShortNameOfMimetype($typeOutput), // possible fix for 4store/fuseki..
                "Accept" => $typeOutput
            ); // fix for sesame
                                      // print_r($data);
            if ($this->_methodHTTPRead == "POST") {
                $response = $client->sendPostData($sUri, $data, array(
                    'Accept: ' . $typeOutput
                ));
            } else {
                $response = $client->fetchUrl($sUri, $data); // fix for wikidata
            }
        }

        $code = $client->getHttpResponseCode();

        if (($code < 200 || $code >= 300)) {
            $error = $this->errorLog($query, $data, $sUri, $code, $response ."\n". $client->getErrorMsg());
            $this->addError($error);
            $this->_lastError = $response;
            return false;
        }else{
            $this->debugLog($query, $sUri, $code, $response);
        }
        return $response;
    }

    /**
     * Send a request SPARQL of type insert data or delete data to endpoint directly.
     * <ul>
     * <li>Example insert : PREFIX ex: <http://example.com/> INSERT DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
     * <li>Example delete : PREFIX ex: <http://example.com/> DELETE DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
     * </ul>
     *
     * @param string $query
     *            : Query Sparql of type insert data or delete data only
     * @param string $typeOutput
     *            by default "application/sparql-results+xml",
     * @return bool true if it did or false if error (to do getErrors())
     * @access public
     */
    public function queryUpdate($query, $typeOutput = "application/sparql-results+xml")
    {
        if (empty($this->_endpointWrite)) {
            $message = "Sorry, you have not configure the endpoint to update the database.\n";
            $error = $this->errorLog('', null, $this->_endpointWrite, 0, $message);
            $this->addError($error);
            return false;
        }

        $client = $this->initCurl();
        $sUri = $this->_endpointWrite;
        $response = "";

        if ($typeOutput == null) {
            $data = array(
                $this->_nameParameterQueryWrite => $query
            );
            if ($this->_methodHTTPWrite == "POST") {
                $response = $client->sendPostData($sUri, $data);
            } else {
                $response = $client->fetchUrl($sUri, $data); // fix for wikidata
            }
        } else {
            $data = array(
                $this->_nameParameterQueryWrite => $query,
                // "output" => ConversionMimetype::getShortNameOfMimetype($typeOutput), //possible fix for
                // 4store/fuseki..
                "Accept" => $typeOutput
            ); // fix for sesame
                                      // print_r($data);
            if ($this->_methodHTTPWrite == "POST") {
                $response = $client->sendPostData($sUri, $data, array(
                    'Accept: ' . $typeOutput
                ));
            } else {
                $response = $client->fetchUrl($sUri, $data); // fix for wikidata
            }
        }

        $code = $client->getHttpResponseCode();


        if ($code < 200 || $code >= 300) {
            $error = $this->errorLog($query, $data, $sUri, $code, $response ."\n". $client->getErrorMsg());
            $this->addError($error);
            $this->_lastError = $response;
            return false;
        }else{
            $this->debugLog($query, $sUri, $code, $response);
        }
        // echo "OK".$response;
        return $response;
    }

    /**
     * Get last message for Sparql editor, ie print only the error syntax message.
     *
     * Message supported for the moment :
     *  - Wikidata
     *  - Virtuoso 7
     *
     * @return string
     */
    public function getLastError()
    {
        $message = "";

        if (preg_match('#QueryException: (.*)\n\s*at java.#sU',
            $this->_lastError,
            $matches)) { // for Wikidata
            $message = $matches[1];
        }else  if (preg_match('#SPARQL compiler,\s*([^\s].*)\n#sU',
            $this->_lastError,
            $matches)){ // for Virtuoso
            $message = $matches[1];
        } else { //others
            $message = $this->_lastError;
        }
        return $message;
    }

    /**
     * *********************************************************************
     */
    // PRIVATE Function
    /**
     * TODO
     * @return float
     */
    static function mtime()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return ((float) $msec + (float) $sec);
    }

    /**
     * write error for human
     * TODO :logs ? https://github.com/Seldaek/monolog
     *
     * @param string $query
     * @param $data
     * @param string $endPoint
     * @param int $httpcode
     * @param string $response
     * @return string
     */
    private function errorLog($query, $data, $endPoint, $httpcode = 0, $response = '')
    {
        $error =
            "Error query  : " . $query . "\n" .
            "Error endpoint: " . $endPoint . "\n" .
            "Error http_response_code: " . $httpcode . "\n" .
            "Error message: " . $response . "\n";

        $error .= "Error data: " . print_r($data, true) . "\n";
        if ($this->_debug) {
            echo "\nDEBUG MESSAGE ERROR #######################\n" .
                $error .
                "\n#######################\n" ;
        } else {
            error_log($error);
        }
        return $error;
    }

    /**
     * Print logs
     * TODO : change logs
     *
     * @param string $query
     * @param string $endPoint
     * @param string $httpcode
     * @param string $response
     */
    private function debugLog($query, $endPoint, $httpcode = '', $response = '')
    {
        if ($this->_debug) {
            $error = "\nDEBUG MESSAGE #######################\n" .
                "query				: " . $query . "\n" .
                "endpoint			: " . $endPoint . "\n" .
                "http_response_code	: " . $httpcode . "\n" .
                "message			: " . $response .
                "\n#######################\n";

            echo $error;
        }
    }

    /**
     * Init an object Curl in function of proxy.
     *
     * @return Curl
     */
    private function initCurl()
    {
        $objCurl = new Curl(); // $this->_debug
        if ($this->_proxyHost != null && $this->_proxyPort != null) {
            $objCurl->setProxy($this->_proxyHost . ":" . $this->_proxyPort);
        }
        if ($this->_login != null && $this->_password != null) {
            $objCurl->setCredentials($this->_login, $this->_password);
        }
        return $objCurl;
    }

}
