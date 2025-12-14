<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 修改表字段类型
        $pdo->exec("ALTER TABLE announcement MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID'");
        $pdo->exec("ALTER TABLE announcement MODIFY COLUMN `content` longtext NOT NULL COMMENT '公告内容'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `item` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `value` varchar(2048) NOT NULL DEFAULT '' COMMENT '配置值'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `class` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类别'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否为公共参数'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置值类型'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `default` varchar(2048) NOT NULL DEFAULT '' COMMENT '默认值'");
        $pdo->exec("ALTER TABLE config MODIFY COLUMN `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注'");
        $pdo->exec("ALTER TABLE detect_ban_log MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '封禁记录ID'");
        $pdo->exec("ALTER TABLE invoice MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单ID'");
        $pdo->exec("ALTER TABLE link MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID'");
        $pdo->exec("ALTER TABLE subscribe_log MODIFY COLUMN `request_user_agent` varchar(1024) NOT NULL DEFAULT '' COMMENT '请求UA'");
        $pdo->exec("ALTER TABLE user_coupon MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠码ID'");
        
        // 检查并添加 subscribe_log 表的索引
        $this->addKeyIfNotExists($pdo, 'subscribe_log', 'request_ip');
        $this->addKeyIfNotExists($pdo, 'subscribe_log', 'request_time');
        $this->addKeyIfNotExists($pdo, 'subscribe_log', 'request_user_agent');
        
        // 检查并删除 user_coupon 表的 code 索引（如果存在）
        $this->dropKeyIfExists($pdo, 'user_coupon', 'code');
        
        // 检查并添加 user_coupon 表的 code 唯一索引
        $this->addUniqueKeyIfNotExists($pdo, 'user_coupon', 'code');

        return 2024031700;
    }

    public function down(): int
    {
        // 由于这些修改主要是数据类型变更和索引添加，回滚需要恢复原状
        // 但原文件没有提供具体的回滚SQL，保持原样
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
     * 检查并添加唯一索引
     */
    private function addUniqueKeyIfNotExists(PDO $pdo, string $table, string $column): void
    {
        $indexName = $this->getIndexName($pdo, $table, $column);
        if ($indexName === null) {
            $pdo->exec("ALTER TABLE `{$table}` ADD UNIQUE KEY (`{$column}`)");
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
