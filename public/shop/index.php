<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Classes
    use App\Classes\DB;
    $db = new DB($pdo);

    // Product Categories
    $sqlCat = "SELECT ul.value, ul.description, COUNT(sp.id) AS total_rows
        FROM universal_lookup ul
        LEFT JOIN shop_products sp ON sp.category_id = ul.value
        WHERE ul.category = 'PRODUCT CATEGORY'
        GROUP BY ul.value, ul.description
        ORDER BY ul.description ";
    $productCategorySet = $db->fetch($sqlCat, [], [], 'all');


    $categoryId = 0;

    if ((isset($_GET['category_id'])) OR (isset($_POST['submit']))) {

        if (isset($_GET['category_id'])) {
            $categoryId = $_GET['category_id'];
            // Products
            $sql = "SELECT shop_products.*, universal_lookup.description as category 
                    FROM shop_products 
                    INNER JOIN universal_lookup ON universal_lookup.value = shop_products.category_id
                    WHERE universal_lookup.category = 'PRODUCT CATEGORY' ";
                    if ($categoryId > 0) {
                        $sql .= " AND category_id = $categoryId ";
                    }
        }

        if (isset($_POST['submit'])) {
            $searchKey = $_POST['search'];
            // Products
            $sql = "SELECT shop_products.*, universal_lookup.description as category 
                    FROM shop_products 
                    INNER JOIN universal_lookup ON universal_lookup.value = shop_products.category_id
                    WHERE universal_lookup.category = 'PRODUCT CATEGORY' AND
                        shop_products.product_name LIKE '%" . trim($searchKey) . "%'";
        }

    } else {
        // Products
        $sql = "SELECT shop_products.*, universal_lookup.description as category 
                FROM shop_products 
                INNER JOIN universal_lookup ON universal_lookup.value = shop_products.category_id
                WHERE universal_lookup.category = 'PRODUCT CATEGORY' ";
    }

    $productSet = $db->fetch($sql, [], [], 'all');
    

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
                        <h1 class="page-title">Shop</h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12">
                    <ul class="breadcrumb-nav">
                        <li><a href="index.html"><i class="lni lni-home"></i> Home</a></li>
                        <li><a href="javascript:void(0)">Shop</a></li>
                        <li>Shop Grid</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Product Grids -->
    <section class="product-grids section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <!-- Start Product Sidebar -->
                    <div class="product-sidebar">
                        <!-- Start Single Widget -->
                        <div class="single-widget search">
                            <h3>Search Product</h3>
                            <form method="post" action="<?php $_PHP_SELF ?>">
                                <input type="text" name='search' placeholder="Search Here...">
                                <button type="submit" name='submit'><i class="lni lni-search-alt"></i></button>
                            </form>
                        </div>
                        <!-- End Single Widget -->
                        <!-- Start Single Widget -->
                        <div class="single-widget">
                            <h3>All Categories</h3>
                            <ul class="list">
                                <?php 
                                    echo ("<li>");
                                        echo ("<a href='" . SHOP_URL . "/index.php?category_id=0'>Show All</a>");
                                    echo ("</li>");
                                    foreach ($productCategorySet as $row) {
                                        
                                        echo ("<li>");
                                            echo ("<a href='" . SHOP_URL . "/index.php?category_id=$row[value]'>" . $row['description']."</a><span>&nbsp;(".$row['total_rows'].")</span>");
                                        echo ("</li>");
                                    }
                                ?>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                    <!-- End Product Sidebar -->
                </div>
                <div class="col-lg-9 col-12">
                    <div class="product-grids-head">
                        <div class="product-grid-topbar">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12">
                                    <!-- <div class="product-sorting">
                                        <label for="sorting">Sort by:</label>
                                        <select class="form-control" id="sorting">
                                            <option>Popularity</option>
                                            <option>Low - High Price</option>
                                            <option>High - Low Price</option>
                                            <option>Average Rating</option>
                                            <option>A - Z Order</option>
                                            <option>Z - A Order</option>
                                        </select>
                                        <h3 class="total-show-product">Showing: <span>1 - 12 items</span></h3>
                                    </div> -->
                                </div>
                                <div class="col-lg-5 col-md-4 col-12">
                                    <nav>
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-grid-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-grid" type="button" role="tab"
                                                aria-controls="nav-grid" aria-selected="true"><i
                                                    class="lni lni-grid-alt"></i></button>
                                            <button class="nav-link" id="nav-list-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-list" type="button" role="tab"
                                                aria-controls="nav-list" aria-selected="false"><i
                                                    class="lni lni-list"></i></button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-grid" role="tabpanel" aria-labelledby="nav-grid-tab">
                                <div class="row">
                                    <?php 
                                        foreach ($productSet as $row) {
                                            echo ("<div class='col-lg-4 col-md-6 col-12'>");
                                                echo ("<div class='single-product'>");
                                                    echo ("<div class='product-image'>");
                                                        $image = "/resize.php?file=".$row['image']."&w=335&h=335";
                                                        echo ("<img src='". SHOP_IMAGES . $image. "' alt='#'>");
                                                        echo ("<div class='button'>");
                                                            echo ("<a href='product-details.php?id=$row[id]' class='btn'><i class='lni lni-eye'></i> Details</a>");
                                                        echo ("</div>");
                                                    echo ("</div>");
                                                    echo ("<div class='product-info'>");
                                                        echo ("<span class='category'>".$row['category']."</span>");
                                                        echo ("<h4 class='title'>");
                                                            echo("<a href='product-grids.html'>".$row['product_name']."</a>");
                                                        echo("</h4>");
                                                        echo ("<ul class='review'>");
                                                            echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                            echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                            echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                            echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                            echo ("<li><i class='lni lni-star'></i></li>");
                                                            echo ("<li><span>4.0 Rating</span></li>");
                                                        echo ("</ul>");
                                                        echo ("<div class='price'>");
                                                            echo ("<span>".$row['srp']."</span>");
                                                        echo ("</div>");
                                                    echo ("</div>");
                                                echo ("</div>");
                                            echo ("</div>");
                                        }    
                                    ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                                <div class="row">
                                    <?php 
                                        foreach ($productSet as $row) {
                                            echo ("<div class='col-lg-12 col-md-12 col-12'>");
                                                echo ("<div class='single-product'>");
                                                    echo ("<div class='row align-items-center'>");
                                                        echo ("<div class='col-lg-4 col-md-4 col-12'>");
                                                            echo ("<div class='product-image'>");
                                                                $image = "/resize.php?file=".$row['image']."&w=335&h=335";
                                                                echo ("<img src='". SHOP_IMAGES . $image. "' alt='#'>");
                                                                echo ("<div class='button'>");
                                                                    echo ("<a href='product-details.php?id=$row[id]' class='btn'><i class='lni lni-eye'></i> Details</a>");
                                                                echo ("</div>");
                                                            echo ("</div>");
                                                        echo ("</div>");
                                                        echo ("<div class='col-lg-8 col-md-8 col-12'>");
                                                            echo ("<div class='product-info'>");
                                                                echo ("<span class='category'>".$row['category']."</span>");
                                                                echo ("<h4 class='title'>");
                                                                    echo("<a href='product-grids.html'>".$row['product_name']."</a>");
                                                                echo ("</h4>");echo ("<ul class='review'>");
                                                                    echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                                    echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                                    echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                                    echo ("<li><i class='lni lni-star-filled'></i></li>");
                                                                    echo ("<li><i class='lni lni-star'></i></li>");
                                                                    echo ("<li><span>4.0 Rating</span></li>");
                                                                echo ("</ul>");
                                                                echo ("<div class='price'>");
                                                                    echo ("<span>".$row['srp']."</span>");
                                                                echo ("</div>");
                                                            echo ("</div>");
                                                        echo ("</div>");
                                                    echo ("</div>");
                                                echo ("</div>");
                                            echo ("</div>");
                                        }    
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Product Grids -->

    <!-- Start Footer Area -->
    <?php include SHOP_FOOTER; ?>
    <!--/ End Footer Area -->

</body>

</html>