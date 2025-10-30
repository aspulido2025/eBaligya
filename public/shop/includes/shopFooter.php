<?php ?>
<!-- Start Footer Area -->
    <footer class="footer">
        <!-- Start Footer Top -->
        <div class="footer-top">
            <div class="container">
                <div class="inner-content">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="footer-logo">
                                <a href="index.php">
                                    <img src="<?= SHOP_URL . '/assets/images/logo/logo_full.png' ?>" alt="#">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                            <div class="footer-newsletter">
                                <h4 class="title">
                                    Subscribe to our Newsletter
                                    <span>Get all the latest information, Sales and Offers.</span>
                                </h4>
                                <div class="newsletter-form-head">
                                    <form action="#" method="get" target="_blank" class="newsletter-form">
                                        <input id="subscribe" name="EMAIL" placeholder="Email address here..." type="email" autocomplete="EMAIL">
                                        <div class="button">
                                            <button class="btn">Subscribe<span class="dir-part"></span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Footer Top -->
        <!-- Start Footer Middle -->
        <div class="footer-middle">
            <div class="container">
                <div class="bottom-inner">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-contact">
                                <h3>Get In Touch With Us</h3>
                                <p class="phone"><?= SHOP_PHONE ?></p>
                                <ul>
                                    <li><span>Monday-Saturday: </span> 8:00 am - 5:00 pm</li>
                                </ul>
                                <p class="mail">
                                    <a href="mailto:aspulidoconsultancy@gmail.com"><?= SHOP_EMAIL ?></a>
                                </p>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer our-app">
                                <h3>Our Mobile App</h3>
                                <ul class="app-btn">
                                    <li>
                                        <a href="javascript:void(0)">
                                            <i class="lni lni-apple"></i>
                                            <span class="small-title">PWA ongoing development</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <i class="lni lni-play-store"></i>
                                            <span class="small-title">PWA ongoing development</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-link">
                                <h3>Information</h3>
                                <ul>
                                    <li><a href="about-us.php">About Us</a></li>
                                    <li><a href="contact.php">Contact Us</a></li>
                                    <li><a href="javascript:void(0)">Sitemap</a></li>
                                    <li><a href="faq.php">FAQs Page</a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-link">
                                <h3>Product Categories</h3>
                                <ul>
                                    <li><a href="javascript:void(0)">Herbal</a></li>
                                    <li><a href="javascript:void(0)">Cosmetics</a></li>
                                    <li><a href="javascript:void(0)">Apparrel</a></li>
                                    <li><a href="javascript:void(0)">Mobile Phones</a></li>
                                    <li><a href="javascript:void(0)">Others</a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Footer Middle -->

        <!-- Start Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="inner-content">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-12">
                            <div class="payment-gateway">
                                <span>We Accept:</span>
                                <img src="<?= SHOP_URL . '/assets/images/footer/credit-cards-footer.png' ?>" alt="#">
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="copyright">
                                <?php echo ("<p>" . PROJECT . ' ' . TITLE . "&nbsp;" . VERSION . " <br> " . COPYRIGHT . date('Y')." <a href='#'>" . AUTHOR . "</a><br>All Rights Reserved.</p>"); ?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <ul class="socila">
                                <li>
                                    <span>Follow Us On:</span>
                                </li>
                                <li><a href="javascript:void(0)"><i class="lni lni-facebook-filled"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="lni lni-instagram"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="lni lni-google"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Footer Bottom -->
    </footer>


<!-- JS -->
<!-- ========================= scroll-top ========================= -->
    <a href="#" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>


    <!-- Register Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register("<?= SHOP_URL . '/service-worker.js' ?>")
        .then(() => console.log('✅ Service Worker registered'))
        .catch(err => console.error('SW registration failed:', err));
    }
    </script>

    <!-- ========================= JS here ========================= -->
    <script src="<?= SHOP_URL . '/assets/js/bootstrap.min.js' ?>"></script>
    <script src="<?= SHOP_URL . '/assets/js/tiny-slider.js' ?>"></script>
    <script src="<?= SHOP_URL . '/assets/js/glightbox.min.js' ?>"></script>
    <script src="<?= SHOP_URL . '/assets/js/main.js' ?>"></script>
