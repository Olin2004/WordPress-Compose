<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'sm_register_lich_day_menu' );

function sm_register_lich_day_menu() {
    add_menu_page(
        'Quản lý lịch dạy',
        'Lịch Dạy',
        'manage_options',
        'lich-day-management',
        'sm_render_lich_day_admin_page',
        'dashicons-calendar',
        22
    );
}

function sm_render_lich_day_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_lich_day';
    $tbl_gv = $wpdb->prefix . 'tbl_giangvien';
    $tbl_mh = $wpdb->prefix . 'tbl_mon_hoc';
    $tbl_phong = $wpdb->prefix . 'tbl_phong';

    $ds_giang_vien = $wpdb->get_results( "SELECT ma_gv, ho_ten FROM {$tbl_gv}" );
    $ds_mon_hoc = $wpdb->get_results( "SELECT ma_mon_hoc, ten_mon_hoc FROM {$tbl_mh}" );
    $ds_phong = $wpdb->get_results( "SELECT ma_phong, ten_phong FROM {$tbl_phong}" );

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['action'] ) ) {
        $action = sanitize_text_field( $_POST['action'] );
        if ( $action === 'add' ) {
            $wpdb->insert( $table_name, [
                'ma_gv'     => sanitize_text_field( $_POST['ma_giang_vien'] ),
                'ma_mon_hoc' => sanitize_text_field( $_POST['ma_mon_hoc'] ),
                'ma_phong'   => sanitize_text_field( $_POST['ma_phong'] ),
                'ngay_day'   => sanitize_text_field( $_POST['ngay_day'] ),
                'tiet'       => sanitize_text_field( $_POST['tiet_bat_dau'] ),
            ] );
            wp_redirect( admin_url( 'admin.php?page=lich-day-management' ) );
            exit;
        }
        if ( $action === 'update' ) {
            $id = intval( $_POST['id'] );
            $wpdb->update(
                $table_name,
                [
                    'ma_gv'     => sanitize_text_field( $_POST['ma_giang_vien'] ),
                    'ma_mon_hoc' => sanitize_text_field( $_POST['ma_mon_hoc'] ),
                    'ma_phong'   => sanitize_text_field( $_POST['ma_phong'] ),
                    'ngay_day'   => sanitize_text_field( $_POST['ngay_day'] ),
                    'tiet'       => sanitize_text_field( $_POST['tiet_bat_dau'] ),
                ],
                [ 'ma_lich_day' => $id ]
            );
            wp_redirect( admin_url( 'admin.php?page=lich-day-management' ) );
            exit;
        }
        if ( $action === 'delete' ) {
            $wpdb->delete( $table_name, [ 'ma_lich_day' => intval( $_POST['id'] ) ] );
            wp_redirect( admin_url( 'admin.php?page=lich-day-management' ) );
            exit;
        }
    }

    if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {
        $id = intval( $_GET['id'] );
        $lich_day = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_lich_day = %d", $id ) );
        if ( $lich_day ) {
            ?>
            
            <div class="wrap fix_scander-admin" style="display: flex; justify-content: center; margin-top: 20px;">
    <div style="border: 2px solid black; padding: 30px; width: 600px; display: flex; flex-direction: column; align-items: center; background: transparent;
        backdrop-filter: blur(20px); border-radius: 10px; background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
        background-size: cover; 
        background-repeat: no-repeat;
        background-attachment: fixed; ">
        <h1 style="font-size: 30px; color: white; font-weight: bold; margin-bottom: 30px; text-align: center;">Chỉnh sửa lịch dạy</h1>
        <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật dữ liệu này?');" style="width: 100%;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo esc_attr( $lich_day->ma_lich_day ); ?>">
            <table class="form-table" style="width: 100%;">
                <tr>
                    <th><label for="ma_giang_vien">Giảng viên</label></th>
                    <td>
                        <select name="ma_giang_vien" required style="width: 100%;">
                            <option value="">Chọn giảng viên</option>
                            <?php foreach ( $ds_giang_vien as $gv ) : ?>
                                <option value="<?php echo esc_attr( $gv->ma_gv ); ?>" <?php selected( $lich_day->ma_gv, $gv->ma_gv ); ?>><?php echo esc_html( $gv->ho_ten ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ma_mon_hoc">Môn học</label></th>
                    <td>
                        <select name="ma_mon_hoc" style="width: 100%;">
                            <option value="">Chọn môn học</option>
                            <?php foreach ( $ds_mon_hoc as $mh ) : ?>
                                <option value="<?php echo esc_attr( $mh->ma_mon_hoc ); ?>" <?php selected( $lich_day->ma_mon_hoc, $mh->ma_mon_hoc ); ?>><?php echo esc_html( $mh->ten_mon_hoc ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ma_phong">Phòng</label></th>
                    <td>
                        <select name="ma_phong" style="width: 100%;">
                            <option value="">Chọn phòng</option>
                            <?php foreach ( $ds_phong as $phong ) : ?>
                                <option value="<?php echo esc_attr( $phong->ma_phong ); ?>" <?php selected( $lich_day->ma_phong, $phong->ma_phong ); ?>><?php echo esc_html( $phong->ten_phong ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ngay_day">Ngày dạy</label></th>
                    <td><input type="date" name="ngay_day" value="<?php echo esc_attr( $lich_day->ngay_day ); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <th><label for="tiet_bat_dau">Tiết bắt đầu</label></th>
                    <td><input type="text" name="tiet_bat_dau" value="<?php echo esc_attr( $lich_day->tiet ); ?>" required style="width: 100%;"></td>
                </tr>
            </table>
            <p style="margin-top: 20px; text-align: center;">
                <button type="submit" class="button button-primary">Cập nhật lịch dạy</button>
                <a href="<?php echo admin_url( 'admin.php?page=lich-day-management' ); ?>" class="button">Hủy</a>
            </p>
        </form>
    </div>
</div>

            <?php
            return;
        }
    }

    $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
    if ( $search !== '' ) {
        if ( is_numeric( $search ) ) {
            $ds_lich_day = $wpdb->get_results( $wpdb->prepare( "SELECT ld.*, gv.ho_ten AS ten_gv, mh.ten_mon_hoc AS ten_mh, p.ten_phong AS ten_phong FROM {$table_name} ld LEFT JOIN {$tbl_gv} gv ON ld.ma_gv = gv.ma_gv LEFT JOIN {$tbl_mh} mh ON ld.ma_mon_hoc = mh.ma_mon_hoc LEFT JOIN {$tbl_phong} p ON ld.ma_phong = p.ma_phong WHERE ld.ma_lich_day = %d", intval( $search ) ) );
        } else {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $ds_lich_day = $wpdb->get_results( $wpdb->prepare( "SELECT ld.*, gv.ho_ten AS ten_gv, mh.ten_mon_hoc AS ten_mh, p.ten_phong AS ten_phong FROM {$table_name} ld LEFT JOIN {$tbl_gv} gv ON ld.ma_gv = gv.ma_gv LEFT JOIN {$tbl_mh} mh ON ld.ma_mon_hoc = mh.ma_mon_hoc LEFT JOIN {$tbl_phong} p ON ld.ma_phong = p.ma_phong WHERE gv.ho_ten LIKE %s OR mh.ten_mon_hoc LIKE %s", $like, $like ) );
        }
    } else {
        $ds_lich_day = $wpdb->get_results( "SELECT ld.*, gv.ho_ten AS ten_gv, mh.ten_mon_hoc AS ten_mh, p.ten_phong AS ten_phong FROM {$table_name} ld LEFT JOIN {$tbl_gv} gv ON ld.ma_gv = gv.ma_gv LEFT JOIN {$tbl_mh} mh ON ld.ma_mon_hoc = mh.ma_mon_hoc LEFT JOIN {$tbl_phong} p ON ld.ma_phong = p.ma_phong" );
    }

    ?>
    <style>
    body{
        background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
        background-size: cover; 
        background-repeat: no-repeat;
        background-attachment: fixed; 
        z-index: -1;
    }
    .sm-lich-day-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border-radius: 5px;
        background: transparent;
        backdrop-filter: blur(20px);
        border: 3px solid white;
        box-shadow: 0 0 10 rgba(0, 0, 0, .2);
        corlor: #fff;
        border-radius: 10px;
    }

    .sm-lich-day-container h1 {
        text-align: center;
        color: #fff;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .sm-lich-day-container .search-form {
        text-align: center;
        margin-bottom: 15px;
    }

    .sm-lich-day-container .search-form input[type="search"] {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        width: 60%;
        max-width: 300px;
    }

    .sm-lich-day-container .search-form .button {
        padding: 8px 15px;
        margin-left: 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .sm-lich-day-container .search-form .button:first-of-type {
        background-color: #007bff;
        color:#333;
    }

    .sm-lich-day-container .search-form .button:last-of-type {
        background-color:rgb(255, 255, 255);
        color: #333;
    }

    .sm-lich-day-container .add-lich-day-form {
        margin-top: 20px;
        text-align: center;
    }

    .sm-lich-day-container .add-lich-day-form table {
        margin: 0 auto;
    }

    .sm-lich-day-container .add-lich-day-form th,
    .sm-lich-day-container .add-lich-day-form td {
        padding: 8px;
        text-align: left;
    }

    .sm-lich-day-container .add-lich-day-form input[type="text"],
    .sm-lich-day-container .add-lich-day-form input[type="date"],
    .sm-lich-day-container .add-lich-day-form input[type="number"],
    .sm-lich-day-container .add-lich-day-form select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .sm-lich-day-container .add-lich-day-form button[type="submit"] {
        background-color: #17a2b8;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-top: 15px;
    }

    .sm-lich-day-container h2 {
        margin-top: 30px;
        color: #333;
    }

    .sm-lich-day-container .wp-list-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .sm-lich-day-container .wp-list-table th,
    .sm-lich-day-container .wp-list-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .sm-lich-day-container .wp-list-table th {
        background-color: #f2f2f2;
    }

    .sm-lich-day-container .wp-list-table .button {
        padding: 5px 10px;
        margin-right: 5px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        text-decoration: none;
    }

    .sm-lich-day-container .wp-list-table .button:first-of-type {
        background-color: #007bff;
        color: white;
    }

    .sm-lich-day-container .wp-list-table .button-link-delete {
        background: none;
        color: #dc3545;
        border: none;
        padding: 5px 0;
        margin: 0;
        cursor: pointer;
        text-decoration: underline;
    }

    .sm-lich-day-container .go-back-button {
        display: inline-block;
        padding: 6px 12px;
        background-color: #f1f1f1;
        color: #0073aa;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .sm-lich-day-container .go-back-button:hover {
        background-color: #e2e2e2;
        color: #000;
        border-color: #999;
    }
    .back-to-home {
    position: fixed; /* Cố định vị trí trên màn hình */
    top: 20px; /* Cách mép trên 20px */
    left: 20px; /* Cách mép trái 20px */
    z-index: 999; /* Đảm bảo nút nằm trên các phần tử khác */
    }

    .back-to-home a {
        display: inline-block; 
        padding: 10px 15px;
        background-color:rgb(255, 255, 255);
        color: #0073aa;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s ease;
        margin: 50px 200px;
    }

    .back-to-home a:hover {
        background-color: #ddd;
        color: #000;
    }


    </style>
    <div class="back-to-home">

        <a href="<?php echo esc_url( admin_url('admin.php?page=qlhs_dashboard') ); ?>" title="Trở về Trang chủ">
            &larr; Trang chủ
        </a>
    </div>
    <div class="sm-lich-day-container">
        
        <h1>Quản lý lịch dạy</h1>
        
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="lich-day-management">
            <input type="search" name="s" placeholder="Tìm kiếm theo ID, tên giảng viên hoặc môn học…" value="<?php echo esc_attr( $search ); ?>">
            <button class="button">Tìm kiếm</button>
            <a class="button" href="<?php echo admin_url( 'admin.php?page=lich-day-management' ); ?>">Tải lại</a>
        </form>

        <form method="post" class="add-lich-day-form">
            <input type="hidden" name="action" value="add">
            <table class="form-table">
                <tr>
                    <th><label for="ma_giang_vien">Giảng viên</label></th>
                    <td>
                        <select name="ma_giang_vien" required>
                            <option value="">Chọn giảng viên</option>
                            <?php foreach ( $ds_giang_vien as $gv ) : ?>
                                <option value="<?php echo esc_attr( $gv->ma_gv ); ?>"><?php echo esc_html( $gv->ho_ten ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ma_mon_hoc">Môn học</label></th>
                    <td>
                        <select name="ma_mon_hoc">
                            <option value="">Chọn môn học</option>
                            <?php foreach ( $ds_mon_hoc as $mh ) : ?>
                                <option value="<?php echo esc_attr( $mh->ma_mon_hoc ); ?>"><?php echo esc_html( $mh->ten_mon_hoc ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ma_phong">Phòng</label></th>
                    <td>
                        <select name="ma_phong">
                            <option value="">Chọn phòng</option>
                            <?php foreach ( $ds_phong as $phong ) : ?>
                                <option value="<?php echo esc_attr( $phong->ma_phong ); ?>"><?php echo esc_html( $phong->ten_phong ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="ngay_day">Ngày dạy</label></th>
                    <td><input type="date" name="ngay_day"></td>
                </tr>
                <tr>
                    <th><label for="tiet_bat_dau">Tiết</label></th>
                    <td><input type="text" name="tiet_bat_dau" required></td>
                </tr>
            </table>
            <button type="submit" class="button button-primary">Thêm lịch dạy</button>
        </form>
    </div>

    <h2 style= "color: white; font-size: 20px;">Danh sách lịch dạy</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Giảng viên</th>
                <th>Môn học</th>
                <th>Phòng</th>
                <th>Ngày dạy</th>
                <th>Tiết</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $ds_lich_day ) : ?>
                <?php foreach ( $ds_lich_day as $lich_day ) : ?>
                    <tr>
                        <td><?php echo esc_html( $lich_day->ma_lich_day ); ?></td>
                        <td><?php echo esc_html( $lich_day->ten_gv ); ?></td>
                        <td><?php echo esc_html( $lich_day->ten_mh ); ?></td>
                        <td><?php echo esc_html( $lich_day->ten_phong ); ?></td>
                        <td><?php echo esc_html( $lich_day->ngay_day ); ?></td>
                        <td><?php echo esc_html( $lich_day->tiet ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=lich-day-management&action=edit&id=' . $lich_day->ma_lich_day ); ?>" class="button">Chỉnh sửa</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch dạy này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo esc_attr( $lich_day->ma_lich_day ); ?>">
                                <button type="submit" class="button button-link-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="8">Chưa có lịch dạy nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
}