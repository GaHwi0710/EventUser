// ===== BEGIN thongtin.js =====
document.addEventListener('DOMContentLoaded', function() {
    
    // --- DỮ LIỆU ĐỊA CHỈ ĐÃ CẬP NHẬT ĐẦY ĐỦ HƠN ---
    const locationData = {
        //Hà Nội
        "hanoi": [
            "Phường Phúc Xá ", "Phường Trúc Bạch ", "Phường Hàng Buồm", 
            "Phường Lý Thái Tổ", "Phường Cầu Giấy", "Phường Dịch Vọng Hậu",
            "Phường Nhân Chính", "Phường Láng Thượng", "Phường Thụy Khuê ",  
        ],
        //TPHCM
        "hcm": [
            "Phường Tân Định ", "Phường Bến Thành ", "Phường Cầu Ông Lãnh ", 
            "Phường Bàn Cờ ", "Phường Xóm Chiếu ", "Phường Tân Phong ", 
            "Phường Thảo Điền ", "Phường An Phú ", "Phường Hiệp Bình Chánh ", 
            "Phường Long Trường ", "Phường Cát Lái "
        ],
        //Đà Năng
        "dn": [
            "Phường Hải Châu I ", "Phường Thạch Thang ", "Phường Thanh Bình ",
            "Phường An Hải Bắc ", "Phường Mỹ An ",
            "Phường Hoà Xuân ", "Phường Khuê Trung ", "Xã Hoà Bắc "
        ],
        //Hưng Yên
        "hy": [
            "phường Thái Bình", "xã Thụy Anh",
            "Phường An Tảo ", "Phường Lam Sơn ", "Xã Đình Dù ", 
            "Xã Lạc Đạo ", "Phường Nhân Hoà "
        ],
        //Huế
        "tph": [
            "Phường Thuận Thành", "Phường Vĩnh Ninh", "Phường Thủy Xuân", "Phường Hương Sơ", 
            "Xã Hương Phong "
        ],
        //Lào Cai
        "lc": [
            "Phường Phố Mới ", "Phường Duyên Hải ", "Xã Bản Phiệt", "Xã Sa Pả"
        ], 
         // Điện Biên
        "db": [
            "Phường Mường Thanh", "Phường Thanh Trường", "Xã Thanh Luông"
        ], 
        // Sơn La
        "sl": [
            "Phường Chiềng Lề", "Phường Quyết Thắng", "Xã Chiềng Xôm", "Xã Mường Bằng"
        ], 
        // Lạng Sơn
        "ls": [
            "Phường Vĩnh Trại", "Phường Đông Kinh", "Xã Hoàng Văn Thụ"
        ], 
        // Quảng Ninh
        "qn": [
            "Phường Hồng Gai", "Phường Bãi Cháy", "Phường Cẩm Thạch", 
            "Phường Mạo Khê"
        ],
        // Thanh Hóa
        "th": [
            "Phường Đông Hải", "Phường Ba Đình", "Phường Quảng Thành", 
            "Xã Hoằng Tiến", "Xã Hải Thanh"
        ],
        // Nghệ An
        "na": [
            "Phường Hưng Bình", "Phường Lê Lợi", "Xã Nghi Liên", 
            "Xã Nghi Phong"
        ],
        // Cao Bằng
        "cb": [
            "Phường Sông Bằng", "Phường Hợp Giang", "Xã Chu Trinh"
        ],
        // Hà Tĩnh
        "ht": [
            "Phường Bắc Hà", "Phường Trần Phú", "Xã Thạch Hưng", "Xã Cẩm Bình"
        ], 	
        // Tuyên Quang
        "tq": [
            "Phường Phan Thiết", "Phường Tân Hà", "Xã An Tường"
        ], 	 	
        // Thái Nguyên
        "tn": [
            "Phường Phan Đình Phùng", "Phường Hoàng Văn Thụ", "Xã Phúc Trìu"
        ], 
        // Phú Thọ
        "pt": [
            "Phường Gia Cẩm", "Phường Vân Cơ", "Xã Hùng Lô", "Xã Tiên Kiên"
        ],
        // Bắc Ninh
        "bn": [
            "Phường Đại Phúc", "Phường Võ Cường", "Phường Đình Bảng", "Xã Phù Lương"
        ], 	
        // TP. Hải Phòng
        "hp": [
            "Phường Máy Tơ", "Phường Đông Hải", "Phường Tràng Cát", 
            "Xã An Đồng", "Thị trấn Núi Đối"
        ],
        // Ninh Bình
        "nb": [
            "Phường Đông Thành", "Phường Phúc Thành", "Xã Ninh Hải"
        ], 
        // TP. Cần Thơ
        "ct": [
            "Phường An Nghiệp", "Phường Cái Khế", "Phường Ba Láng", 
            "Phường Long Tuyền", "Xã Thạnh Phú"
        ],
        // Vĩnh Long
        "vl": [
            "Phường Bến Tre", "Phường An Hộ", "Phường Trà Vinh", "Phường Trường Long Hoà"
        ],
        // An Giang
        "ag": [
            "Phường Mỹ Long", "Phường Châu Phú B", "Xã Vĩnh Thạnh"
        ],
        // Cà Mau
        "cm": [
            "Phường Bạc Liêu", "phường Tân Thành", "phường Hoà Thành", "Xã Hàm Rồng (Năm Căn)"
        ],
        // Đồng Tháp
        "dt": [
            "Phường Cao Lãnh", "Phường Sa Đéc", "phường Cai Lậy", "phường Bình Xuân"
        ],
        // Tây Ninh
        "tn": [
            "Phường Hòa Thành", "Phường Thanh Điền", "Phường Long An", "Phường Gò Dầu"
        ], 	
        // Đồng Nai
        "dn": [
            "Phường Tân Hiệp", "Phường Long Bình", "Phường Quang Vinh", 
            "Xã Phước Khánh", "Thị trấn Trảng Bom"
        ], 	
        // Khánh Hòa
        "kh": [
            "Phường Lộc Thọ", "Phường Vĩnh Hải", "Xã Vĩnh Thái"
        ], 	 
        // Gia Lai
        "gl": [
            "Phường Hoa Lư", "Phường Tây Sơn", "Xã Trà Đa"
        ],
        // Quảng Ngãi
        "qn": [
            "Phường Trần Phú", "Phường Lê Hồng Phong", "Xã Tịnh Kỳ"
        ], 	
        // Quảng Trị
        "qt": [
            "Phường Đông Hà", "Phường Quảng Trị", "Phường Ba Đồn"
        ],
        // Bình Dương
        "bd": [
            "Phường Bình Dương", "Phường Thủ Dầu Một", "Phường Chánh Hiệp"
        ],
        // Bình Định
        "bđ": [
            "Phường Quy Nhơn", "Phường Bình Định", "Phường Quy Nhơn Tây", "Phường Quy Nhơn Đông"
        ]
    };

    // --- LẤY CÁC ELEMENT CẦN THIẾT ---
    const provinceSelect = document.getElementById('province');
    const wardSelect = document.getElementById('ward');

    // Khóa wardSelect khi tải trang
    wardSelect.disabled = true;

    // --- THÊM SỰ KIỆN "CHANGE" CHO Ô TỈNH/THÀNH ---
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;

        // Xóa các lựa chọn cũ trong ô Xã/Phường
        wardSelect.innerHTML = '';

        if (selectedProvince) {
            // Mở khóa và thêm lựa chọn mặc định cho ô Xã/Phường
            wardSelect.disabled = false;
            wardSelect.add(new Option('-- Chọn Xã/Phường --', ''));

            // Lấy danh sách các xã/phường tương ứng
            const wards = locationData[selectedProvince] || [];

            // Thêm từng xã/phường vào dropdown
            wards.forEach(function(wardName) {
                wardSelect.add(new Option(wardName, wardName));
            });
        } else {
            // Nếu không chọn tỉnh nào, khóa ô Xã/Phường lại
            wardSelect.disabled = true;
            wardSelect.add(new Option('-- Vui lòng chọn Tỉnh/Thành trước --', ''));
        }
    });

    // --- HÀM ĐẾM KÝ TỰ ---
    function setupCharacterCounter(inputId, counterId) {
        const inputElement = document.getElementById(inputId);
        const counterElement = document.getElementById(counterId);
        if (!inputElement || !counterElement) return;
        const maxLength = inputElement.getAttribute('maxlength');
        const updateCounter = () => {
            counterElement.textContent = `${inputElement.value.length}/${maxLength}`;
        };
        inputElement.addEventListener('input', updateCounter);
        updateCounter();
    }

    // --- KHỞI CHẠY HÀM ĐẾM KÝ TỰ ---
    setupCharacterCounter('street-address', 'address-counter');
});
// ===== END thongtin.js =====

// ===== BEGIN loaive.js =====
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Hàm thiết lập bộ đếm ký tự cho một trường input
     */
    function setupCharacterCounter(inputId, counterId) {
        const inputElement = document.getElementById(inputId);
        const counterElement = document.getElementById(counterId);
        
        if (!inputElement || !counterElement) return;

        const maxLength = inputElement.getAttribute('maxlength');
        
        inputElement.addEventListener('input', () => {
            counterElement.textContent = `${inputElement.value.length}/${maxLength}`;
        });
        // Initialize
        counterElement.textContent = `${inputElement.value.length}/${maxLength}`;
    }

    /**
     * Hàm xử lý logic cho checkbox "Miễn phí"
     */
    function setupFreeTicketLogic(checkboxId, priceInputId) {
        const checkbox = document.getElementById(checkboxId);
        const priceInput = document.getElementById(priceInputId);

        if (!checkbox || !priceInput) return;
        
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                priceInput.value = '0';
                priceInput.disabled = true;
            } else {
                priceInput.disabled = false;
                priceInput.value = ''; // Xóa giá trị để người dùng tự nhập
            }
        });
    }

    // --- KHỞI CHẠY CÁC HÀM ---
    setupCharacterCounter('ticket-name', 'ticket-name-counter');
    setupFreeTicketLogic('free-ticket-checkbox', 'ticket-price');
});
// ===== END loaive.js =====

// ===== BEGIN thanhtoan.js =====
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Hàm thiết lập bộ đếm ký tự cho nhiều trường input
     * @param {Array<string[]>} fields - Mảng chứa các cặp [inputId, counterId]
     */
    function setupAllCharacterCounters(fields) {
        fields.forEach(field => {
            const inputId = field[0];
            const counterId = field[1];

            const inputElement = document.getElementById(inputId);
            const counterElement = document.getElementById(counterId);

            if (!inputElement || !counterElement) {
                console.warn(`Could not find elements: ${inputId}, ${counterId}`);
                return;
            }

            const maxLength = inputElement.getAttribute('maxlength');

            // Hàm cập nhật
            const updateCounter = () => {
                const currentLength = inputElement.value.length;
                counterElement.textContent = `${currentLength}/${maxLength}`;
            };

            // Gọi khi có thay đổi
            inputElement.addEventListener('input', updateCounter);

            // Gọi lần đầu khi tải trang
            updateCounter();
        });
    }

    // --- KHỞI CHẠY HÀM ---
    // Danh sách các trường cần bộ đếm
    const fieldsToCount = [
        ['account-holder', 'account-holder-counter'],
        ['account-number', 'account-number-counter'],
        ['bank-name', 'bank-name-counter'],
        ['branch-name', 'branch-name-counter'],
        ['full-name', 'full-name-counter'],
        ['address', 'address-counter'],
        ['tax-code', 'tax-code-counter']
    ];

    setupAllCharacterCounters(fieldsToCount);
});
// ===== END thanhtoan.js =====

// ===== BEGIN caidat.js =====
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Hàm thiết lập bộ đếm ký tự cho một trường input/textarea
     * @param {string} inputId - ID của trường input hoặc textarea
     * @param {string} counterId - ID của phần tử span hiển thị bộ đếm
     */
    function setupCharacterCounter(inputId, counterId) {
        const inputElement = document.getElementById(inputId);
        const counterElement = document.getElementById(counterId);
        
        if (!inputElement || !counterElement) {
            console.warn(`Could not find elements for counter: ${inputId}, ${counterId}`);
            return;
        }

        const maxLength = inputElement.getAttribute('maxlength');

        // Cập nhật bộ đếm khi có sự kiện nhập liệu
        inputElement.addEventListener('input', () => {
            const currentLength = inputElement.value.length;
            counterElement.textContent = `${currentLength}/${maxLength}`;
        });
    }

    // Khởi tạo các bộ đếm
    setupCharacterCounter('custom-path', 'path-counter');
    setupCharacterCounter('confirmation-message', 'message-counter');

});
// ===== END caidat.js =====

// ===== BEGIN add.js =====
document.addEventListener('DOMContentLoaded', function() {
    
    // --- DỮ LIỆU ĐỊA CHỈ VÍ DỤ ---
    const locationData = {
    //Hà Nội
        "hanoi": [
            "Phường Phúc Xá ", "Phường Trúc Bạch ", "Phường Hàng Buồm", 
            "Phường Lý Thái Tổ", "Phường Cầu Giấy", "Phường Dịch Vọng Hậu",
            "Phường Nhân Chính", "Phường Láng Thượng", "Phường Thụy Khuê ",  
        ],
        //TPHCM
        "hcm": [
            "Phường Tân Định ", "Phường Bến Thành ", "Phường Cầu Ông Lãnh ", 
            "Phường Bàn Cờ ", "Phường Xóm Chiếu ", "Phường Tân Phong ", 
            "Phường Thảo Điền ", "Phường An Phú ", "Phường Hiệp Bình Chánh ", 
            "Phường Long Trường ", "Phường Cát Lái "
        ],
        //Đà Năng
        "dn": [
            "Phường Hải Châu I ", "Phường Thạch Thang ", "Phường Thanh Bình ",
            "Phường An Hải Bắc ", "Phường Mỹ An ",
            "Phường Hoà Xuân ", "Phường Khuê Trung ", "Xã Hoà Bắc "
        ],
        //Hưng Yên
        "hy": [
            "phường Thái Bình", "xã Thụy Anh",
            "Phường An Tảo ", "Phường Lam Sơn ", "Xã Đình Dù ", 
            "Xã Lạc Đạo ", "Phường Nhân Hoà "
        ],
        //Huế
        "tph": [
            "Phường Thuận Thành", "Phường Vĩnh Ninh", "Phường Thủy Xuân", "Phường Hương Sơ", 
            "Xã Hương Phong "
        ],
        //Lào Cai
        "lc": [
            "Phường Phố Mới ", "Phường Duyên Hải ", "Xã Bản Phiệt", "Xã Sa Pả"
        ], 
         // Điện Biên
        "db": [
            "Phường Mường Thanh", "Phường Thanh Trường", "Xã Thanh Luông"
        ], 
        // Sơn La
        "sl": [
            "Phường Chiềng Lề", "Phường Quyết Thắng", "Xã Chiềng Xôm", "Xã Mường Bằng"
        ], 
        // Lạng Sơn
        "ls": [
            "Phường Vĩnh Trại", "Phường Đông Kinh", "Xã Hoàng Văn Thụ"
        ], 
        // Quảng Ninh
        "qn": [
            "Phường Hồng Gai", "Phường Bãi Cháy", "Phường Cẩm Thạch", 
            "Phường Mạo Khê"
        ],
        // Thanh Hóa
        "th": [
            "Phường Đông Hải", "Phường Ba Đình", "Phường Quảng Thành", 
            "Xã Hoằng Tiến", "Xã Hải Thanh"
        ],
        // Nghệ An
        "na": [
            "Phường Hưng Bình", "Phường Lê Lợi", "Xã Nghi Liên", 
            "Xã Nghi Phong"
        ],
        // Cao Bằng
        "cb": [
            "Phường Sông Bằng", "Phường Hợp Giang", "Xã Chu Trinh"
        ],
        // Hà Tĩnh
        "ht": [
            "Phường Bắc Hà", "Phường Trần Phú", "Xã Thạch Hưng", "Xã Cẩm Bình"
        ], 	
        // Tuyên Quang
        "tq": [
            "Phường Phan Thiết", "Phường Tân Hà", "Xã An Tường"
        ], 	 	
        // Thái Nguyên
        "tn": [
            "Phường Phan Đình Phùng", "Phường Hoàng Văn Thụ", "Xã Phúc Trìu"
        ], 
        // Phú Thọ
        "pt": [
            "Phường Gia Cẩm", "Phường Vân Cơ", "Xã Hùng Lô", "Xã Tiên Kiên"
        ],
        // Bắc Ninh
        "bn": [
            "Phường Đại Phúc", "Phường Võ Cường", "Phường Đình Bảng", "Xã Phù Lương"
        ], 	
        // TP. Hải Phòng
        "hp": [
            "Phường Máy Tơ", "Phường Đông Hải", "Phường Tràng Cát", 
            "Xã An Đồng", "Thị trấn Núi Đối"
        ],
        // Ninh Bình
        "nb": [
            "Phường Đông Thành", "Phường Phúc Thành", "Xã Ninh Hải"
        ], 
        // TP. Cần Thơ
        "ct": [
            "Phường An Nghiệp", "Phường Cái Khế", "Phường Ba Láng", 
            "Phường Long Tuyền", "Xã Thạnh Phú"
        ],
        // Vĩnh Long
        "vl": [
            "Phường Bến Tre", "Phường An Hộ", "Phường Trà Vinh", "Phường Trường Long Hoà"
        ],
        // An Giang
        "ag": [
            "Phường Mỹ Long", "Phường Châu Phú B", "Xã Vĩnh Thạnh"
        ],
        // Cà Mau
        "cm": [
            "Phường Bạc Liêu", "phường Tân Thành", "phường Hoà Thành", "Xã Hàm Rồng (Năm Căn)"
        ],
        // Đồng Tháp
        "dt": [
            "Phường Cao Lãnh", "Phường Sa Đéc", "phường Cai Lậy", "phường Bình Xuân"
        ],
        // Tây Ninh
        "tn": [
            "Phường Hòa Thành", "Phường Thanh Điền", "Phường Long An", "Phường Gò Dầu"
        ], 	
        // Đồng Nai
        "dn": [
            "Phường Tân Hiệp", "Phường Long Bình", "Phường Quang Vinh", 
            "Xã Phước Khánh", "Thị trấn Trảng Bom"
        ], 	
        // Khánh Hòa
        "kh": [
            "Phường Lộc Thọ", "Phường Vĩnh Hải", "Xã Vĩnh Thái"
        ], 	 
        // Gia Lai
        "gl": [
            "Phường Hoa Lư", "Phường Tây Sơn", "Xã Trà Đa"
        ],
        // Quảng Ngãi
        "qn": [
            "Phường Trần Phú", "Phường Lê Hồng Phong", "Xã Tịnh Kỳ"
        ], 	
        // Quảng Trị
        "qt": [
            "Phường Đông Hà", "Phường Quảng Trị", "Phường Ba Đồn"
        ],
        // Bình Dương
        "bd": [
            "Phường Bình Dương", "Phường Thủ Dầu Một", "Phường Chánh Hiệp"
        ],
        // Bình Định
        "bđ": [
            "Phường Quy Nhơn", "Phường Bình Định", "Phường Quy Nhơn Tây", "Phường Quy Nhơn Đông"
        ]
    };

    const provinceSelect = document.getElementById('province');
    const wardSelect = document.getElementById('ward');

    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        wardSelect.innerHTML = ''; // Xóa các lựa chọn cũ

        if (selectedProvince) {
            wardSelect.disabled = false;
            wardSelect.add(new Option('-- Chọn Xã/Phường --', ''));
            const wards = locationData[selectedProvince] || [];
            wards.forEach(wardName => {
                wardSelect.add(new Option(wardName, wardName));
            });
        } else {
            wardSelect.disabled = true;
            wardSelect.add(new Option('-- Chọn Tỉnh/Thành trước --', ''));
        }
    });

    // --- XỬ LÝ TẢI ẢNH VÀ XEM TRƯỚC ---
    const bannerInput = document.getElementById('event-banner');
    const previewContainer = document.getElementById('banner-preview-container');
    const previewImage = document.getElementById('banner-preview');
    const removePreviewBtn = document.getElementById('remove-banner-preview');
    const uploadPlaceholder = document.querySelector('.file-upload-placeholder');

    bannerInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
            };
            
            reader.readAsDataURL(file);
        }
    });

    removePreviewBtn.addEventListener('click', function() {
        bannerInput.value = ''; // Xóa file đã chọn
        previewImage.src = '#';
        previewContainer.style.display = 'none';
        uploadPlaceholder.style.display = 'block';
    });
});
// ===== END add.js =====

