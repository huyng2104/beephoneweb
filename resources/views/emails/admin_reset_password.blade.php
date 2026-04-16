<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Khôi phục mật khẩu tài khoản</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333 text-align: center;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #4CAF50;">Bee Phone - Khôi phục mật khẩu</h2>
        <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
        <p>Quản trị viên Bee Phone vừa thực hiện yêu cầu cấp lại mật khẩu cho tài khoản của bạn.</p>
        <p>Đây là mật khẩu mới của bạn dùng để đăng nhập vào hệ thống:</p>
        
        <div style="background-color: #f5f5f5; border: 2px dashed #ccc; padding: 15px; text-align: center; margin: 20px 0; border-radius: 8px;">
            <span style="font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #0056b3;">{{ $password }}</span>
        </div>

        <p style="color: #d9534f; font-size: 14px;"><strong>Lưu ý:</strong> Vui lòng đăng nhập lại bằng mật khẩu này và nên thay đổi mật khẩu trong phần cài đặt tài khoản của bạn càng sớm càng tốt để đảm bảo an toàn.</p>
        
        <br>
        <p>Trân trọng,</p>
        <p><strong>Đội ngũ Bee Phone</strong></p>
    </div>
</body>
</html>
