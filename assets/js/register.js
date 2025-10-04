
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('.login-form');
    if (!registerForm) return; 

    // Kiểm tra xem đây có phải là form Đăng ký hay không (dựa vào ID các trường)
    if (!document.getElementById('confirm_password')) {
        // Nếu không có trường confirm_password, có thể đây là trang login, thoát
        return; 
    }

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    // Biểu thức chính quy (Regex) để kiểm tra định dạng Email và Số điện thoại
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
    const phoneRegex = /^(0|\+84)\d{9,10}$/; 

    // --- Các hàm hỗ trợ ---

    function displayError(inputElement, message) {
        const errorId = inputElement.dataset.errorTarget;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.textContent = message;
            inputElement.classList.add('input-error'); 
        }
    }

    function clearError(inputElement) {
        const errorId = inputElement.dataset.errorTarget;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.textContent = '';
            inputElement.classList.remove('input-error');
        }
    }

    // --- Hàm kiểm tra chính ---

    function validateRegistrationForm(e) {
        e.preventDefault(); 
        let isValid = true;

        // Xóa lỗi cũ
        clearError(emailInput);
        clearError(passwordInput);
        clearError(confirmPasswordInput);
        
        const emailOrPhoneValue = emailInput.value.trim();
        const passwordValue = passwordInput.value;
        const confirmPasswordValue = confirmPasswordInput.value;

        // 1. Kiểm tra Email/SĐT
        if (emailOrPhoneValue === '') {
            displayError(emailInput, 'Vui lòng nhập Email hoặc số điện thoại.');
            isValid = false;
        } else if (!emailRegex.test(emailOrPhoneValue) && !phoneRegex.test(emailOrPhoneValue)) {
            displayError(emailInput, 'Email hoặc số điện thoại không hợp lệ!');
            isValid = false;
        }
        
        // 2. Kiểm tra Mật khẩu
        if (passwordValue === '') {
            displayError(passwordInput, 'Vui lòng nhập Mật khẩu.');
            isValid = false;
        } else if (passwordValue.length < 6) {
             displayError(passwordInput, 'Mật khẩu phải có tối thiểu 6 ký tự.');
             isValid = false;
        }

        // 3. Kiểm tra Nhập lại Mật khẩu
        if (confirmPasswordValue === '') {
            displayError(confirmPasswordInput, 'Vui lòng nhập lại Mật khẩu.');
            isValid = false;
        } else if (confirmPasswordValue !== passwordValue) {
            displayError(confirmPasswordInput, 'Mật khẩu nhập lại không khớp.');
            isValid = false;
        }

        // 4. Nếu hợp lệ, gửi form
        if (isValid) {
            registerForm.submit(); 
        }
    }

    // Gắn sự kiện kiểm tra khi người dùng click nút "Tiếp tục"
    registerForm.addEventListener('submit', validateRegistrationForm);

    // Tùy chọn: Xóa thông báo lỗi khi người dùng nhập lại
    emailInput.addEventListener('input', () => clearError(emailInput));
    passwordInput.addEventListener('input', () => clearError(passwordInput));
    confirmPasswordInput.addEventListener('input', () => clearError(confirmPasswordInput));
});