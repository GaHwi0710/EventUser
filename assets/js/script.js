document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const meter = document.getElementById('passwordStrengthMeter');
            const text = document.getElementById('passwordStrengthText');
            
            if (!meter || !text) return;
            
            let strength = 0;
            
            if (password.length >= 8) {
                strength += 1;
            }
            
            if (password.match(/[a-z]+/)) {
                strength += 1;
            }
            
            if (password.match(/[A-Z]+/)) {
                strength += 1;
            }
            
            if (password.match(/[0-9]+/)) {
                strength += 1;
            }
            
            if (password.match(/[$@#&!]+/)) {
                strength += 1;
            }
            
            meter.style.width = `${(strength / 5) * 100}%`;
            
            if (password.length === 0) {
                meter.style.backgroundColor = 'transparent';
                text.textContent = '';
            } else if (strength < 2) {
                meter.style.backgroundColor = '#dc3545';
                text.textContent = 'Mật khẩu yếu';
                text.style.color = '#dc3545';
            } else if (strength < 4) {
                meter.style.backgroundColor = '#ffc107';
                text.textContent = 'Mật khẩu trung bình';
                text.style.color = '#ffc107';
            } else {
                meter.style.backgroundColor = '#28a745';
                text.textContent = 'Mật khẩu mạnh';
                text.style.color = '#28a745';
            }
        });
    }
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.classList.add('fade');
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 3000);
    });
});

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('vi-VN', options);
}
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const eventItems = document.querySelectorAll('.event-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            eventItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});