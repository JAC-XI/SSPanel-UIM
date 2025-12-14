<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并添加 is_dynamic_rate 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'is_dynamic_rate'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE node ADD COLUMN `is_dynamic_rate` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用动态流量倍率';");
        }
        
        // 2. 检查并添加 dynamic_rate_config 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'dynamic_rate_config'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE node ADD COLUMN `dynamic_rate_config` longtext COMMENT '动态流量倍率配置';");
        }

        return 2023102200;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并删除 is_dynamic_rate 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'is_dynamic_rate'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE node DROP COLUMN `is_dynamic_rate`;');
        }
        
        // 2. 检查并删除 dynamic_rate_config 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'dynamic_rate_config'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE node DROP COLUMN `dynamic_rate_config`;');
        }

        return 2023082000;
    }
};
