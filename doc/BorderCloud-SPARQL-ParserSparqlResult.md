BorderCloud\SPARQL\ParserSparqlResult
===============

Class ParserSparqlResult




* Class name: ParserSparqlResult
* Namespace: BorderCloud\SPARQL
* Parent class: [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)





Properties
----------


### $_result

    private array $_result





* Visibility: **private**


### $_rowCurrent

    private  $_rowCurrent





* Visibility: **private**


### $_cellCurrent

    private  $_cellCurrent





* Visibility: **private**


### $_value

    private  $_value





* Visibility: **private**


### $_errors

    private array $_errors

TODO



* Visibility: **private**


### $_maxErrors

    private integer $_maxErrors

TODO



* Visibility: **private**


Methods
-------


### __construct

    mixed BorderCloud\SPARQL\Base::__construct()

TODO

Base constructor.

* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)




### getParser

    resource BorderCloud\SPARQL\ParserSparqlResult::getParser()





* Visibility: **public**




### getResult

    array BorderCloud\SPARQL\ParserSparqlResult::getResult()





* Visibility: **public**




### startElement

    mixed BorderCloud\SPARQL\ParserSparqlResult::startElement($parserObject, $elementname, $attribute)

callback for the start of each element



* Visibility: **public**


#### Arguments
* $parserObject **mixed**
* $elementname **mixed**
* $attribute **mixed**



### endElement

    mixed BorderCloud\SPARQL\ParserSparqlResult::endElement($parserObject, $elementname)

callback for the end of each element



* Visibility: **public**


#### Arguments
* $parserObject **mixed**
* $elementname **mixed**



### contentHandler

    mixed BorderCloud\SPARQL\ParserSparqlResult::contentHandler($parserObject, $data)

callback for the content within an element



* Visibility: **public**


#### Arguments
* $parserObject **mixed**
* $data **mixed**



### sortResult

    mixed BorderCloud\SPARQL\ParserSparqlResult::sortResult($array)

TODO



* Visibility: **public**


#### Arguments
* $array **mixed**



### mySortResult

    integer BorderCloud\SPARQL\ParserSparqlResult::mySortResult($row1, $row2)

TODO



* Visibility: **public**


#### Arguments
* $row1 **mixed**
* $row2 **mixed**



### compare

    array BorderCloud\SPARQL\ParserSparqlResult::compare($rs1, $rs2, boolean $ordered, boolean $distinct)

TODO write comment and clean



* Visibility: **public**
* This method is **static**.


#### Arguments
* $rs1 **mixed**
* $rs2 **mixed**
* $ordered **boolean**
* $distinct **boolean**



### addError

    boolean BorderCloud\SPARQL\Base::addError($error)

TODO



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)


#### Arguments
* $error **mixed**



### getErrors

    array BorderCloud\SPARQL\Base::getErrors()

Give the errors



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)




### resetErrors

    mixed BorderCloud\SPARQL\Base::resetErrors()

TODO



* Visibility: **public**
* This method is defined by [BorderCloud\SPARQL\Base](BorderCloud-SPARQL-Base.md)



