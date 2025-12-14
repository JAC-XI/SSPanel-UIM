<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 created_at 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user_invite_code` LIKE 'created_at'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `user_invite_code` DROP COLUMN `created_at`');
        }
        
        // 检查并删除 updated_at 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user_invite_code` LIKE 'updated_at'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `user_invite_code` DROP COLUMN `updated_at`');
        }

        return 2023111800;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 created_at 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user_invite_code` LIKE 'created_at'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `user_invite_code` ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '创建时间'");
        }
        
        // 检查并添加 updated_at 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user_invite_code` LIKE 'updated_at'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `user_invite_code` ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '更新时间'");
        }

        return 2023111700;
    }
};
