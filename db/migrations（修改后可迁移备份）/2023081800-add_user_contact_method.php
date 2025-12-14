<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 contact_method 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'contact_method'
        ");
        $stmt->execute();
        
        // 如果列不存在，则添加
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `contact_method` smallint(6) unsigned NOT NULL DEFAULT 1 COMMENT '偏好的联系方式';");
        }

        return 2023081800;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 contact_method 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'contact_method'
        ");
        $stmt->execute();
        
        // 如果列存在，则删除
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user DROP COLUMN `contact_method`;');
        }

        return 2023080900;
    }
};
