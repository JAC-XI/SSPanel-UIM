<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 invoice 表的 type 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `invoice` LIKE 'type'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `invoice` ADD COLUMN `type` varchar(255) NOT NULL DEFAULT 'product' COMMENT '类型'");
        }
        
        // 检查并添加 invoice 表的 type 索引
        $this->addKeyIfNotExists($pdo, 'invoice', 'type');
        
        // 修改 invoice 表的其他列
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '归属用户ID'");
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `order_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID'");
        
        // 修改 content 列，移除默认值和CHECK约束
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `content` longtext NOT NULL COMMENT '账单内容'");
        
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `price` double unsigned NOT NULL DEFAULT 0 COMMENT '账单金额'");
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `status` varchar(255) NOT NULL DEFAULT '' COMMENT '账单状态'");
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间'");
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间'");
        $pdo->exec("ALTER TABLE `invoice` MODIFY COLUMN `pay_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '支付时间'");
        
        // 检查并添加 order 表的 product_type 索引（注意order是保留字，需要用反引号）
        $this->addKeyIfNotExists($pdo, 'order', 'product_type');
        
        // 修改 node 表的 traffic_rate 列
        $pdo->exec("ALTER TABLE `node` MODIFY COLUMN `traffic_rate` double unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率'");

        return 2024040500;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 invoice 表的 type 索引
        $this->dropKeyIfExists($pdo, 'invoice', 'type');
        
        // 检查并删除 invoice 表的 type 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `invoice` LIKE 'type'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `invoice` DROP COLUMN `type`');
        }

        return 2024031700;
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
     * 检查并删除索引
     */
    private function dropKeyIfExists(PDO $pdo, string $table, string $column): void
    {
        $indexName = $this->getIndexName($pdo, $table, $column);
        if ($indexName !== null) {
            $pdo->exec("ALTER TABLE `{$table}` DROP KEY `{$indexName}`");
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
