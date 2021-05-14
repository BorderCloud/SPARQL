<?php
declare(strict_types=1);

namespace BorderCloud\SPARQL\Tests;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BorderCloud\\SPARQL\\', __DIR__ . '/../src');

use BorderCloud\SPARQL\ParserSparqlResult;
use Exception;
use PHPUnit\Framework\TestCase;

final class ParserSparqlResultTest extends TestCase
{
    public function testResetParserAsk()
    {
        //check write
        $response1 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head>
  </head>
  <boolean>true</boolean>
</sparql>
EOT;
        $response2 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head>
  </head>
  <boolean>true</boolean>
</sparql>
EOT;
        $parserSparqlResult = new ParserSparqlResult();

        $parser1 = $parserSparqlResult->getParser();
        $parser2 = $parserSparqlResult->getParser();

        $success = xml_parse($parser1, $response1, true);
        //$this->assertTrue($success);
        $result1 = $parserSparqlResult->getResult();

        $success = xml_parse($parser2, $response2, true);
        //$this->assertTrue($success);
        $result2 = $parserSparqlResult->getResult();

        //print_r( $result1);
        //print_r( $result2);

        $sort = false;
        $distinct = false;
        $tabDiff = ParserSparqlResult::compare($result1,$result2,$sort,$distinct);
        //print_r( $tabDiff);
        $this->assertTrue(count($tabDiff)==0);
    }

    public function testResetParserAsk2()
    {
        //check write
        $response1 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head>
  </head>
  <boolean>true</boolean>
</sparql>
EOT;
        $response2 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head/>
  <boolean>true</boolean>
</sparql>
EOT;
        $parserSparqlResult = new ParserSparqlResult();

        $parser1 = $parserSparqlResult->getParser();
        $parser2 = $parserSparqlResult->getParser();

        $success = xml_parse($parser1, $response1, true);
        //$this->assertTrue($success);
        $result1 = $parserSparqlResult->getResult();

        $success = xml_parse($parser2, $response2, true);
        //$this->assertTrue($success);
        $result2 = $parserSparqlResult->getResult();

        //print_r( $result1);
        //print_r( $result2);

        $sort = false;
        $distinct = false;
        $tabDiff = ParserSparqlResult::compare($result1,$result2,$sort,$distinct);
        //print_r( $tabDiff);
        $this->assertTrue(count($tabDiff)==0);
    }


    public function testResetParserAskDiff()
    {
        //check write
        $response1 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head>
  </head>
  <boolean>true</boolean>
</sparql>
EOT;
        $response2 = <<<EOT
<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#">
  <head/>
  <boolean>false</boolean>
</sparql>
EOT;
        $parserSparqlResult = new ParserSparqlResult();

        $parser1 = $parserSparqlResult->getParser();
        $parser2 = $parserSparqlResult->getParser();

        $success = xml_parse($parser1, $response1, true);
        //$this->assertTrue($success);
        $result1 = $parserSparqlResult->getResult();

        $success = xml_parse($parser2, $response2, true);
        //$this->assertTrue($success);
        $result2 = $parserSparqlResult->getResult();

        //print_r( $result1);
        //print_r( $result2);

        $sort = false;
        $distinct = false;
        $tabDiff = ParserSparqlResult::compare($result1,$result2,$sort,$distinct);
        //print_r( $tabDiff);
        $this->assertTrue(count($tabDiff)>0);
    }

}
