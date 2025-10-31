<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include(MIDDLEWARE);

    // Classes
    use App\Classes\DB;
    use App\Classes\RBAC;
    use App\Classes\URLKeeper;
    $db = new DB($pdo);
    $rbac = new RBAC($db, $_SESSION['rbac']['user_id']); 
    
    // Governance Layer 
    // blackBox('user_accounts', 'view', $rbac, $db);
    
    // Combo Box Submit $_PHP_SELF
    if(isset($_POST['submit'])) {	
        $defaultUserRole = $_POST['varUserRole'];
	} else {
        $defaultUserRole = 5; // Class Advisers
    }
    
    // Theme files
    include(DASHBOARDMETA);
?>
</head>
<body>

    <?php include PRELOADER; ?>
    <div id="main-wrapper">
        <?php include DASHBOARDHEADER; ?>
        <?php include SIDEBARDYNAMIC; ?>

        <div class="content-body">
            <div class="container-fluid">
                <?php 
                    $breadCrumb = "Administration, Shop, Products";
                    getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success">
                                <button type="submit" name="submit" class="btn btn-sm btn-secondary"><i class="ti-reload"></i> Refresh</button>
                                <a href="../crud/cu_user_accounts.php?crud=c" class="btn btn-sm btn-secondary text-light"><i class="fa fa-plus-circle"></i> Create New Product</a>
                                
                                <a href="#bottom" class="btn btn-sm btn-secondary float-end"><i class="ti-arrow-down"></i> Bottom</a>
                            </div>
                            <div class="card-body">
                                <div>
                                    <table id="aspTable" class="table table-hover table-striped border" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="dt-head-right">#</th>
                                                <th width="20%">Product Description</th>
                                                <th width="15%">Category</th>
                                                <th>SKU<br>Weight</th>
                                                <th class="dt-head-right">S.R.P.</th>
                                                <th class="dt-head-right">On Sale</th>
                                                <th class="dt-head-right">Sale Price</th>
                                                <th width="15%" class="dt-head-right">Encoder<br>Timestamp</th>
                                                <th class="text-center">Action(s)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $sqlTimeBegin = microtime(true);
                                                
                                                // $sql = "SELECT *, system_users.id, system_users.fullname as encoder
                                                //         FROM shop_products 
                                                //         LEFT JOIN rbac_user_roles On rbac_user_roles.user_id = system_users.id 
                                                //         LEFT JOIN rbac_roles On rbac_roles.id = rbac_user_roles.role_id 
                                                //         LEFT JOIN system_users On system_users.id = shop_products.updated_by   ";

                                                $sql = "SELECT shop_products.*,
                                                            universal_lookup.description As category,
                                                            system_users.fullname As encoder
                                                        FROM shop_products 
                                                        INNER JOIN universal_lookup On universal_lookup.value = shop_products.category_id 
                                                        LEFT JOIN system_users On system_users.id = shop_products.updated_by
                                                        WHERE universal_lookup.category = 'PRODUCT CATEGORY'";

                                                    // if ($defaultUserRole < 999) {
                                                    //     $sql .=  "WHERE rbac_user_roles.role_id = '$defaultUserRole' ";
                                                    // }
                                                    
                                                $dataSet = $db->fetch($sql, [], [], 'all');

                                                foreach ($dataSet as $row) {

                                                    echo ("<tr><td class='dt-body-right'></td>");

                                                    echo ("<td><b>".$row['product_name']."</b><br>"
                                                        .'<i><font color=blue>'.substr($row['description'],0,25).'</font></i><br>'
                                                        .($row['is_active']==0 ? "<small class='text-danger'><i class='ti-na'></i>&nbsp;Not Available</small>": "")."</td>");
                                                    
                                                    echo ("<td>".$row['category']."</td>");
													echo ("<td>".$row['sku']."<br><small class='text-primary'>".$row['weight_g']." Kg.</small></td>");
													echo ("<td class='dt-body-right'><b>".$row['srp']."</b></td>");
                                                    
													echo ("<td class='dt-body-right'>".($row['is_on_sale']==0 ? "<small class='text-primary'><i class='ti-na'></i>&nbsp;No</small>" : 
                                                            "<small class='text-warning'><i class='ti-check'></i>&nbsp;Yes</small>")."</td>");
                                                    
													echo ("<td class='dt-body-right'>".$row['sale_price']."</td>");

                                                    // Latest Update
                                                    echo ("<td class='dt-body-right' data-order='{$row['updated_at']}'>".
                                                        (empty($row['encoder']) ? "<font color='red'>Admin Data</font>" : "<font color='blue'>".formatValues($row['encoder'])."</font>")."<br>".
                                                        formatValues($row['updated_at'], 'datetime', 
                                                            ['dateFormat' => 'M d, Y','timeFormat' => 'h:i:s A', 'dateTag' => 'b', 'timeTag'=> 
                                                            ['small','style="color:gray; font-style:italic;"']]).
                                                        "</td>");

                                                    // Action Buttons
                                                    echo ("<td class='dt-body-center'>");

                                                    if ($rbac->can('system_users:update')) { 
                                                        
                                                            echo ("<a title='Edit' href='../crud/cu_user_accounts.php?crud=u&id=$row[id]' class='btn btn-info btn-sm'>
                                                                <i class='ti-pencil-alt'></i></a>&nbsp;");    
                                                                
                                                            echo ("<a title='Create New Role' href='../crud/cu_user_accounts.php?id=$row[id]' class='btn btn-primary btn-sm'>
                                                                <i class='ti-id-badge'></i></a>&nbsp;");   

                                                            echo ("<a title='Toggle Access Status' href='../sql/toggle_access_status.php?id=$row[id]&status=$row[is_active]' class='btn btn-dark btn-sm'>
                                                                <i class='ti-split-v-alt'></i></a>&nbsp;"); 
                                                       
                                                    
                                                    }
                                                    echo ("</td></tr>");
                                                }
                                                $sqlTimeEnd = microtime(true); 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-success" id="bottom">
                                <?php 
                                    server_execution_time($sqlTimeEnd - $sqlTimeBegin); 
                                    latest_modification("shop_products.php");
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include DASHBOARDFOOTER; ?>
    </div>        
	<?php include DASHBOARDSCRIPTS; ?>

    

    <!-- aspTable /-->
    <script>
        $(function () {
            var t = $('#aspTable').DataTable({
                dom:
                '<"row mb-2"' +
                    '<"col-md-4 d-flex align-items-center"l>' +
                    '<"col-md-4 d-flex justify-content-center"B>' +
                    '<"col-md-4 d-flex justify-content-end"f>' +
                '>' +
                'rt' +
                '<"row mt-2"' +
                    '<"col-md-6"i>' +
                    '<"col-md-6 d-flex justify-content-end"p>' +
                '>',

                autoWidth: false,
                displayLength: 25,
                order: [[1, 'asc']], // initial sort
                buttons: ['copy','csv','excel','pdf','print'],

                language: {
                    paginate: {
                        previous: '&laquo;',  // «
                        next: '&raquo;'       // »
                    }
                },

                columnDefs: [{"searchable": false, "orderable": false, "targets": [0,3] }],
				
                // Continuous numbering across pages, independent of sorting
                rowCallback: function (row, data, displayIndex) {
                    var api = this.api();
                    var pageInfo = api.page.info();
                    $('td:eq(0)', row).html(pageInfo.start + displayIndex + 1);
                },

                // Style DT buttons to Bootstrap look
                initComplete: function () {
                    $('.dt-button')
                    .removeClass('dt-button')
                    .addClass('btn btn-sm btn-secondary me-1');


                    // ✅ Give the search box a unique id based on the table id
                    let api = this.api();
                    let tableId = api.table().node().id;
                    $(api.table().container())
                        .find('input[type="search"]')
                        .attr('id', tableId + '_search');
                }
            });
            
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                });
            }).draw();
        });
    </script>
</body>

</html>