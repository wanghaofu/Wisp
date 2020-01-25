<?php



/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2016/12/9
 * Time: 15:19
 */
class expExample extends  schema
{


/**
    @var \Wisp\Db\DAO $this;
**/
//    /@ # select * from xx where xx=:xx and yy=:yy

//    var $getRow = function($xx,$yy){
//    {
//        return "select * from xx where xx=:xx and yy=:yy";
//    };
//
//
//
    /**
     * select * from xx where xx=:xx and yy=:yy
     * @param $xx
     * @param $yy
     */
    function getxx($xx,$yy){}

    /**
     * sql:select * from ee wehre nn=:nn and ss >=:ssx
     */
    function getbyxx($nn,$ss){}







}
/**
 * 配置：
 *
 *
 **/
$sql= "
getMaxById#30#select * from _TABLE_NAME_ where xx>:xx and xx<:max
";



class DAOExtends{


    function __call($name,$arguments)
    {
       return  $this->find($name,$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $self = new static();
       return  $self->find($name,$arguments);
    }


}

$res = $schema->getMaxById($xx,$max);



/**
 *
 *
 * 用工具的模式
 * 在不生成的枪框下 也可以通过魔术方法来构建
 *第一次定义的时候 可以快速些 保证不会终端书写
 *
 *
 * 后边统一生成 这样后边用的话就可以得到提示
 *
 */



$s = new expExample();
