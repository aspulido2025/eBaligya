<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;

    // Classes
    use App\Classes\DB;
    use App\Classes\RBAC;
    use App\Classes\URLKeeper;
    $db = new DB($pdo);
    $rbac = new RBAC($db, $_SESSION['rbac']['user_id']); 

    // Governance Layer 
    // blackBox('role_management', 'view', $rbac, $db); 

    // Theme files
    include DASHBOARDMETA;
?>
<!-- Datatable Styles -->
<style>
    th.dt-head-right,   
    td.dt-body-right {
        text-align: right !important;
    }

    /* ✅ force wrapping in aspTable */
    #aspTable th {
        white-space: normal !important;     /* allow multi-line */
        word-break: break-word;             /* break long strings (URLs, JSON, etc.) */
        color: #fff !important;           /* force white header text */
    }

    #aspTable td {
        white-space: normal !important;     /* allow multi-line */
        word-break: break-word;             /* break long strings (URLs, JSON, etc.) */
    }

    /* Make DT buttons visible and centerable */
    div.dt-buttons {
        display: inline-flex !important;
        float: none !important;             /* your theme was forcing float */
        gap: .2rem;
    }

    /* Keep bottom info/pager tidy (optional) */
    .dataTables_wrapper .row > [class*="col-"] { align-items: center; }
</style>
</head>
<body>

    <?php include PRELOADER; ?>
    <div id="main-wrapper">
        <?php include DASHBOARDHEADER; ?>
        <?php include SIDEBARDYNAMIC; ?>

        <div class="content-body">
            <div class="container-fluid">
                <?php 
                    $breadCrumb = "Administration, RBAC, Role Management";
                    getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                ?>
                <div class="row">
                    <div class="col-lg-12 col-xlg-12 col-md-12">
                        <div class="card">
                            <div class="card-header bg-success d-flex align-items-center">
                                <button onClick="history.go(0);" class="btn btn-sm btn-primary me-2"><i class="ti-reload"></i> Refresh</button>
                                
                                <?php
                                    // CREATE ROLE PROFILE
                                    $link = BASE_URL . '/rbac/create_role.php?token=' . urlencode(URLKeeper::encode('', ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION));
                                    if ($rbac->can('role_management:create')) { ?>  
                                        <a title='Create Role Profile' href="<?php echo $link;  ?>" class="btn btn-sm btn-primary me-2">
                                            <i class="fa fa-plus-circle"></i> Create New Role</a>
                                <?php } ?>

                                <a href="#bottom" class="btn btn-sm btn-primary ms-auto"><i class="ti-arrow-down"></i> Bottom</a>
                            </div>
                            <div class="card-body">                                
                                <div>
                                    <table id="aspTable" class="table table-hover table-striped border" cellspacing="0" width="100%">
                                        <thead>
                                            <tr class="bg-dark">
                                                <th class="dt-head-right">#</th>
                                                <th>Role Name</th>
                                                <th>Description</th>
                                                <th class="dt-head-right">Encoder<br>Timestamp</th>
                                                <th>Actions<br>Buttons</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $sqlTimeBegin = microtime(true);
                                                
                                                $sql = "SELECT * FROM rbac_roles WHERE id BETWEEN 1 AND 4 OR id > 15 ORDER BY id ASC"; // 

                                                $dataSet = $db->fetch($sql, [], [], 'all');

                                                foreach ($dataSet as $row) {

                                                    echo ("<tr><td align='right'></td>");
                                                    
                                                    echo ("<td data-order='{$row['id']}'><b>".formatValues($row['access'])."</b></td>");
                                                    echo ("<td>".formatValues($row['description'])."</td>");

                                                    // Latest Update
                                                    echo ("<td align='right' data-order='{$row['updated']}'>".
                                                            (empty($row['encoder']) ? "<font color='red'>Admin Data</font>" : "<font color='blue'>".formatValues($row['encoder'])."</font>").
                                                            "<br>".formatValues($row['updated'], 'datetime', 
                                                            ['dateFormat' => 'M d, Y','timeFormat' => 'h:i:s A', 'dateTag' => 'b', 'timeTag'=> ['small','style="color:gray; font-style:italic;"']]).
                                                        "</td>");

                                                    // Action Buttons
                                                    echo ("<td class='dt-body-center'>");

                                                    // UPDATE ROLE PROFILE
                                                    if ($rbac->can('role_management:update')) {
                                                        $link =  BASE_URL . '/rbac/update_role.php?token=' . urlencode(URLKeeper::encode($row['id'], ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION)); 
                                                        echo ("<a title='Update Role Profile' href='$link' class='btn btn-secondary btn-sm'>
                                                            <i class='ti-pencil-alt'></i></a>&nbsp;");
                                                    }

                                                    // UPDATE ROLE PERMISSIONS 
                                                    if ($rbac->can('role_management:update_permissions')) {
                                                        $link = BASE_URL . '/rbac/update_role_permissions.php?token=' . urlencode(URLKeeper::encode($row['id'], ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION)); 
                                                        echo ("<a title='Update Role Permissions' href='$link' class='btn btn-primary btn-sm'>
                                                            <i class='ti-panel'></i></a>&nbsp;");
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
                                    latest_modification("tbl_role_management.php");
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
                    // ✅ Buttons secondary
                    $('.dt-button').removeClass('dt-button').addClass('btn btn-sm btn-primary me-1');

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
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary me-1');
        });
    </script>
</body>

</html>