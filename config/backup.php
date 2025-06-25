<?php

// config/backup.php
return [
    'enable_cron' => env('BACKUP_CRON_ENABLED', true),
    'cron_time_at' => env('BACKUP_CRON_TIME_AT', '23:00'),
    'admin_email' => env('BACKUP_ADMIN_EMAIL', 'richievc@gmail.com'),
];
