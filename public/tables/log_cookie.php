<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;

    // Classes
    use App\Classes\DB;
    use App\Classes\DateHelper;
    use App\Classes\URLKeeper;
    $db = new DB($pdo);

    //  Token 
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

    /* ✅ force wrapping in cookieTable */
    #cookieTable th {
        white-space: normal !important;     /* allow multi-line */
        word-break: break-word;             /* break long strings (URLs, JSON, etc.) */
        color: #fff !important;           /* force white header text */
    }

    #cookieTable td {
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
                        $breadCrumb = "Administration, System Logs, Cookie Logs";
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
                                        <table id="cookieTable" class="table table-hover table-striped border" cellspacing="0" width="100%">
                                            <thead>
                                                <tr class='bg-secondary'>
                                                    <th class="dt-head-right">#</th> 
                                                    <th>User</th>
                                                    <th>Selector</th>
                                                    <th class="dt-head-right">Expiration</th>
                                                    <th>User Agent</th>
                                                    <th class="dt-head-right">Timestamp</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-success d-flex justify-content-between" id="bottom">
                                    <?php server_execution_time_placeholder(); ?>
                                    <span><?php latest_modification("log_cookie.php"); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		    <?php include DASHBOARDFOOTER; ?>
        </div>
	    <?php include DASHBOARDSCRIPTS; ?>
                
        
        <script>
        $(function() {
            $('#cookieTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'ajax/ajax_cookie.php',
                type: 'GET',
                dataSrc: 'data',
                deferRender: true,
                autoWidth: false,
                pageLength: 25,
                order: [[5, 'desc']], // order by created DESC

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
                buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                columns: [
                    { data: null },  // will be row number
                    
                    { data: 'fullname', 
                        render: function(data, type, row) {
                            return '<b>' + data + '</b>'
                        }
                    },

                    { data: 'selector' },
                    { data: 'expires_at',
                        render: function (data, type, row) {

                            if (type === 'display' || type === 'filter') {
                                let d = new Date(data);

                                let optionsDate = { month: 'short', day: '2-digit', year: 'numeric' };
                                let optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit',
                                                    hour12: true };

                                let formattedDate = d.toLocaleDateString('en-US', optionsDate);
                                let formattedTime = d.toLocaleTimeString('en-US', optionsTime);

                                return `${formattedDate}<br>
                                        <small style="color:gray; font-style:italic;">${formattedTime}</small>`;
                            }
                            return data; // raw value for sorting/searching
                        }
                     },



                    { data: null,
                        render: function (data, type, row) {
                            return row.ipaddress  
                                    + "<br>" +
                                '<small>' + row.user_agent + '</small>';
                        }
                    },

                    { data: "created_at",
                        render: function (data, type, row) {

                            if (type === 'display' || type === 'filter') {
                                let d = new Date(data);

                                let optionsDate = { month: 'short', day: '2-digit', year: 'numeric' };
                                let optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit',
                                                    hour12: true };

                                let formattedDate = d.toLocaleDateString('en-US', optionsDate);
                                let formattedTime = d.toLocaleTimeString('en-US', optionsTime);

                                return `<b>${formattedDate}</b><br>
                                        <small style="color:gray; font-style:italic;">${formattedTime}</small>`;
                            }
                            return data; // raw value for sorting/searching
                        }
                    }

                ],

                language: {
                    paginate: {
                        previous: '&laquo;',  // «
                        next: '&raquo;'       // »
                    }
                },
                
                columnDefs: [
                    { targets: 0, orderable: false },
                    { targets: [0,3,5], className: 'dt-body-right dt-head-right' } 
                    // 👆 applies right alignment to row#, ref_id, user_id, created
                ],

                drawCallback: function(settings) {
                    let api = this.api();
                    api.column(0, { search:'applied', order:'applied' }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1 + settings._iDisplayStart;
                    });

                    let tableId = api.table().node().id;
                    $('#' + tableId).on('xhr.dt', function(e, settings, json) {
                        if (json.debug) {
                            const exec = (json.debug.execution_ms / 1000).toFixed(4) + ' s';
                            const mem  = json.debug.memory_mb ? json.debug.memory_mb.toFixed(2) + ' MB' : '—';
                            const hit  = json.debug['opcache_hit%'] ? json.debug['opcache_hit%'].toFixed(2) + '%' : '—';

                            $('#execTime').html(`
                                <span class="text-warning">⚙️</span> ${exec} 
                                &nbsp;|&nbsp; <span class="text-info">💾</span> ${mem} 
                                &nbsp;|&nbsp; <span class="text-success">🧩</span> ${hit}
                            `);
                        }
                    });
                },

                initComplete: function () {
                    // ✅ Style DT buttons to Bootstrap look
                    $('.dt-button')
                        .removeClass('dt-button')
                        .addClass('btn btn-sm btn-primary me-1');

                    // ✅ Ensure unique search input ID per table
                    let api = this.api();
                    let tableId = api.table().node().id; // e.g. "vendorsTable"
                    $(api.table().container())
                        .find('input[type="search"]')
                        .attr('id', tableId + '_search'); // → "vendorsTable_search"
                    
                }
                
                
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