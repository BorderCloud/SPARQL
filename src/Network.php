<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class Network gives several tools : ping, port,...
 *
 * @package BorderCloud\SPARQL
 */
class Network
{
    /**
     * Ping a service HTTP
     *
     * @param string $address URL
     * @return float if -1 the server is down
     */
    static function ping($address)
    {
        $urlInfo = parse_url($address);
        $domain = $urlInfo['host'];
        $port = Network::getUrlPort($urlInfo);
        $startTime = microtime(true);
        $file = @fsockopen($domain, $port, $errorNumber, $errorStr, 10);
        $stopTime = microtime(true);
        $status = 0.0;

        if (! $file) {
            $status = - 1.0; // Site is down
        } else {
            fclose($file);
            $status = ($stopTime - $startTime) * 1000;
            $status = floor($status);
        }
        return $status;
    }

    /**
     * Get the port writes in the protocol of URL
     *
     * @param $urlInfo
     * @return int
     */
    private static function getUrlPort($urlInfo)
    {
        if (isset($urlInfo['port'])) {
            $port = $urlInfo['port'];
        } else { // no port specified; get default port
            if (isset($urlInfo['scheme'])) {
                switch ($urlInfo['scheme']) {
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
