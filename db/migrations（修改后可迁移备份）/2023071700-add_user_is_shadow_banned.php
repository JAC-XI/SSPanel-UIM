<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并添加 is_shadow_banned 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_shadow_banned'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `is_shadow_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于账户异常状态';");
        }
        
        // 2. 修改 is_dark_mode 列（如果存在）
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_dark_mode'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            try {
                $pdo->exec("ALTER TABLE user MODIFY COLUMN `is_dark_mode` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用暗黑模式';");
            } catch (Exception $e) {
                // 如果修改失败，忽略错误
                error_log("修改 is_dark_mode 列失败: " . $e->getMessage());
            }
        }
        
        // 3. 修改 is_inactive 列（如果存在）
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_inactive'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            try {
                $pdo->exec("ALTER TABLE user MODIFY COLUMN `is_inactive` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态';");
            } catch (Exception $e) {
                // 如果修改失败，忽略错误
                error_log("修改 is_inactive 列失败: " . $e->getMessage());
            }
        }
        
        // 4. 检查并删除 use_new_shop 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'use_new_shop'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `use_new_shop`;");
        }

        return 2023071700;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并删除 is_shadow_banned 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'is_shadow_banned'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user DROP COLUMN `is_shadow_banned`;");
        }
        
        // 2. 检查并添加 use_new_shop 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'use_new_shop'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `use_new_shop` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否启用新商店';");
        }

        return 2023071600;
    }
};
