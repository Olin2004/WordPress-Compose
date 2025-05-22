<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sm_create_mon_hoc_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_mon_hoc';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        ma_mon_hoc INT NOT NULL AUTO_INCREMENT,
        ten_mon_hoc VARCHAR(255) NOT NULL,
        so_tin_chi INT DEFAULT 3,
        mo_ta TEXT,
        PRIMARY KEY (ma_mon_hoc)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
