ParserSparqlResult
===============






* Class name: ParserSparqlResult
* Namespace: 
* Parent class: [Base](Base.md)





Properties
----------


### $_result

```
private mixed $_result
```





* Visibility: **private**


### $_rowCurrent

```
private mixed $_rowCurrent
```





* Visibility: **private**


### $_cellCurrent

```
private mixed $_cellCurrent
```





* Visibility: **private**


### $_value

```
private mixed $_value
```





* Visibility: **private**


### $_errors

```
private mixed $_errors
```





* Visibility: **private**


### $_max_errors

```
private mixed $_max_errors
```





* Visibility: **private**


Methods
-------


### \ParserSparqlResult::__construct()

```
mixed ParserSparqlResult::\ParserSparqlResult::__construct()()
```





* Visibility: **public**



### \ParserSparqlResult::getParser()

```
mixed ParserSparqlResult::\ParserSparqlResult::getParser()()
```





* Visibility: **public**



### \ParserSparqlResult::getResult()

```
mixed ParserSparqlResult::\ParserSparqlResult::getResult()()
```





* Visibility: **public**



### \ParserSparqlResult::startElement()

```
mixed ParserSparqlResult::\ParserSparqlResult::startElement()($parser_object, $elementname, $attribute)
```





* Visibility: **public**

#### Arguments

* $parser_object **mixed**
* $elementname **mixed**
* $attribute **mixed**



### \ParserSparqlResult::endElement()

```
mixed ParserSparqlResult::\ParserSparqlResult::endElement()($parser_object, $elementname)
```





* Visibility: **public**

#### Arguments

* $parser_object **mixed**
* $elementname **mixed**



### \ParserSparqlResult::contentHandler()

```
mixed ParserSparqlResult::\ParserSparqlResult::contentHandler()($parser_object, $data)
```





* Visibility: **public**

#### Arguments

* $parser_object **mixed**
* $data **mixed**



### \ParserSparqlResult::sortResult()

```
mixed ParserSparqlResult::\ParserSparqlResult::sortResult()($array)
```





* Visibility: **public**

#### Arguments

* $array **mixed**



### \ParserSparqlResult::mySortResult()

```
mixed ParserSparqlResult::\ParserSparqlResult::mySortResult()($row1, $row2)
```





* Visibility: **public**

#### Arguments

* $row1 **mixed**
* $row2 **mixed**



### \Base::__construct()

```
mixed ParserSparqlResult::\Base::__construct()()
```





* Visibility: **public**



### \Base::AddError()

```
mixed ParserSparqlResult::\Base::AddError()($error)
```





* Visibility: **public**

#### Arguments

* $error **mixed**



### \Base::GetErrors()

```
array ParserSparqlResult::\Base::GetErrors()()
```

Give the errors



* Visibility: **public**



### \Base::ResetErrors()

```
mixed ParserSparqlResult::\Base::ResetErrors()()
```





* Visibility: **public**


