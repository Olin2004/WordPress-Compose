<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Đăng ký trang menu quản trị.
function sm_register_menu() {
    add_menu_page(
        'Đăng nhập',              // Tiêu đề trang.
        'Hệ thống quản lý giảng viên',             // Tên menu.
        'manage_options',         // Quyền hạn truy cập.
        'user-management',        // Slug.
        'sm_render_users_page',   // Hàm render nội dung trang.
        'dashicons-admin-generic',  // Icon.
        10                        // Vị trí (order).
    );
}
add_action('admin_menu', 'sm_register_menu');

function sm_render_users_page() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_giangvien';  // Bảng giảng viên
    $error = '';

    // Xử lý khi form được gửi.
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

        // Truy vấn kiểm tra tài khoản trong bảng tbl_giangvien.
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE email = %s",
                $email
            )
        );

        // Kiểm tra nếu người dùng tồn tại và so sánh mật khẩu đã mã hóa.
        if ( $user && wp_check_password( $password, $user->password) ) {
            // Kiểm tra quyền của người dùng.
            if ($user->quyen == 'Admin') {
                // Nếu là Admin, chuyển đến trang quản trị WordPress.
                wp_redirect( admin_url( 'admin.php?page=qlhs_dashboard' ) ); // Cập nhật slug cho trang thông tin giảng viên của bạn

                exit;
            } elseif ($user->quyen == 'GiangVien') {
                // Nếu là Giảng viên, chuyển đến trang thông tin giảng viên.
                $wpdb->update(
                    $table_name,
                    array( 'trang_thai_dang_nhap' => 'ON' ),
                    array( 'ma_gv' => $user->ma_gv ) // Cập nhật theo ma_gv
                );
                wp_redirect( admin_url( 'admin.php?page=teacher-management' ) ); // Cập nhật slug cho trang thông tin giảng viên của bạn
                exit;
            }
        } else {
            $error = 'Email hoặc mật khẩu không đúng!';
        }
    }
    ?>
    <style>
        /* General Form Styles */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f6f9;
        }

        .col {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form {
            padding: 20px;
        }

        .form-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .form-inputs {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Input Fields */
        .input-box {
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .input-field:focus {
            border-color: #4caf50;
        }

        .icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
        }

        .input-submit {
            padding: 12px;
            background-color: #4caf50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;  /* Center text inside the button */
            display: block; /* Ensures the button is treated as block element */
            width: 100%; /* Make button full width of its container */
        }

        .input-submit:hover {
            background-color: #45a049;
        }

        /* Error Message */
        .error-message {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .col {
                width: 100%;
                padding: 15px;
            }

            .form-title {
                font-size: 20px;
            }
        }
    </style>

    <div class="form-container">
        <div class="col col-2">
            <!--Login Form Container-->
            <form action="" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="login-form">
                    <div class="form-title">
                        <span>Đăng nhập Giảng viên</span>
                    </div>
                    <div class="form-inputs">
                        <div class="input-box">
                            <input type="email" name="email" id="email" class="input-field" placeholder="Email" required>
                            <i class='bx bx-user icon'></i>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" id="password" class="input-field" placeholder="Mật khẩu" required>
                            <i class='bx bx-lock-alt icon'></i>
                        </div>
                        <div class="input-box">
                            <button type="submit" class="input-submit">
                                <span>Đăng nhập</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </button>
                        </div>
                        <?php if ( $error ) : ?>
                            <p class="error-message"><?php echo esc_html($error); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
