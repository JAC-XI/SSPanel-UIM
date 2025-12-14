<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 last_day_t 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_day_t'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列存在，则删除
        if ($columnExists) {
            $pdo->exec('ALTER TABLE user DROP COLUMN `last_day_t`;');
        }
        
        // 检查 transfer_today 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'transfer_today'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列不存在，则添加
        if (!$columnExists) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `transfer_today` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户今日所用流量';");
        }

        return 2023050800;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 last_day_t 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_day_t'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列不存在，则添加
        if (!$columnExists) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `last_day_t` bigint(20) DEFAULT 0 COMMENT '今天之前已使用的流量';");
        }
        
        // 检查 transfer_today 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'transfer_today'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列存在，则删除
        if ($columnExists) {
            $pdo->exec('ALTER TABLE user DROP COLUMN `transfer_today`;');
        }

        return 2023032600;
    }
};
