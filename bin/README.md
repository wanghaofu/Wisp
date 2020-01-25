## 命令参数
drop|create|migrate|migrate_from|create_db|drop_db

### example
* php ./korm.php schema create_db   创建数据库 预览
* php ./korm.php  schema create_db -exec   创建数据库 执行


#### 创建指定数据库
* 参数是库名也是配置的主键
* PHP ./korm.php schema create_db --db passport 创建数据库 passport 预览
* PHP ./korm.php schema create_db --db passport --exec 创建数据库 passport 执行


#### 创建库 制定表的维护
* PHP ./korm.php schema create --db passport --exec 创建表 passport 预览

#### 删除库
* php ./korm.php schema drop_db passport --exec  删除库
