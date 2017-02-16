<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\ORM\Condition;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Akeneo\Component\StorageUtils\Exception\PropertyException;
use Doctrine\ORM\QueryBuilder;
use Pim\Component\Catalog\Query\Filter\Operators;

/**
 * Criteria condition utils
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CriteriaCondition
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @param QueryBuilder $qb
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * Prepare criteria condition with field, operator and value
     *
     * @param string|array $field    the backend field name
     * @param string|array $operator the operator used to filter
     * @param string|array $value    the value(s) to filter
     *
     * @throws PropertyException
     *
     * @return string
     */
    public function prepareCriteriaCondition($field, $operator, $value)
    {
        return $this->prepareSingleCriteriaCondition($field, $operator, $value);
    }

    /**
     * Prepare single criteria condition with field, operator and value
     *
     * @param string       $field    the backend field name
     * @param string       $operator the operator used to filter
     * @param string|array $value    the value(s) to filter
     *
     * @throws InvalidPropertyException
     * @throws InvalidPropertyTypeException
     *
     * @return string
     */
    protected function prepareSingleCriteriaCondition($field, $operator, $value)
    {
        $operators = [
            Operators::EQUALS                => 'eq',
            Operators::NOT_EQUAL             => 'neq',
            Operators::LOWER_THAN            => 'lt',
            Operators::LOWER_OR_EQUAL_THAN   => 'lte',
            Operators::GREATER_THAN          => 'gt',
            Operators::GREATER_OR_EQUAL_THAN => 'gte',
            Operators::IS_LIKE               => 'like',
            Operators::IS_NOT_LIKE           => 'notLike'
        ];
        if (array_key_exists($operator, $operators)) {
            if (!is_scalar($value)) {
                throw new InvalidPropertyTypeException(
                    $field,
                    $value,
                    static::class,
                    sprintf('Only scalar values are allowed for operators %s.', implode(', ', $operators)),
                    InvalidPropertyTypeException::SCALAR_EXPECTED_CODE
                );
            }
            $method = $operators[$operator];
            $condition = $this->qb->expr()->$method($field, $this->qb->expr()->literal($value));

            return is_object($condition) ? $condition->__toString() : $condition;
        }

        $operators = [
            Operators::IS_NULL      => 'isNull',
            Operators::IS_NOT_NULL  => 'isNotNull',
            Operators::IS_EMPTY     => 'isNull',
            Operators::IS_NOT_EMPTY => 'isNotNull'
        ];
        if (array_key_exists($operator, $operators)) {
            $method = $operators[$operator];

            return $this->qb->expr()->$method($field);
        }

        $operators = [Operators::IN_LIST => 'in', Operators::NOT_IN_LIST => 'notIn'];
        if (array_key_exists($operator, $operators)) {
            if (!is_array($value)) {
                throw new InvalidPropertyTypeException(
                    $field,
                    $value,
                    static::class,
                    sprintf('Only array values are allowed for operators %s.', implode(', ', $operators)),
                    InvalidPropertyTypeException::ARRAY_EXPECTED_CODE
                );
            }

            if (0 === count($value)) {
                throw InvalidPropertyException::valueNotEmptyExpected($field, static::class);
            }

            $method = $operators[$operator];

            return $this->qb->expr()->$method($field, $value)->__toString();
        }

        if (Operators::BETWEEN === $operator) {
            if (!is_array($value)) {
                throw new InvalidPropertyTypeException(
                    $field,
                    $value,
                    static::class,
                    'Only array values are allowed for operators BETWEEN.',
                    InvalidPropertyTypeException::ARRAY_EXPECTED_CODE
                );
            }

            return sprintf(
                '%s BETWEEN %s AND %s',
                $field,
                $this->qb->expr()->literal($value[0]),
                $this->qb->expr()->literal($value[1])
            );
        }

        throw new InvalidPropertyException(
            $field,
            $value,
            static::class,
            sprintf('Operator "%s" is not supported', $operator),
            InvalidPropertyException::EXPECTED_CODE
        );
    }
}
