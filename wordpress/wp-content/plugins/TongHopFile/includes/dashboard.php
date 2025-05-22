<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'qlhs_register_menu');
function qlhs_register_menu() {
    add_menu_page(
        'Quản lý Giảng Viên',
        'QL Học sinh',
        'manage_options',
        'qlhs_dashboard',
        'qlhs_dashboard_page',
        'dashicons-welcome-learn-more',
        6
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Quản lý Phòng học',
        'Phòng học',
        'manage_options',
        'phong-management',
        'qlhs_phong_page'
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Quản lý Môn học',
        'Môn học',
        'manage_options',
        'mon-hoc-management',
        'qlhs_monhoc_page'
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Quản lý Giảng viên',
        'Giảng viên',
        'manage_options',
        'giangvien-management',
        'qlhs_giangvien_page'
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Quản lý Lịch dạy',
        'Lịch dạy',
        'manage_options',
        'lich-day-management',
        'sm_lich_day_page'
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Quản lý Khoa',
        'Khoa',
        'manage_options',
        'khoa-management',
        'sm_khoa_page'
    );

    add_submenu_page(
        'qlhs_dashboard',
        'Báo cáo tổng số',
        'Báo cáo tổng số',
        'manage_options',
        'qlhs_baocao',
        'qlhs_baocao_page'
    );
}

function qlhs_dashboard_page() {
    // Kiểm tra đăng nhập
    qlhs_check_login();
    ?>
    <div class="wrap container">
        <h1>Quản lý giảng viên - Dashboard</h1>
        <p>Chào mừng bạn đến với hệ thống quản lý giảng viên.</p>

        <div class="function-section">
            <h2>Chức năng</h2>
            <ul class="function-list">
                <li><a href="<?php echo admin_url('admin.php?page=giangvien-management'); ?>">Quản lý Giảng viên</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=mon-hoc-management'); ?>">Quản lý Môn học</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=phong-management'); ?>">Quản lý Phòng học</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=lich-day-management'); ?>">Quản lý Lịch dạy</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=khoa-management'); ?>">Quản lý Khoa</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=qlhs_baocao'); ?>">Báo cáo tổng số</a></li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="<?php echo admin_url('admin.php?page=user-management&sm_logout=1'); ?>" class="btn logout">Đăng xuất</a>
        </div>
    </div>
    <?php
}

function qlhs_baocao_page() {
    qlhs_check_login();
    global $wpdb;

    // Lấy tổng số phòng, môn học, giảng viên
    $total_phong = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}phong");
    $total_monhoc = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mon_hoc");
    $total_giangvien = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}giangvien");

    // Tải Chart.js
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);

    // Truyền dữ liệu cho JavaScript
    wp_localize_script('sm-qlhs-script', 'qlhsBaocaoData', [
        'phong' => $total_phong,
        'monhoc' => $total_monhoc,
        'giangvien' => $total_giangvien
    ]);

    ?>
    <div class="wrap container">
        <h1>Báo cáo tổng số</h1>
        <div class="chart-container">
            <canvas id="baocaoChart"></canvas>
        </div>
        <a href="<?php echo admin_url('admin.php?page=qlhs_dashboard'); ?>" class="btn">Quay lại Dashboard</a>
        <a href="<?php echo admin_url('admin.php?page=user-management&sm_logout=1'); ?>" class="btn logout">Đăng xuất</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('baocaoChart').getContext('2d');
            const data = {
                labels: ['Phòng học', 'Môn học', 'Giảng viên'],
                datasets: [{
                    label: 'Tổng số',
                    data: [
                        qlhsBaocaoData.phong,
                        qlhsBaocaoData.monhoc,
                        qlhsBaocaoData.giangvien
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            };

            new Chart(ctx, {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Thống kê tổng số',
                            font: {
                                size: 18
                            }
                        }
                    }
                }
            });
        });
    </script>
    <?php
}
?>

<style>
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Body */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    /* Container chính */
    .container {
        background: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        margin: 0 auto;
        border: 1px solid #e0e0e0;
    }

    /* Tiêu đề */
    .container h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #1a3c34;
        font-size: 2.5em;
        font-weight: bold;
    }

    .container p {
        text-align: center;
        color: #555;
        margin-bottom: 30px;
        font-size: 1.2em;
    }

    /* Chức năng */
    .function-section {
        margin-top: 30px;
    }

    .function-section h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #1a3c34;
        font-size: 1.8em;
        border-bottom: 2px solid #1a3c34;
        padding-bottom: 10px;
    }

    .function-list {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .function-list li {
        flex: 1 1 200px;
        text-align: center;
    }

    .function-list li a {
        display: block;
        background: #4CAF50;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2em;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .function-list li a:hover {
        background: #45a049;
        transform: translateY(-3px);
    }

    /* Nút hành động */
    .action-buttons {
        margin-top: 40px;
        text-align: center;
    }

    .btn {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        font-size: 1em;
        text-decoration: none;
        transition: background 0.3s ease, transform 0.2s ease;
        margin: 5px;
    }

    .btn:hover {
        background: #45a049;
        transform: translateY(-2px);
    }

    /* Nút logout riêng */
    .btn.logout {
        background: #f44336;
    }

    .btn.logout:hover {
        background: #e53935;
    }

    /* Canvas cho biểu đồ */
    .chart-container {
        max-width: 500px;
        margin: 30px auto;
    }
</style>