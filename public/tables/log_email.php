<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;

    // Classes
    use App\Classes\DB;
    use App\Classes\DateHelper;
    use App\Classes\URLKeeper;
    use App\Classes\UniversalLookup;
    $db = new DB($pdo);
    $lookup = new UniversalLookup($pdo);

    // Token 
    if (isset($_GET['token'])) {
        $timeBound = URLKeeper::decode('token' ?? null, ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION);        
    }

    // Governance Layer 
    // blackBox('role_management', 'view', $rbac, $db); 

    // Combo Box Submit $_PHP_SELF
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $varDateRange = $_POST['varDateRange'];
        list($dateMin, $dateMax) = explode(' - ', $varDateRange);
        if ($dateMin <> $_SESSION['rbac']['date_min'] OR $dateMax <> $_SESSION['rbac']['date_max']) {
            $db->exec("UPDATE system_users SET date_min = ?, date_max = ? WHERE id = ?", [ $dateMin, $dateMax, $_SESSION['rbac']['user_id'] ], [ 'string', 'string', 'string' ] );
            $_SESSION['rbac']['date_min'] = $dateMin;
            $_SESSION['rbac']['date_max'] = $dateMax;
        }
	} else {
        // Initialize/Reset Local Variables
        if($_SESSION['rbac']['date_min'] <> "0000-00-00") {
            $varDateRange = $_SESSION['rbac']['date_min']." - ".$_SESSION['rbac']['date_max'];
        } else {
            $varDateRange = date('Y-m-01')." - ".date('Y-m-d');
        }
        list($dateMin, $dateMax) = explode(' - ', $varDateRange);
    }
    $range = DateHelper::localDateRangeToUtcRange($dateMin, $dateMax);

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
                        $breadCrumb = "Administration, System Logs, eMail Logs";
                        getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success">
                                    <form class="form-inline w-100" method="post" action="<?php $_PHP_SELF ?>">	
                                        <div class="form-body">
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label text-white" for="varDateRange">Select Date Range</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text"> <i class="ti-calendar"></i></span>
                                                            <input type='text' class="form-control" name="varDateRange" id="varDateRange" value="<?php echo $varDateRange; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-sm btn-primary"><i class="ti-reload"></i> Refresh</button>
                                        <a href="#bottom" class="btn btn-sm btn-primary float-end"><i class="ti-arrow-down"></i> Bottom</a>
                                    </form>
                                </div>

                                <div class="card-body">
                                    <div>
                                        <table id="aspTable" class="table table-hover table-striped border" cellspacing="0" width="100%">
                                            <thead>
                                                <tr class="bg-secondary">
                                                    <th class="dt-head-right">#</th>            
                                                    <th>eMail Address</th>
                                                    <th>eMail String</th>
                                                    <th>Status</th>
                                                    <th class="dt-head-right">Sender<br>Timestamp</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $sqlTimeBegin = microtime(true);
    
                                                    $sql = "SELECT log_email.*, system_users.fullname AS sender
                                                            FROM log_email
                                                            LEFT JOIN system_users ON system_users.id = log_email.created_id
                                                            WHERE log_email.created BETWEEN ? AND ? ";

                                                    $dataSet = $db->fetch($sql, [ $range['start'], $range['end'] ], [], 'all');
                                                    
                                                    foreach ($dataSet as $row) {
                                                        echo ("<tr><td class='dt-body-right'></td>");

                                                        // Masking - Replace with RBAC enforcement
                                                        echo ("<td>".($_SESSION['rbac']['role_id'] > 2 ?
                                                            mask_email_address($row['email_address']) :
                                                            $row['email_address']) . 
                                                            "</td>");
                                                        echo ("<td class='text-info'>".($_SESSION['rbac']['role_id'] > 2 ?
                                                            formatValues(preg_replace('/^[^,]*/', 'Your Verification Code is ********', $row['email_string'])) :
                                                            formatValues($row['email_string'])) .
                                                            "</td>");
                                                        // End Masking

                                                        // Status Code Map
                                                        $desc = $lookup->getDescription("EMAIL CODES", $row['sending_status']);
                                                        echo ("<td>".formatValues($desc)."</td>");
                                                        
                                                        // TimeStamp
                                                        echo ("<td class='dt-body-right' data-order='{$row['created']}'>".
                                                                (empty($row['sender']) ? "<font color='red'>Admin Data</font>" : "<font color='blue'>".formatValues($row['sender'])."</font>").
                                                                "<br>".formatValues($row['created'], 'datetime', 
                                                                ['dateFormat' => 'M d, Y','timeFormat' => 'h:i:s A', 'dateTag' => 'b', 'timeTag'=> ['small','style="color:gray; font-style:italic;"']]).
                                                            "</td>");

                                                        echo ("</tr>");
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
                                        latest_modification("log_error.php");
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
                // TOP: length (L) left, Buttons (B) centered, Filter (f) right
                // MIDDLE: table (t)
                // BOTTOM: info (i) left, paging (p) right
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
                order: [[4, 'desc']], // initial sort
                buttons: ['copy','csv','excel','pdf','print'],

                language: {
                    paginate: {
                        previous: '&laquo;',  // «
                        next: '&raquo;'       // »
                    }
                },

                columnDefs: [
                    
                    // # column (no sort/search)
                    { targets: 0, searchable: false, orderable: false, className: 'dt-body-right' },

                    // Extra Context preview (col 4)
                    {
                        targets: [4],
                        render: function (data, type, row, meta) {
                            if (!data) return "";
                            try {
                                let obj = JSON.parse(data);
                                let preview = JSON.stringify(obj);
                                if (preview.length > 50) preview = preview.substring(0, 50) + "...";
                                let jsonStr = JSON.stringify(obj)
                                .replace(/'/g, "&apos;")
                                .replace(/"/g, "&quot;");
                                let colTitle = meta.col == 4 ? "Extra Context" : ("");
                                return `<span class="json-preview text-primary"
                                            data-json="${jsonStr}"
                                            data-title="${colTitle}"
                                            style="cursor:pointer;">
                                            ${preview}
                                        </span>`;
                            } catch(e) {
                                return data;
                            }
                        }
                    }
                ],

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

            // JSON preview modal
            $(document).on("click", ".json-preview", function() {
                let jsonStr = $(this).attr("data-json");
                let title   = $(this).attr("data-title");
                $("#jsonModalLabel").text("JSON Viewer - " + title);
                try {
                    let obj = JSON.parse(jsonStr);
                    let pretty = JSON.stringify(obj, null, 2);
                    $("#jsonModalContent").html("<pre>" + syntaxHighlight(pretty) + "</pre>");
                } catch(e) {
                    $("#jsonModalContent").text(jsonStr);
                }
                $("#jsonModal").modal("show");
            });
        });
        </script>

        <!-- Separate JS init -->
        <script>
            $(function() {
                var minDate = "<?= DATE_MINIMUM ?>";
                var maxDate = moment().add(1, 'months');

                $('#varDateRange').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')]
                },
                minDate: new Date(minDate),
                maxDate: new Date(maxDate),
                locale: { format: 'YYYY-MM-DD' }
                });
            });
        </script>

    </body>
</html>