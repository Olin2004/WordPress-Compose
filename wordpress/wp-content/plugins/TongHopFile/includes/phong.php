<?php
add_action( 'admin_menu', 'sm_register_phong_menu' );
function sm_register_phong_menu() {
    add_menu_page(
        'Quản lý phòng',
        'Phòng',
        'manage_options',
        'phong-management',
        'sm_render_phong_admin_page',
        'dashicons-admin-multisite',
        22
    );
}

function sm_render_phong_admin_page() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_phong';

    // Xử lý POST: add / update / delete
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['action'] ) ) {
        $action = sanitize_text_field( $_POST['action'] );
        if ( $action === 'add' ) {
            $wpdb->insert( $table_name, [
                'ten_phong'  => sanitize_text_field( $_POST['ten_phong'] ),
                'loai_phong' => sanitize_text_field( $_POST['loai_phong'] ),
                'ghi_chu'    => sanitize_textarea_field( $_POST['ghi_chu'] ),
            ] );
            wp_redirect( admin_url( 'admin.php?page=phong-management' ) );
            exit;
        }
        if ( $action === 'update' ) {
            $ma_phong = intval( $_POST['ma_phong'] );
            $wpdb->update(
                $table_name,
                [
                    'ten_phong'  => sanitize_text_field( $_POST['ten_phong'] ),
                    'loai_phong' => sanitize_text_field( $_POST['loai_phong'] ),
                    'ghi_chu'    => sanitize_textarea_field( $_POST['ghi_chu'] ),
                ],
                [ 'ma_phong' => $ma_phong ]
            );
            wp_redirect( admin_url( 'admin.php?page=phong-management' ) );
            exit;
        }
        if ( $action === 'delete' ) {
            $wpdb->delete( $table_name, [ 'ma_phong' => intval( $_POST['ma_phong'] ) ] );
            wp_redirect( admin_url( 'admin.php?page=phong-management' ) );
            exit;
        }
    }

    // Nếu là edit
    if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {
        $ma_phong = intval( $_GET['id'] );
        $phong    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_phong = %d", $ma_phong ) );
        if ( $phong ) {
            ?>
            <div class="wrap fix-khoa">
                <h1>Chỉnh sửa phòng</h1>
                <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật phòng này?');">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ma_phong" value="<?php echo esc_attr( $phong->ma_phong ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="ten_phong">Tên phòng</label></th>
                            <td><input type="text" name="ten_phong" value="<?php echo esc_attr( $phong->ten_phong ); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="loai_phong">Loại phòng</label></th>
                            <td>
                                <select name="loai_phong">
                                    <option value="Lý thuyết" <?php selected( $phong->loai_phong, 'Lý thuyết' ); ?>>Lý thuyết</option>
                                    <option value="Thực hành" <?php selected( $phong->loai_phong, 'Thực hành' ); ?>>Thực hành</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="ghi_chu">Ghi chú</label></th>
                            <td><textarea name="ghi_chu"><?php echo esc_textarea( $phong->ghi_chu ); ?></textarea></td>
                        </tr>
                    </table>
                    <p>
                        <button type="submit" class="button button-primary">Cập nhật phòng</button>
                        <a href="<?php echo admin_url( 'admin.php?page=phong-management' ); ?>" class="button">Hủy</a>
                    </p>
                </form>
            </div>
            <?php
            return;
        }
    }

    // Xử lý tìm kiếm
    $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
    if ( $search !== '' ) {
        if ( is_numeric( $search ) ) {
            $ds_phong = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_phong = %d", intval( $search ) ) );
        } else {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $ds_phong = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ten_phong LIKE %s", $like ) );
        }
    } else {
        $ds_phong = $wpdb->get_results( "SELECT * FROM {$table_name}" );
    }
    ?>
    <div class="wrap">
        <div class="go-back-wrapper">
            <a href="<?php echo admin_url('admin.php?page=qlhs_dashboard'); ?>" class="go-back-button">&#8592; Trở lại</a>
        </div>

        <div class="sm-box">
            <h1>Quản lý phòng</h1>

            <form method="get" class="search-form">
                <input type="hidden" name="page" value="phong-management">
                <input type="search" name="s" placeholder="Nhập mã hoặc tên phòng…" value="<?php echo esc_attr( $search ); ?>">
                <button class="button">Tìm kiếm</button>
                <a class="button" href="<?php echo admin_url( 'admin.php?page=phong-management' ); ?>">Tải lại</a>
            </form>

            <form method="post" class="add-khoa-form">
                <input type="hidden" name="action" value="add">
                <table class="form-table">
                    <tr>
                        <th><label for="ten_phong">Tên phòng</label></th>
                        <td><input type="text" name="ten_phong" required></td>
                    </tr>
                    <tr>
                        <th><label for="loai_phong">Loại phòng</label></th>
                        <td>
                            <select name="loai_phong">
                                <option value="Lý thuyết">Lý thuyết</option>
                                <option value="Thực hành">Thực hành</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="ghi_chu">Ghi chú</label></th>
                        <td><textarea name="ghi_chu"></textarea></td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary">Thêm phòng</button>
            </form>
        </div>

        <h2>Danh sách phòng</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Mã phòng</th>
                    <th>Tên phòng</th>
                    <th>Loại phòng</th>
                    <th>Ghi chú</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ( $ds_phong ) : ?>
                <?php foreach ( $ds_phong as $phong ) : ?>
                    <tr>
                        <td><?php echo esc_html( $phong->ma_phong ); ?></td>
                        <td><?php echo esc_html( $phong->ten_phong ); ?></td>
                        <td><?php echo esc_html( $phong->loai_phong ); ?></td>
                        <td><?php echo esc_html( $phong->ghi_chu ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=phong-management&action=edit&id=' . $phong->ma_phong ); ?>" class="button">Chỉnh sửa</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ma_phong" value="<?php echo esc_attr( $phong->ma_phong ); ?>">
                                <button type="submit" class="button button-link-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">Chưa có phòng nào.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- CSS giữ nguyên như cũ -->
    <style>
        body {
            background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .wrap .sm-box {
            border: 2px solid #fff;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            box-sizing: border-box;
            text-align: center;
            background: transparent;
            backdrop-filter: blur(10px);
        }
        .wrap .sm-box h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 27px;
            font-weight: bold;
            color: #333;
        }
        .wrap .sm-box .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }
        .wrap .sm-box .search-form input[type="search"] {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        .wrap .sm-box .search-form .button {
            padding: 8px 15px;
        }
        .wrap .sm-box .form-table th {
            width: 150px;
            padding-right: 10px;
            text-align: left;
        }
        .wrap .sm-box .form-table td input[type="text"],
        .wrap .sm-box .form-table td select,
        .wrap .sm-box .form-table td textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        .wrap .sm-box .form-table td textarea {
            min-height: 80px;
        }
        .go-back-wrapper {
            text-align: left;
            margin-bottom: 15px;
        }
        .go-back-button {
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
        .go-back-button:hover {
            background-color: #e2e2e2;
            color: #000;
            border-color: #999;
        }
    </style>
    <?php
}
?>
