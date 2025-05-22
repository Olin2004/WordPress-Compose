<?php
add_action( 'admin_menu', 'sm_register_mon_hoc_menu' );

function sm_register_mon_hoc_menu() {
    add_menu_page(
        'Quản lý môn học',
        'Môn học',
        'manage_options',
        'mon-hoc-management',
        'sm_render_mon_hoc_admin_page',
        'dashicons-welcome-learn-more',
        21
    );
}

function sm_render_mon_hoc_admin_page() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_mon_hoc';

    // Xử lý POST
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['action'] ) ) {
        $action = sanitize_text_field( $_POST['action'] );
        if ( $action === 'add' ) {
            $wpdb->insert( $table_name, [
                'ten_mon_hoc' => sanitize_text_field( $_POST['ten_mon_hoc'] ),
                'so_tin_chi'  => intval( $_POST['so_tin_chi'] ),
                'mo_ta'       => sanitize_textarea_field( $_POST['mo_ta'] ),
            ] );
            wp_redirect( admin_url( 'admin.php?page=mon-hoc-management' ) );
            exit;
        }
        if ( $action === 'update' ) {
            $ma_mon_hoc = intval( $_POST['ma_mon_hoc'] );
            $wpdb->update(
                $table_name,
                [
                    'ten_mon_hoc' => sanitize_text_field( $_POST['ten_mon_hoc'] ),
                    'so_tin_chi'  => intval( $_POST['so_tin_chi'] ),
                    'mo_ta'       => sanitize_textarea_field( $_POST['mo_ta'] ),
                ],
                [ 'ma_mon_hoc' => $ma_mon_hoc ]
            );
            wp_redirect( admin_url( 'admin.php?page=mon-hoc-management' ) );
            exit;
        }
        if ( $action === 'delete' ) {
            $wpdb->delete( $table_name, [ 'ma_mon_hoc' => intval( $_POST['ma_mon_hoc'] ) ] );
            wp_redirect( admin_url( 'admin.php?page=mon-hoc-management' ) );
            exit;
        }
    }

    // Chỉnh sửa
    if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {
        $ma_mon_hoc = intval( $_GET['id'] );
        $mon_hoc = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_mon_hoc = %d", $ma_mon_hoc ) );
        if ( $mon_hoc ) {
            ?>
            <div class="wrap fix-khoa">
                <h1>Chỉnh sửa môn học</h1>
                <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật môn học này?');">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ma_mon_hoc" value="<?php echo esc_attr( $mon_hoc->ma_mon_hoc ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="ten_mon_hoc">Tên môn học</label></th>
                            <td><input type="text" name="ten_mon_hoc" value="<?php echo esc_attr( $mon_hoc->ten_mon_hoc ); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="so_tin_chi">Số tín chỉ</label></th>
                            <td><input type="number" name="so_tin_chi" value="<?php echo esc_attr( $mon_hoc->so_tin_chi ); ?>" min="1" required></td>
                        </tr>
                        <tr>
                            <th><label for="mo_ta">Mô tả</label></th>
                            <td><textarea name="mo_ta"><?php echo esc_textarea( $mon_hoc->mo_ta ); ?></textarea></td>
                        </tr>
                    </table>
                    <p>
                        <button type="submit" class="button button-primary">Cập nhật môn học</button>
                        <a href="<?php echo admin_url( 'admin.php?page=mon-hoc-management' ); ?>" class="button">Hủy</a>
                    </p>
                </form>
            </div>
            <?php
            sm_render_mon_hoc_style();
            return;
        }
    }

    // Tìm kiếm
    $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
    if ( $search !== '' ) {
        if ( is_numeric( $search ) ) {
            $ds_mon_hoc = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_mon_hoc = %d", intval( $search ) ) );
        } else {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $ds_mon_hoc = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ten_mon_hoc LIKE %s", $like ) );
        }
    } else {
        $ds_mon_hoc = $wpdb->get_results( "SELECT * FROM {$table_name}" );
    }

    ?>
    <div class="wrap">
        <div class="go-back-wrapper">
            <a href="<?php echo admin_url('admin.php?page=qlhs_dashboard'); ?>"  class="go-back-button">&#8592; Trở lại</a>
        </div>

        <div class="sm-box">
            <h1>Quản lý môn học</h1>

            <form method="get" class="search-form">
                <input type="hidden" name="page" value="mon-hoc-management">
                <input type="search" name="s" placeholder="Nhập mã hoặc tên môn học…" value="<?php echo esc_attr( $search ); ?>">
                <button class="button">Tìm kiếm</button>
                <a class="button" href="<?php echo admin_url( 'admin.php?page=mon-hoc-management' ); ?>">Tải lại</a>
            </form>

            <form method="post" class="add-khoa-form">
                <input type="hidden" name="action" value="add">
                <table class="form-table">
                    <tr>
                        <th><label for="ten_mon_hoc">Tên môn học</label></th>
                        <td><input type="text" name="ten_mon_hoc" required></td>
                    </tr>
                    <tr>
                        <th><label for="so_tin_chi">Số tín chỉ</label></th>
                        <td><input type="number" name="so_tin_chi" min="1" required></td>
                    </tr>
                    <tr>
                        <th><label for="mo_ta">Mô tả</label></th>
                        <td><textarea name="mo_ta"></textarea></td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary">Thêm môn học</button>
            </form>
        </div>

        <h2>Danh sách môn học</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Mã môn học</th>
                    <th>Tên môn học</th>
                    <th>Số tín chỉ</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ( $ds_mon_hoc ) : ?>
                <?php foreach ( $ds_mon_hoc as $mon_hoc ) : ?>
                    <tr>
                        <td><?php echo esc_html( $mon_hoc->ma_mon_hoc ); ?></td>
                        <td><?php echo esc_html( $mon_hoc->ten_mon_hoc ); ?></td>
                        <td><?php echo esc_html( $mon_hoc->so_tin_chi ); ?></td>
                        <td><?php echo esc_html( $mon_hoc->mo_ta ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=mon-hoc-management&action=edit&id=' . $mon_hoc->ma_mon_hoc ); ?>" class="button">Chỉnh sửa</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa môn học này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ma_mon_hoc" value="<?php echo esc_attr( $mon_hoc->ma_mon_hoc ); ?>">
                                <button type="submit" class="button button-link-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">Chưa có môn học nào.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    sm_render_mon_hoc_style();
}

function sm_render_mon_hoc_style() {
    ?>
    <style>
        /* --- Giữ nguyên CSS như bạn yêu cầu --- */
        body {
            background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
            background-size: cover; /* Để hình nền phủ toàn bộ trang */
            background-repeat: no-repeat; /* Ngăn hình nền lặp lại */
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
        .wrap .sm-box .form-table th {
            width: 150px;
            padding-right: 10px;
            text-align: left;
        }
        .wrap .sm-box .form-table td input[type="text"],
        .wrap .sm-box .form-table td input[type="number"],
        .wrap .sm-box .form-table td textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        .wrap .sm-box .form-table td textarea {
            min-height: 80px;
        }
        .wrap .sm-box .button-primary {
            margin-top: 20px;
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
