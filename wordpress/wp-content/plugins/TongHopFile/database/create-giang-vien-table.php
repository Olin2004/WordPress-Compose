<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sm_create_giang_vien_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_giangvien';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
    CREATE TABLE {$table_name} (
        ma_gv              INT AUTO_INCREMENT PRIMARY KEY,
        ho_ten             VARCHAR(100) NOT NULL,
        email              VARCHAR(100) UNIQUE,
        so_dien_thoai      VARCHAR(20),
        ngay_sinh          DATE,
        gioi_tinh          ENUM('Nam','Nữ','Khác') DEFAULT 'Nam',
        dia_chi            TEXT,
        cccd               VARCHAR(20),
        avatar             VARCHAR(255),
        ma_khoa            INT NOT NULL,
        chuc_vu            VARCHAR(100),
        trinh_do           VARCHAR(100),
        bo_mon             VARCHAR(100),
        mon_giang_day      TEXT,
        trang_thai         ENUM('Đang công tác','Nghỉ phép','Nghỉ việc') DEFAULT 'Đang công tác',
        ngay_vao_truong    DATE,
        password           VARCHAR(255) NOT NULL,
        quyen              ENUM('Admin','GiangVien') DEFAULT 'GiangVien',
        trang_thai_dang_nhap ENUM('ON','OFF') DEFAULT 'OFF',
        FOREIGN KEY (ma_khoa) REFERENCES {$wpdb->prefix}tbl_khoa(ma_khoa) ON DELETE CASCADE
    ) {$charset_collate};
    ";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
