<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<span class="breadcrumb-item active"> <?PHP echo $title; ?></span>
		</nav>
	</div><!-- br-pageheader -->
	<div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
		<h4 class="tx-gray-800 mg-b-5"><?PHP echo $title; ?></h4>
		
	</div>
	
    <div class="br-pagebody">	
		
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Group Name</th>
							<th>Group Members</th>
							<th>Group Post</th>
							<th>Created Date</th>
							<th>Created By</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($groups) > 0){ $i = 1;
					foreach($groups as $row){
						if($row->createdBy == 1){
							$createdBy = 'Admin';
						} else {
							$createdBy = 'User';
						}
					?>
						<tr>
							<td><?PHP echo $row->groupName; ?></td>
							<td><?PHP echo getTotalmemberscnt($row->groupId); ?></td>
							<td><?PHP echo getTotalmempostcnt($row->groupId); ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td><?PHP echo $createdBy; ?></td>
							<td>
								<?PHP if($row->status == 1){ ?>
									<a href="javascript:void(0);" title="click to Inactive this news" id="<?php echo $row->groupId;?>" rel="2" class="btn btn-success btn-with-icon activeGroup">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-on"></i></span>
											<span class="pd-x-15">Active</span>
										</div>
									</a>
									
							<?PHP if($row->reportAbuse == 1){
								$getreportabCnt = getReportabusememStatus($row->groupId);
								if(count($getreportabCnt) !=0){
							?>
								<a href="javascript:void(0);" class="btn btn-info btn-with-icon" id="<?php echo $row->groupId;?>" rel="0" title="click to view the reported users" data-toggle="modal" data-target="#modaldemo<?PHP echo $i; ?>" >
									<div class="ht-40">
										<span class="pd-x-15">Reported</span>
									</div>
								</a>
								
								<div id="modaldemo<?PHP echo $i; ?>" class="modal fade">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content tx-size-sm">
											<div class="modal-header pd-y-20 pd-x-25">
												<h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Reported Users</h6>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body pd-25">
												<input type="hidden" name="groupId" id="groupId" value="<?php echo $row->groupId;?>" />
												<table>
													<thead>
														<tr>
															<th class="tx-10-force tx-mont tx-medium">Image</th>
															<th class="tx-10-force tx-mont tx-medium">Name</th>
															<th class="tx-10-force tx-mont tx-medium hidden-xs-down">Comments</th>
														</tr>
													</thead>
													<tbody>
													<?PHP 
													$CI = & get_instance();
													 $CI->load->model("users_model");
													foreach($getreportabCnt AS $reportUsers){ 
														$userdetails = $CI->users_model->getUseriddetails($reportUsers['createdBy']);
													?>
														<tr>                
															<td>
																<div class="d-flex align-items-center">
																	<img src="<?PHP echo base_url("uploads/users/".$userdetails->profileimg); ?>" class="wd-40 rounded-circle" alt="">
																</div>
															</td>
															<td class="valign-middle hidden-xs-down">
																<b><?PHP echo $userdetails->firstName; ?></b>
															</td>
															<td class="valign-middle hidden-xs-down"><?PHP echo $reportUsers['comments']; ?></td>
														</tr>
													<?PHP } ?>
													</tbody>
												</table>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium reportAbuseblock">Block</button>
												<button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-dismiss="modal">Not Now</button>
											</div>
										</div>
									</div><!-- modal-dialog -->
								</div><!-- modal -->
							
							<?PHP } } ?>
								<?PHP } else if($row->status == 2){ ?>
									<a href="javascript:void(0);" title="click to Active this news" id="<?php echo $row->groupId;?>" rel="1" class="btn btn-danger btn-with-icon activeGroup">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-off"></i></span>
											<span class="pd-x-15">InActive</span>
										</div>
									</a>
								<?PHP } else if($row->status == 3) { ?>
									<div class="ht-40">										
										<span class="pd-x-15">Deleted</span>
									</div>
								<?PHP } ?>
							</td>
						</tr>
					<?php $i++; } } else { ?>
						<tr align="center">
							<td colspan="10">No Records Found</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div><!-- bd -->
			<div id="pagination" class="ht-80 bd d-flex align-items-center justify-content-center"><?php echo $this->pagination->create_links(); ?></div>
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->
<script type="text/javascript">
	jQuery(function ($) {		
		$(document).on('click', '.activeGroup', function(){
			if (confirm('Are you sure want to do this action?')){
				var groupId = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>groups/updatestatusGroup',
					data:{groupId:groupId, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Group status not updated!');
							location.reload();
						} else {
							alert('Group status updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.reportAbuseblock', function(){
			if (confirm('Are you sure want to do this action?')){
				var gid = $('#groupId').val();	
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>groups/reportAbuseblocksts',
					data:{gid:gid},
					success: function(data){
						if(data == 1){
							alert('Group block request not updated!');
							location.reload();
						} else {
							alert('Group block request updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
	});
</script>
	  