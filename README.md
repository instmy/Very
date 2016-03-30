Shadowsocks Manyuser 控制面板

Demo：https://fire.qiyichao.cn

依赖 PDO MySQL。

 1. 导入 fire.sql，配置 library/medoo.php
 2. 新建数据库，导入 region_fire.sql，这是 manyuser 的数据库，根据此数据库的信息配置 library/region.php，并且配置 manyuser
 3. 配置 member.php 的 pay action 中的支付网关或者重写支付代码

各表含义：

 - active：用户活动记录
 - commodity：用户购买的套餐
 - discount：折扣码
 - member：基础用户数据
 - node：服务器节点
 - region：区域
 - token：用户登录授权码

服务器端节点请参考
https://github.com/mengskysama/shadowsocks/tree/manyuser
