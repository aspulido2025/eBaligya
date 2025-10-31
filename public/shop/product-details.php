<?php 
    require __DIR__ . '/../../config/init.php';

    // Classes
    use App\Classes\DB;
    $db = new DB($pdo);

    // Products
    $id = $_GET['id'];
    $sql = "SELECT shop_products.*, universal_lookup.description as category 
            FROM shop_products 
            INNER JOIN universal_lookup ON universal_lookup.value = shop_products.category_id
            WHERE universal_lookup.category = 'PRODUCT CATEGORY' AND id = ?";
    $row = $db->fetch($sql, [$id ], [], 'row');

    $imageBig = "/resize.php?file=".$row['image']."&w=335&h=335";
    // SEO    
    include SHOP_META;
?>
    
    <!-- Start Header Area -->
    <?php include SHOP_HEADER; ?>
    <!--/ End Header Area -->

    <!-- Start Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="breadcrumbs-content">
                        <h1 class="page-title"><?= $row['product_name'] ?></h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12">
                    <ul class="breadcrumb-nav">
                        <li><a href="index.html"><i class="lni lni-home"></i> Home</a></li>
                        <li><a href="index.html">Shop</a></li>
                        <li><?= $row['product_name'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Item Details -->
    <section class="item-details section">
        <div class="container">
            <div class="top-area">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="product-images">
                            <main id="gallery">
                                <div class="main-img mt-0">
                                    <?php 
                                        echo ("<img src='". SHOP_IMAGES . $imageBig. "' alt='#'>");
                                    ?>
                                </div>
                                <!-- <div class="images">
                                    <img src="https://placehold.co/1000x670" class="img" alt="#">
                                    <img src="https://placehold.co/1000x670" class="img" alt="#">
                                    <img src="https://placehold.co/1000x670" class="img" alt="#">
                                    <img src="https://placehold.co/1000x670" class="img" alt="#">
                                    <img src="https://placehold.co/1000x670" class="img" alt="#">
                                </div> -->
                            </main>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="product-info">
                            <h2 class="title"><?= $row['product_name'] ?></h2>
                            <p class="category"><i class="lni lni-tag"></i><?= $row['category'] ?></a></p>
                            <h3 class="price"><?php echo ($row['is_on_sale']==1 ? $row['sale_price']."<span>".$row['srp']."</span>" : $row['srp']); ?></h3>
                            <p class="info-text"><?= $row['description'] ?></p>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="color">Weight</label><br>
                                        <span><?= $row['weight_g'] ?>&nbsp;Kg</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="form-group quantity">
                                        <label for="color">Quantity</label>
                                        <select class="form-control">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bottom-content">
                                <div class="row align-items-end">
                                    <div class="col-lg-4 col-md-4 col-12">
                                        <div class="button cart-button">
                                            <button class="btn" style="width: 100%;">Add to Cart</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Item Details -->


    <!-- Start Footer Area -->
    <?php include SHOP_FOOTER; ?>
    <!--/ End Footer Area -->


    <script type="text/javascript">
        const current = document.getElementById("current");
        const opacity = 0.6;
        const imgs = document.querySelectorAll(".img");
        imgs.forEach(img => {
            img.addEventListener("click", (e) => {
                //reset opacity
                imgs.forEach(img => {
                    img.style.opacity = 1;
                });
                current.src = e.target.src;
                //adding class 
                //current.classList.add("fade-in");
                //opacity
                e.target.style.opacity = opacity;
            });
        });
    </script>
</body>

</html>