<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Hàm xử lý đăng xuất và cập nhật trạng thái
function sm_custom_logout() {
    global $wpdb;
    $tbl_gv = $wpdb->prefix . 'tbl_giangvien';

    // Lấy thông tin giảng viên đang đăng nhập
    $gv = $wpdb->get_row( $wpdb->prepare(
        "SELECT ma_gv
        FROM {$tbl_gv}
        WHERE trang_thai_dang_nhap = %s
        LIMIT 1",
        'ON'
    ) );

    if ($gv && isset($gv->ma_gv)) {
        // Cập nhật trạng thái đăng nhập thành 'OFF'
        $wpdb->update(
            $tbl_gv,
            ['trang_thai_dang_nhap' => 'OFF'],
            ['ma_gv' => $gv->ma_gv]
        );
    }

    // Chuyển hướng đến trang đăng nhập
    // wp_redirect(home_url('/dangNhap')); 
    wp_redirect( admin_url( 'admin.php?page=user-management' ) ); // Cập nhật slug cho trang thông tin giảng viên của bạn
    exit;
}
add_action('admin_init', 'sm_handle_custom_logout');
function sm_handle_custom_logout() {
    if (isset($_GET['sm_logout'])) {
        sm_custom_logout();
    }
}

function sm_register_giangvien_menu() {
    add_menu_page(
        'Thông tin giảng viên',
        'Giảng viên',
        'manage_options',
        'teacher-management',
        'sm_render_giangvien_page',
        'dashicons-welcome-learn-more',
        21
    );
}
add_action('admin_menu', 'sm_register_giangvien_menu');

function sm_render_giangvien_page() {
    global $wpdb;
    $tbl_gv   = $wpdb->prefix . 'tbl_giangvien';
    $tbl_khoa = $wpdb->prefix . 'tbl_khoa';
    $tbl_lich_day = $wpdb->prefix . 'tbl_lich_day';

    // Truy vấn lấy 1 giảng viên đang đăng nhập (trạng thái ON)
    $gv = $wpdb->get_row( $wpdb->prepare(
        "SELECT gv.*, k.ten_khoa
        FROM {$tbl_gv} AS gv
        LEFT JOIN {$tbl_khoa} AS k ON gv.ma_khoa = k.ma_khoa
        WHERE gv.trang_thai_dang_nhap = %s
        LIMIT 1",
        'ON'
    ) );

    function _val($x) {
        return isset($x) ? esc_html($x) : '';
    }

    $avatar = isset($gv->avatar) && $gv->avatar
                ? esc_url($gv->avatar)
                : SM_PLUGIN_URL . 'assets/img/default-avatar.png';

    $is_edit = isset($_GET['action']) && $_GET['action'] === 'edit';
    $show_schedule = isset($_GET['action']) && $_GET['action'] === 'schedule';

    // Cập nhật thông tin cá nhân
    if (isset($_POST['update_info'])) {
        $wpdb->update($tbl_gv, [
            'so_dien_thoai' => sanitize_text_field($_POST['so_dien_thoai']),
            'ngay_sinh'     => sanitize_text_field($_POST['ngay_sinh']),
            'gioi_tinh'     => sanitize_text_field($_POST['gioi_tinh']),
            'dia_chi'       => sanitize_text_field($_POST['dia_chi']),
            'cccd'          => sanitize_text_field($_POST['cccd']),
        ], ['ma_gv' => $gv->ma_gv]); // Cập nhật dựa trên ma_gv của giảng viên tìm được
        echo '<div class="notice notice-success is-dismissible"><p>Đã cập nhật thông tin cá nhân.</p></div>';
        echo '<script type="text/javascript">
              window.location.href = "?page=teacher-management";
              </script>';
        exit;
    }
    ?>
    <div class="gv-wrapper">
        <div class="gv-main-box">
            <?php if ($is_edit): ?>
                <h2 class="gv-title">CHỈNH SỬA THÔNG TIN CÁ NHÂN</h2>
                <form method="post" class="gv-title-fix-infor" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật thông tin cá nhân?');">
                    <div class="gv_fix_infor">
                    <table>
                        <tr><td>SĐT:</td><td><input type="text" name="so_dien_thoai" value="<?php echo _val($gv->so_dien_thoai ?? ''); ?>"></td></tr>
                        <tr><td>Ngày sinh:</td><td><input type="date" name="ngay_sinh" value="<?php echo _val($gv->ngay_sinh ?? ''); ?>"></td></tr>
                        <tr><td>Giới tính:</td><td>
                            <select name="gioi_tinh">
                                <option value="Nam" <?php selected($gv->gioi_tinh ?? '', 'Nam'); ?>>Nam</option>
                                <option value="Nữ" <?php selected($gv->gioi_tinh ?? '', 'Nữ'); ?>>Nữ</option>
                                <option value="Khác" <?php selected($gv->gioi_tinh ?? '', 'Khác'); ?>>Khác</option>
                            </select>
                        </td></tr>
                        <tr><td>Địa chỉ:</td><td><input type="text" name="dia_chi" value="<?php echo _val($gv->dia_chi ?? ''); ?>"></td></tr>
                        <tr><td>CCCD:</td><td><input type="text" name="cccd" value="<?php echo _val($gv->cccd ?? ''); ?>"></td></tr>
                    </table>
                    <p><button type="submit" name="update_info" class="button button-primary">Cập nhật thông tin</button></p>
                    </div>
                </form>
            <?php elseif ($show_schedule): ?>
                <h2 class="gv-title">LỊCH DẠY CỦA BẠN</h2>
                <div class="gv-schedule-section">
                    <?php
                    if ($gv && isset($gv->ma_gv)) {
                        $lich_day = $wpdb->get_results( $wpdb->prepare(
                            "SELECT ld.*, mh.ten_mon_hoc, p.ten_phong
                            FROM {$tbl_lich_day} AS ld
                            LEFT JOIN {$wpdb->prefix}tbl_mon_hoc AS mh ON ld.ma_mon_hoc = mh.ma_mon_hoc
                            LEFT JOIN {$wpdb->prefix}tbl_phong AS p ON ld.ma_phong = p.ma_phong
                            WHERE ld.ma_gv = %d
                            ORDER BY ld.ngay_day ASC, ld.tiet ASC",
                            $gv->ma_gv
                        ) );

                        if ($lich_day) {
                            echo '<table>';
                            echo '<thead><tr><th>Ngày</th><th>Tiết</th><th>Môn học</th><th>Phòng</th><th>Ghi chú</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($lich_day as $lich) {
                                echo '<tr>';
                                echo '<td>' . esc_html($lich->ngay_day) . '</td>';
                                echo '<td>' . esc_html($lich->tiet) . '</td>';
                                echo '<td>' . esc_html($lich->ten_mon_hoc) . '</td>';
                                echo '<td>' . esc_html($lich->ten_phong) . '</td>';
                                echo '<td>' . esc_html($lich->ghi_chu) . '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<p>Không có lịch dạy.</p>';
                        }
                    } else {
                        echo '<p>Không tìm thấy thông tin giảng viên.</p>';
                    }
                    ?>
                </div>
            <?php else: ?>
                <h2 class="gv-title">THÔNG TIN GIẢNG VIÊN</h2>
                <div class="gv-profile-section">
                    <div class="gv-avatar">
                        <img src="<?php echo $avatar; ?>" alt="Avatar">
                        <p><strong>MÃ GV: <?php echo _val($gv->ma_gv ?? ''); ?></strong></p>
                    </div>
                    <div class="gv-info">
                        <h3 style= "margin-left: 8px;"><?php echo _val($gv->ho_ten ?? ''); ?></h3>
                        <table>
                            <tr><td>Trạng thái:</td><td><span class="active"><?php echo _val($gv->trang_thai ?? ''); ?></span></td></tr>
                            <tr><td>Ngày vào trường:</td><td><?php echo _val($gv->ngay_vao_truong ?? ''); ?></td></tr>
                            <tr><td>Chức vụ:</td><td><?php echo _val($gv->chuc_vu ?? ''); ?></td></tr>
                            <tr><td>Khoa:</td><td><?php echo _val($gv->ten_khoa ?? ''); ?></td></tr>
                            <tr><td>Bộ môn:</td><td><?php echo _val($gv->bo_mon ?? ''); ?></td></tr>
                            <tr><td>Trình độ:</td><td><?php echo _val($gv->trinh_do ?? ''); ?></td></tr>
                            <tr><td>Môn giảng dạy:</td><td><?php echo _val($gv->mon_giang_day ?? ''); ?></td></tr>
                        </table>
                    </div>
                </div>

                <h2 class="gv-title">THÔNG TIN CÁ NHÂN</h2>
                <div class="gv-personal-section">
                    <table>
                        <tr><td>Email:</td><td><?php echo _val($gv->email ?? ''); ?></td></tr>
                        <tr><td>SĐT:</td><td><?php echo _val($gv->so_dien_thoai ?? ''); ?></td></tr>
                        <tr><td>Ngày sinh:</td><td><?php echo _val($gv->ngay_sinh ?? ''); ?></td></tr>
                        <tr><td>Giới tính:</td><td><?php echo _val($gv->gioi_tinh ?? ''); ?></td></tr>
                        <tr><td>Địa chỉ:</td><td><?php echo _val($gv->dia_chi ?? ''); ?></td></tr>
                        <tr><td>CCCD:</td><td><?php echo _val($gv->cccd ?? ''); ?></td></tr>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="gv-sidebar">
            <div class="gv-greeting">
                <p>Xin chào<br><strong><?php echo _val($gv->ho_ten ?? ''); ?></strong></p>
                <a href="<?php echo admin_url('admin.php?page=teacher-management&sm_logout=1'); ?>" class="button-logout">Đăng xuất</a>
            </div>
            <div class="gv-panel">
                <h3>CHỨC NĂNG</h3>
                <ul>
                    <li><a href="?page=teacher-management">Thông tin giảng viên</a></li>
                    <li><a href="?page=teacher-management&action=edit">Chỉnh sửa thông tin</a></li>
                    <li><a href="?page=teacher-management&action=schedule">Xem lịch dạy</a></li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        .gv-wrapper {
            display: flex;
            gap: 20px;
            font-family: Arial, sans-serif;
        }
        .gv-main-box {
            flex: 3;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .gv-title {
            background: #2d1582;
            color: white;
            padding: 8px 12px;
            font-size: 16px;
            margin-bottom: 15px;
            margin: auto;
            text-align: center;
        }
        .gv-profile-section {
            display: flex;
            gap: 20px;
        }
        .gv-avatar {
            text-align: center;
            flex: 1;
        }
        .gv-avatar img {
            width: 160px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .gv-info {
            flex: 2;
        }
        .gv-info table, .gv-personal-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .gv-info td, .gv-personal-section td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
        }
        .gv-info td:first-child, .gv-personal-section td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .gv-personal-section {
            margin-top: 10px;
        }
        .gv-sidebar {
            flex: 1;
        }
        .gv-greeting {
            background: #f3f3f3;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .button-logout {
            background: #f44336;
            color: white;
            padding: 6px 12px;
            display: inline-block;
            text-decoration: none;
            border-radius: 4px;
        }
        .gv-panel {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .gv-panel ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .gv-panel li {
            margin-bottom: 8px;
        }
        .gv-panel a {
            text-decoration: none;
            color: #333;
        }
        .active {
            color: red;
            font-weight: bold;
        }
        .gv-schedule-section {
            margin-top: 20px;
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .gv-schedule-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .gv-schedule-section th, .gv-schedule-section td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        .gv-schedule-section th {
            background-color: #f2f2f2;
        }
        .gv_fix_infor{
            margin-top: 20px;
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            
        }
        .gv-title-fix-infor{
            align-items: center;
            display: flex;
            justify-content: center;
            flex-direction: column;
            margin: auto;
        }

        .gv-title-fix-infor .button-primary{
            margin-left: 70px;
        }
    </style>
    <?php
}