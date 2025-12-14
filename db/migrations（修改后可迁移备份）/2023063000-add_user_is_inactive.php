<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并删除 t 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 't'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `t`;");
        }
        
        // 2. 检查并添加 is_inactive 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_inactive'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `is_inactive` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态';");
        }
        
        // 3. 检查并添加 last_use_time 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_use_time'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `last_use_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间';");
        }
        
        // 4. 检查并添加 last_login_time 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_login_time'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `last_login_time` int(11) unsigned DEFAULT 0 COMMENT '最后登录时间';");
        }
        
        // 5. 检查并添加 is_inactive 索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'is_inactive'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD KEY `is_inactive` (`is_inactive`);");
        }

        return 2023063000;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并添加 t 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 't'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `t` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间';");
        }
        
        // 2. 检查并删除 is_inactive 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_inactive'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `is_inactive`;");
        }
        
        // 3. 检查并删除 last_use_time 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_use_time'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `last_use_time`;");
        }
        
        // 4. 检查并删除 last_login_time 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'last_login_time'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `last_login_time`;");
        }
        
        // 5. 检查并删除 is_inactive 索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'is_inactive'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP KEY `is_inactive`;");
        }

        return 2023061800;
    }
};
