<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 修改表字段类型
        $pdo->exec("ALTER TABLE invoice MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '账单金额'");
        $pdo->exec("ALTER TABLE node MODIFY COLUMN `traffic_rate` decimal(5,2) unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率'");
        $pdo->exec("ALTER TABLE node MODIFY COLUMN `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点限速'");
        $pdo->exec("ALTER TABLE `order` MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '订单金额'");
        $pdo->exec("ALTER TABLE paylist MODIFY COLUMN `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关识别码'");
        $pdo->exec("ALTER TABLE product MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '售价'");
        $pdo->exec("ALTER TABLE user MODIFY COLUMN `money` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额'");
        $pdo->exec("ALTER TABLE user MODIFY COLUMN `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '用户限速'");
        $pdo->exec("ALTER TABLE user MODIFY COLUMN `im_type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '联系方式类型'");
        $pdo->exec("ALTER TABLE user MODIFY COLUMN `contact_method` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '偏好的联系方式'");
        $pdo->exec("ALTER TABLE user MODIFY COLUMN `daily_mail_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '每日报告开关'");
        
        // 检查并添加 user 表的索引
        $this->addKeyIfNotExists($pdo, 'user', 'contact_method');
        $this->addKeyIfNotExists($pdo, 'user', 'class');
        $this->addKeyIfNotExists($pdo, 'user', 'class_expire');
        $this->addKeyIfNotExists($pdo, 'user', 'node_group');
        
        $pdo->exec("ALTER TABLE user_money_log MODIFY COLUMN `before` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动前账户余额'");
        $pdo->exec("ALTER TABLE user_money_log MODIFY COLUMN `after` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动后账户余额'");
        $pdo->exec("ALTER TABLE user_money_log MODIFY COLUMN `amount` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '变动总额'");

        return 2024061600;
    }

    public function down(): int
    {
        // 由于这些修改主要是数据类型变更和索引添加，回滚需要恢复原状
        // 但原文件没有提供具体的回滚SQL，保持原样
        return 2024061600;
    }
    
    /**
     * 检查并添加普通索引
     */
    private function addKeyIfNotExists(PDO $pdo, string $table, string $column): void
    {
        $indexName = $this->getIndexName($pdo, $table, $column);
        if ($indexName === null) {
            $pdo->exec("ALTER TABLE `{$table}` ADD KEY (`{$column}`)");
        }
    }
    
    /**
     * 获取索引名称
     */
    private function getIndexName(PDO $pdo, string $table, string $column): ?string
    {
        $stmt = $pdo->query("SHOW INDEX FROM `{$table}` WHERE Column_name = '{$column}'");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return !empty($indexes) ? $indexes[0]['Key_name'] : null;
    }
};
