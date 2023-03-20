<?php

require_once __DIR__ . '/../lib/functions.php';
init_yuploader();

$log_files = YUPLOADER_APP_HOME . '/log/yuploader' . date("Ym",strtotime("-1 month")) . '*.log';
