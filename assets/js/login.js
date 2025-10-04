// script.js - JavaScript cho validation form đăng nhập

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('.login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Biểu thức chính quy (Regex) để kiểm tra định dạng
    // Regex cho Email: Kiểm tra định dạng cơ bản (chứa @ và .)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
    
    // Regex cho Số điện thoại Việt Nam: (Bắt đầu bằng 0 hoặc +84, theo sau là 9-10 chữ số)
    // Lưu ý: Đây là regex đơn giản, có thể cần điều chỉnh chi tiết hơn tùy theo yêu cầu
    const phoneRegex = /^(0|\+84)\d{9,10}$/; 

    // Hàm hiển thị lỗi
    function displayError(inputElement, message) {
        const errorId = inputElement.dataset.errorTarget;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.textContent = message;
            inputElement.classList.add('input-error'); 
        }
    }

    // Hàm xóa lỗi
    function clearError(inputElement) {
        const errorId = inputElement.dataset.errorTarget;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.textContent = '';
            inputElement.classList.remove('input-error');
        }
    }

    // Hàm kiểm tra form tổng thể
    function validateForm(e) {
        e.preventDefault(); 

        let isValid = true;
        clearError(emailInput);
        clearError(passwordInput);
        
        const emailOrPhoneValue = emailInput.value.trim();

        // 1. Kiểm tra Email/SĐT
        if (emailOrPhoneValue === '') {
            displayError(emailInput, 'Vui lòng nhập Email hoặc số điện thoại.');
            isValid = false;
        } else if (!emailRegex.test(emailOrPhoneValue) && !phoneRegex.test(emailOrPhoneValue)) {
            // Kiểm tra: Nếu ko phải email và ko phải số điện thoại, thì báo lỗi.
            displayError(emailInput, 'Email hoặc số điện thoại không hợp lệ!');
            isValid = false;
        }
        
        // 2. Kiểm tra Mật khẩu
        if (passwordInput.value.trim() === '') {
            displayError(passwordInput, 'Vui lòng nhập Mật khẩu.');
            isValid = false;
        } else if (passwordInput.value.length < 6) {
             displayError(passwordInput, 'Mật khẩu phải có ít nhất 6 ký tự.');
             isValid = false;
        }

        // 3. Nếu hợp lệ, gửi form
        if (isValid) {
            loginForm.submit(); 
        }
    }

    // Gắn sự kiện kiểm tra khi người dùng click nút "Tiếp tục"
    if (loginForm) {
        loginForm.addEventListener('submit', validateForm);
    }
    
    // Xóa thông báo lỗi khi người dùng bắt đầu nhập lại
    emailInput.addEventListener('input', () => clearError(emailInput));
    passwordInput.addEventListener('input', () => clearError(passwordInput));
});