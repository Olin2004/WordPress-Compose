<?php
add_action( 'admin_menu', 'sm_register_khoa_menu' );
function sm_register_khoa_menu() {
    add_menu_page(
        'Quản lý khoa',
        'Khoa',
        'manage_options',
        'khoa-management',
        'sm_render_khoa_admin_page',
        'dashicons-welcome-learn-more',
        21
    );
}

function sm_render_khoa_admin_page() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_khoa';

    // Xử lý POST: add / update / delete
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['action'] ) ) {
        $action = sanitize_text_field( $_POST['action'] );
        if ( $action === 'add' ) {
            $wpdb->insert( $table_name, [
                'ten_khoa'       => sanitize_text_field( $_POST['ten_khoa'] ),
                'truong_khoa'    => sanitize_text_field( $_POST['truong_khoa'] ),
                'ngay_thanh_lap' => sanitize_text_field( $_POST['ngay_thanh_lap'] ),
                'ghi_chu'        => sanitize_textarea_field( $_POST['ghi_chu'] ),
            ] );
            wp_redirect( admin_url( 'admin.php?page=khoa-management' ) );
            exit;
        }
        if ( $action === 'update' ) {
            $ma_khoa = intval( $_POST['ma_khoa'] );
            $wpdb->update(
                $table_name,
                [
                    'ten_khoa'       => sanitize_text_field( $_POST['ten_khoa'] ),
                    'truong_khoa'    => sanitize_text_field( $_POST['truong_khoa'] ),
                    'ngay_thanh_lap' => sanitize_text_field( $_POST['ngay_thanh_lap'] ),
                    'ghi_chu'        => sanitize_textarea_field( $_POST['ghi_chu'] ),
                ],
                [ 'ma_khoa' => $ma_khoa ]
            );
            wp_redirect( admin_url( 'admin.php?page=khoa-management' ) );
            exit;
        }
        if ( $action === 'delete' ) {
            $wpdb->delete( $table_name, [ 'ma_khoa' => intval( $_POST['ma_khoa'] ) ] );
            wp_redirect( admin_url( 'admin.php?page=khoa-management' ) );
            exit;
        }
    }

    if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'edit' && ! empty( $_GET['id'] ) ) {
        $ma_khoa = intval( $_GET['id'] );
        $khoa    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_khoa = %d", $ma_khoa ) );
        if ( $khoa ) {
            ?>
            <div class="wrap fix-khoa">

                <h1>Chỉnh sửa khoa</h1>
                <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật khoa này?');">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ma_khoa" value="<?php echo esc_attr( $khoa->ma_khoa ); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label for="ten_khoa">Tên khoa</label></th>
                            <td><input type="text" name="ten_khoa" value="<?php echo esc_attr( $khoa->ten_khoa ); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="truong_khoa">Trưởng khoa</label></th>
                            <td><input type="text" name="truong_khoa" value="<?php echo esc_attr( $khoa->truong_khoa ); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="ngay_thanh_lap">Ngày thành lập</label></th>
                            <td><input type="date" name="ngay_thanh_lap" value="<?php echo esc_attr( $khoa->ngay_thanh_lap ); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="ghi_chu">Ghi chú</label></th>
                            <td><textarea name="ghi_chu"><?php echo esc_textarea( $khoa->ghi_chu ); ?></textarea></td>
                        </tr>
                    </table>
                    <p>
                        <button type="submit" class="button button-primary">Cập nhật khoa</button>
                        <a href="<?php echo admin_url( 'admin.php?page=khoa-management' ); ?>" class="button">Hủy</a>
                    </p>
                </form>
            </div>
            <style>
                body{
                    background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
                    background-size: cover;
                    background-repeat: no-repeat; 
                    background-attachment: fixed; 
                    margin: auto;
                }
                .fix-khoa {
                   

                    border: 2px solid #fff;
                    padding: 20px;
                    margin: 20px 2px 20px 300px;
                    max-width: 800px;
                    box-sizing: border-box;
                    background: transparent;
                    backdrop-filter: blur(10px);
                    align-items: center;
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
            return;
        }
    }

    // Xử lý tìm kiếm
    $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
    if ( $search !== '' ) {
        if ( is_numeric( $search ) ) {
            $ds_khoa = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ma_khoa = %d", intval( $search ) ) );
        } else {
            $like    = '%' . $wpdb->esc_like( $search ) . '%';
            $ds_khoa = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE ten_khoa LIKE %s", $like ) );
        }
    } else {
        $ds_khoa = $wpdb->get_results( "SELECT * FROM {$table_name}" );
    }

    // Hiển thị giao diện chính
    ?>
    <div class="wrap">
        <div class="go-back-wrapper">
            <a href="<?php echo admin_url('admin.php?page=qlhs_dashboard'); ?>"  class="go-back-button">
                &#8592; Trở lại
            </a>
        </div>

        <div class="sm-box">
            <h1>Quản lý khoa</h1>

            <form method="get" class="search-form">
                <input type="hidden" name="page" value="khoa-management">
                <input type="search" name="s" placeholder="Nhập mã hoặc tên khoa…" value="<?php echo esc_attr( $search ); ?>">
                <button class="button">Tìm kiếm</button>
                <a class="button" href="<?php echo admin_url( 'admin.php?page=khoa-management' ); ?>">Tải lại</a>
            </form>

            <form method="post" class="add-khoa-form">
                <input type="hidden" name="action" value="add">
                <table class="form-table">
                    <tr>
                        <th><label for="ten_khoa">Tên khoa</label></th>
                        <td><input type="text" name="ten_khoa" required></td>
                    </tr>
                    <tr>
                        <th><label for="truong_khoa">Trưởng khoa</label></th>
                        <td><input type="text" name="truong_khoa"></td>
                    </tr>
                    <tr>
                        <th><label for="ngay_thanh_lap">Ngày thành lập</label></th>
                        <td><input type="date" name="ngay_thanh_lap"></td>
                    </tr>
                    <tr>
                        <th><label for="ghi_chu">Ghi chú</label></th>
                        <td><textarea name="ghi_chu"></textarea></td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary">Thêm khoa</button>
            </form>
        </div>

        <h2>Danh sách khoa</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Mã khoa</th>
                    <th>Tên khoa</th>
                    <th>Trưởng khoa</th>
                    <th>Ngày thành lập</th>
                    <th>Ghi chú</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if ( $ds_khoa ) : ?>
                <?php foreach ( $ds_khoa as $khoa ) : ?>
                    <tr>
                        <td><?php echo esc_html( $khoa->ma_khoa ); ?></td>
                        <td><?php echo esc_html( $khoa->ten_khoa ); ?></td>
                        <td><?php echo esc_html( $khoa->truong_khoa ); ?></td>
                        <td><?php echo esc_html( $khoa->ngay_thanh_lap ); ?></td>
                        <td><?php echo esc_html( $khoa->ghi_chu ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=khoa-management&action=edit&id=' . $khoa->ma_khoa ); ?>" class="button">Chỉnh sửa</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khoa này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="ma_khoa" value="<?php echo esc_attr( $khoa->ma_khoa ); ?>">
                                <button type="submit" class="button button-link-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6">Chưa có khoa nào.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <style>
        body{
            background-image: url('https://media.istockphoto.com/vectors/abstract-blue-curve-background-vector-id1225899140?k=6&m=1225899140&s=612x612&w=0&h=Gun_wcGstqbBdyHlccsQuR805rztpKSPpV0bSQBIVBM=');
            background-size: cover; /* Để hình nền phủ toàn bộ trang */
            background-repeat: no-repeat; /* Ngăn hình nền lặp lại */
            background-attachment: fixed; /* Giữ hình nền cố định khi cuộn trang */
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
            color: #fff;
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

        .wrap .sm-box h2,
        .wrap .sm-box .add-khoa-form,
        .wrap .sm-box .form-table {
            text-align: left; 
            margin-bottom: 15px;
        }

        .wrap .sm-box .form-table th {
            width: 150px;
            padding-right: 10px;
            text-align: left;
        }

        .wrap .sm-box .form-table td input[type="text"],
        .wrap .sm-box .form-table td input[type="date"],
        .wrap .sm-box .form-table td textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        .wrap .sm-box .form-table td textarea {
            min-height: 80px;
        }

        .wrap .sm-box .add-khoa-form button[type="submit"] {
            display: block; 
            margin: 20px auto 0; 
        }

        .wrap .sm-box .button-primary {
            margin-right: 10px;
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
