<?php
declare(strict_types=1);

namespace BorderCloud\SPARQL\Tests;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BorderCloud\\SPARQL\\', __DIR__ . '/../src');
$loader->addPsr4('BorderCloud\\SPARQL\\Parser\\', __DIR__ . '/../gen');

use BorderCloud\SPARQL\ParserSparql;
use Exception;
use PHPUnit\Framework\TestCase;

final class ParserSparqlTest extends TestCase
{
    public function testSelect1()
    {
        $q = "select *  where {?x ?y ?z.} LIMIT 5";
        $this->assertTrue(ParserSparql::isSelectQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testSelect2()
    {
        $q =  <<<EOT
PREFIX wd: <http://www.wikidata.org/entity/>
PREFIX wdt: <http://www.wikidata.org/prop/direct/>

# Get 10 paintings that have a link to RKDimages (P350)
# Use the formatter URL (P1630) to construct the links to RKDimages
#defaultView:ImageGrid
SELECT ?item ?image ?rkdurl  WHERE {
  wd:P350 wdt:P1630 ?formatterurl .
  ?item wdt:P31 wd:Q3305213 .
  ?item wdt:P18 ?image .
  ?item wdt:P350 ?rkdid .
  BIND(IRI(REPLACE(?rkdid, '^(.+)$', ?formatterurl)) AS ?rkdurl).
  } LIMIT 10
EOT;
        $this->assertTrue(ParserSparql::isSelectQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testAsk1()
    {
        $q =  <<<EOT
PREFIX a: <http://example.com/test/a/>
        PREFIX b: <http://example.com/test/b/>
        ask where { GRAPH <http://truc.fr/> {a:A b:Name \"Test3\" .}}
EOT;
        $this->assertTrue(ParserSparql::isAskQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testConstruct1()
    {
        $q =  <<<EOT
PREFIX foaf:   <http://xmlns.com/foaf/0.1/>
PREFIX org:    <http://example.com/ns#>

CONSTRUCT { ?x foaf:name ?name }
WHERE  { ?x org:employeeName ?name }
EOT;
        $this->assertTrue(ParserSparql::isConstructQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testDescribe1()
    {
        $q =  <<<EOT
PREFIX foaf:   <http://xmlns.com/foaf/0.1/>
DESCRIBE ?x
WHERE    { ?x foaf:mbox <mailto:alice@org> }
EOT;
        $this->assertTrue(ParserSparql::isDescribeQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testSelectWithLoad()
    {
        $q =  <<<EOT
select * where { ?s rdfs:label ?l . filter(contains(str(?l), "LOAD")) . }
EOT;
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertTrue(ParserSparql::isReadQuery($q));
        $this->assertFalse(ParserSparql::isUpdateQuery($q));
    }

    public function testINSERT1()
    {
        $q =  <<<EOT
PREFIX dc: <http://purl.org/dc/elements/1.1/>
INSERT DATA
{
  <http://example/book1> dc:title "A new book" ;
                         dc:creator "A.N.Other" .
}
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testINSERT2()
    {
        $q =  <<<EOT
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX ns: <http://example.org/ns#>
INSERT DATA
{ GRAPH <http://example/bookStore> { <http://example/book1>  ns:price  42 } }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testDELETE1()
    {
        $q =  <<<EOT
PREFIX dc: <http://purl.org/dc/elements/1.1/>

DELETE DATA
{
  <http://example/book2> dc:title "David Copperfield" ;
                         dc:creator "Edmund Wells" .
}
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testDELETE2()
    {
        $q =  <<<EOT
PREFIX dc: <http://purl.org/dc/elements/1.1/>
DELETE DATA
{ GRAPH <http://example/bookStore> { <http://example/book1>  dc:title  "Fundamentals of Compiler Desing" } } ;

PREFIX dc: <http://purl.org/dc/elements/1.1/>
INSERT DATA
{ GRAPH <http://example/bookStore> { <http://example/book1>  dc:title  "Fundamentals of Compiler Design" } }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }

    public function testWITH()
    {
        $q =  <<<EOT
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>

WITH <http://example/addresses>
DELETE { ?person foaf:givenName 'Bill' }
INSERT { ?person foaf:givenName 'William' }
WHERE
  { ?person foaf:givenName 'Bill'
  }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testDELETEWHERE()
    {
        $q =  <<<EOT
PREFIX dc:  <http://purl.org/dc/elements/1.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

DELETE
 { ?book ?p ?v }
WHERE
 { ?book dc:date ?date .
   FILTER ( ?date > "1970-01-01T00:00:00-02:00"^^xsd:dateTime )
   ?book ?p ?v
 }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testWITHDELETEWHERE()
    {
        $q =  <<<EOT
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>

WITH <http://example/addresses>
DELETE { ?person ?property ?value }
WHERE { ?person ?property ?value ; foaf:givenName 'Fred' }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testINSERTWHERE()
    {
        $q =  <<<EOT
PREFIX dc:  <http://purl.org/dc/elements/1.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

INSERT
  { GRAPH <http://example/bookStore2> { ?book ?p ?v } }
WHERE
  { GRAPH  <http://example/bookStore>
       { ?book dc:date ?date .
         FILTER ( ?date > "1970-01-01T00:00:00-02:00"^^xsd:dateTime )
         ?book ?p ?v
  } }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testINSERTWHERE2()
    {
        $q =  <<<EOT
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>

INSERT
  { GRAPH <http://example/addresses>
    {
      ?person  foaf:name  ?name .
      ?person  foaf:mbox  ?email
    } }
WHERE
  { GRAPH  <http://example/people>
    {
      ?person  foaf:name  ?name .
      OPTIONAL { ?person  foaf:mbox  ?email }
    } }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testINSERTDELETEWHERE()
    {
        $q =  <<<EOT
PREFIX dc:  <http://purl.org/dc/elements/1.1/>
PREFIX dcmitype: <http://purl.org/dc/dcmitype/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

INSERT
  { GRAPH <http://example/bookStore2> { ?book ?p ?v } }
WHERE
  { GRAPH  <http://example/bookStore>
     { ?book dc:date ?date .
       FILTER ( ?date < "2000-01-01T00:00:00-02:00"^^xsd:dateTime )
       ?book ?p ?v
     }
  } ;

WITH <http://example/bookStore>
DELETE
 { ?book ?p ?v }
WHERE
 { ?book dc:date ?date ;
         dc:type dcmitype:PhysicalObject .
   FILTER ( ?date < "2000-01-01T00:00:00-02:00"^^xsd:dateTime )
   ?book ?p ?v
 }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testDELETEWHERE2()
    {
        $q =  <<<EOT
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>

DELETE WHERE { ?person foaf:givenName 'Fred';
                       ?property      ?value }
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }

    public function testDELETEWHERE3()
    {
        $q =  <<<EOT
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>

DELETE WHERE {
  GRAPH <http://example.com/names> {
    ?person foaf:givenName 'Fred' ;
            ?property1 ?value1
  }
  GRAPH <http://example.com/addresses> {
    ?person ?property2 ?value2
  }
}
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testCLEAR()
    {
        $q =  <<<EOT
# Remove all triples from a specified graph.
CLEAR GRAPH <http://example.com/names>
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testDROP()
    {
        $q =  <<<EOT
DROP GRAPH <http://example.com/names>
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testCOPY()
    {
        $q =  <<<EOT
COPY GRAPH <http://example.com/names> TO GRAPH <http://example.com/names2>
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testMOVE()
    {
        $q =  <<<EOT
MOVE GRAPH <http://example.com/names> TO GRAPH <http://example.com/names2>
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }
    public function testADD()
    {
        $q =  <<<EOT
ADD GRAPH <http://example.com/names> TO GRAPH <http://example.com/names2>
EOT;
        $this->assertTrue(ParserSparql::isUpdateQuery($q));
        $this->assertFalse(ParserSparql::isReadQuery($q));
    }

}
