# WordPress Docker Compose

Một môi trường phát triển WordPress hoàn chỉnh sử dụng Docker Compose với các thành phần MariaDB, WordPress và phpMyAdmin.

---

## 📦 Components / Thành phần

- **WordPress**: Container WordPress bản mới nhất  
- **MariaDB**: Cơ sở dữ liệu MySQL dành cho WordPress  
- **phpMyAdmin**: Giao diện quản lý cơ sở dữ liệu qua web

---

## ⚙️ Configuration / Cấu hình

Tất cả các tùy chọn cấu hình chính được tập trung trong file `.env`:

### Database Settings / Cài đặt cơ sở dữ liệu

| Biến môi trường       | Ý nghĩa                            |
|----------------------|----------------------------------|
| `MYSQL_DATABASE`     | Tên cơ sở dữ liệu WordPress       |
| `MYSQL_USER`         | Tên người dùng cơ sở dữ liệu      |
| `MYSQL_PASSWORD`     | Mật khẩu người dùng cơ sở dữ liệu  |
| `MYSQL_ROOT_PASSWORD`| Mật khẩu root cho MySQL/MariaDB   |

### Port Configuration / Cổng mạng

| Biến môi trường       | Mặc định | Ý nghĩa                                  |
|----------------------|----------|-----------------------------------------|
| `DB_PORT`            | 3306     | Cổng MariaDB                            |
| `PHPMYADMIN_PORT`    | 8081     | Cổng phpMyAdmin                         |
| `WORDPRESS_PORT`     | 8080     | Cổng truy cập WordPress                 |

### Resource Limits / Giới hạn tài nguyên

| Biến môi trường     | Mặc định | Ý nghĩa                       |
|--------------------|----------|------------------------------|
| `DB_MEMORY_LIMIT`   | 2048m    | Bộ nhớ tối đa cho MariaDB     |

### phpMyAdmin Settings / Cấu hình phpMyAdmin

| Biến môi trường        | Mặc định | Ý nghĩa                            |
|-----------------------|----------|-----------------------------------|
| `PMA_UPLOAD_LIMIT`     | 750M     | Giới hạn upload tối đa phpMyAdmin |
| `PMA_MAX_EXECUTION_TIME`| 5000    | Thời gian thực thi tối đa (ms)    |

---

## 🐘 PHP Configuration / Cấu hình PHP

### uploads.ini (dùng cho WordPress)

- File uploads enabled (cho phép upload file)  
- memory_limit = 500M  
- upload_max_filesize = 500M  
- post_max_size = 500M  
- max_execution_time = 600  

### php.ini (chung)

- memory_limit = 1024M  
- ionCube loader cấu hình  
- SOAP extension enabled  
- max_input_vars = 3000  

---

## 🚀 Usage / Hướng dẫn sử dụng

### Start / Khởi chạy

```bash
docker-compose up -d
```

### Stop / Dừng

```bash
docker-compose down
```

### Access / Truy cập

- WordPress: [http://localhost:8084](http://localhost:8084) (mặc định) hoặc theo port trong `.env`  
- phpMyAdmin: [http://localhost:8081](http://localhost:8081) (Server: database, Username: root, Password: theo `.env`)  

---

## 💾 Persistent Data / Dữ liệu lưu trữ

- WordPress files: thư mục `./wordpress` trên host  
- Database data: volume Docker `db-data`

---

## 🛠 Customization / Tuỳ chỉnh

- Sửa file `.env` để thay đổi các cài đặt mà không cần chỉnh `docker-compose.yml`  
- Điều chỉnh các file PHP `uploads.ini` hoặc `php.ini` nếu cần

---

## 📌 Giới thiệu

Dự án này cung cấp môi trường phát triển WordPress sử dụng Docker, giúp đơn giản hóa việc thiết lập và thử nghiệm.

---

## 🙏 Cảm ơn

Dự án được phát triển dựa trên ý tưởng và cấu trúc gốc từ anh [**congdinh2008**](https://github.com/congdinh2008). Xin chân thành cảm ơn!
