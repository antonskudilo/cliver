<?php

namespace App\Enums;

enum ComparisonOperatorEnum: string
{
    case EQ = '=';
    case EQQ = '==';
    case NEQ = '!=';
    case NEQ2 = '<>';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case IN = 'in';
    case NOT_IN = 'not in';
    case LIKE = 'like';
    case NOT_LIKE = 'not like';
    case IS_NULL = 'is null';
    case IS_NOT_NULL = 'is not null';

    /**
     * @return bool
     */
    public function expectsArray(): bool
    {
        return in_array($this, [self::IN, self::NOT_IN], true);
    }

    /**
     * @return bool
     */
    public function isComparison(): bool
    {
        return in_array($this, [self::GT, self::GTE, self::LT, self::LTE], true);
    }
}
