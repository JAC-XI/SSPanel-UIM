<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 expire_in 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'expire_in'
        ");
        $stmt->execute();
        
        // 如果列存在，则删除
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user DROP COLUMN `expire_in`;');
        }

        return 2023082000;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 expire_in 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'expire_in'
        ");
        $stmt->execute();
        
        // 如果列不存在，则添加
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `expire_in` datetime NOT NULL DEFAULT '2199-01-01 00:00:00' COMMENT '账户过期时间';");
        }

        return 2023081800;
    }
};
