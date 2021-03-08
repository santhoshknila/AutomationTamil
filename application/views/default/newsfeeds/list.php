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
		<a href="<?php echo base_url();?>newsfeeds/addedit"><button class="btn btn-primary mg-b-20">Add Newsfeed</button></a>
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<!--<th>Sno</th>-->
							<th>Title</th>
							<!-- <th>Image/Video</th>-->
							<th>Likes</th>
							<th>Comments</th>
							<th>Report Abuse</th>
							<th>Created Date</th>
							<th>Created By</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($newsfeeds) > 0){ $i = 1;
					foreach($newsfeeds as $row){					
						if($row->createdBy == 1){
							$createdBy = 'Admin';
						} else {
							$createdBy = 'User';
						}
					?>
						<tr>
							<!-- <td scope="row">< ?PHP echo $i; ?></td> -->
							<td><?PHP echo $row->title; ?></td>
							<td><?PHP echo getTotallikecnt($row->newsFeedsId); ?></td>
							<td><?PHP echo count(getTotalCntcomments($row->newsFeedsId)); ?></td>
							<td>
							<?PHP if($row->reportAbuse == 1){
								$getreportabCnt = getReportabuseStatus($row->newsFeedsId);
								if(count($getreportabCnt) !=0){
							?>
								<a href="javascript:void(0);" class="btn btn-info btn-with-icon" id="<?php echo $row->newsFeedsId;?>" rel="0" title="click to view the reported users" data-toggle="modal" data-target="#modaldemo<?PHP echo $i; ?>" >
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
												<input type="hidden" name="reportAbusefeedId" id="reportAbusefeedId" value="<?php echo $row->newsFeedsId;?>" />
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
								
								
								<?PHP } else { ?>
								<a href="javascript:void(0);" class="btn btn-danger btn-with-icon blockNews" id="<?php echo $row->newsFeedsId;?>" rel="0" title="click to block this news" >
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-ban"></i></span>
										<span class="pd-x-15">Block</span>
									</div>
								</a>
								<?PHP } ?>
							<?PHP } else if($row->reportAbuse == 0){ ?>
								<a href="javascript:void(0);" class="btn btn-success btn-with-icon blockNews" id="<?php echo $row->newsFeedsId;?>" rel="1" title="click to unblock this news" >
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-ban"></i></span>
										<span class="pd-x-15">Unblock</span>
									</div>
								</a>
							<?PHP } ?>
							</td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td><?PHP echo $createdBy; ?></td>
							<td>
								<a href="<?php echo base_url();?>newsfeeds/editnews/<?php echo $row->newsFeedsId;?>" title="Edit" id="edit_<?php echo $row->newsFeedsId;?>" rel="<?php echo $row->newsFeedsId;?>" class="btn btn-primary btn-with-icon">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-pencil"></i></span>
										<span class="pd-x-15">Edit</span>
									</div>
								</a>
								
								<?PHP /* <a href="javascript:void(0);" title="Delete this news" id="<?php echo $row->newsFeedsId;?>" rel="0" class="btn btn-danger btn-with-icon deleteNews">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-trash"></i></span>
										<span class="pd-x-15">Delete</span>
									</div>
								</a> */ ?>
								<?PHP if($row->isActive == 1){ ?>
									<a href="javascript:void(0);" title="click to Inactive this news" id="<?php echo $row->newsFeedsId;?>" rel="0" class="btn btn-success btn-with-icon activeNews">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-on"></i></span>
											<span class="pd-x-15">Active</span>
										</div>
									</a>
								<?PHP } else if($row->isActive == 0){ ?>
									<a href="javascript:void(0);" title="click to Active this news" id="<?php echo $row->newsFeedsId;?>" rel="1" class="btn btn-danger btn-with-icon activeNews">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-off"></i></span>
											<span class="pd-x-15">InActive</span>
										</div>
									</a>
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
		$(document).on('click', '.deleteNews', function(){
			if (confirm('Are you sure you want delete this row?')){
				var news_id = $(this).attr("id");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>newsfeeds/deleteNews',
					data:{news_id:news_id},
					success: function(data){
						if(data == 1){
							alert('News not deleted. Kindly try again!');
							location.reload();
						}else{
							alert('News has been deleted successfully.');
							location.reload();							
						}
					}
				});
			}
		});

		$(document).on('click', '.activeNews', function(){
			if (confirm('Are you sure want to do this action?')){
				var news_id = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>newsfeeds/updatestatusNews',
					data:{news_id:news_id, relval:relval},
					success: function(data){
						if(data == 1){
							alert('News status not updated!');
							location.reload();
						} else {
							alert('News status updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.blockNews', function(){
			if (confirm('Are you sure want to do this action?')){
				var news_id = $(this).attr("id");
				var relval = $(this).attr("rel");				
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>newsfeeds/blockstatusNews',
					data:{news_id:news_id, relval:relval},
					success: function(data){
						if(data == 1){
							alert('News block not updated!');
							location.reload();
						} else {
							alert('News block updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.reportAbuseblock', function(){
			if (confirm('Are you sure want to do this action?')){
				var news_id = $('#reportAbusefeedId').val();	
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>newsfeeds/reportAbuseblocksts',
					data:{news_id:news_id},
					success: function(data){
						if(data == 1){
							alert('News block not updated!');
							location.reload();
						} else {
							alert('News block updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
	});
</script>
	  