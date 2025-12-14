<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 禁用外键检查
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        
        // 1. 修改 announcement 表
        $this->executeIfColumnExists($pdo, 'announcement', 'id', "ALTER TABLE announcement MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告ID';");
        $this->executeIfColumnExists($pdo, 'announcement', 'date', "ALTER TABLE announcement MODIFY COLUMN `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '公告日期';");
        $this->executeIfColumnExists($pdo, 'announcement', 'content', "ALTER TABLE announcement MODIFY COLUMN `content` text COMMENT '公告内容';");
        
        // 2. 修改 config 表
        $this->executeIfColumnExists($pdo, 'config', 'value', "ALTER TABLE config MODIFY COLUMN `value` varchar(2048) DEFAULT NULL COMMENT '值';");
        
        // 3. 修改 detect_ban_log 表
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'id', "ALTER TABLE detect_ban_log MODIFY COLUMN `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'user_id', "ALTER TABLE detect_ban_log MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'detect_number', "ALTER TABLE detect_ban_log MODIFY COLUMN `detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '本次违规次数';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'ban_time', "ALTER TABLE detect_ban_log MODIFY COLUMN `ban_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '本次封禁时长';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'start_time', "ALTER TABLE detect_ban_log MODIFY COLUMN `start_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '统计开始时间';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'end_time', "ALTER TABLE detect_ban_log MODIFY COLUMN `end_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '统计结束时间';");
        $this->executeIfColumnExists($pdo, 'detect_ban_log', 'all_detect_number', "ALTER TABLE detect_ban_log MODIFY COLUMN `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数';");
        
        // 4. 修改 detect_list 表
        $this->executeIfColumnExists($pdo, 'detect_list', 'id', "ALTER TABLE detect_list MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '审计规则ID';");
        $this->executeIfColumnExists($pdo, 'detect_list', 'name', "ALTER TABLE detect_list MODIFY COLUMN `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称';");
        $this->executeIfColumnExists($pdo, 'detect_list', 'text', "ALTER TABLE detect_list MODIFY COLUMN `text` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称';");
        $this->executeIfColumnExists($pdo, 'detect_list', 'regex', "ALTER TABLE detect_list MODIFY COLUMN `regex` varchar(255) NOT NULL DEFAULT '' COMMENT '正则表达式';");
        $this->executeIfColumnExists($pdo, 'detect_list', 'type', "ALTER TABLE detect_list MODIFY COLUMN `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '规则类型';");
        
        // 5. 修改 detect_log 表
        $this->executeIfColumnExists($pdo, 'detect_log', 'id', "ALTER TABLE detect_log MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'detect_log', 'user_id', "ALTER TABLE detect_log MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'detect_log', 'list_id', "ALTER TABLE detect_log MODIFY COLUMN `list_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '规则ID';");
        $this->executeIfColumnExists($pdo, 'detect_log', 'datetime', "ALTER TABLE detect_log MODIFY COLUMN `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '触发时间';");
        $this->executeIfColumnExists($pdo, 'detect_log', 'node_id', "ALTER TABLE detect_log MODIFY COLUMN `node_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点ID';");
        $this->executeIfColumnExists($pdo, 'detect_log', 'status', "ALTER TABLE detect_log MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态';");
        
        // 6. 修改 docs 表
        $this->executeIfColumnExists($pdo, 'docs', 'date', "ALTER TABLE docs MODIFY COLUMN `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '文档日期';");
        $this->executeIfColumnExists($pdo, 'docs', 'title', "ALTER TABLE docs MODIFY COLUMN `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文档标题';");
        $this->executeIfColumnExists($pdo, 'docs', 'content', "ALTER TABLE docs MODIFY COLUMN `content` longtext COMMENT '文档内容';");
        
        // 7. 删除 docs 表的 markdown 列
        $this->dropColumnIfExists($pdo, 'docs', 'markdown');
        
        // 8. 修改 email_queue 表
        $this->executeIfColumnExists($pdo, 'email_queue', 'id', "ALTER TABLE email_queue MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'email_queue', 'to_email', "ALTER TABLE email_queue MODIFY COLUMN `to_email` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人邮箱';");
        $this->executeIfColumnExists($pdo, 'email_queue', 'subject', "ALTER TABLE email_queue MODIFY COLUMN `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件标题';");
        $this->executeIfColumnExists($pdo, 'email_queue', 'template', "ALTER TABLE email_queue MODIFY COLUMN `template` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件模板';");
        $this->executeIfColumnExists($pdo, 'email_queue', 'array', "ALTER TABLE email_queue MODIFY COLUMN `array` longtext COMMENT '模板参数';");
        $this->executeIfColumnExists($pdo, 'email_queue', 'time', "ALTER TABLE email_queue MODIFY COLUMN `time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间';");
        
        // 9. 修改 gift_card 表
        $this->executeIfColumnExists($pdo, 'gift_card', 'id', "ALTER TABLE gift_card MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '礼品卡ID';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'card', "ALTER TABLE gift_card MODIFY COLUMN `card` text COMMENT '卡号';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'balance', "ALTER TABLE gift_card MODIFY COLUMN `balance` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '余额';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'create_time', "ALTER TABLE gift_card MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'status', "ALTER TABLE gift_card MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '使用状态';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'use_time', "ALTER TABLE gift_card MODIFY COLUMN `use_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '使用时间';");
        $this->executeIfColumnExists($pdo, 'gift_card', 'use_user', "ALTER TABLE gift_card MODIFY COLUMN `use_user` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '使用用户';");
        
        // 10. 修改 invoice 表
        $this->executeIfColumnExists($pdo, 'invoice', 'id', "ALTER TABLE invoice MODIFY COLUMN `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '账单ID';");
        $this->executeIfColumnExists($pdo, 'invoice', 'user_id', "ALTER TABLE invoice MODIFY COLUMN `user_id` bigint(20) unsigned DEFAULT 0 COMMENT '归属用户';");
        $this->executeIfColumnExists($pdo, 'invoice', 'order_id', "ALTER TABLE invoice MODIFY COLUMN `order_id` bigint(20) unsigned DEFAULT 0 COMMENT '订单ID';");
        $this->executeIfColumnExists($pdo, 'invoice', 'content', "ALTER TABLE invoice MODIFY COLUMN `content` longtext COMMENT '账单内容';");
        $this->executeIfColumnExists($pdo, 'invoice', 'price', "ALTER TABLE invoice MODIFY COLUMN `price` decimal(12,2) unsigned DEFAULT 0 COMMENT '账单金额';");
        $this->executeIfColumnExists($pdo, 'invoice', 'status', "ALTER TABLE invoice MODIFY COLUMN `status` varchar(255) DEFAULT '' COMMENT '账单状态';");
        $this->executeIfColumnExists($pdo, 'invoice', 'create_time', "ALTER TABLE invoice MODIFY COLUMN `create_time` int(11) unsigned DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'invoice', 'update_time', "ALTER TABLE invoice MODIFY COLUMN `update_time` int(11) unsigned DEFAULT 0 COMMENT '更新时间';");
        $this->executeIfColumnExists($pdo, 'invoice', 'pay_time', "ALTER TABLE invoice MODIFY COLUMN `pay_time` int(11) unsigned DEFAULT 0 COMMENT '支付时间';");
        
        // 11. 修改 link 表
        $this->executeIfColumnExists($pdo, 'link', 'id', "ALTER TABLE link MODIFY COLUMN `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'link', 'token', "ALTER TABLE link MODIFY COLUMN `token` varchar(255) NOT NULL DEFAULT '' COMMENT '订阅token';");
        $this->executeIfColumnExists($pdo, 'link', 'userid', "ALTER TABLE link MODIFY COLUMN `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        
        // 12. 修改 login_ip 表
        $this->executeIfColumnExists($pdo, 'login_ip', 'id', "ALTER TABLE login_ip MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'login_ip', 'userid', "ALTER TABLE login_ip MODIFY COLUMN `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'login_ip', 'ip', "ALTER TABLE login_ip MODIFY COLUMN `ip` varchar(255) NOT NULL DEFAULT '' COMMENT '登录IP';");
        $this->executeIfColumnExists($pdo, 'login_ip', 'datetime', "ALTER TABLE login_ip MODIFY COLUMN `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '登录时间';");
        $this->executeIfColumnExists($pdo, 'login_ip', 'type', "ALTER TABLE login_ip MODIFY COLUMN `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '登录类型';");
        
        // 13. 为 login_ip 表添加 type 索引
        $this->addIndexIfNotExists($pdo, 'login_ip', 'type', "ALTER TABLE login_ip ADD KEY `type` (`type`);");
        
        // 14. 修改 node 表
        $this->executeIfColumnExists($pdo, 'node', 'id', "ALTER TABLE node MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '节点ID';");
        $this->executeIfColumnExists($pdo, 'node', 'name', "ALTER TABLE node MODIFY COLUMN `name` varchar(255) NOT NULL DEFAULT '' COMMENT '节点名称';");
        $this->executeIfColumnExists($pdo, 'node', 'type', "ALTER TABLE node MODIFY COLUMN `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '节点显示';");
        $this->executeIfColumnExists($pdo, 'node', 'server', "ALTER TABLE node MODIFY COLUMN `server` varchar(255) NOT NULL DEFAULT '' COMMENT '节点地址';");
        $this->executeIfColumnExists($pdo, 'node', 'custom_config', "ALTER TABLE node MODIFY COLUMN `custom_config` longtext COMMENT '自定义配置';");
        $this->executeIfColumnExists($pdo, 'node', 'sort', "ALTER TABLE node MODIFY COLUMN `sort` tinyint(2) unsigned NOT NULL DEFAULT 14 COMMENT '节点类型';");
        $this->executeIfColumnExists($pdo, 'node', 'traffic_rate', "ALTER TABLE node MODIFY COLUMN `traffic_rate` decimal(5,2) unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率';");
        $this->executeIfColumnExists($pdo, 'node', 'node_class', "ALTER TABLE node MODIFY COLUMN `node_class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点等级';");
        $this->executeIfColumnExists($pdo, 'node', 'node_speedlimit', "ALTER TABLE node MODIFY COLUMN `node_speedlimit` decimal(10,2) unsigned NOT NULL DEFAULT 0 COMMENT '节点限速';");
        $this->executeIfColumnExists($pdo, 'node', 'node_bandwidth', "ALTER TABLE node MODIFY COLUMN `node_bandwidth` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量';");
        $this->executeIfColumnExists($pdo, 'node', 'node_bandwidth_limit', "ALTER TABLE node MODIFY COLUMN `node_bandwidth_limit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量限制';");
        $this->executeIfColumnExists($pdo, 'node', 'bandwidthlimit_resetday', "ALTER TABLE node MODIFY COLUMN `bandwidthlimit_resetday` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT '流量重置日';");
        $this->executeIfColumnExists($pdo, 'node', 'node_heartbeat', "ALTER TABLE node MODIFY COLUMN `node_heartbeat` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点心跳';");
        $this->executeIfColumnExists($pdo, 'node', 'online_user', "ALTER TABLE node MODIFY COLUMN `online_user` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点在线用户';");
        $this->executeIfColumnExists($pdo, 'node', 'node_group', "ALTER TABLE node MODIFY COLUMN `node_group` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点群组';");
        $this->executeIfColumnExists($pdo, 'node', 'online', "ALTER TABLE node MODIFY COLUMN `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '在线状态';");
        $this->executeIfColumnExists($pdo, 'node', 'gfw_block', "ALTER TABLE node MODIFY COLUMN `gfw_block` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否被GFW封锁';");
        $this->executeIfColumnExists($pdo, 'node', 'password', "ALTER TABLE node MODIFY COLUMN `password` varchar(255) NOT NULL DEFAULT '' COMMENT '后端连接密码';");
        
        // 15. 删除 node 表的 node_connector 和 mu_only 列
        $this->dropColumnIfExists($pdo, 'node', 'node_connector');
        $this->dropColumnIfExists($pdo, 'node', 'mu_only');
        
        // 16. 为 node 表添加索引
        $this->addIndexIfNotExists($pdo, 'node', 'type', "ALTER TABLE node ADD KEY `type` (`type`);");
        $this->addIndexIfNotExists($pdo, 'node', 'sort', "ALTER TABLE node ADD KEY `sort` (`sort`);");
        $this->addIndexIfNotExists($pdo, 'node', 'node_class', "ALTER TABLE node ADD KEY `node_class` (`node_class`);");
        $this->addIndexIfNotExists($pdo, 'node', 'node_group', "ALTER TABLE node ADD KEY `node_group` (`node_group`);");
        $this->addIndexIfNotExists($pdo, 'node', 'online', "ALTER TABLE node ADD KEY `online` (`online`);");
        
        // 17. 修改 online_log 表
        $this->executeIfColumnExists($pdo, 'online_log', 'id', "ALTER TABLE online_log MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'online_log', 'user_id', "ALTER TABLE online_log MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID';");
        // 注意：这里需要修改 ip 列的数据类型，但原SQL使用了inet6，我们需要改为varchar(45)
        $this->executeIfColumnExists($pdo, 'online_log', 'ip', "ALTER TABLE online_log MODIFY COLUMN `ip` varchar(45) NOT NULL COMMENT 'IP地址';");
        $this->executeIfColumnExists($pdo, 'online_log', 'node_id', "ALTER TABLE online_log MODIFY COLUMN `node_id` int(11) unsigned NOT NULL COMMENT '节点ID';");
        $this->executeIfColumnExists($pdo, 'online_log', 'first_time', "ALTER TABLE online_log MODIFY COLUMN `first_time` int(11) unsigned NOT NULL COMMENT '首次在线时间';");
        $this->executeIfColumnExists($pdo, 'online_log', 'last_time', "ALTER TABLE online_log MODIFY COLUMN `last_time` int(11) unsigned NOT NULL COMMENT '最后在线时间';");
        
        // 18. 修改 order 表
        $this->executeIfColumnExists($pdo, 'order', 'id', "ALTER TABLE `order` MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID';");
        $this->executeIfColumnExists($pdo, 'order', 'user_id', "ALTER TABLE `order` MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '提交用户';");
        $this->executeIfColumnExists($pdo, 'order', 'product_id', "ALTER TABLE `order` MODIFY COLUMN `product_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID';");
        $this->executeIfColumnExists($pdo, 'order', 'product_type', "ALTER TABLE `order` MODIFY COLUMN `product_type` varchar(255) NOT NULL DEFAULT '' COMMENT '商品类型';");
        $this->executeIfColumnExists($pdo, 'order', 'product_name', "ALTER TABLE `order` MODIFY COLUMN `product_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称';");
        $this->executeIfColumnExists($pdo, 'order', 'product_content', "ALTER TABLE `order` MODIFY COLUMN `product_content` longtext COMMENT '商品内容';");
        $this->executeIfColumnExists($pdo, 'order', 'coupon', "ALTER TABLE `order` MODIFY COLUMN `coupon` varchar(255) NOT NULL DEFAULT '' COMMENT '订单优惠码';");
        $this->executeIfColumnExists($pdo, 'order', 'price', "ALTER TABLE `order` MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '订单金额';");
        $this->executeIfColumnExists($pdo, 'order', 'status', "ALTER TABLE `order` MODIFY COLUMN `status` varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态';");
        $this->executeIfColumnExists($pdo, 'order', 'create_time', "ALTER TABLE `order` MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'order', 'update_time', "ALTER TABLE `order` MODIFY COLUMN `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间';");
        
        // 19. 修改 payback 表
        $this->executeIfColumnExists($pdo, 'payback', 'id', "ALTER TABLE payback MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'payback', 'total', "ALTER TABLE payback MODIFY COLUMN `total` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '总金额';");
        $this->executeIfColumnExists($pdo, 'payback', 'userid', "ALTER TABLE payback MODIFY COLUMN `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'payback', 'ref_by', "ALTER TABLE payback MODIFY COLUMN `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人ID';");
        $this->executeIfColumnExists($pdo, 'payback', 'ref_get', "ALTER TABLE payback MODIFY COLUMN `ref_get` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人获得金额';");
        $this->executeIfColumnExists($pdo, 'payback', 'datetime', "ALTER TABLE payback MODIFY COLUMN `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        
        // 20. 修改 paylist 表
        $this->executeIfColumnExists($pdo, 'paylist', 'id', "ALTER TABLE paylist MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'paylist', 'userid', "ALTER TABLE paylist MODIFY COLUMN `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'paylist', 'total', "ALTER TABLE paylist MODIFY COLUMN `total` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '总金额';");
        $this->executeIfColumnExists($pdo, 'paylist', 'status', "ALTER TABLE paylist MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态';");
        $this->executeIfColumnExists($pdo, 'paylist', 'invoice_id', "ALTER TABLE paylist MODIFY COLUMN `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID';");
        $this->executeIfColumnExists($pdo, 'paylist', 'tradeno', "ALTER TABLE paylist MODIFY COLUMN `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关单号';");
        $this->executeIfColumnExists($pdo, 'paylist', 'gateway', "ALTER TABLE paylist MODIFY COLUMN `gateway` varchar(255) NOT NULL DEFAULT '' COMMENT '支付网关';");
        $this->executeIfColumnExists($pdo, 'paylist', 'datetime', "ALTER TABLE paylist MODIFY COLUMN `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        
        // 21. 修改 product 表
        $this->executeIfColumnExists($pdo, 'product', 'id', "ALTER TABLE product MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID';");
        $this->executeIfColumnExists($pdo, 'product', 'type', "ALTER TABLE product MODIFY COLUMN `type` varchar(255) NOT NULL DEFAULT 'tabp' COMMENT '类型';");
        $this->executeIfColumnExists($pdo, 'product', 'name', "ALTER TABLE product MODIFY COLUMN `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称';");
        $this->executeIfColumnExists($pdo, 'product', 'price', "ALTER TABLE product MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '售价';");
        $this->executeIfColumnExists($pdo, 'product', 'content', "ALTER TABLE product MODIFY COLUMN `content` longtext COMMENT '内容';");
        $this->executeIfColumnExists($pdo, 'product', 'limit', "ALTER TABLE product MODIFY COLUMN `limit` longtext COMMENT '购买限制';");
        $this->executeIfColumnExists($pdo, 'product', 'status', "ALTER TABLE product MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '销售状态';");
        $this->executeIfColumnExists($pdo, 'product', 'create_time', "ALTER TABLE product MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'product', 'update_time', "ALTER TABLE product MODIFY COLUMN `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间';");
        $this->executeIfColumnExists($pdo, 'product', 'sale_count', "ALTER TABLE product MODIFY COLUMN `sale_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计销售数';");
        $this->executeIfColumnExists($pdo, 'product', 'stock', "ALTER TABLE product MODIFY COLUMN `stock` int(11) NOT NULL DEFAULT -1 COMMENT '库存';");
        
        // 22. 为 product 表添加 status 索引
        $this->addIndexIfNotExists($pdo, 'product', 'status', "ALTER TABLE product ADD KEY `status` (`status`);");
        
        // 23. 修改 ticket 表
        $this->executeIfColumnExists($pdo, 'ticket', 'id', "ALTER TABLE ticket MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '工单ID';");
        $this->executeIfColumnExists($pdo, 'ticket', 'title', "ALTER TABLE ticket MODIFY COLUMN `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工单标题';");
        $this->executeIfColumnExists($pdo, 'ticket', 'content', "ALTER TABLE ticket MODIFY COLUMN `content` longtext COMMENT '工单内容';");
        $this->executeIfColumnExists($pdo, 'ticket', 'userid', "ALTER TABLE ticket MODIFY COLUMN `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        $this->executeIfColumnExists($pdo, 'ticket', 'datetime', "ALTER TABLE ticket MODIFY COLUMN `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'ticket', 'status', "ALTER TABLE ticket MODIFY COLUMN `status` varchar(255) NOT NULL DEFAULT '' COMMENT '工单状态';");
        $this->executeIfColumnExists($pdo, 'ticket', 'type', "ALTER TABLE ticket MODIFY COLUMN `type` varchar(255) NOT NULL DEFAULT '' COMMENT '工单类型';");
        
        // 24. 为 ticket 表添加索引
        $this->addIndexIfNotExists($pdo, 'ticket', 'userid', "ALTER TABLE ticket ADD KEY `userid` (`userid`);");
        $this->addIndexIfNotExists($pdo, 'ticket', 'status', "ALTER TABLE ticket ADD KEY `status` (`status`);");
        
        // 25. 修改 user 表
        $this->executeIfColumnExists($pdo, 'user', 'class', "ALTER TABLE user MODIFY COLUMN `class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '等级';");
        
        // 26. 删除 user 表的 node_connector 列
        $this->dropColumnIfExists($pdo, 'user', 'node_connector');
        
        // 27. 修改 user_coupon 表
        $this->executeIfColumnExists($pdo, 'user_coupon', 'id', "ALTER TABLE user_coupon MODIFY COLUMN `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠码ID';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'code', "ALTER TABLE user_coupon MODIFY COLUMN `code` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠码';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'content', "ALTER TABLE user_coupon MODIFY COLUMN `content` longtext COMMENT '优惠码内容';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'limit', "ALTER TABLE user_coupon MODIFY COLUMN `limit` longtext COMMENT '优惠码限制';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'use_count', "ALTER TABLE user_coupon MODIFY COLUMN `use_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计使用次数';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'create_time', "ALTER TABLE user_coupon MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';");
        $this->executeIfColumnExists($pdo, 'user_coupon', 'expire_time', "ALTER TABLE user_coupon MODIFY COLUMN `expire_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '过期时间';");
        
        // 28. 修改 user_invite_code 表
        $this->executeIfColumnExists($pdo, 'user_invite_code', 'id', "ALTER TABLE user_invite_code MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        $this->executeIfColumnExists($pdo, 'user_invite_code', 'code', "ALTER TABLE user_invite_code MODIFY COLUMN `code` varchar(255) NOT NULL DEFAULT '' COMMENT '邀请码';");
        $this->executeIfColumnExists($pdo, 'user_invite_code', 'user_id', "ALTER TABLE user_invite_code MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID';");
        
        // 29. 修改 user_money_log 表
        $this->executeIfColumnExists($pdo, 'user_money_log', 'id', "ALTER TABLE user_money_log MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';");
        
        // 启用外键检查
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        return 2023061800;
    }

    public function down(): int
    {
        return 2023061800;
    }
    
    /**
     * 检查列是否存在，如果存在则执行SQL
     */
    private function executeIfColumnExists($pdo, $table, $column, $sql)
    {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
            ");
            $stmt->execute([$table, $column]);
            if ($stmt->fetchColumn()) {
                $pdo->exec($sql);
            }
        } catch (Exception $e) {
            error_log("执行SQL失败: " . $sql . " 错误: " . $e->getMessage());
        }
    }
    
    /**
     * 删除列，如果列存在
     */
    private function dropColumnIfExists($pdo, $table, $column)
    {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
            ");
            $stmt->execute([$table, $column]);
            if ($stmt->fetchColumn()) {
                $pdo->exec("ALTER TABLE `{$table}` DROP COLUMN `{$column}`;");
            }
        } catch (Exception $e) {
            error_log("删除列失败: {$table}.{$column} 错误: " . $e->getMessage());
        }
    }
    
    /**
     * 添加索引，如果索引不存在
     */
    private function addIndexIfNotExists($pdo, $table, $index, $sql)
    {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND INDEX_NAME = ?
            ");
            $stmt->execute([$table, $index]);
            if (!$stmt->fetchColumn()) {
                $pdo->exec($sql);
            }
        } catch (Exception $e) {
            error_log("添加索引失败: " . $sql . " 错误: " . $e->getMessage());
        }
    }
};
