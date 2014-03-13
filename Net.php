<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class Net {
	
	/**
	 * Ping a address
	 * @return int if -1 the server is down
	 * @access public
    */
	static function ping($address){
		$urlInfo = parse_url($address);
		$domain = $urlInfo['host'];
		$port = Net::getUrlPort( $urlInfo );
	    $starttime = microtime(true);
	    $file      = @fsockopen ($domain,$port, $errno, $errstr, 10);
	    $stoptime  = microtime(true);
	    $status    = 0;
	
	    if (!$file) $status = -1;  // Site is down
	    else {
	        fclose($file);
	        $status = ($stoptime - $starttime) * 1000;
	        $status = floor($status);
	    }
	    return $status;
	}
	
	private static function getUrlPort( $urlInfo )
	{
	    if( isset($urlInfo['port']) ) {
	        $port = $urlInfo['port'];
	    } else { // no port specified; get default port
	        if (isset($urlInfo['scheme']) ) {
	            switch( $urlInfo['scheme'] ) {
	                case 'http':
	                    $port = 80; // default for http
	                    break;
	                case 'https':
	                    $port = 443; // default for https
	                    break;
	                case 'ftp':
	                    $port = 21; // default for ftp
	                    break;
	                case 'ftps':
	                    $port = 990; // default for ftps
	                    break;
	                default:
	                    $port = 0; // error; unsupported scheme
	                    break;
	            }
	        } else {
	            $port = 0; // error; unknown scheme
	        }
	    }
	    return $port;
	} 
}

