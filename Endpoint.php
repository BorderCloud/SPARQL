<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/
require_once("Curl.php");
require_once("Net.php");
require_once("Base.php");
require_once("ToolsConvert.php");
require_once("ToolsBlankNode.php");
require_once("ParserSparqlResult.php");
require_once("ConversionMimetype.php");

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
 * require_once('bordercloud/Endpoint.php');
 * 
 *     $endpoint ="http://dbpedia.org/";
 *     $sp_readonly = new Endpoint($endpoint);
 *  $q = "select *  where {?x ?y ?z.} LIMIT 5";
 *  $rows = $sp_readonly->query($q, 'rows');
 *  $err = $sp_readonly->getErrors();
 *  if ($err) {
 *       print_r($err);
 *       throw new Exception(print_r($err,true));
 *     }
 * 
 *  foreach($rows["result"]["variables"] as $variable){
 *  	printf("%-20.20s",$variable);
 *  	echo '|';
 *  }
 *  echo "\n";
 *  
 *  foreach ($rows["result"]["rows"] as $row){
 *  	foreach($rows["result"]["variables"] as $variable){
 *  		printf("%-20.20s",$row[$variable]);
 *  	echo '|';
 *  	}
 *  	echo "\n";
 *  }
 * ?>
 * ```
 *
 * For the different server, you can use the property setEndpointQuery,
 * setEndpointUpdate,setNameParameterQueryRead or setNameParameterQueryWrite.
 * 
 * EXAMPLE to config : Virtuoso
 * ```php
 * $sp_readonly = new Endpoint("http://localhost/tests/",$modeRead,$modeDebug);
 * ```
 * 
 * EXAMPLE to config : 4Store
 * ```php
 * $sp_readonly = new Endpoint("http://localhost/",$modeRead,$modeDebug);
 * ```
 * 
 * EXAMPLE to config : Sesame
 * ```php
 * $sp_readonly = new Endpoint("",$modeRead,$modeDebug);
 * $sp_readonly->setEndpointQuery("http://localhost/openrdf-sesame/repositories/tests");
 * $sp_readonly->setEndpointUpdate("http://localhost/openrdf-sesame/repositories/tests/statements");
 * ```
 * 
 * EXAMPLE to config : Fuseki
 * ```php
 * $sp_readonly = new Endpoint("",$modeRead,$modeDebug);
 * $sp_readonly->setEndpointQuery("http://localhost/tests/query");
 * $sp_readonly->setEndpointUpdate("http://localhost/tests/update");
 * ```
 * 
 * EXAMPLE to config : Allegrograph
 * ```php
 * $sp_readonly = new Endpoint("",$modeRead,$modeDebug);
 * $sp_readonly->setEndpointQuery("http://localhost/repositories/tests");
 * $sp_readonly->setEndpointUpdate("http://localhost/repositories/tests");
 * $sp_readonly->setNameParameterQueryWrite("query");
 * ```
 * 
 *  With a query ASK, you can use the parameter 'raw'
 *  in the function query and read directly the result true or false.
 * 
 * Example : send a query ASK with the parameter raw
 * ```php
 * <?php
 *    $q = "PREFIX a: <http://example.com/test/a/>
 *            PREFIX b: <http://example.com/test/b/>
 *            ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
 *    $res = $sp_readonly->query($q, 'raw');
 *    $err = $sp_readonly->getErrors();
 *    if ($err) {
 *        print_r($err);
 *        throw new Exception(print_r($err,true));
 *    }
 *    var_dump($res);
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
 *     $sp_write = new Endpoint($MyEndPointSparql,$MyCode,$MyGraph);
 *     echo "\nInsert :";
 *     $q = "
 *             PREFIX a: <http://example.com/test/a/>
 *             PREFIX b: <http://example.com/test/b/>
 *             INSERT DATA {
 *                 GRAPH <".$MyGraph."> {
 *                 a:A b:Name \"Test1\" .
 *                 a:A b:Name \"Test2\" .
 *                 a:A b:Name \"Test3\" .
 *             }}";
 *     $res = $sp_write->query($q,'raw');
 *     $err = $sp_write->getErrors();
 *     if ($err) {
 *         print_r($err);
 *         throw new Exception(print_r($err,true));
 *     }
 *     var_dump($res);
 * ```
 *  
 * Example : send a query Delete
 * ```php
 *     $sp_write = new Endpoint($MyEndPointSparql,$MyCode,$MyGraph);
 * 
 *     echo "\nDelete :";
 *     $q = "
 *             PREFIX a: <http://example.com/test/a/>
 *             PREFIX b: <http://example.com/test/b/>
 *             DELETE DATA {
 *                 GRAPH <".$MyGraph."> {
 *                 a:A b:Name \"Test2\" .
 *             }}";
 *     
 *     $res = $sp_write->query($q,'raw');
 *     $err = $sp_write->getErrors();
 *     if ($err) {
 *         print_r($err);
 *         throw new Exception(print_r($err,true));
 *     }
 *     var_dump($res);
 * ```
 *  
 *  You can change the format of the response with the function
 *  QueryRead and QueryUpdate.
 */
class Endpoint extends Base {
	/**
	 * Root of the URL Endpoint
	 * @access private
	 * @var string
	 */	 
	private $_endpoint_root;
	
	/**
	 * URL of Endpoint to read
	 * @access private
	 * @var string
	 */
	private $_endpoint;
		
	/**
	 * URL  sparql to write
	 * @access private
	 * @var string
	 */
	private $_endpoint_write;
	
	/**
	 * in the constructor set debug to true in order to get usefull output
	 * @access private
	 * @var bool
	 */
	private $_debug;
	
	/**
	 * in the constructor set the right to write or not in the store
	 * @access private
	 * @var string
	 */
	private $_readOnly;
	
	/**
	 * in the constructor set the proxy_host if necessary
	 * @access private
	 * @var string
	 */
	private $_proxy_host;
	
	/**
	 * in the constructor set the proxy_port if necessary
	 * @access private
	 * @var int
	 */
	private $_proxy_port;
	
	/**
	 * Parser of XML result
	 * @access private
	 * @var ParserSparqlResult
	 */
	private $_parserSparqlResult;
	
	/**
	 * Name of parameter HTTP to send a query SPARQL to read data.
	 * @access private
	 * @var string
	 */
	private $_nameParameterQueryRead;
	
	/**
	 * Name of parameter HTTP to send a query SPARQL to write data.
	 * @access private
	 * @var string
	 */
	private $_nameParameterQueryWrite;
	
	/**
	 * Method HTTP to send a query SPARQL to read data.
	 * @access private
	 * @var string
	 */
	private $_MethodHTTPRead;
	private $_MethodHTTPWrite;
	

	private $_login;
	private $_password;
	

	/**
	 * Constructor of Graph
	 * @param string $endpoint : url of endpoint, example : http://lod.bordercloud.com/sparql
	 * @param boolean $readOnly : true by default, if you allow the function query to write in the database
	 * @param boolean $debug : false by default, set debug to true in order to get usefull output
	 * @param string $proxy_host : null by default, IP of your proxy
	 * @param string $proxy_port : null by default, port of your proxy
	 * @access public
	 */
	public function __construct($endpoint,
								$readOnly = true,
								$debug = false,
								$proxy_host = null,
								$proxy_port = null)
	{				
		parent::__construct();
		
		if($readOnly){
			$this->_endpoint = $endpoint;
		}else{
			if (preg_match("|/sparql/?$|i", $endpoint)) {
				$this->_endpoint = $endpoint;
				$this->_endpoint_root = preg_replace("|^(.*/)sparql/?$|i", "$1", $endpoint);
			} else {
				$this->_endpoint_root = $endpoint;
				$this->_endpoint = 	$this->_endpoint_root."sparql/";
			}
		}
	
		$this->_debug = $debug;
		$this->_endpoint_write = $this->_endpoint_root."update/"; 
		$this->_readOnly = $readOnly;
		
		$this->_proxy_host = $proxy_host;
		$this->_proxy_port = $proxy_port;		
		
		if($this->_proxy_host != null && $this->_proxy_port != null){
			$this->_config = array(
				/* remote endpoint */
			  'remote_store_endpoint' => $this->_endpoint,
				  /* network */
			  'proxy_host' => $this->_proxy_host,
			  'proxy_port' => $this->_proxy_port,			
			);
		}else{
			$this->_config = array(
			/* remote endpoint */
			  'remote_store_endpoint' => $this->_endpoint,
			);			
		}
		
		// init parameter in the standard
		$this->_nameParameterQueryRead = "query";
		$this->_nameParameterQueryWrite = "update";		

		//init parser
 		$this->_parserSparqlResult = new ParserSparqlResult();
 		
 		//FIX for Wikidata
 		if( $endpoint == "https://query.wikidata.org/sparql"){
 		   $this->_MethodHTTPRead= "GET";
 		}else{
 		   $this->_MethodHTTPRead= "POST"; // by default
 		}
 		
	}
	
	//FIX for WIKIDATA
	/**
	 * Set the method HTTP to read
	 * @param string $method : HTTP method (GET or POST) for reading data (by default is POST)
	 * @access public
	 */
	public function setMethodHTTPRead($method) {
		$this->_MethodHTTPRead = $method;
	}
        
	/**
	 * Set the method HTTP to write
	 * @param string $method : HTTP method (GET or POST) for writing data (by default is POST)
	 * @access public
	 */
	public function setMethodHTTPWrite($method) {
		$this->_MethodHTTPWrite = $method;
	}
	
	/**
	 * Set the url to read
	 * @param string $url : endpoint's url to read
	 * @access public
	 */
	public function setEndpointQuery($url) {
		$this->_endpoint = $url;
	}
	
	/**
	 * Get the url to read
	 * @return string $url : endpoint's url to read
	 * @access public
	 */
	public function getEndpointQuery() {
		return $this->_endpoint;
	}
	
	/**
	 * Set the url to write
	 * @param string $url : endpoint's url to write
	 * @access public
	 */
	public function setEndpointUpdate($url) {
		$this->_endpoint_write = $url;
	}
		
	/**
	 * Get the url to write
	 * @return string $url : endpoint's url to write
	 * @access public
	 */
	public function getEndpointUpdate() {
		return $this->_endpoint_write;
	}
	
	/**
	 * Set the parameter in the query to write
	 * @param string $name : name of parameter
	 * @access public
	 */
	public function setNameParameterQueryWrite($name) {
		$this->_nameParameterQueryWrite = $name;
	}
	
	/**
	 * Get the parameter in the query to write
	 * @return string $name : name of parameter
	 * @access public
	 */
	public function getNameParameterQueryWrite() {
		return $this->_nameparameterQueryWrite;
	}
	
	/**
	 * Set the parameter in the query to read
	 * @param string $name : name of parameter
	 * @access public
	 */
	public function setNameParameterQueryRead($name) {
		$this->_nameParameterQueryRead = $name;
	}
	
	/**
	 * Get the parameter in the query to read
	 * @return string $name : name of parameter
	 * @access public
	 */
	public function getNameParameterQueryRead() {
		return $this->_nameparameterQueryRead;
	}

	/**
	 * Set the server login
	 * @param string $login : server login
	 * @access public
	 */
	public function setLogin($login) {
		$this->_login = $login;
	}
	
	/**
	 * Get the server login
	 * @return string $login : server login
	 * @access public
	 */
	public function getLogin() {
		return $this->_login;
	}
	
	/**
	 * Set the server password
	 * @param string $password : server password
	 * @access public
	 */
	public function setPassword($password) {
		$this->_password = $password;
	}
	
	/**
	 * Get the server login
	 * @return string $password : server password
	 * @access public
	 */
	public function getPassword() {
		return $this->_password;
	}
	
	/**
	 * Check if the server is up.
	 * @return boolean true if the triplestore is up.
	 * @access public
	 */
	public function check() {
		return Net::ping($this->_endpoint) != -1;
	}
	
	/**
	 * This function parse a SPARQL query, send the query and parse the SPARQL result in a array. 
	 * You can custom the result with the parameter $result_format : 
	 * <ul>
	 * <li>rows to return array of results
	 * <li>row to return array of first result
	 * <li>raw to return boolean for request ask, insert and delete
	 * </ul>
	 * @param string $q : Query SPARQL 
	 * @param string $result_format : Optional,  rows, row or raw
	 * @return array|boolean in function of parameter $result_format
	 * @access public
	 */
	public function query($q, $result_format = 'rows') {	
            $t1 = Endpoint::mtime();
            $result = null;
            switch($result_format)
            {
                case "json" :			
                    $response = $this->queryRead($q,"application/sparql-results+json");
                    $result = json_decode($response);
                    break;
                case "row" :
                case "raw" :
                default: //rows	
                    $response ="";
                    if(preg_match("/(INSERT|DELETE|CLEAR|LOAD)/i",$q)){
                        $response = $this->queryUpdate($q);
                    }else{
                        $response = $this->queryRead($q);
                    }
                    $parser = $this->_parserSparqlResult->getParser();
                    $success = xml_parse($parser,$response, true);		
                    $result = $this->_parserSparqlResult->getResult();
                    if(!$success ){ //if(! array_key_exists("result",$result)){
                        $message = "Error parsing XML result:" 
                                .xml_error_string(xml_get_error_code($parser))
                                .' Response : '.$response."\n";
                        $error = $this->errorLog($q,null, $this->_endpoint,200,$message);
                        $this->addError($error);
                        return false;
                    }
            }
            $result['query_time'] =   Endpoint::mtime() - $t1 ;
            switch($result_format)
            {
                case "row" :
                        return $result["result"]["rows"][0];
                case "raw" :
                    return $result["result"]["rows"][0][$result["result"]["variables"][0]];
                case "json" :
                default: //rows				
                        return $result;
            }
	}
/*
	public function queryConstruct($q) {	
		$t1 = Endpoint::mtime();
		$result = null;
					
		$response = $this->queryRead($q,"text/turtle");
		return $response;
		$result = ParserTurtle::turtle_to_array($response,"");
				
		$result['query_time'] =   Endpoint::mtime() - $t1 ;
					
				return $result;
	}
	*/
	
	/**
	* Send a request SPARQL of type select or ask to endpoint directly and output the response
	* of server. If you want parse the result of this function, it's better and simpler
	* to use the function query().
	*
	* @param string $query : Query Sparql
	* @param string $typeOutput by default "application/sparql-results+xml",
	* @return string response of server or false if error (to do getErrors())
	* @access public
	*/
	public function queryRead($query,$typeOutput="application/sparql-results+xml" ) {
            $client = $this->initCurl();
            $sUri    = $this->_endpoint;	
            $response ="";

            if($typeOutput == null){
                $data = array($this->_nameParameterQueryRead =>   $query);
                if($this->_MethodHTTPRead == "POST"){
                    $response = $client->send_post_data($sUri,$data);
                }else{
                    $response = $client->fetch_url($sUri,$data);//fix for wikidata
                }
            }else{
                $data = array($this->_nameParameterQueryRead =>   $query,
                "output" => ConversionMimetype::getShortnameOfMimetype($typeOutput), //possible fix for 4store/fuseki..
                "Accept" => $typeOutput); //fix for sesame
                //print_r($data);
                if($this->_MethodHTTPRead == "POST"){
                   $response = $client->send_post_data($sUri,$data,array('Accept: '.$typeOutput));
                }else{
                   $response = $client->fetch_url($sUri,$data);//fix for wikidata
                }
            }		

            $code = $client->get_http_response_code();

            $this->debugLog($query,$sUri,$code,$response);

            if(($code < 200 || $code >= 300) )
            {
                $error = $this->errorLog($query,$data,$sUri,$code,$response.
                        $client->get_error_msg() );
                $this->addError($error);
                return false;
            }
            return $response;
	}

	/**
	 * Send a request SPARQL of type insert data or delete data to endpoint directly.
	 * <ul>
	 * <li>Example insert : PREFIX ex: <http://example.com/> INSERT DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
	 * <li>Example delete : PREFIX ex: <http://example.com/> DELETE DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
	 * </ul>
	 * @param string $query : Query Sparql of type insert data or delete data only
	 * @param string $typeOutput by default "application/sparql-results+xml",
	 * @return boolean true if it did or false if error (to do getErrors())
	 * @access public
	 */
	public function queryUpdate($query,$typeOutput="application/sparql-results+xml") { 
            if($this->_readOnly){
                 $message = "Sorry, you have not the right to update the database.\n";
                 $error = $this->errorLog('',null, $this->_endpoint,0,$message);
                 $this->addError($error);
                 return false;
            }
            
            $client = $this->initCurl();
            $sUri  =   $this->_endpoint_write;	
            $response ="";
            
            if($typeOutput == null){
                $data = array($this->_nameParameterQueryWrite =>   $query);
                if($this->_MethodHTTPWrite == "POST"){
                    $response = $client->send_post_data($sUri,$data);
                }else{
                    $response = $client->fetch_url($sUri,$data);//fix for wikidata
                }
            }else{
                $data = array($this->_nameParameterQueryWrite =>   $query,
                //"output" => ConversionMimetype::getShortnameOfMimetype($typeOutput), //possible fix for 4store/fuseki..
                "Accept" => $typeOutput); //fix for sesame
                //print_r($data);
                if($this->_MethodHTTPWrite == "POST"){
                   $response = $client->send_post_data($sUri,$data,array('Accept: '.$typeOutput));
                }else{
                   $response = $client->fetch_url($sUri,$data);//fix for wikidata
                }
            }	

            $code = $client->get_http_response_code();

            $this->debugLog($query,$sUri,$code,$response);

            if($code < 200 || $code >= 300 ){
                    $error = $this->errorLog($query,$data,$sUri,$code,$response);
                    $this->addError($error);
                    return false;
            }
            //echo "OK".$response;
            return $response;
        }
		
	/************************************************************************/
	//PRIVATE Function
	
	static function mtime(){
		list($msec, $sec) = explode(" ", microtime());
		return ((float)$msec + (float)$sec);
	}
	
	/**
	 * write error for human
	 * @param string $query
	 * @param string $endPoint
	 * @param number $httpcode
	 * @param string $response
	 * @access private
	 */
	private function errorLog($query,$data,$endPoint,$httpcode=0,$response=''){
		$error = 	"Error query  : " .$query."\n" .
					"Error endpoint: " .$endPoint."\n" .
					"Error http_response_code: " .$httpcode."\n" .
					"Error message: " .$response."\n";			
					"Error data: " .print_r($data,true)."\n";			
		if($this->_debug){
			echo '=========================>>>>>>'.$error ;
		}else{
			error_log($error);
		}
		return $error;
	}

	/**
	 * Print infos
	 * @param unknown_type $query
	 * @param unknown_type $endPoint
	 * @param unknown_type $httpcode
	 * @param unknown_type $response
	 * @access private
	 */
	private function debugLog($query,$endPoint,$httpcode='',$response=''){
		if($this->_debug)
		{
			$error = 	"\n#######################\n".
						"query				: " .$query."\n" .
                        "endpoint			: " .$endPoint."\n" .
                        "http_response_code	: " .$httpcode."\n" .
                        "message			: " .$response.
                        "\n#######################\n";

			echo $error ;
		}
	}
	
	/**
	 * Init an object Curl in function of proxy.
	 * @return an object of type Curl
	 * @access private
	 */
	private function initCurl(){
		$objCurl = new Curl();//$this->_debug
		if($this->_proxy_host != null && $this->_proxy_port != null){
			$objCurl->set_proxy($this->_proxy_host.":".$this->_proxy_port);	
		}
		if($this->_login != null && $this->_password != null){
			$objCurl->set_credentials($this->_login,$this->_password);
		}
		return $objCurl;
	}
}
