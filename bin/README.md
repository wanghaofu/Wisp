# 数据库管理
## 设置默认数据库 ./korm.php
define("DEFAULT_DB_NAME",'oil_discount'); 

## 命令参数
drop|create|migrate|migrate_from|create_db|drop_db

### example 1 无默认 请修改
* php ./korm.php schema create_db   创建数据库 预览
* php ./korm.php  schema create_db -exec   创建数据库 执行


#### 创建指定数据库
* 参数是库名也是配置的主键DEFAULT_DB_NAME
* PHP ./korm.php schema create_db --db passport 创建数据库 passport 预览
* PHP ./korm.php schema create_db --db passport --exec 创建数据库 passport 执行


#### 表结构更新
#### 默认库
php ./korm.php schema create  --exec
#### 指定库
* PHP ./korm.php schema create --db passport --exec 创建表 passport 预览

#### 删除库
* php ./korm.php schema drop_db --db passport --exec  删除库

### 更新库 结构
* php ./korm.php schema migrate --db oil_discount  exec   

#从数据库生成数据模型
* 配置wisp 默认数据库 连接数据
* 修改 GeneratorDomainDao 的数据库名
* 执行  php ./GeneratorDomainDao.php 即可生成  
* 说明 dao是最新的版本支持的功能更多


 