<?php
declare(strict_types=1);

namespace BorderCloud\SPARQL\Tests;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('BorderCloud\\SPARQL\\', __DIR__ . '/../src');

use BorderCloud\SPARQL\ParserCSV;
use Exception;
use PHPUnit\Framework\TestCase;

final class ParserCsvTest extends TestCase
{
    public function testParserTsv()
    {
        //check write
        $sample = file_get_contents (__DIR__ . "/csvtsv02.tsv");
        $expected = [
            0 => [
                "?s" => "<http://example.org/s1>",
                    "?p" => "<http://example.org/p1>",
                    "?o" => "<http://example.org/s2>",
                    "?p2" => "<http://example.org/p2>",
                    "?o2" => "foo"
                 ],
            1 => [
                "?s" => "<http://example.org/s2>",
                    "?p" => "<http://example.org/p2>",
                    "?o" => "foo",
                    "?p2" => null,
                    "?o2" => null
                 ],
            2 => [
                "?s" => "<http://example.org/s3>",
                "?p" => "<http://example.org/p3>",
                    "?o" => "bar",
                    "?p2" => null,
                    "?o2" => null
                 ],
            3 => [
                "?s" => "<http://example.org/s4>",
                "?p" => "<http://example.org/p4>",
                    "?o" => 4,
                    "?p2" => null,
                    "?o2" => null
                 ],
            4 => [
                "?s" => "<http://example.org/s5>",
                "?p" => "<http://example.org/p5>",
                "?o" => 5.5,
                "?p2" => null,
                "?o2" => null
            ],
            5 => [
                "?s" => "<http://example.org/s6>",
                "?p" => "<http://example.org/p6>",
                "?o" => "_:b0",
                "?p2" => null,
                "?o2" => null
            ],
            6 => [
                "?s" => "<http://example.org/s7>",
                "?p" => "<http://example.org/p7>",
                "?o" => "10",
                "?p2" => null,
                "?o2" => null
            ]
        ];
        //"text/tab-separated-values; charset=utf-8":
		$tabResultDataset = ParserCSV::csvToArray($sample,"\t");
        $sort = true;
        $distinct = false;
        $tabDiff = ParserCSV::compare($expected,$tabResultDataset,$sort,$distinct);
        print_r($tabDiff);
        print_r($tabResultDataset);
        $this->assertTrue(count($tabDiff)==0,print_r($tabDiff,true));
    }

}
