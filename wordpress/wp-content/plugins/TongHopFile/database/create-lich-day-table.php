<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sm_create_lich_day_table() {
    global $wpdb;
    $gv_table = $wpdb->prefix . 'tbl_giangvien';
    $phong_table = $wpdb->prefix . 'tbl_phong';
    $mon_hoc_table = $wpdb->prefix . 'tbl_mon_hoc';
    $table_name = $wpdb->prefix . 'tbl_lich_day';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        ma_lich_day INT NOT NULL AUTO_INCREMENT,
        ma_gv INT NOT NULL,
        ma_phong INT NOT NULL,
        ma_mon_hoc INT NOT NULL,
        tiet VARCHAR(20),
        phong VARCHAR(50),
        ngay_day DATE,
        ghi_chu TEXT,
        PRIMARY KEY (ma_lich_day),
        FOREIGN KEY (ma_gv) REFERENCES {$gv_table}(ma_gv),
        FOREIGN KEY (ma_phong) REFERENCES {$phong_table}(ma_phong),
        FOREIGN KEY (ma_mon_hoc) REFERENCES {$mon_hoc_table}(ma_mon_hoc)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
