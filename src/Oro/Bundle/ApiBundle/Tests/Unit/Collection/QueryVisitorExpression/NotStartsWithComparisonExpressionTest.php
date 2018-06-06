<?php

namespace Oro\Bundle\ApiBundle\Tests\Unit\Collection\QueryVisitorExpression;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Parameter;
use Oro\Bundle\ApiBundle\Collection\QueryExpressionVisitor;
use Oro\Bundle\ApiBundle\Collection\QueryVisitorExpression\NotStartsWithComparisonExpression;

class NotStartsWithComparisonExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testWalkComparisonExpression()
    {
        $expression = new NotStartsWithComparisonExpression();
        $expressionVisitor = new QueryExpressionVisitor();
        $fieldName = 'e.test';
        $parameterName = 'test_1';
        $value = 'text';

        $result = $expression->walkComparisonExpression(
            $expressionVisitor,
            $fieldName,
            $parameterName,
            $value
        );

        self::assertEquals(
            new Comparison($fieldName, 'NOT LIKE', ':' . $parameterName),
            $result
        );
        self::assertEquals(
            [new Parameter($parameterName, $value . '%', \PDO::PARAM_STR)],
            $expressionVisitor->getParameters()
        );
    }
}
