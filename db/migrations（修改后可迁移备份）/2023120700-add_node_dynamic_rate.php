<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 dynamic_rate_type 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'dynamic_rate_type'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `node` ADD COLUMN `dynamic_rate_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '动态流量倍率计算方式'");
        }

        return 2023120700;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 dynamic_rate_type 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `node` LIKE 'dynamic_rate_type'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `node` DROP COLUMN `dynamic_rate_type`');
        }

        return 2023111801;
    }
};
