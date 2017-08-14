<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: karima
 * Date: 13/08/17
 * Time: 14:19
 */
namespace BorderCloud\SPARQL\Tests;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BorderCloud\\SPARQL\\', __DIR__ . '/../src');

use BorderCloud\SPARQL\SparqlClient;
use Exception;
use PHPUnit\Framework\TestCase;

final class virtuosoTest extends TestCase
{
    /**
     * @var SparqlClient
     */
    private $_client;
    private $_endpoint = "http://database-test:8890/sparql-auth/";
    private $_login = "dba";
    private $_password = "dba";

    public function setUp()
    {
        $this->_client = new SparqlClient(false);
        $this->_client->setEndpointRead($this->_endpoint);
        $this->_client->setEndpointWrite($this->_endpoint);
        $this->_client->setLogin($this->_login);
        $this->_client->setPassword($this->_password);

        //check delete
        $q =  <<<EOT
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
DELETE DATA {
GRAPH <http://truc.fr/> {
a:A b:Name "Test1" .
a:A b:Name "Test2" .
a:A b:Name "Test3" .
}}
EOT;

        $res = $this->_client->query($q,'raw');
        $err = $this->_client->getErrors();
        if ($err) {
            print_r($err);
            throw new Exception(print_r($err,true));
        }
        echo "Delete :";
        var_dump($res);
    }

    public function testVirtuosoRead()
    {
        $q = "select *  where {?x ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }
        $this->assertCount(5, $rows["result"]["rows"]);
    }

    public function testVirtuosoAsk()
    {
        //read if empty
        $q = "PREFIX a: <http://example.com/test/a/>
        select *  where {a:A ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }
        $this->assertCount(0, $rows["result"]["rows"]);

        //check ask false
        $q = "PREFIX a: <http://example.com/test/a/>
        PREFIX b: <http://example.com/test/b/>
        ask where { GRAPH <http://truc.fr/> {a:A b:Name \"Test3\" .}} ";
        $res = $this->_client->query($q);
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err,true));
        }
        $this->assertFalse($res);

        //check write
        $q = <<<EOT
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
INSERT DATA {
GRAPH <http://truc.fr/> {
a:A b:Name "Test1" .
a:A b:Name "Test2" .
a:A b:Name "Test3" .
}}
EOT;

        $res = $this->_client->query($q,'raw');
        $err = $this->_client->getErrors();
        if ($err) {
            print_r($err);
            throw new Exception(print_r($err,true));
        }
        echo "Write :";
        var_dump($res);

        // check if write is OK
        $q = "PREFIX a: <http://example.com/test/a/>
        select *  where {a:A ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }
        $this->assertCount(3, $rows["result"]["rows"]);

        //check ask is true
        $q = "PREFIX a: <http://example.com/test/a/>
        PREFIX b: <http://example.com/test/b/>
        ask where { GRAPH <http://truc.fr/> {a:A b:Name \"Test3\" .}} ";
        $res = $this->_client->query($q, 'raw');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err,true));
        }
        $this->assertTrue($res);

        //check delete
        $q =  <<<EOT
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
DELETE DATA {
GRAPH <http://truc.fr/> {
a:A b:Name "Test1" .
a:A b:Name "Test2" .
a:A b:Name "Test3" .
}}
EOT;

        $res = $this->_client->query($q,'raw');
        $err = $this->_client->getErrors();
        if ($err) {
            print_r($err);
            throw new Exception(print_r($err,true));
        }
        echo "Delete :";
        var_dump($res);

        // check if write is OK
        $q = "PREFIX a: <http://example.com/test/a/>
        select *  where {a:A ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        if ($err) {
            //print_r($err);
            throw new Exception(print_r($err, true));
        }
        $this->assertCount(0, $rows["result"]["rows"]);
    }

    public function testErrorQuery()
    {
        //read if empty
        $q = "se *  where {?x ?y ?z.} LIMIT 5";
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $this->_client->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"line 3: syntax error at 'se' before '*'");

        $sc = new SparqlClient(true);
        $sc->setEndpointRead($this->_endpoint);
        $sc->setLogin($this->_login);
        $sc->setPassword("dba");
        //error in the query
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
        $this->assertEquals($errorMessage,"line 3: syntax error at 'se' before '*'");
    }


    public function testErrorUpdate()
    {
        //read if empty
        $q =  <<<EOT
PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/>
DELETE DATA {
GRAPH <http://truc.fr/> {
a:A b:Name "Test1" .
a:A b:Name "Test2" .
a:A b:Name "Test3" .

EOT;
        $rows = $this->_client->query($q, 'rows');
        $err = $this->_client->getErrors();
        $isError = false;
        $errorMessage = "";
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $this->_client->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"line 9: syntax error");


        $sc = new SparqlClient(true);
        $sc->setEndpointRead($this->_endpoint);
        $sc->setEndpointWrite($this->_endpoint);
        $sc->setLogin($this->_login);
        $sc->setPassword($this->_password);
        //error in the query
        $q = "s *  where {?x ?y ?z.} LIMIT 5";
        $rows = $sc->query($q, 'rows');
        $err = $sc->getErrors();
        $isError = false;
        if ($err) {
            $isError = true;
            //print_r($err);
            //throw new Exception(print_r($err, true));
            $errorMessage =  $this->_client->getLastError();
        }
        $this->assertTrue($isError);
        $this->assertEquals($errorMessage,"line 9: syntax error");
    }


}
