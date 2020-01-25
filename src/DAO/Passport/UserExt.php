<?php
namespace Wisp\DAO\Passport;

use Wisp\Db\DAO;

/**
 * @description
 **/
class UserExt extends DAO
{
    var $uuid;
    var $username; // 用户名
    var $email; // 邮箱
    var $have_bind_email; // 是否绑邮箱 0 未绑定 1 已绑定 邮箱为真 通过邮箱激活后设置为绑定
    var $quick_reg; // 注册途径 0  正常注册 1 快速注册 默认为正常注册
    var $realname; // 真实姓名
    var $ID_card; // 身份证号
    var $dentity_verification; // 实名认证 0 未认证 1 认证通过
    var $real_ip; // 注册ip
    var $ctime;
    var $mtime;
    const F_UUID = 'uuid'; //
    const F_USERNAME = 'username'; // 用户名
    const F_EMAIL = 'email'; // 邮箱
    const F_HAVE_BIND_EMAIL = 'have_bind_email'; // 是否绑邮箱 0 未绑定 1 已绑定 邮箱为真 通过邮箱激活后设置为绑定
    const F_QUICK_REG = 'quick_reg'; // 注册途径 0  正常注册 1 快速注册 默认为正常注册
    const F_REALNAME = 'realname'; // 真实姓名
    const F_ID_CARD = 'ID_card'; // 身份证号
    const F_DENTITY_VERIFICATION = 'dentity_verification'; // 实名认证 0 未认证 1 认证通过
    const F_REAL_IP = 'real_ip'; // 注册ip
    const F_CTIME = 'ctime'; //
    const F_MTIME = 'mtime'; //
    static $fields = [ 'uuid', 'username', 'email', 'have_bind_email', 'quick_reg', 'realname', 'ID_card', 'dentity_verification', 'real_ip', 'ctime', 'mtime' ];

    function __construct( $dbName = null )
    {
        $this->__schemaName = 'passport';
        $this->__tableName = 'user_ext';
        parent::__construct( $dbName );
        $this->__primaryKey = 'uuid'; //
    }
}
