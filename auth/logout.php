<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_unset();
session_destroy();

setFlash('Bạn đã đăng xuất thành công', 'success');
redirect(SITE_URL . '/auth/login.php');
?>