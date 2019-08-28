<?php

namespace App\Doctrine\Query\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class InJsonArray extends FunctionNode
{
    /**
     * @var string
     */
    const FUNCTION_NAME = 'in_json_array';

    private $field;
    private $value;

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $field = $sqlWalker->walkStringPrimary($this->field);
        if ($this->value instanceof InputParameter) {
            $value = $sqlWalker->walkStringPrimary($this->value);
        } else {
            $value = "'{$this->value->identificationVariable}.{$this->value->field}'";
        }

        return "in_array($field, $value)";
    }

    /**
     * @param Parser $parser
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->value = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
