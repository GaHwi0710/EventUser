// ==== EventUser | EDIT PAGE SCRIPTS ====
document.addEventListener('DOMContentLoaded', function () {
  /* Province/Ward */
  const locationData = {
    hanoi:["Phường Phúc Xá","Phường Trúc Bạch","Phường Hàng Buồm","Phường Lý Thái Tổ","Phường Cầu Giấy","Phường Dịch Vọng Hậu","Phường Nhân Chính","Phường Láng Thượng","Phường Thụy Khuê"],
    hcm:["Phường Tân Định","Phường Bến Thành","Phường Cầu Ông Lãnh","Phường Bàn Cờ","Phường Xóm Chiếu","Phường Tân Phong","Phường Thảo Điền","Phường An Phú","Phường Hiệp Bình Chánh","Phường Long Trường","Phường Cát Lái"],
    dn:["Phường Hải Châu I","Phường Thạch Thang","Phường Thanh Bình","Phường An Hải Bắc","Phường Mỹ An","Phường Hoà Xuân","Phường Khuê Trung","Xã Hoà Bắc"]
  };
  const provinceSelect = document.getElementById('province');
  const wardSelect = document.getElementById('ward');

  function fillWards(provinceKey, selectedWard = '') {
    wardSelect.innerHTML = '';
    if (!provinceKey) {
      wardSelect.add(new Option('-- Chọn Tỉnh/Thành trước --',''));
      wardSelect.disabled = true;
      return;
    }
    const wards = locationData[provinceKey] || [];
    wardSelect.disabled = false;
    wardSelect.add(new Option('-- Chọn Xã/Phường --',''));
    wards.forEach(w => {
      const opt = new Option(w, w);
      if (selectedWard && selectedWard === w) opt.selected = true;
      wardSelect.add(opt);
    });
  }

  if (provinceSelect && wardSelect) {
    const selectedWard = wardSelect.dataset.selectedWard || '';
    const selectedProvince = provinceSelect.value || '';
    if (selectedProvince) fillWards(selectedProvince, selectedWard);
    else {
      wardSelect.add(new Option('-- Chọn Tỉnh/Thành trước --',''));
      wardSelect.disabled = true;
    }
    provinceSelect.addEventListener('change', function(){ fillWards(this.value, ''); });
  }

  /* Banner preview */
  const bannerInput = document.getElementById('event-banner');
  const previewContainer = document.getElementById('banner-preview-container');
  const previewImage = document.getElementById('banner-preview');
  const removePreviewBtn = document.getElementById('remove-banner-preview');
  const uploadPlaceholder = document.querySelector('.file-upload-placeholder');

  if (bannerInput && previewContainer && previewImage) {
    bannerInput.addEventListener('change', function (e) {
      const file = e.target.files && e.target.files[0];
      if (!file || !file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = ev => {
        previewImage.src = ev.target.result;
        previewContainer.style.display = 'block';
        if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
      };
      reader.readAsDataURL(file);
    });
  }
  if (removePreviewBtn && previewContainer && uploadPlaceholder) {
    removePreviewBtn.addEventListener('click', function(){
      if (bannerInput) bannerInput.value = '';
      previewImage.src = '#';
      previewContainer.style.display = 'none';
      uploadPlaceholder.style.display = 'block';
    });
  }

  // (tuỳ chọn) validate JS trước khi submit
  const form = document.getElementById('edit-event-form');
  if (form) {
    form.addEventListener('submit', function(){
      // ví dụ: kiểm tra tên sự kiện không rỗng
      // const name = document.getElementById('event-name');
      // if (!name.value.trim()) { alert('Vui lòng nhập tên sự kiện'); event.preventDefault(); }
    });
  }
});