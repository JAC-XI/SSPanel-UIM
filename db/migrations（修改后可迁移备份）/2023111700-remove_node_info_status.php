<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并删除 info 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'info'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE node DROP COLUMN `info`;');
        }
        
        // 2. 检查并删除 status 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'status'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE node DROP COLUMN `status`;');
        }

        return 2023111700;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并添加 info 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'info'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE node ADD COLUMN `info` varchar(255) NOT NULL DEFAULT '' COMMENT '节点信息';");
        }
        
        // 2. 检查并添加 status 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'node' 
            AND COLUMN_NAME = 'status'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE node ADD COLUMN `status` varchar(255) NOT NULL DEFAULT '' COMMENT '节点状态';");
        }

        return 2023102200;
    }
};
