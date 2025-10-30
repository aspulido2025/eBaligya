<?php 
    require __DIR__ . '/../../config/init.php';
    include SHOP_META;
?>

<body>

  <!-- Start Error Area -->
  <div class="maill-success">
    <div class="d-table">
      <div class="d-table-cell">
        <div class="container">
          <div class="success-content">
            <i class="lni lni-envelope"></i>
            <h2>Your Mail Sent Successfully</h2>
            <p>Thanks for contacting with us, We will get back to you asap.</p>
            <div class="button">
              <a href="index.php" class="btn">Back to Home</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Error Area -->

  <!-- ========================= JS here ========================= -->
  <script src="assets/js/bootstrap.min.js"></script>
  <script>
    window.onload = function () {
      window.setTimeout(fadeout, 500);
    }

    function fadeout() {
      document.querySelector('.preloader').style.opacity = '0';
      document.querySelector('.preloader').style.display = 'none';
    }
  </script>
</body>

</html>