<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap see_wrap">

<h1><?php echo __('Sessions Activity','activitytime'); ?></h1>

<div class="see-wrapper">
    <div class="see-panel see-panel-default">
        <div class="see-panel-heading flex">
            <h3 class="see-panel-title"><?php echo __('All sessions','activitytime'); ?></h3>
            <a href="#bulk_remove-form" id="bulk_remove" class="page-title-action pull-right popup-with-form"><i class="fa fa-remove"></i>&nbsp;&nbsp;<?php echo __('Bulk remove','activitytime')?></a>
        </div>
        <div class="see-panel-body">

            <!-- Data Table -->
            <div class="box box-without-bottom-padding">
                <div class="tableWrap dataTable table-responsive js-select">
                    <table id="din-table" class="table table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th data-priority="1">#</th>
                                <th data-priority="2"><?php echo __('User', 'activitytime'); ?></th>
                                <th data-priority="2"><?php echo __('Time Start', 'activitytime'); ?></th>
                                <th data-priority="3"><?php echo __('Last activity', 'activitytime'); ?></th>
                                <th data-priority="2"><?php echo __('Total (m:s)', 'activitytime'); ?></th>
                                <th data-priority="3"></th>
                                <th><input type="checkbox" class="selectAll" name="selectAll" value="all"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th><input type="text" name="filter_id" class="dinamic_par"  placeholder="<?php echo __('Filter #', 'activitytime'); ?>" /></th>
                                <th><input type="text" name="filter_user" id="filter_user" class="dinamic_par" value="<?php echo wmvc_show_data('filter_user', $_GET, ''); ?>"  placeholder="<?php echo __('Filter User', 'activitytime'); ?>" /></th>
                                <th><input type="text" name="filter_time_start" class="dinamic_par" placeholder="<?php echo __('Filter Time Start', 'activitytime'); ?>" /></th>
                                <th><input type="text" name="filter_time_end" class="dinamic_par" placeholder="<?php echo __('Filter Last activity', 'activitytime'); ?>" /></th>
                                <th><input type="text" name="filter_total" class="dinamic_par" placeholder="<?php echo __('Filter Total', 'activitytime'); ?>" /></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-inline">
                <div class="footer-btns">
                    <a href="<?php echo admin_url("admin.php?page=actt_sessions&function=export_csv_sessions"); ?>" class="btn btn-warning pull-right <?php if ( !function_exists('activitytimepro_fms') || !activitytimepro_fms()->is_plan_or_trial('activitytimepropro') ) echo 'wal-pro'; ?>"><i class="fa fa-download"></i>&nbsp;&nbsp;<?php echo __('Export CSV','activitytime')?></a>
                    <a href="#clear_filters" id="clear_filters" class="btn btn-danger pull-right pull-right"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo __('Clear all filters','activitytime')?></a>
                </div>
            </div>

        </div>
    </div>
    
</div>
</div>


<?php

wp_enqueue_style('activitytime_basic_wrapper');
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'dataTables-responsive' );
wp_enqueue_script( 'dataTables-select' );

wp_enqueue_style( 'dataTables-select' );
?>

<script>
 
var wal_timer_live_monitoring;
var temp_change = '';

// Generate table
jQuery(document).ready(function($) {
    var table;

    /* clear all filters*/
    $('#clear_filters').click(function(e){
        e.preventDefault();
        $('.dinamic_par:not([name="sw_log_count"]):not([name="sw_log_search"])').val('').trigger('change');
        $('.dinamic_par[name="sw_log_count"]').val('10').trigger('change');
        /*fix if set not date */
        //jQuery('#filter_date').data("DateTimePicker").date(new Date());
        $('#filter_date').data("DateTimePicker").clear()
        table.search('');
        table.draw();
        return false;
    });

    //$(".selectAll").unbind();

    $(".selectAll").on( "click", function(e) {
        if ($(this).is( ":checked" )) {
            table.rows(  ).select();        
            //$(this).attr('checked','checked');
        } else {
            table.rows(  ).deselect(); 
            //$(this).attr('checked','');
        }
        //return false;
    });

    $('#bulk_remove').click(function(){
        var count = table.rows( { selected: true } ).count();
        
        if(count == 0)
        {
            alert('<?php echo esc_attr__('Please select listings to remove', 'activitytime'); ?>');
            return false;
        }
        else
        {

            if(confirm('<?php echo_js(__('Are you sure?', 'activitytime')); ?>'))
            {
                $('img#ajax-indicator-masking').show();

                var form_selected_listings = table.rows( { selected: true } );
                var ids = table.rows( { selected: true } ).data().pluck( 'iduser_sessions' ).toArray();

                // ajax to remove rows
                $.post('<?php menu_page_url( 'actt_sessions', true ); ?>&function=bulk_remove', { user_sessions_ids: ids }, function(data) {

                    $('img#ajax-indicator-masking').hide();

                    table.ajax.reload();

                });
            }
        }

        return false;
    });


	if ($('#din-table').length) {

        sw_log_s_table_load_counter = 0;

        table = $('#din-table').DataTable({
            "ordering": true,
            "responsive": true,
            "processing": true,
            "serverSide": true,
            'ajax': {
                "url": ajaxurl,
                "type": "POST",
                "data": function ( d ) {

                    $(".selectAll").prop('checked', false);

                    return $.extend( {}, d, {
                        "page": 'actt_sessions',
                        "function": 'datatable',
                        "action": 'activitytime_mvc_action'
                    } );


                }
            },
            "language": {
                search: "<?php echo_js(__('Search', 'activitytime')); ?>",
                searchPlaceholder: "<?php echo_js(__('Enter here filter tag for any column', 'activitytime')); ?>"
            },
            "initComplete": function(settings, json) {
            },
            "fnDrawCallback": function (oSettings){

                if(sw_log_s_table_load_counter == 0)
                {
                    sw_log_s_table_load_counter++;
                    if($('#filter_user').val() != '')
                    setTimeout(function(){ table.columns(1).search( $('#filter_user').val() ).draw(); }, 1000);
                    
                }

                $('a.delete_button').click(function(){
                    
                    if(confirm('<?php echo_js(__('Are you sure?', 'activitytime')); ?>'))
                    {
                       // ajax to remove row
                        $.post($(this).attr('href'), function( [] ) {
                            table.row($(this).parent()).remove().draw( false );
                        });
                    }

                   return false;
                });

                if ( table.responsive.hasHidden() )
                {
                    jQuery('table.dataTable td.details-control').addClass('details-control');
                }
                else
                {
                    jQuery('table.dataTable td.details-control').removeClass('details-control');
                }
                jQuery('.dataTable div.dataTables_wrapper div.dataTables_filter input').addClass("dinamic_par").attr('name','sw_log_search');
                jQuery('.dataTable div.dataTables_wrapper div.dataTables_length select').addClass("dinamic_par").attr('name','sw_log_count');
                
            },
            'columns': [
                { data: "iduser_sessions" },
                { data: "user_info" },
                { data: "time_start" },
                { data: "time_end" },
                { data: "time_sec_total" },
                { data: "edit" },
                { data: "checkbox" }
            ],
//            columnDefs: [
//                { responsivePriority: 1, targets: 0 },
//                { responsivePriority: 2, targets: -2 }
//            ],
            responsive: {
                details: {
                    type: 'column',
                    target: 0
                }
            },
            order: [[ 0, 'desc' ]],
            columnDefs: [   {
                                //className: 'control',
                                className: 'details-control',
                                orderable: true,
                                targets:   0
                            },
                            {
                                //className: 'control',
                                //className: 'details-control',
                                orderable: true,
                                targets:   1
                            },
                            {
                                //className: 'control',
                                //className: 'details-control',
                                orderable: false,
                                targets:   5
                            },
                            {
                                className: 'select-checkbox',
                                orderable: false,
                                defaultContent: '',
                                targets:   6
                            }
            ],
            select: {
                style:    'multi',
                selector: 'td:last-child'
            },
			'oLanguage': {
				'oPaginate': {
					'sPrevious': '<i class="fa fa-angle-left"></i>',
					'sNext': '<i class="fa fa-angle-right"></i>'
				},
                'sSearch': "<?php echo_js(__('Search', 'activitytime')); ?>",
                "sLengthMenu": "<?php echo_js(__('Show _MENU_ entries', 'activitytime')); ?>",
                "sInfoEmpty": "<?php echo_js(__('Showing 0 to 0 of 0 entries', 'activitytime')); ?>",
                "sInfo": "<?php echo_js( __('Showing _START_ to _END_ of _TOTAL_ entries', 'activitytime')); ?>",
                "sEmptyTable": "<?php echo_js(__('No data available in table', 'activitytime')); ?>",
			},
			'dom': "<'row'<'col-sm-7 col-md-5'f><'col-sm-5 col-md-6'l>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"
		});
        
//		$('.js-select select:not(.basic-select)').select2({
//			minimumResultsForSearch: Infinity
//		});
        
        // Apply the search
        table.columns().every( function () {
            var that = this;
     
            $( 'input,select', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );

        } );

        if ($('#wal_live_monitoring').is(':checked')) {
            wal_timer_live_monitoring = setInterval(function(){ table.ajax.reload(); }, 10000);
        }
        
	}

    // Add event listener for opening and closing details
    $('table.dataTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            //row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            //row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });


});

</script>


<style>

.see-wrapper #din-table_wrapper .row
{
    margin:0px;
}

.see-wrapper .dataTable div.dataTables_wrapper label
{
    width:100%;
    padding:10px 0px;
}

.dataTable div.dataTables_wrapper div.dataTables_filter input
{
    display:inline-block;
    width:65%;
    margin: 0 10px;
}

.dataTable div.dataTables_wrapper div.dataTables_length select
{
    display:inline-block;
    width:100px;
    margin: 0 10px;
}

.dataTable td.control
{
    color:#337AB7;
    display:table-cell !important;
    font-weight: bold;
}

.dataTable th.control
{
    display:table-cell !important;
}

.see-wrapper .table > tbody > tr > td, .see-wrapper .table > tbody > tr > th, 
.see-wrapper .table > tfoot > tr > td, .see-wrapper .table > tfoot > tr > th, 
.see-wrapper .table > thead > tr > td, .see-wrapper .table > thead > tr > th {
    vertical-align: middle;
}

table.dataTable tbody > tr.odd.selected, table.dataTable tbody > tr > .odd.selected {
    background-color: #B0BED9;
}

.see-wrapper table.dataTable tbody td.select-checkbox::before, 
.see-wrapper table.dataTable tbody td.select-checkbox::after, 
.see-wrapper table.dataTable tbody th.select-checkbox::before, 
.see-wrapper table.dataTable tbody th.select-checkbox::after {
    display: block;
    position: absolute;
    /*top: 2.5em;*/
    top:50%;
    left: 50%;
    width: 12px;
    height: 12px;
    box-sizing: border-box;
}

.see-wrapper a#bulk_remove:hover,
.see-wrapper a#bulk_remove:focus {
    text-decoration: none;
}

tfoot input{
    width:100%;
    min-width:70px;
}

img.avatar
{
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.wal-system-icon{
    width: 50px;
    font-size: 50px;
    height: 50px;
}

.dashicons.wal-system-icon.dashicons-before::before {
    display: inline-block;
    font-family: dashicons;
    transition: color .1s ease-in;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    width: 50px;
    font-size: 50px;
    height: 50px;
}

/* sw_log_notify */

.sw_log_notify-box {
    position: fixed;
    right: 15px;
    bottom: 0;
    z-index: 100;
    
    position: fixed;
    z-index: 5000;
    bottom: 10px;
    right: 10px;
}

.sw_log_notify {
    position: relative;
    background: #fffffff7;
    padding: 12px 15px;
    border-radius: 15px;
    width: 250px;
    box-shadow: 0px 1px 0px 0.25px rgba(0, 0, 0, 0.07);
    -webkit-box-shadow: 0px 0 3px 2px rgba(0, 0, 0, 0.08);
    margin: 0;
    margin-bottom: 10px;
    font-size: 16px;
    
    background: #5cb811;
    background: rgba(92, 184, 17, 0.9);
    padding: 15px;
    border-radius: 4px;
    color: #fff;
    text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.5);
    
    -webkit-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -moz-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -ms-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -o-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.sw_log_notify.error  {
    margin: 0;
    margin-bottom: 10px;
    background: #cf2a0e;
    padding: 12px 15px;
}

.sw_log_notify.loading  {
    background: #5bc0de;
}

.sw_log_notify {
    display: block;
    margin-top: 10px;
    position: relative;
    opacity: 0;
    transform: translateX(120%);
}

.sw_log_notify.show {
    transform: translateX(0);
    opacity: 1;
}
    
/* end sw_log_notify */

.see-wrapper .dataTables_filter .form-control {
    height: 30px;
}


body .see-wrapper .table-responsive {
    overflow-x: visible;
}


body .datepicker table.table-condensed tbody > tr:hover > td:first-child, body .datepicker table.table-condensed tbody > tr.selected > td:first-child {
    border-left: 0px solid #fba56a;
    border-radius: 3px 0 0 3px;
}
body .datepicker table.table-condensed tbody > tr > td:first-child {
    border-left: 0px solid #ffff;
    border-radius: 3px 0 0 3px;
}

</style>

<?php $this->view('general/footer', $data); ?>
