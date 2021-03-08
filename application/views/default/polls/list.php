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
		<a href="<?php echo base_url();?>polls/addedit"><button class="btn btn-primary mg-b-20">Add Polls</button></a>
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<!--<th>Sno</th>-->
							<th>Title</th>
							<th>Answers</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Created Date</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($polls) > 0){ $i = 1;
					foreach($polls as $row){
					?>
						<tr >
							<!--<th scope="row">< ?PHP echo $i; ?></th>-->
							<td><?PHP echo $row->pollingQuestion; ?></td>	
							<td>
								<?PHP 
									$pollAnswer = getPollsanswer($row->pollingId);
									if(!empty($pollAnswer)){
										$i=1;
										foreach($pollAnswer as $pans){
											$percentage = getTotalpollcnt($pans['pollingAnswerId'], $row->pollingId);
											echo "<span>".$i.") </span> <span class='tx-info tx-bold'>".$pans['answer']."</span> - <span class='tx-inverse'>".$percentage."</span><span class='tx-danger'>&nbsp;%</span>&nbsp;&nbsp;";
											//if($i == 2){
												echo "<br />";
											//}
											$i++;
										}
									}
								?>
							</td>			
							<td><?PHP echo date("d-m-Y", strtotime($row->startDate)); ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->endDate)); ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td>
							<?PHP if($row->endDate >= date("Y-m-d")){ ?>
								<a href="<?php echo base_url();?>polls/editpolls/<?php echo $row->pollingId;?>" title="Edit" id="edit_<?php echo $row->pollingId;?>" rel="<?php echo $row->pollingId;?>" class="btn btn-primary btn-with-icon">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-pencil"></i></span>
										<span class="pd-x-15">Edit</span>
									</div>
								</a>
								<?PHP if($row->isActive == 1){ ?>
									<a href="javascript:void(0);" title="click to Inactive this news" id="<?php echo $row->pollingId;?>" rel="0" class="btn btn-success btn-with-icon activePoll">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-on"></i></span>
											<span class="pd-x-15">Active</span>
										</div>
									</a>
								<?PHP } else if($row->isActive == 0){ ?>
									<a href="javascript:void(0);" title="click to Active this news" id="<?php echo $row->pollingId;?>" rel="1" class="btn btn-danger btn-with-icon activePoll">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-off"></i></span>
											<span class="pd-x-15">InActive</span>
										</div>
									</a>
								<?PHP } ?>
							<?PHP } else { ?>
								<span class="tx-danger"><b>Expired</b></span>
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
		$(document).on('click', '.activePoll', function(){
			if (confirm('Are you sure want to do this action?')){
				var pollingId = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>polls/updatestatusPolls',
					data:{pollingId:pollingId, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Polls status not updated!');
							location.reload();
						}else{
							alert('Polls status updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
	});
</script>
	  