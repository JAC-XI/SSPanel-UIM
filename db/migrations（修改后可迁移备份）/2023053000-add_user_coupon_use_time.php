<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 use_count 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user_coupon' 
            AND COLUMN_NAME = 'use_count'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列不存在，则添加
        if (!$columnExists) {
            $pdo->exec("ALTER TABLE user_coupon ADD COLUMN `use_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计使用次数';");
        }

        return 2023053000;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 use_count 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user_coupon' 
            AND COLUMN_NAME = 'use_count'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列存在，则删除
        if ($columnExists) {
            $pdo->exec('ALTER TABLE user_coupon DROP COLUMN `use_count`;');
        }

        return 2023050800;
    }
};
