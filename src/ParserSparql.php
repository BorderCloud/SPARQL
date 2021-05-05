<?php

namespace BorderCloud\SPARQL;

final class ParserSparql
{
    //const comment = '(?:\s*#[^\n]*)?';
    const prologue = '(?:(\s*BASE\s+[^\n]+|\s*PREFIX\s+[^\n]+|\s*#[^\n]*)*\s*)?';
    const selectQuery = 'SELECT\s.*';
    const constructQuery = 'CONSTRUCT\s.*';
    const describeQuery = 'DESCRIBE\s.*';
    const askQuery = 'ASK\s.*';
    const valuesClause = '(?:VALUES\s.*)?';

    const read = '(?:SELECT|CONSTRUCT|DESCRIBE|ASK)\s.*';
    const update = '(?:LOAD|CLEAR|DROP|ADD|MOVE|COPY|CREATE|INSERT|DELETE|WITH)\s.*';

    public static function isReadQuery($query) {
        $pattern = '~\A'.self::prologue.self::read.self::valuesClause.'\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
    public static function isSelectQuery($query) {
        $pattern = '~\A'.self::prologue.self::selectQuery.self::valuesClause.'\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
    public static function isConstructQuery($query) {
        $pattern = '~\A'.self::prologue.self::constructQuery.self::valuesClause.'\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
    public static function isDescribeQuery($query) {
        $pattern = '~\A'.self::prologue.self::describeQuery.self::valuesClause.'\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
    public static function isAskQuery($query) {
        $pattern = '~\A'.self::prologue.self::askQuery.self::valuesClause.'\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
    public static function isUpdateQuery($query) {
        $pattern = '~\A'.
            self::prologue.
            '(?:'.self::update.'(?:;'.self::prologue.'\s+'.self::update.')*\s*(?:;'.self::prologue.')?)?'.
            '\Z~ims';
        return preg_match($pattern, $query) === 1;
    }
}
