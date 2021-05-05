<?php
declare(strict_types=1);

namespace BorderCloud\SPARQL\Tests;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BorderCloud\\SPARQL\\', __DIR__ . '/../src');

use BorderCloud\SPARQL\SparqlClient;
use Exception;
use PHPUnit\Framework\TestCase;

final class SparqlClientTest extends TestCase
{
    /**
     * @var SparqlClient
     */
    private $_client;


    public function setUp():void
    {
        $endpoint = "https://query.wikidata.org/sparql";
        $this->_client = new SparqlClient();
        $this->_client->setEndpointRead($endpoint);
        //$_client->setMethodHTTPRead("GET");
    }

    //************************ EXAMPLES in the folder example

    public function testWikidata()
    {
        $q = "select *  where {?x ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }

        foreach ($rows["result"]["variables"] as $variable) {
            printf("%-20.20s", $variable);
            echo '|';
        }
        echo "\n";

        foreach ($rows["result"]["rows"] as $row) {
            foreach ($rows["result"]["variables"] as $variable) {
                printf("%-20.20s", $row[$variable]);
                echo '|';
            }
            echo "\n";
        }
        //print_r($rows);
        $this->assertCount(2, $rows);
        $this->assertCount(2, $rows["result"]);
        $this->assertCount(3, $rows["result"]["variables"]);
        $this->assertCount(5, $rows["result"]["rows"]);
    }

    public function testbnf()
    {
        $endpoint = "http://data.bnf.fr/sparql";
        $sc_readonly = new SparqlClient();
        $sc_readonly->setEndpointRead($endpoint);
        $sc_readonly->setMethodHTTPRead("GET");
        $q = "select *  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc_readonly->query($q, 'rows');
        $err = $sc_readonly->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }

        $this->assertCount(5, $rows["result"]["rows"]);
    }

// ERROR 405...  access is closed or an error in their config ?
//    public function testDBpedia()
//    {
//        $endpoint = "http://dbpedia.org/sparql";
//        $sc_readonly = new SparqlClient();
//        $sc_readonly->setEndpointRead($endpoint);
//        $q = "select *  where {?x ?y ?z.} LIMIT 5";
//        $rows = $sc_readonly->query($q, 'rows');
//        $err = $sc_readonly->getErrors();
//        if ($err) {
//            //print_r($err);
//            throw new Exception(print_r($err, true));
//        }
//
//        $this->assertCount(5, $rows["result"]["rows"]);
//    }

    public function testSetterAndGetter()
    {
        $sc = new SparqlClient();
        $sc->setMethodHTTPRead("GET");
        $this->assertEquals($sc->getMethodHTTPRead(),"GET");

        $sc->setMethodHTTPWrite("GET");
        $this->assertEquals($sc->getMethodHTTPWrite(),"GET");

        $sc->setEndpointRead("https://query.truc.org/sparql");
        $this->assertEquals($sc->getEndpointRead(),"https://query.truc.org/sparql");

        $sc->setEndpointWrite("https://query.truc.org/sparql_auth");
        $this->assertEquals($sc->getEndpointWrite(),"https://query.truc.org/sparql_auth");

        $sc->setLogin("toto");
        $this->assertEquals($sc->getLogin(),"toto");

        $sc->setPassword("pass");
        $this->assertEquals($sc->getPassword(),"pass");

        $sc->setNameParameterQueryRead("paramRead");
        $this->assertEquals($sc->getNameParameterQueryRead(),"paramRead");

        $sc->setNameParameterQueryWrite("paramWrite");
        $this->assertEquals($sc->getNameParameterQueryWrite(),"paramWrite");

        $sc->setProxyHost("http://example.com");
        $this->assertEquals($sc->getProxyHost(),"http://example.com");

        $sc->setProxyPort(1234);
        $this->assertEquals($sc->getProxyPort(),1234);
    }

    public function testError()
    {
        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);
        //error in the query
        $q = "s *  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q);
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"Lexical error at line 1, column 2.  Encountered: \" \" (32), after : \"s\"");

        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient(true);
        $sc->setEndpointRead($endpoint);
        //error in the query
        $q = "s *  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q, 'rows');
        $err = $sc->getErrors();
        $isError = false;
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"Lexical error at line 1, column 2.  Encountered: \" \" (32), after : \"s\"");

        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);
        //error in the query
        $q = "s count(*)  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q, 'row');
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"Lexical error at line 1, column 2.  Encountered: \" \" (32), after : \"s\"");

        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);
        //error in the query
        $q = "s count(*)  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q, 'rows');
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"Lexical error at line 1, column 2.  Encountered: \" \" (32), after : \"s\"");

        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);
        //error in the query
        $q = "s count(*)  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q, 'json');
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"Lexical error at line 1, column 2.  Encountered: \" \" (32), after : \"s\"");
    }

    public function testErrorTimeout()
    {
        $endpoint = "https://10.255.255.1/sparql"; //fake...
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);
        $sc->setEndpointWrite($endpoint);
        //error in the query
        $q = "select ...";
        $rows = $sc->query($q,'rows',1);
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertStringContainsString("Connection timed out after",$errorMessage);

        $q = "insert ...";
        $rows = $sc->query($q,'raw',1);
        $err = $sc->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $sc->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertStringContainsString("Connection timed out after",$errorMessage);
    }
}
