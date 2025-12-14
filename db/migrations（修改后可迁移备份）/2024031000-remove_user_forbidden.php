<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 forbidden_ip 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'forbidden_ip'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `user` DROP COLUMN `forbidden_ip`');
        }
        
        // 检查并删除 forbidden_port 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'forbidden_port'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `user` DROP COLUMN `forbidden_port`');
        }
        
        // 修改 api_token 列的类型
        $pdo->exec("ALTER TABLE `user` MODIFY COLUMN `api_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'API Token'");
        
        // 修改 uuid 列的类型（使用 char(36) 替代 uuid 类型）
        $pdo->exec("ALTER TABLE `user` MODIFY COLUMN `uuid` char(36) NOT NULL COMMENT 'UUID'");
        
        // 检查并添加 passwd 的唯一索引
        $this->addUniqueKeyIfNotExists($pdo, 'user', 'passwd');

        return 2024031000;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 forbidden_ip 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'forbidden_ip'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `user` ADD COLUMN `forbidden_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问IP'");
        }
        
        // 检查并添加 forbidden_port 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'forbidden_port'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `user` ADD COLUMN `forbidden_port` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问端口'");
        }
        
        // 修改 api_token 列的类型
        $pdo->exec("ALTER TABLE `user` MODIFY COLUMN `api_token` char(36) NOT NULL DEFAULT '' COMMENT 'API 密钥'");
        
        // 修改 uuid 列的类型（保持为 char(36)）
        $pdo->exec("ALTER TABLE `user` MODIFY COLUMN `uuid` char(36) NOT NULL COMMENT 'UUID'");
        
        // 删除 passwd 的唯一索引（需要先获取索引名称）
        $this->dropUniqueKeyIfExists($pdo, 'user', 'passwd');

        return 2024030300;
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
     * 检查并删除唯一索引
     */
    private function dropUniqueKeyIfExists(PDO $pdo, string $table, string $column): void
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
