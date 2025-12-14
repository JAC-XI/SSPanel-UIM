<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 为 announcement 表添加 status 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `announcement` LIKE 'status'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `announcement` ADD COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '公告状态'");
        }
        
        // 为 announcement 表添加 sort 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `announcement` LIKE 'sort'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `announcement` ADD COLUMN `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '公告排序'");
        }
        
        // 为 announcement 表添加 status 索引
        $this->addKeyIfNotExists($pdo, 'announcement', 'status');
        
        // 为 announcement 表添加 sort 索引
        $this->addKeyIfNotExists($pdo, 'announcement', 'sort');
        
        // 为 docs 表添加 status 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `docs` LIKE 'status'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `docs` ADD COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '文档状态'");
        }
        
        // 为 docs 表添加 sort 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `docs` LIKE 'sort'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `docs` ADD COLUMN `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '文档排序'");
        }
        
        // 为 docs 表添加 status 索引
        $this->addKeyIfNotExists($pdo, 'docs', 'status');
        
        // 为 docs 表添加 sort 索引
        $this->addKeyIfNotExists($pdo, 'docs', 'sort');

        return 2024052400;
    }

    public function down(): int
    {
        // 由于添加列和索引通常是安全的，且原文件没有提供down的具体实现
        // 这里保持原样，不实现回滚逻辑
        return 2024052400;
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
