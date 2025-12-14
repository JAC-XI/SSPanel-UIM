<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 为每个表添加索引，检查索引是否存在
        $this->addKeyIfNotExists($pdo, 'config', 'item');
        $this->addKeyIfNotExists($pdo, 'config', 'class');
        $this->addKeyIfNotExists($pdo, 'config', 'is_public');
        $this->addKeyIfNotExists($pdo, 'hourly_usage', 'date');
        
        // 为node表添加唯一索引
        $this->addUniqueKeyIfNotExists($pdo, 'node', 'password');
        $this->addKeyIfNotExists($pdo, 'node', 'is_dynamic_rate');
        $this->addKeyIfNotExists($pdo, 'node', 'bandwidthlimit_resetday');
        
        $this->addKeyIfNotExists($pdo, 'online_log', 'node_id');
        $this->addKeyIfNotExists($pdo, 'payback', 'userid');
        $this->addKeyIfNotExists($pdo, 'payback', 'ref_by');
        $this->addKeyIfNotExists($pdo, 'payback', 'invoice_id');
        
        // 为paylist表添加唯一索引
        $this->addUniqueKeyIfNotExists($pdo, 'paylist', 'tradeno');
        $this->addKeyIfNotExists($pdo, 'paylist', 'status');
        $this->addKeyIfNotExists($pdo, 'paylist', 'invoice_id');
        
        $this->addKeyIfNotExists($pdo, 'subscribe_log', 'type');
        $this->addKeyIfNotExists($pdo, 'ticket', 'type');
        $this->addKeyIfNotExists($pdo, 'user', 'is_shadow_banned');

        return 2024021900;
    }

    public function down(): int
    {
        // 由于添加索引是可逆的，但删除索引需要知道索引名称
        // 这里不实现down方法，因为添加索引通常是安全的
        return 2024021900;
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
     * 获取索引名称
     */
    private function getIndexName(PDO $pdo, string $table, string $column): ?string
    {
        $stmt = $pdo->query("SHOW INDEX FROM `{$table}` WHERE Column_name = '{$column}'");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return !empty($indexes) ? $indexes[0]['Key_name'] : null;
    }
};
