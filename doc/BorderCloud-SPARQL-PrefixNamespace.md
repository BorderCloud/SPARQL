BorderCloud\SPARQL\PrefixNamespace
===============

Class PrefixNamespace




* Class name: PrefixNamespace
* Namespace: BorderCloud\SPARQL





Properties
----------


### $_namespaces

    protected array $_namespaces = array()

List of namespace



* Visibility: **protected**
* This property is **static**.


Methods
-------


### addW3CNamespace

    mixed BorderCloud\SPARQL\PrefixNamespace::addW3CNamespace()

Init the list of namespace



* Visibility: **public**
* This method is **static**.




### addNamespace

    mixed BorderCloud\SPARQL\PrefixNamespace::addNamespace($prefix, $url)

Add a namespace



* Visibility: **public**
* This method is **static**.


#### Arguments
* $prefix **mixed**
* $url **mixed**



### getNamespace

    mixed BorderCloud\SPARQL\PrefixNamespace::getNamespace($prefix)

Get a namespace



* Visibility: **public**
* This method is **static**.


#### Arguments
* $prefix **mixed**



### toSparql

    string BorderCloud\SPARQL\PrefixNamespace::toSparql()

Get namespace prefix for Sparql and Turtle 1.1



* Visibility: **public**
* This method is **static**.




### toTurtle

    string BorderCloud\SPARQL\PrefixNamespace::toTurtle()

Get namespace prefix for Turtle 1.0



* Visibility: **public**
* This method is **static**.



