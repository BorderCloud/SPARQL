<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class ParserSparqlResult
 * @package BorderCloud\SPARQL
 */
class ParserSparqlResult extends Base
{

    /**
     * @var array
     */
    private $_result;

    /**
     * @var
     */
    private $_rowCurrent;

    /**
     * @var
     */
    private $_cellCurrent;

    /**
     * @var
     */
    private $_value;

    /**
     * ParserSparqlResult constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_result = array();
    }

    /**
     * @return resource
     */
    public function getParser()
    {
        $objectParser = xml_parser_create();
        xml_set_object($objectParser, $this);

        // Don't alter the case of the data
        xml_parser_set_option($objectParser, XML_OPTION_CASE_FOLDING, false);

        xml_set_element_handler($objectParser, "startElement", "endElement");
        xml_set_character_data_handler($objectParser, "contentHandler");
        return $objectParser;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * callback for the start of each element
     *
     * @param $parserObject
     * @param $elementname
     * @param $attribute
     */
    public function startElement($parserObject, $elementname, $attribute)
    {
        if ($elementname == "sparql") {
            // init a new response
            unset($this->_result['boolean']); // clean ASK response
            $this->_result['result'] = array();
        } else if ($elementname == "head") {
            $this->_result['result']['variables'] = array();
        } else if ($elementname == "variable") {
            $this->_result['result']['variables'][] = $attribute['name'];
        } else if ($elementname == "results") {
            $this->_rowCurrent = - 1;
            $this->_result['result']['rows'] = array();
        } else if ($elementname == "result") {
            $this->_rowCurrent ++;
            $this->_result['result']['rows'][] = array();
        } else if ($elementname == "binding") {
            $this->_value = "";
            $this->_cellCurrent = $attribute['name'];
        } else if ($this->_cellCurrent != null) {
            $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"] = $elementname;

            if (isset($attribute['xml:lang'])) {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " lang"] = $attribute['xml:lang'];
            }
            if (isset($attribute['datatype'])) {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " datatype"] = $attribute['datatype'];
            }
        } else if ($elementname == "boolean") {
            $this->_cellCurrent = "boolean" ;
        }
    }

    /**
     * callback for the end of each element
     *
     * @param $parserObject
     * @param $elementname
     */
    public function endElement($parserObject, $elementname)
    {
        if ($elementname == "binding") {

            if (strlen(trim($this->_value)) == 0) {
                return;
            }

            if (! isset($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"])) {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"] = NULL;
            }

            if ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"] == "uri") {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = trim($this->_value);
            } elseif ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"] == "bnode") {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = trim($this->_value);
            } elseif ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " type"] == "literal") {
                $value = trim($this->_value);
                if (array_key_exists($this->_cellCurrent . " datatype", $this->_result['result']['rows'][$this->_rowCurrent])) {
                    if ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " datatype"] == "http://www.w3.org/2001/XMLSchema#double" || $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " datatype"] == "http://www.w3.org/2001/XMLSchema#decimal") {
                        $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = floatval($value);
                    } elseif ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " datatype"] == "http://www.w3.org/2001/XMLSchema#integer") {
                        $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = intval($value);
                    } elseif ($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent . " datatype"] == "http://www.w3.org/2001/XMLSchema#boolean") {
                        $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value === "true" ? true : false;
                    } else {
                        $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value;
                    }
                } else {
                    $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value;
                }
            } else {
                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $this->_value;
            }
            $this->_cellCurrent = null;
            $this->_value = "";
        }else if ($elementname == "boolean") {
            $this->_result['boolean']= $this->_value == "true" ? true : false;
        }
    }

    /**
     * callback for the content within an element
     *
     * @param $parserObject
     * @param $data
     */
    public function contentHandler($parserObject, $data)
    {
        if ($this->_cellCurrent != null) {
            // echo "DATA". $data." - ".$this->_cellCurrent."\n";
            $this->_value .= $data;
        }
    }

    /**
     * TODO
     *
     * @param $array
     * @return mixed
     */
    public function sortResult($array)
    {
        $result = $array;
        if (isset($result['result']['rows']))
            usort($result['result']['rows'], 'ParserSparqlResult::mySortResult');
        return $result;
    }

    /**
     * TODO
     *
     * @param $row1
     * @param $row2
     * @return int
     */
    public function mySortResult($row1, $row2)
    {
        $result = 0;
        $countTab = 0;
        if (count($row1) > count($row2)) {
            $countTab = count($row1);
        } else {
            $countTab = count($row2);
        }

        for ($i = 0; $i < $countTab; $i ++) {
            if ((! isset($row1[$i])) || (! isset($row2[$i]))) {
                if (isset($row1[$i]) && isset($row2[$i])) { // impossible in theory
                    $result = 0;
                    break;
                } elseif (! isset($row1[$i])) {
                    $result = - 1;
                    break;
                } elseif (! isset($row2[$i])) {
                    $result = 1;
                    break;
                }
            } else if ($row1[$i] < $row2[$i]) {
                $result = 1;
                break;
            } else if ($row1[$i] < $row2[$i]) {
                $result = - 1;
                break;
            }
        }
        return $result;
    }

    /**
     * TODO write comment and clean
     *
     * @param $rs1
     * @param $rs2
     * @param bool $ordered
     * @param bool $distinct
     * @return array
     */
    public static function compare($rs1, $rs2, $ordered = false, $distinct = false)
    {
        $difference = array();
        // A/ Check the variables lists in the header are the same.
        if (! isset($rs1['result']['variables']) && ! isset($rs2['result']['variables'])) {
            return $difference; // return true ;
        } elseif (! isset($rs1['result']['variables']) || ! isset($rs2['result']['variables'])) {
            $difference[1] = $rs1['result']['variables'];
            $difference[2] = $rs2['result']['variables'];
            return $difference; // return false ;
        }

        // Check if there are blanknodes ref : http://blog.datagraph.org/2010/03/rdf-isomorphism

        // 1.Compare graph sizes and all statements without blank nodes. If they do not match, fail.
        // 1.1 remove blank nodes
        $clone1WithoutBlanknodes = null;
        $clone2WithoutBlanknodes = null;
        if ($distinct) {
            $clone1WithoutBlanknodes = $rs1['result']['rows'];
            $clone2WithoutBlanknodes = $rs2['result']['rows'];
        } else {
            $clone1WithoutBlanknodes = ToolsBlankNode::removeDuplicate($rs1['result']['rows']);
            $clone2WithoutBlanknodes = ToolsBlankNode::removeDuplicate($rs2['result']['rows']);
        }

        $bnodesInRs1 = array();
        $bnodesInRs2 = array();

        // echo "BEFORE";
        // print_r($clone1);
        // print_r($clone2);
        foreach ($clone1WithoutBlanknodes as $key => &$row) {
            $arrayVariableTypeBnode = array_keys($row, "bnode");
            foreach ($arrayVariableTypeBnode as $variableTypeBnode) {
                $variableArray = explode(" ", $variableTypeBnode);
                $variable = $variableArray[0];
                $bnodesInRs1[] = $row[$variable];
                $row[$variable] = "BLANKNODE"; // remove
            }
        }
        foreach ($clone2WithoutBlanknodes as $key => &$row) {
            $arrayVariableTypeBnode = array_keys($row, "bnode");
            foreach ($arrayVariableTypeBnode as $variableTypeBnode) {
                $variableArray = explode(" ", $variableTypeBnode);
                $variable = $variableArray[0];
                $bnodesInRs2[] = $row[$variable];
                $row[$variable] = "BLANKNODE"; // remove
            }
        }

        // print_r($clone1WithoutBlanknodes);
        // print_r($clone2WithoutBlanknodes);
        // 1.2 compare
        if ($ordered) {
            $difference = ToolsBlankNode::arrayDiffAssocRecursive($clone1WithoutBlanknodes,
                $clone2WithoutBlanknodes);
        } else {
            $difference = ToolsBlankNode::arrayDiffAssocUnordered($clone1WithoutBlanknodes,
                $clone2WithoutBlanknodes);
        }

        // Check if there are blank nodes
        if ((count($bnodesInRs1) == 0 && count($bnodesInRs2) == 0) || count($difference) != 0)
            return $difference;

        // With blank nodes
        $bnodesInRs1 = array_values(array_unique($bnodesInRs1));
        $bnodesInRs2 = array_values(array_unique($bnodesInRs2));
        if (count($bnodesInRs1) != count($bnodesInRs2)) {
            $difference[1] = "Nb bnode :" . count($bnodesInRs1);
            $difference[2] = "Nb bnode :" . count($bnodesInRs2);
            return $difference;
        }

        $clone1 = $rs1['result']['rows'];

        $arrayPermutationsBnode = ToolsBlankNode::allPermutations($bnodesInRs2);

        foreach ($arrayPermutationsBnode as $permute) {
            $clone2 = $rs2['result']['rows'];

            // print_r($clone2);
            foreach ($clone2 as $key => &$row) {
                $arrayVariableTypeBnode = array_keys($row, "bnode");
                foreach ($arrayVariableTypeBnode as $variableTypeBnode) {
                    $variableArray = explode(" ", $variableTypeBnode);
                    $variable = $variableArray[0];

                    $row[$variable] = $bnodesInRs1[array_search($row[$variable], $permute)];
                }
            }

            if ($ordered) {
                $difference = ToolsBlankNode::arrayDiffAssocRecursive($clone1, $clone2);
            } else {
                $difference = ToolsBlankNode::arrayDiffAssocUnordered($clone1, $clone2);
            }

            if (count($difference) == 0) {
                return $difference;
            }
        }

        return $difference;
    }
}
