<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        $pdo = DB::getPdo();
        
        // 1. 检查并添加 locale 列
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'locale'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user ADD COLUMN `locale` varchar(16) NOT NULL DEFAULT 'zh-TW' COMMENT '显示语言';");
        }
        
        // 2. 修改现有列的数据类型（这些应该已经存在）
        $modifyStatements = [
            "ALTER TABLE user MODIFY COLUMN `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名';",
            "ALTER TABLE user MODIFY COLUMN `email` varchar(255) NOT NULL COMMENT 'Email';",
            "ALTER TABLE user MODIFY COLUMN `pass` varchar(255) NOT NULL COMMENT '登录密码';",
            "ALTER TABLE user MODIFY COLUMN `passwd` varchar(255) NOT NULL COMMENT '节点密码';",
            "ALTER TABLE user MODIFY COLUMN `u` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前上传流量';",
            "ALTER TABLE user MODIFY COLUMN `d` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前下载流量';",
            "ALTER TABLE user MODIFY COLUMN `transfer_today` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户今日所用流量';",
            "ALTER TABLE user MODIFY COLUMN `transfer_total` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户累计使用流量';",
            "ALTER TABLE user MODIFY COLUMN `transfer_enable` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前可用流量';",
            "ALTER TABLE user MODIFY COLUMN `last_detect_ban_time` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '最后一次被封禁的时间';",
            "ALTER TABLE user MODIFY COLUMN `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数';",
            "ALTER TABLE user MODIFY COLUMN `last_check_in_time` int(11) unsigned DEFAULT 0 COMMENT '最后签到时间';",
            "ALTER TABLE user MODIFY COLUMN `reg_date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '注册时间';",
            "ALTER TABLE user MODIFY COLUMN `money` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额';",
            "ALTER TABLE user MODIFY COLUMN `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '邀请人ID';",
            "ALTER TABLE user MODIFY COLUMN `method` varchar(255) NOT NULL DEFAULT 'aes-128-gcm' COMMENT 'Shadowsocks加密方式';",
            "ALTER TABLE user MODIFY COLUMN `reg_ip` varchar(255) NOT NULL DEFAULT '127.0.0.1' COMMENT '注册IP';",
            "ALTER TABLE user MODIFY COLUMN `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否管理员';",
            "ALTER TABLE user MODIFY COLUMN `im_type` smallint(6) unsigned NOT NULL DEFAULT 1 COMMENT '联系方式类型';",
            "ALTER TABLE user MODIFY COLUMN `class` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '等级';",
            "ALTER TABLE user MODIFY COLUMN `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间';",
            "ALTER TABLE user MODIFY COLUMN `theme` varchar(255) NOT NULL DEFAULT 'tabler' COMMENT '网站主题';",
            "ALTER TABLE user MODIFY COLUMN `ga_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'GA密钥';",
            "ALTER TABLE user MODIFY COLUMN `ga_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'GA开关';",
            "ALTER TABLE user MODIFY COLUMN `node_group` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点分组';",
            "ALTER TABLE user MODIFY COLUMN `is_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否封禁';",
            "ALTER TABLE user MODIFY COLUMN `banned_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '封禁理由';",
            "ALTER TABLE user MODIFY COLUMN `expire_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '过期提醒';",
            "ALTER TABLE user MODIFY COLUMN `traffic_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '流量提醒';",
            "ALTER TABLE user MODIFY COLUMN `auto_reset_day` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量日';",
            "ALTER TABLE user MODIFY COLUMN `auto_reset_bandwidth` decimal(12,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '自动重置流量';",
            "ALTER TABLE user MODIFY COLUMN `is_dark_mode` tinyint(1) NOT NULL DEFAULT 0;",
        ];
        
        foreach ($modifyStatements as $sql) {
            try {
                $pdo->exec($sql);
            } catch (Exception $e) {
                // 如果修改列失败，可能是因为列不存在或其他原因
                // 继续执行其他语句
                error_log("执行SQL失败: " . $sql . " 错误: " . $e->getMessage());
            }
        }
        
        // 3. 更新 im_value 字段
        try {
            $pdo->exec("UPDATE user SET im_value = '' WHERE im_value IS NULL;");
        } catch (Exception $e) {
            error_log("更新im_value失败: " . $e->getMessage());
        }
        
        // 4. 修改 im_value 列
        try {
            $pdo->exec("ALTER TABLE user MODIFY COLUMN `im_value` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式';");
        } catch (Exception $e) {
            error_log("修改im_value列失败: " . $e->getMessage());
        }
        
        // 5. 修改 remark 列
        try {
            $pdo->exec("ALTER TABLE user MODIFY COLUMN `remark` text COMMENT '备注';");
        } catch (Exception $e) {
            error_log("修改remark列失败: " . $e->getMessage());
        }
        
        // 6. 检查并删除 user_name 索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'user_name'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user DROP KEY `user_name`;');
        }
        
        // 7. 检查并添加 api_token 唯一索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'api_token'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            // 先检查列是否存在
            $stmt2 = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'user' 
                AND COLUMN_NAME = 'api_token'
            ");
            $stmt2->execute();
            if ($stmt2->fetchColumn()) {
                $pdo->exec('ALTER TABLE user ADD UNIQUE KEY `api_token` (`api_token`);');
            }
        }
        
        // 8. 检查并添加 is_admin 索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'is_admin'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user ADD KEY `is_admin` (`is_admin`);');
        }
        
        // 9. 检查并添加 is_banned 索引
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND INDEX_NAME = 'is_banned'
        ");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user ADD KEY `is_banned` (`is_banned`);');
        }
        
        // 10. 检查并重命名 sendDailyMail 列为 daily_mail_enable
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'sendDailyMail'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec("ALTER TABLE user CHANGE COLUMN `sendDailyMail` `daily_mail_enable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '每日报告开关';");
        }

        return 2023060300;
    }

    public function down(): int
    {
        $pdo = DB::getPdo();
        
        // 检查 locale 列是否存在
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'user' 
            AND COLUMN_NAME = 'locale'
        ");
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE user DROP COLUMN `locale`;');
        }

        return 2023053000;
    }
};
