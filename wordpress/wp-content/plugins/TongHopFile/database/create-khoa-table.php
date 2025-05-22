<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sm_create_khoa_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_khoa';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        ma_khoa INT NOT NULL AUTO_INCREMENT,
        ten_khoa VARCHAR(100) NOT NULL,
        truong_khoa VARCHAR(100),
        ngay_thanh_lap DATE,
        ghi_chu TEXT,
        PRIMARY KEY (ma_khoa)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
