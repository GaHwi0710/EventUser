document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-link');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.dataset.tab;
            const targetPane = document.getElementById(targetId);

            // Bỏ active ở tất cả các tab và nội dung
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));

            // Thêm active cho tab và nội dung được chọn
            this.classList.add('active');
            targetPane.classList.add('active');
        });
    });
});