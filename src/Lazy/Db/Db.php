<?php

namespace Lazy\Db;

use Lazy\Db\Sql\Delete;
use Lazy\Db\Sql\Insert;
use Lazy\Db\Sql\Select;
use Lazy\Db\Sql\Update;

class Db
{
    /**
     * @var
     */
    protected static $quoter;

    /**
     * @param string $identifier
     * @return string
     */
    public static function quoteIdentifier($identifier)
    {
        if (is_array($identifier)) {
            $result = [];
            foreach ($identifier as $i) {
                $result[] = static::quoteIdentifier($i);
            }

            return $result;
        }

        $parts = explode('.', $identifier);
        foreach ($parts as &$item) {
            $item = '`' . str_replace('`', '``', $item) . '`';
        }

        return implode('.', $parts);
    }

    /**
     * @param callable $quoter
     */
    public static function setQuoter(callable $quoter)
    {
        self::$quoter = $quoter;
    }

    /**
     * @return callable
     */
    public static function getDefaultQuoter()
    {
        $quoter = function($value) use (&$quoter) {
            if (is_array($value)) {
                $quotedValue = [];
                foreach ($value as $k => $v) {
                    $quotedValue[$k] = call_user_func($quoter, $v);
                }

                return $quotedValue;
            }

            if (is_bool($value)) {
                return $value? 1 : 0;
            }

            if (is_null($value)) {
                return 'NULL';
            }

            if (is_numeric($value)) {
                return $value;
            }

            if ($value instanceof Expr || $value instanceof Select) {
                return $value->toString();
            }

            return '\'' . addcslashes((String) $value, "\x00\n\r\\'\"\x1a") . '\'';
        };

        return $quoter;
    }

    /**
     * @return callable
     */
    public static function getQuoter()
    {
        return self::$quoter?: self::getDefaultQuoter();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function quote($value)
    {
        if ($value instanceof Expr) {
            return $value->toString();
        }

        return call_user_func(self::getQuoter(), $value);
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public static function escape($value)
    {
        $quoted = self::quote($value);
        if ('\'' == $quoted[0]) {
            return substr($quoted, 1, -1);
        }
        return $quoted;
    }

    /**
     * @param string $expr
     * @return Expr
     */
    public static function expr($expr)
    {
        return new Expr($expr);
    }

    public static function select($tableName = null, $columns = null)
    {
        $select = new Select();

        !$tableName || $select->from($tableName);
        !$columns || $select->select($columns);

        return $select;
    }

    public static function insert($tableName = null, array $values = null)
    {
        $insert = new Insert();

        !$tableName || $insert->into($tableName);
        !$values || $insert->values($values);

        return $insert;
    }

    public static function update($tableName = null, array $values = null, $where = null)
    {
        $update = new Update();

        !$tableName || $update->from($tableName);
        !$values || $update->set($values);
        !$where || $update->where($where);

        return $update;
    }

    public static function delete($tableName = null, $where = null)
    {
        $delete = new Delete();

        !$tableName || $delete->from($tableName);
        !$where || $delete->where($where);

        return $delete;
    }
}