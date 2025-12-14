<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 invoice_id 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'paylist' 
            AND COLUMN_NAME = 'invoice_id'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列不存在，则添加
        if (!$columnExists) {
            $pdo->exec('ALTER TABLE paylist ADD COLUMN `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT "账单ID";');
        }
        
        return 2023030500;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 invoice_id 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'paylist' 
            AND COLUMN_NAME = 'invoice_id'
        ");
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();
        
        // 如果列存在，则删除
        if ($columnExists) {
            $pdo->exec('ALTER TABLE paylist DROP COLUMN `invoice_id`;');
        }
        
        return 2023021600;
    }
};
