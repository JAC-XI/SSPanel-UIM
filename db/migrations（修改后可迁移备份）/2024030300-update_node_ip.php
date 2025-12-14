<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 ipv4 列（使用 varchar 存储 IPv4 地址）
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'ipv4'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `node` ADD COLUMN `ipv4` varchar(45) NOT NULL DEFAULT '127.0.0.1' COMMENT 'IPv4地址'");
        }
        
        // 检查并添加 ipv6 列（使用 varchar 存储 IPv6 地址）
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'ipv6'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `node` ADD COLUMN `ipv6` varchar(45) NOT NULL DEFAULT '::1' COMMENT 'IPv6地址'");
        }
        
        // 检查并删除 node_ip 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'node_ip'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `node` DROP COLUMN `node_ip`');
        }

        return 2024030300;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 node_ip 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'node_ip'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `node` ADD COLUMN `node_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '节点IP'");
        }
        
        // 检查并删除 ipv4 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'ipv4'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `node` DROP COLUMN `ipv4`');
        }
        
        // 检查并删除 ipv6 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'ipv6'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `node` DROP COLUMN `ipv6`');
        }

        return 2024021900;
    }
};
