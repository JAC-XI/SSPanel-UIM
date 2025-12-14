<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 user_name 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `detect_ban_log` LIKE 'user_name'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `detect_ban_log` DROP COLUMN `user_name`');
        }
        
        // 检查并删除 email 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `detect_ban_log` LIKE 'email'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `detect_ban_log` DROP COLUMN `email`');
        }

        return 2023111801;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 user_name 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `detect_ban_log` LIKE 'user_name'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `detect_ban_log` ADD COLUMN `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名'");
        }
        
        // 检查并添加 email 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `detect_ban_log` LIKE 'email'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `detect_ban_log` ADD COLUMN `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱'");
        }

        return 2023111800;
    }
};
