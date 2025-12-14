<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 gateway 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'paylist' 
            AND COLUMN_NAME = 'gateway'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列不存在，则添加
        if (!$columnExists) {
            $pdo->exec("ALTER TABLE paylist ADD COLUMN `gateway` varchar(255) NOT NULL DEFAULT '' COMMENT '支付网关';");
        }
        
        return 2023031701;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 gateway 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'paylist' 
            AND COLUMN_NAME = 'gateway'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列存在，则删除
        if ($columnExists) {
            $pdo->exec('ALTER TABLE paylist DROP COLUMN `gateway`;');
        }
        
        return 2023031700;
    }
};
