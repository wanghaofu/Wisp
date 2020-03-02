<?php namespace Wisp\Orm\Dev;

use Symfony\Component\Console\Application;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

Type::addType('geometry', 'Wisp\Orm\Types\GeometryType');
Type::addType('point', 'Wisp\Orm\Types\PointType');
class Toolbox extends Application
{
    public function __construct($name = 'KingOrm Toolbox', $version = 'dev')
    {
        parent::__construct($name, $version);

        foreach(glob(__DIR__ . '/Commands/*.php') as $file) {
            $name = substr($file, strlen(__DIR__), -4);
            $name = __NAMESPACE__ . str_replace('/', '\\', $name);
            $this->add(new $name);
        }
    }

    public static function main()
    {
        $instance = new static;
        $instance->run();
    }
}
