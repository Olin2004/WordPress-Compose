<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sm_create_phong_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_phong';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        ma_phong INT NOT NULL AUTO_INCREMENT,
        ten_phong VARCHAR(100) NOT NULL,
        loai_phong ENUM('Lý thuyết', 'Thực hành') DEFAULT 'Lý thuyết',
        ghi_chu TEXT,
        PRIMARY KEY (ma_phong)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
