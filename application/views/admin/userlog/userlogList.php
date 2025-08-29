<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-line-chart"></i> <?php //echo $this->lang->line('reports'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Auto Attendance Status Card -->
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-clock-o"></i> Auto Attendance Status
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-success btn-sm" onclick="runAutoAttendance()">
                            <i class="fa fa-refresh"></i> Run Auto Attendance
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="auto-attendance-status" class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        Auto attendance marking is ready. Click "Run Auto Attendance" to process today's teacher logins.
                    </div>
                </div>
            </div>
        </div>

        <div class="row"> 

            <div class="col-md-12">
                <div class="nav-tabs-custom theme-shadow">
                    <ul class="nav nav-tabs pull-right">

                        <li><a href="#tab_parent" data-toggle="tab" data-list="parent-list"><?php echo $this->lang->line('parent'); ?></a></li>
                        <li><a href="#tab_student" data-toggle="tab" data-list="student-list"><?php echo $this->lang->line('students'); ?></a></li>

                        <li><a href="#tab_staff" data-toggle="tab" data-list="staff-list"><?php echo $this->lang->line('staff') ?></a></li>
                        <li class="active"><a href="#tab_allusers" data-toggle="tab" data-list="all-list"><?php echo $this->lang->line('all_users'); ?></a></li>

                        <li class="pull-left header"><?php echo $this->lang->line('user_log'); ?></li>
                    </ul>				
					
                    <div class="tab-content">					
						
						
                        <div class="tab-pane active table-responsive" id="tab_allusers">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<a class="btn btn-primary btn-sm pull-right checkbox-toggle clear_userlog" ><?php echo $this->lang->line('clear_userlog_record'); ?>  </a>
								</div>	
							</div>	
						</div>
                            <table class="table table-striped table-bordered table-hover all-list" data-export-title="<?php echo $this->lang->line('user_log'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('users'); ?></th>
                                        <th width="150"><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('ip_address'); ?></th>
                                        <th width="200"><?php echo $this->lang->line('login_date_time'); ?></th>
                                        <th><?php echo $this->lang->line('user_agent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                        <!-- /.tab-pane -->
                        <div class="tab-pane table-responsive" id="tab_staff">
                            <table class="table table-striped table-bordered table-hover staff-list" data-export-title="<?php echo $this->lang->line('user_log'); ?>" data-target="staff-list">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('users'); ?></th>
                                        <th width="150"><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('ip_address'); ?></th>
                                        <th width="200"><?php echo $this->lang->line('login_date_time'); ?></th>
                                        <th><?php echo $this->lang->line('user_agent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane table-responsive" id="tab_student">
                            <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('user_log'); ?>" data-target="student-list">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('users'); ?></th>
                                        <th width="150"><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('ip_address'); ?></th>
                                        <th width="200"><?php echo $this->lang->line('login_date_time'); ?></th>
                                        <th><?php echo $this->lang->line('user_agent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <!-- /.tab-pane -->
                        <div class="tab-pane table-responsive" id="tab_parent">
                            <table class="table table-striped table-bordered table-hover parent-list" data-export-title="<?php echo $this->lang->line('user_log'); ?>" data-target="parent-list">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('users'); ?></th>
                                        <th width="150"><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('ip_address'); ?></th>
                                        <th width="200"><?php echo $this->lang->line('login_date_time'); ?></th>
                                        <th><?php echo $this->lang->line('user_agent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
	$(function () {
		$('.clear_userlog').on('click', function () {			
			if (confirm("<?php echo $this->lang->line('user_log_delete') ?>")) {				
				$.ajax({
					url: '<?php echo base_url(); ?>admin/userlog/delete/',
					success: function (data) {
						if (data.status == "fail") {                        
							errorMsg(message);
						} else {
							successMsg(data.message);
							window.location.reload(true);
						}
					}
				});
			}
		});
	});
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var target_ = $(e.target).attr("href"); // activated tab
        var target = $(e.target).data('list'); // activated tab
        if (target == "staff-list") {
            initDatatable(target, 'admin/userlog/getStaffDatatable', [],[], 100);
        } else if (target == "student-list") {
            initDatatable(target, 'admin/userlog/getStudentDatatable', [],[], 100);
        } else if (target == "parent-list") {
            initDatatable(target, 'admin/userlog/getParentDatatable', [],[], 100);
        } else if (target == "all-list") {
            initDatatable(target, 'admin/userlog/getDatatable', [],[], 100);
        }

    });

    (function ($) {
        'use strict';
        $(document).ready(function () {
            initDatatable('all-list', 'admin/userlog/getDatatable', [],[], 100);
        });
    }(jQuery))
</script>

<!-- //========staff auto attendance===== -->
<script>
// Run auto attendance marking
function runAutoAttendance() {
    $('#auto-attendance-status').html('<i class="fa fa-spinner fa-spin"></i> Processing auto attendance...');
    
    $.ajax({
        url: '<?php echo base_url("admin/userlog/run_auto_attendance"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#auto-attendance-status').html(
                    '<i class="fa fa-check-circle text-success"></i> ' + 
                    'Successfully marked attendance for ' + response.marked_count + ' teachers. ' +
                    'Updated ' + response.updated_count + ' existing records.'
                );
                
                // Refresh the page to show updated status
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                $('#auto-attendance-status').html(
                    '<i class="fa fa-exclamation-triangle text-danger"></i> ' + 
                    'Error: ' + response.message
                );
            }
        },
        error: function() {
            $('#auto-attendance-status').html(
                '<i class="fa fa-exclamation-triangle text-danger"></i> ' + 
                'An error occurred while processing auto attendance.'
            );
        }
    });
}

// Mark individual attendance
function markIndividualAttendance(userId, loginDatetime) {
    if (confirm('Mark attendance for this teacher based on login time?')) {
        $.ajax({
            url: '<?php echo base_url("admin/userlog/mark_individual_attendance"); ?>',
            type: 'POST',
            data: {
                user_id: userId,
                login_datetime: loginDatetime
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Attendance marked successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while marking attendance.');
            }
        });
    }
}
</script>

<style>
.teacher-row {
    background-color: #f0f8f0 !important;
}
</style>