<?php
/**
 * Created by PhpStorm.
 * User: wangh
 * Date: 2020/3/2
 * Time: 19:49
 */

namespace Wisp\Orm\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * My custom datatype.
 */
class GeometryType extends Type
{
    const GEOMETRY = 'geometry'; // modify to match your type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'geometry';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function getName()
    {
        return self::GEOMETRY;
    }
    public function canRequireSQLConversion()
    {
       return true;
    }
}