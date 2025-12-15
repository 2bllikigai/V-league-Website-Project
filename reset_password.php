<?php
// ĐÂY LÀ MẬT KHẨU BẠN MUỐN DÙNG
$password_moi = "admin123"; 

// Băm (hash) mật khẩu đó thành chuỗi bảo mật
$hashed_password = password_hash($password_moi, PASSWORD_DEFAULT);

echo "MẬT KHẨU CỦA BẠN (Đã mã hóa): <br>";
echo "<strong style='word-break: break-all; color: red;'>$hashed_password</strong>";
?>