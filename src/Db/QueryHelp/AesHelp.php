<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/9/29
 * Time: 16:28
 */

namespace Wisp\Db\QueryHelp;

//@TODO current not use
trait  AesHelp
{
    /**
     * @ aes ??
     *
     * @param array $values
     * @param null $keyPrefix
     * @return mixed
     */
    public function aesParams(array $values, $keyPrefix = null)
    {
        if (is_null($keyPrefix)) {
            $keyPrefix = 'p_' . count($this->__params);
        }

        $keys = [];
        $aes = Cipher::init(static::AESKEY);
        foreach ($values as $k => $value) {
            $key = ':' . $keyPrefix . '_' . $k;
            $this->__params[$key] = $aes->encrypt($value);
            $keys[] = $key;
        }

        return self::wrap(self::comma($keys));
    }

    /**
     *
     * @param
     *            $value
     * @param null $key
     * @return null|string
     */
    public function aesParam($value, $key = null)
    {
        if (is_null($key)) {
            $key = 'p_' . count($this->__params);
        }
        $key = ":$key";
        $aes = Cipher::init(static::AESKEY);
        $this->__params[$key] = $aes->encrypt($value);
        return $key;
    }


        public function aesIn($field, $value)
    {
        return $this->andWhere($field, 'IN', $this->aesParam($value));
    }

    /**
     *  此类的全部用 注册方式完成
     * @param string $keyField
     *            外键字段名 XXSchema::FLD_YY
     * @param callable|string $parentKey
     *            传callable时参数为parent单行数据，返回该行的key值(false代表跳过)，字符串代表$row->{$parentKey}为key值
     * @return JoinTarget
     * @throws \Exception
     */
    public function on($keyField, $parentKey)
    {
        if (! in_array($keyField, static::$fields)) {
            throw new \Exception(__METHOD__ . '/' . __LINE__);
        }

        return new JoinTarget($this, $keyField, $parentKey);
    }

    /**
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function aesEq($field, $value)
    {
        return $this->andWhere($field, '=', $this->aesParam($value));
    }

    public static function getPkField()
    {
        if ( count( static::$pk ) !== 1 ) {
            throw new \Exception( __METHOD__ . '/' . __LINE__ );
        }

        return reset( static::$pk );
    }
    /**
     *
     * @param
     *            $field
     * @param
     *            $value
     * @return $this
     */
    public function in($field, array $values)
    {
        if (empty($values)) {
            // mysql IN () 报错,这里替代一下
            return $this->andWhere('"in" = "empty array"');
        } else {
            return $this->andWhere($field, 'IN', $this->params($values));
        }
    }
    /**
     * ?? params
     *
     * @param $field
     * @param $value
     * @return $this
     */
    public function dmOp($field, array $values)
    {
        return $this->andWhere($this->param($field), '=', $this->param($values[0]) . '|' . $values[1]);
    }

    public function params( array $values, $keyPrefix = null )
    {
        if ( is_null( $keyPrefix ) ) {
            $keyPrefix = 'p_' . count( $this->__params );
        }

        $keys = [ ];
        foreach ( $values as $k => $value ) {
            $key = ':' . $keyPrefix . '_' . $k;
            $this->__params[ $key ] = $value;
            $keys[] = $key;
        }

        return self::wrap( self::comma( $keys ) );
    }
    /**
     *
     * @param
     *            $target
     * @return $this
     * @throws \Exception
     */
    public function locate($target)
    {
        $driver = static::getDriver();
        $target = $this->convertLocateTarget($target);

        switch (count(static::$pk)) {
            case 0:
                throw new \Exception('PK undefined');
            default:
                array_walk(static::$pk, function ($fld) use ($target, $driver) {
                    call_user_func([$this, 'andWhere'], $driver->quote($fld), '=', $this->param($target[$fld]));
                });
        }

        return $this;
    }
}

/**
 *  * // * @property array content
 * // * @property string fields
 * // * @property string table
 * // * @property string where
 * // * @property string order
 * // * @property int limit
 * // * @property int offset
 * // * @property array params
 * // * @property array paramsx
 */