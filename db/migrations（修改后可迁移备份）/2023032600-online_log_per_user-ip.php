<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 首先检查 online_log 表是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'online_log'
        ");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        // 如果表不存在，则创建
        if (!$tableExists) {
            $pdo->exec('
                CREATE TABLE online_log (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "记录ID",
                    user_id INT UNSIGNED NOT NULL COMMENT "用户ID",
                    ip VARCHAR(45) NOT NULL COMMENT "IP地址",
                    node_id INT UNSIGNED NOT NULL COMMENT "节点ID",
                    first_time INT UNSIGNED NOT NULL COMMENT "首次在线时间",
                    last_time INT UNSIGNED NOT NULL COMMENT "最后在线时间",
                    PRIMARY KEY (id),
                    UNIQUE KEY user_ip_unique (user_id, ip),
                    KEY node_id_idx (node_id),
                    KEY last_time_idx (last_time)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
        }
        
        // 检查 alive_ip 表是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'alive_ip'
        ");
        $stmt->execute();
        $aliveIpExists = $stmt->fetchColumn();
        
        // 如果 alive_ip 表存在，则删除
        if ($aliveIpExists) {
            $pdo->exec('DROP TABLE alive_ip');
        }

        return 2023032600;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 alive_ip 表是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'alive_ip'
        ");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        // 如果表不存在，则创建
        if (!$tableExists) {
            $pdo->exec('
                CREATE TABLE alive_ip (
                    id BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT "记录ID",
                    nodeid INT(11) DEFAULT NULL COMMENT "节点ID",
                    userid INT(11) DEFAULT NULL COMMENT "用户ID",
                    ip VARCHAR(255) DEFAULT NULL COMMENT "IP地址",
                    datetime BIGINT(20) DEFAULT NULL COMMENT "时间戳",
                    PRIMARY KEY (id),
                    KEY nodeid_idx (nodeid),
                    KEY userid_idx (userid)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
        }
        
        // 检查 online_log 表是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'online_log'
        ");
        $stmt->execute();
        $onlineLogExists = $stmt->fetchColumn();
        
        // 如果 online_log 表存在，则删除
        if ($onlineLogExists) {
            $pdo->exec('DROP TABLE online_log');
        }

        return 2023031701;
    }
};
