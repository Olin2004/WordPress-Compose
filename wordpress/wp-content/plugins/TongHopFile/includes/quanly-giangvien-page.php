<?php
if (!ob_start()) {
    ob_start();
}

function cm_register_giangvien_menu() {
    add_menu_page(
        'Quản lý giảng viên',
        'Giảng viên',
        'manage_options',
        'giangvien-management',
        'cm_render_giangvien_admin_page',
        'dashicons-admin-users',
        22
    );
}
add_action('admin_menu', 'cm_register_giangvien_menu');

function cm_render_giangvien_admin_page() {
    global $wpdb;
    $table_giangvien = $wpdb->prefix . 'tbl_giangvien';
    $table_khoa = $wpdb->prefix . 'tbl_khoa';

    // Xử lý POST thêm/sửa/xóa
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $wpdb->insert($table_giangvien, [
                'ho_ten' => sanitize_text_field($_POST['ho_ten']),
                'email' => sanitize_email($_POST['email']),
                'so_dien_thoai' => sanitize_text_field($_POST['so_dien_thoai']),
                'ngay_sinh' => sanitize_text_field($_POST['ngay_sinh']),
                'gioi_tinh' => sanitize_text_field($_POST['gioi_tinh']),
                'dia_chi' => sanitize_textarea_field($_POST['dia_chi']),
                'cccd' => sanitize_text_field($_POST['cccd']),
                'avatar' => sanitize_text_field($_POST['avatar']),
                'ma_khoa' => intval($_POST['ma_khoa']),
                'chuc_vu' => sanitize_text_field($_POST['chuc_vu']),
                'trinh_do' => sanitize_text_field($_POST['trinh_do']),
                'bo_mon' => sanitize_text_field($_POST['bo_mon']),
                'mon_giang_day' => sanitize_textarea_field($_POST['mon_giang_day']),
                'trang_thai' => sanitize_text_field($_POST['trang_thai']),
                'ngay_vao_truong' => sanitize_text_field($_POST['ngay_vao_truong']),
                'password' => wp_hash_password($_POST['password']),
                'quyen' => sanitize_text_field($_POST['quyen'] ?? 'giangvien')
            ]);
            wp_redirect(admin_url('admin.php?page=giangvien-management'));
            exit;
        } elseif ($_POST['action'] === 'update') {
            $id = intval($_POST['id']);
            $update_data = [
                'ho_ten' => sanitize_text_field($_POST['ho_ten']),
                'email' => sanitize_email($_POST['email']),
                'so_dien_thoai' => sanitize_text_field($_POST['so_dien_thoai']),
                'ngay_sinh' => sanitize_text_field($_POST['ngay_sinh']),
                'gioi_tinh' => sanitize_text_field($_POST['gioi_tinh']),
                'dia_chi' => sanitize_textarea_field($_POST['dia_chi']),
                'cccd' => sanitize_text_field($_POST['cccd']),
                'avatar' => sanitize_text_field($_POST['avatar']),
                'ma_khoa' => intval($_POST['ma_khoa']),
                'chuc_vu' => sanitize_text_field($_POST['chuc_vu']),
                'trinh_do' => sanitize_text_field($_POST['trinh_do']),
                'bo_mon' => sanitize_text_field($_POST['bo_mon']),
                'mon_giang_day' => sanitize_textarea_field($_POST['mon_giang_day']),
                'trang_thai' => sanitize_text_field($_POST['trang_thai']),
                'ngay_vao_truong' => sanitize_text_field($_POST['ngay_vao_truong']),
                'quyen' => sanitize_text_field($_POST['quyen'] ?? 'giangvien')
            ];
            if (!empty($_POST['password'])) {
                $update_data['password'] = wp_hash_password($_POST['password']);
            }
            $wpdb->update($table_giangvien, $update_data, ['ma_gv' => $id]);
            wp_redirect(admin_url('admin.php?page=giangvien-management'));
            exit;
        } elseif ($_POST['action'] === 'delete') {
            $wpdb->delete($table_giangvien, ['ma_gv' => intval($_POST['id'])]);
            wp_redirect(admin_url('admin.php?page=giangvien-management'));
            exit;
        }
    }

    // Nếu là chỉnh sửa
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $giangvien = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_giangvien WHERE ma_gv = %d", $id));
        $khoa = $wpdb->get_results("SELECT * FROM $table_khoa");
        if ($giangvien) {
            ?>
            <div class="wrap">
                <h1 style="font-weight: bold; text-align: center; color: #1a3c34; font-size: 2em; margin-bottom: 20px;">Chỉnh sửa giảng viên</h1>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $giangvien->ma_gv; ?>">
                    <?php cm_render_giangvien_form($giangvien, $khoa); ?>
                    <p>
                        <button type="submit" class="button button-primary">Cập nhật</button>
                        <a href="<?php echo admin_url('admin.php?page=giangvien-management'); ?>" class="button">Hủy</a>
                    </p>
                </form>
            </div>
            <?php
            return;
        }
    }

    // Hiển thị danh sách + form thêm
    $search = '';
    if (isset($_GET['s'])) {
        $search = sanitize_text_field($_GET['s']);
    }

    if ($search !== '') {
        if (is_numeric($search)) {
            $ds_giangvien = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_giangvien WHERE ma_gv = %d", intval($search)));
        } else {
            $ds_giangvien = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_giangvien WHERE ho_ten LIKE %s", '%' . $wpdb->esc_like($search) . '%'));
        }
    } else {
        $ds_giangvien = $wpdb->get_results("SELECT * FROM $table_giangvien");
    }

    $khoa = $wpdb->get_results("SELECT * FROM $table_khoa");
    ?>
    <div class="back-to-home">

    <a href="<?php echo esc_url( admin_url('admin.php?page=qlhs_dashboard') ); ?>" title="Trở về Trang chủ">
        &larr; Trang chủ
    </a>
    </div>
    <div class="wrap">
        <h1>Quản lý giảng viên</h1>

        <form method="GET" action="">
            <input type="hidden" name="page" value="giangvien-management">
            <input type="text" name="s" placeholder="Nhập ID hoặc tên..." value="<?php echo esc_attr($search); ?>">
            <button type="submit" class="button">Tìm kiếm</button>
            <a href="<?php echo admin_url('admin.php?page=giangvien-management'); ?>" class="button">Tải lại</a>
        </form>

        <h2>Thêm mới giảng viên</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <?php cm_render_giangvien_form(null, $khoa); ?>
            <p><button type="submit" class="button button-primary">Thêm mới</button></p>
        </form>

        <h2>Danh sách giảng viên</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ds_giangvien)): ?>
                    <?php foreach ($ds_giangvien as $giangvien): ?>
                        <tr>
                            <td><?php echo $giangvien->ma_gv; ?></td>
                            <td><?php echo esc_html($giangvien->ho_ten); ?></td>
                            <td><?php echo esc_html($giangvien->email); ?></td>
                            <td><?php echo esc_html($giangvien->so_dien_thoai); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=giangvien-management&action=edit&id=' . $giangvien->ma_gv); ?>" class="button">Chỉnh sửa</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn xóa?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $giangvien->ma_gv; ?>">
                                    <button type="submit" class="button button-link-delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Không có giảng viên nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Hàm render form chung
function cm_render_giangvien_form($giangvien = null, $khoa = []) {
?>
    <table class="form-table">
        <tr>
            <th>Họ tên</th>
            <td><input type="text" name="ho_ten" value="<?php echo esc_attr($giangvien->ho_ten ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" value="<?php echo esc_attr($giangvien->email ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Số điện thoại</th>
            <td><input type="text" name="so_dien_thoai" value="<?php echo esc_attr($giangvien->so_dien_thoai ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Ngày sinh</th>
            <td><input type="date" name="ngay_sinh" value="<?php echo esc_attr($giangvien->ngay_sinh ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Giới tính</th>
            <td>
                <select name="gioi_tinh" required>
                    <option value="Nam" <?php selected($giangvien->gioi_tinh ?? '', 'Nam'); ?>>Nam</option>
                    <option value="Nữ" <?php selected($giangvien->gioi_tinh ?? '', 'Nữ'); ?>>Nữ</option>
                    <option value="Khác" <?php selected($giangvien->gioi_tinh ?? '', 'Khác'); ?>>Khác</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Địa chỉ</th>
            <td><textarea name="dia_chi" required><?php echo esc_textarea($giangvien->dia_chi ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <th>CCCD</th>
            <td><input type="text" name="cccd" value="<?php echo esc_attr($giangvien->cccd ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Avatar URL</th>
            <td><input type="text" name="avatar" value="<?php echo esc_attr($giangvien->avatar ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Khoa</th>
            <td>
                <select name="ma_khoa" required>
                    <?php foreach ($khoa as $k): ?>
                        <option value="<?php echo $k->ma_khoa; ?>" <?php selected($giangvien->ma_khoa ?? '', $k->ma_khoa); ?>><?php echo esc_html($k->ten_khoa); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Chức vụ</th>
            <td><input type="text" name="chuc_vu" value="<?php echo esc_attr($giangvien->chuc_vu ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Trình độ</th>
            <td><input type="text" name="trinh_do" value="<?php echo esc_attr($giangvien->trinh_do ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Bộ môn</th>
            <td><input type="text" name="bo_mon" value="<?php echo esc_attr($giangvien->bo_mon ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Môn giảng dạy</th>
            <td><textarea name="mon_giang_day" required><?php echo esc_textarea($giangvien->mon_giang_day ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>
                <select name="trang_thai" required>
                    <option value="Đang công tác" <?php selected($giangvien->trang_thai ?? '', 'Đang công tác'); ?>>Đang công tác</option>
                    <option value="Nghỉ hưu" <?php selected($giangvien->trang_thai ?? '', 'Nghỉ hưu'); ?>>Nghỉ hưu</option>
                    <option value="Nghỉ phép" <?php selected($giangvien->trang_thai ?? '', 'Nghỉ phép'); ?>>Nghỉ phép</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Ngày vào trường</th>
            <td><input type="date" name="ngay_vao_truong" value="<?php echo esc_attr($giangvien->ngay_vao_truong ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th>Password</th>
            <td><input type="password" name="password" value="<?php echo esc_attr($giangvien->password ?? ''); ?>"></td>
        </tr>
        <tr>
            <th>Quyền</th>
            <td>
                <select name="quyen">
                    <option value="giangvien" <?php selected($giangvien->quyen ?? 'giangvien', 'giangvien'); ?>>Giảng viên</option>
                    <option value="admin" <?php selected($giangvien->quyen ?? 'giangvien', 'admin'); ?>>Admin</option>
                </select>
            </td>
        </tr>
    </table>
    <style>
/* Trang tổng thể */

body{
    background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
    background-size: cover; /* Để hình nền phủ toàn bộ trang */
    background-repeat: no-repeat; /* Ngăn hình nền lặp lại */
    background-attachment: fixed;
}
.wrap {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Tiêu đề */

.wrap h1, .wrap h2 {
    color: #1d2327;
    font-weight: 600;
    margin-bottom: 20px;
    
}

/* Bảng Form */
.form-table {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.form-table th {
    text-align: left;
    padding: 12px 10px;
    background-color: #f0f0f0;
    color: #222;
    border-radius: 5px 0 0 5px;
}

.form-table td {
    padding: 12px 10px;
    border-top: 1px solid #eee;
}

.form-table input[type="text"],
.form-table input[type="email"],
.form-table input[type="date"],
.form-table input[type="password"],
.form-table select,
.form-table textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    background: #f9f9f9;
    transition: border-color 0.3s, background-color 0.3s;
}

.form-table input:focus,
.form-table select:focus,
.form-table textarea:focus {
    border-color: #007cba;
    background: #fff;
    outline: none;
}

/* Button chung */
.button {
    background-color: #007cba;
    color: #fff;
    border: none;
    padding: 10px 18px;
    font-size: 14px;
    border-radius: 6px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #005a9e;
}

/* Button xóa */
.button-link-delete {
    background-color: #d63638;
    color: #fff;
    padding: 10px 18px;
    border-radius: 6px;
    border: none;
    transition: background-color 0.3s;
}

.button-link-delete:hover {
    background-color: #a50002;
}

/* Bảng danh sách */
.wp-list-table {
    background: #fff;
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.wp-list-table th, 
.wp-list-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.wp-list-table th {
    background-color: #f0f0f0;
    font-weight: 600;
}

.wp-list-table tbody tr:hover {
    background-color: #f9f9f9;
}

/* Link trở về trang chủ */
.back-to-home {
    margin: 15px 0;
}

.back-to-home a {
    text-decoration: none;
    color: #0073aa;
}

.back-to-home a:hover {
    text-decoration: underline;
}
</style>

<?php
}
