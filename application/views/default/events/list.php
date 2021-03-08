<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<span class="breadcrumb-item active"><?PHP echo $title; ?></span>
		</nav>
	</div><!-- br-pageheader -->
	<div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
		<h4 class="tx-gray-800 mg-b-5"><?PHP echo $title; ?></h4>
		
	</div>
	
    <div class="br-pagebody">	
		<a href="<?php echo base_url();?>events/addedit"><button class="btn btn-primary mg-b-20">Add Events</button></a>
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<!--<th>Sno</th>-->
							<th>Title</th>
							<th>Start Date & Time</th>
							<th>End Date & Time</th>
							<th>Location</th>
							<th>Going</th>
							<th>Interested</th>
							<th>Created Date</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					$CI = & get_instance();
					$CI->load->model("events_model");
					if(count($events) > 0){ $i = 1;
					foreach($events as $row){
						$goingStatuscnt = $CI->events_model->goingStatuscnt($row->eventId);
						$interestStatuscnt = $CI->events_model->interestStatuscnt($row->eventId);
					?>
						<tr >
							<td><?PHP echo $row->title; ?></td>
							<td><b><?PHP echo $row->startDate; ?> <?PHP echo date('h:i a', strtotime($row->startTime)); ?></b></td>
							<td><b><?PHP echo $row->endDate; ?> <?PHP echo date('h:i a', strtotime($row->endTime)); ?></b></td>	
							<td><b><?PHP echo $row->locationDetails; ?></b></td>
							<td><b><?PHP echo $goingStatuscnt; ?></b></td>								
							<td><b><?PHP echo $interestStatuscnt; ?></b></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td>
							<?PHP if($row->status == 2 && ($row->endDate <= date('Y-m-d')) && ($row->endTime <= date('H:i'))){ ?>
								<span class="tx-danger"><b>Expired</b></span>
							<?PHP } else if($row->status == 2){ ?>
								<span class="tx-warning"><b>Waiting Approval</b></span>
								<?PHP /* <button class="btn btn-warning btn-block statusEvents" title="click to Active this events"id="<?php echo $row->eventId;?>" rel="1" style="cursor:pointer;">Waiting Approval</button> */ ?>
							<?PHP } else if($row->status == 1){ ?>
								<span class="tx-success"><b>Approved</b></span>
							<?PHP } else if($row->status == 4){ ?>	
								<span class="tx-danger"><b>Rejected</b></span>
							<?PHP } else if($row->status == 3){?>
								<span class="tx-danger"><b>Deleted</b></span>
							<?PHP } ?>
							</td>
							<td>
								<?PHP if($row->status != 3){?>
								<a href="<?php echo base_url();?>events/editEvents/<?php echo $row->eventId;?>" title="Edit" id="edit_<?php echo $row->eventId;?>" rel="<?php echo $row->eventId;?>" class="btn btn-primary btn-with-icon">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-pencil"></i></span>
										<span class="pd-x-15">Edit</span>
									</div>
								</a>
								<?PHP 
								}	
								if($row->isActive == 1 && $row->status == 1){ ?>
									<a href="javascript:void(0);" title="click to Inactive this Events" id="<?php echo $row->eventId;?>" rel="0" class="btn btn-success btn-with-icon activeEvents">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-on"></i></span>
											<span class="pd-x-15">Active</span>
										</div>
									</a>
								<?PHP } else if($row->isActive == 0 && $row->status == 1){ ?>
									<a href="javascript:void(0);" title="click to Active this Events" id="<?php echo $row->eventId;?>" rel="1" class="btn btn-danger btn-with-icon activeEvents">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-off"></i></span>
											<span class="pd-x-15">InActive</span>
										</div>
									</a>
								<?PHP  } ?>
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
		$(document).on('click', '.statusEvents', function(){
			if (confirm('Are you sure want to approve this Event?')){
				var eventId = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>events/updatestatusEvents',
					data:{eventId:eventId, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Events not approved!');
							location.reload();
						}else{
							alert('Events approved successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.activeEvents', function(){
			if (confirm('Are you sure want to do this action?')){
				var eventId = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>events/updateactiveEvents',
					data:{eventId:eventId, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Events not updated!');
							location.reload();
						}else{
							alert('Events updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
	});
</script>
	  