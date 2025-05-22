<?php
/*
Plugin Name: Hệ thống quản lý Teachers
Description: Plugin hệ thống quản lý Teachers và Học sinh.
Version: 1.1
Author: Team 3
*/

if (!defined('ABSPATH')) {
    exit;
}

define('SM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Định nghĩa hàm kiểm tra đăng nhập (từ plugin Quản lý Học sinh)
function qlhs_check_login() {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url());
        exit;
    }
}

// Kích hoạt / hủy kích hoạt plugin
register_activation_hook(__FILE__, 'sm_activate_plugin');

function sm_activate_plugin() {
    ob_start();
    // Bật buffer để ngăn mọi đầu ra từ dbDelta()
    require_once SM_PLUGIN_DIR . 'database/create-giang-vien-table.php';
    sm_create_giang_vien_table();
    require_once SM_PLUGIN_DIR . 'database/create-khoa-table.php';
    sm_create_khoa_table();
    require_once SM_PLUGIN_DIR . 'database/create-phong-table.php';
    sm_create_phong_table();
    require_once SM_PLUGIN_DIR . 'database/create-mon-hoc-table.php';
    sm_create_mon_hoc_table();
    require_once SM_PLUGIN_DIR . 'database/create-lich-day-table.php';
    sm_create_lich_day_table();
    ob_end_clean();
}

// Gọi các tệp phụ từ TONGHOPFILE
if (is_admin()) {
    require_once SM_PLUGIN_DIR . 'includes/login.php';
    require_once SM_PLUGIN_DIR . 'includes/dashboard.php';
    require_once SM_PLUGIN_DIR . 'includes/monhoc.php';
    require_once SM_PLUGIN_DIR . 'includes/phong.php';
    require_once SM_PLUGIN_DIR . 'includes/giang-vien-admin-page.php';
    require_once SM_PLUGIN_DIR . 'includes/khoa-admin-page.php';
    require_once SM_PLUGIN_DIR . 'includes/lich-day-admin-page.php';
    require_once SM_PLUGIN_DIR . 'includes/quanly-giangvien-page.php';
}

// Tích hợp CSS và JS (kết hợp từ plugin Quản lý Học sinh)
add_action('admin_enqueue_scripts', 'sm_qlhs_enqueue_assets');
add_action('wp_enqueue_scripts', 'sm_qlhs_enqueue_assets');
function sm_qlhs_enqueue_assets($hook) {
    $admin_pages = [
        'toplevel_page_qlhs_dashboard',
        'qlhs_dashboard_page_phong-management',
        'qlhs_dashboard_page_mon-hoc-management',
        'qlhs_dashboard_page_giangvien-management',
        'qlhs_dashboard_page_lich-day-management',
        'qlhs_dashboard_page_khoa-management',
        'qlhs_dashboard_page_qlhs_baocao' // Thêm trang Báo cáo tổng số
    ];

    if (is_admin() && !in_array($hook, $admin_pages)) {
        return;
    }

    // Tải CSS (style.css của TONGHOPFILE đã hợp nhất với style.css của Quản lý Học sinh)
    wp_enqueue_style('sm-qlhs-style', plugins_url('assets/style.css', __FILE__));

    // Tải JS (script.js của Quản lý Học sinh)
    wp_enqueue_script('sm-qlhs-script', plugins_url('assets/script.js', __FILE__), ['jquery'], null, true);
}