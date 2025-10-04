<?php
// JS tương ứng với trang hiện tại
$jsPath = "../assets/js/{$page}.js";
?>
<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-section">
        <h4>Về EvenUser</h4>
        <p>Nền tảng quản lý & khám phá sự kiện hiện đại dành cho sinh viên.</p>
      </div>
      <div class="footer-section">
        <h4>Liên kết nhanh</h4>
        <ul>
          <li><a href="<?php echo $baseUrl; ?>/index.php">Trang chủ</a></li>
          <li><a href="<?php echo $baseUrl; ?>/../app/views/event/list.php">Danh sách sự kiện</a></li>
          <li><a href="<?php echo $baseUrl; ?>/../app/views/event/add.php">Tạo sự kiện</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Kết nối</h4>
        <div class="social-links">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      © <?php echo date('Y'); ?> EvenUser. All rights reserved.
    </div>
  </div>
</footer>

<?php if (file_exists($jsPath)): ?>
  <script src="<?php echo $jsPath; ?>"></script>
<?php endif; ?>
</body>
</html>
