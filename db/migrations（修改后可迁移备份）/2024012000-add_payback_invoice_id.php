<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 invoice_id 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `payback` LIKE 'invoice_id'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `payback` ADD COLUMN `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID'");
        }

        return 2024012000;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 invoice_id 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `payback` LIKE 'invoice_id'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `payback` DROP COLUMN `invoice_id`');
        }

        return 2023120700;
    }
};
