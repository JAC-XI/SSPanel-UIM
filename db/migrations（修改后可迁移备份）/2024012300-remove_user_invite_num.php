<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并删除 invite_num 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'invite_num'");
        if ($stmt->rowCount() > 0) {
            $pdo->exec('ALTER TABLE `user` DROP COLUMN `invite_num`');
        }

        return 2024012300;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查并添加 invite_num 列
        $stmt = $pdo->query("SHOW COLUMNS FROM `user` LIKE 'invite_num'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `user` ADD COLUMN `invite_num` int(11) NOT NULL DEFAULT 0 COMMENT '可用邀请次数'");
        }

        return 2024012000;
    }
};
