<?php

namespace Oro\Bundle\ApiBundle\Collection\QueryVisitorExpression;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Expr;

use Oro\Bundle\ApiBundle\Collection\QueryExpressionVisitor;

class EqComparisonExpression implements ComparisonExpressionInterface
{
    /**
     * {@inheritdoc}
     */
    public function walkComparisonExpression(
        QueryExpressionVisitor $expressionVisitor,
        Comparison $comparison,
        $parameterName,
        $field
    ) {
        $expr = new Expr();
        if ($expressionVisitor->walkValue($comparison->getValue()) === null) {
            return $expr->isNull($field);
        }

        // set parameter
        $parameter = new Parameter($parameterName, $expressionVisitor->walkValue($comparison->getValue()));
        $expressionVisitor->addParameter($parameter);

        // generate expression
        return $expr->eq($field, $expressionVisitor->buildPlaceholder($parameterName));
    }
}
