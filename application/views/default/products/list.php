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
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Title</th>
							<th>Category</th>
							<th>Location</th>
							<th>Created Date</th>
							<th>Created By</th>
							<th>Report Abuse</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					$CI = & get_instance();
					$CI->load->model("users_model");
					$CI->load->model("category_model");
					$CI->load->model("locations_model");
					if(count($products) > 0){ $i = 1;
					foreach($products as $row){					
						if($row->createdBy == 1){
							$createdBy = 'Admin';
						} else {
							$userdetails = $CI->users_model->getUseriddetails($row->createdBy);							
							$createdBy = $userdetails->firstName.' '.$userdetails->surName;
						}
						
						/*Category Details*/
						$wherecat['cat.parentId'] = 0;
						$wherecat['cat.categoryId'] = $row->procatId;
						$categorymain = $CI->category_model->get($wherecat);
						
						$wheresubcat['cat.parentId'] = $row->procatId;
						$wheresubcat['cat.categoryId'] = $row->prosubcatId;
						$categorysub = $CI->category_model->get($wheresubcat);
						if(!empty($categorysub)){
							$category = $categorymain[0]->name.' >> '.$categorysub[0]->name;
							$categorySubname = $categorysub[0]->name;
						} else {
							$category = $categorymain[0]->name;
							$categorySubname = '';
						}
						
						/*Locations details*/
						$whereloc['loc.parentId'] = 0;
						$whereloc['loc.locationsId'] = $row->prolocId;
						$locationmain = $CI->locations_model->get($whereloc);
						
						$wheresubloc['loc.parentId'] = $row->prolocId;
						$wheresubloc['loc.locationsId'] = $row->prosublocId;
						$locationsub = $CI->locations_model->get($wheresubloc);
						
						if(!empty($locationsub)){
							$location = $locationmain[0]->name.' >> '.$locationsub[0]->name;
							$locationsubname = $locationsub[0]->name;
						} else if(!empty($locationmain)){
							$location = $locationmain[0]->name;
							$locationsubname = '';
						} else {
						    $location = '';
							$locationsubname = '';
						}
					?>
						<tr>
							<td><?PHP echo $row->proName; ?></td>
							<td><?PHP echo $category; ?></td>
							<td><?PHP echo $location; ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td><?PHP echo $createdBy; ?></td>
							<td>
							<?PHP if($row->reportAbuse == 1){
								$getreportabCnt = getReportabuseStatuspro($row->productId);
								if(count($getreportabCnt) !=0){
							?>
								<a href="javascript:void(0);" class="btn btn-info btn-with-icon" id="<?php echo $row->productId;?>" rel="0" title="click to view the reported users" data-toggle="modal" data-target="#modaldemo<?PHP echo $i; ?>" >
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
												<input type="hidden" name="reportAbuseproId" id="reportAbuseproId" value="<?php echo $row->productId;?>" />
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
								<a href="javascript:void(0);" class="btn btn-danger btn-with-icon blockProduct" id="<?php echo $row->productId;?>" rel="0" title="click to block this product" >
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-ban"></i></span>
										<span class="pd-x-15">Block</span>
									</div>
								</a>
								<?PHP } ?>
							<?PHP } else if($row->reportAbuse == 0){ ?>
								<a href="javascript:void(0);" class="btn btn-success btn-with-icon blockProduct" id="<?php echo $row->productId;?>" rel="1" title="click to unblock this product" >
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-ban"></i></span>
										<span class="pd-x-15">Unblock</span>
									</div>
								</a>
							<?PHP } ?>
							</td>
							<td>
								<?PHP /*<a href="<?php echo base_url();?>products/editnews/<?php echo $row->productId;?>" title="Edit" id="edit_<?php echo $row->productId;?>" rel="<?php echo $row->productId;?>" class="btn btn-primary btn-with-icon">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-pencil"></i></span>
										<span class="pd-x-15">Edit</span>
									</div>
								</a>*/ ?>
								
								<?PHP /* <a href="javascript:void(0);" title="Delete this news" id="<?php echo $row->productId;?>" rel="0" class="btn btn-danger btn-with-icon deleteNews">
									<div class="ht-40">
										<span class="icon wd-40"><i class="fa fa-trash"></i></span>
										<span class="pd-x-15">Delete</span>
									</div>
								</a> */ ?>
								<?PHP if($row->status == 1){ ?>
									<a href="javascript:void(0);" title="click to Inactive this product" id="<?php echo $row->productId;?>" rel="0" class="btn btn-success btn-with-icon activeProduct">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-on"></i></span>
											<span class="pd-x-15">Active</span>
										</div>
									</a>
								<?PHP } else if($row->status == 0){ ?>
									<a href="javascript:void(0);" title="click to Active this product" id="<?php echo $row->productId;?>" rel="1" class="btn btn-danger btn-with-icon activeProduct">
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
		$(document).on('click', '.deleteProduct', function(){
			if (confirm('Are you sure you want delete this row?')){
				var pro_id = $(this).attr("id");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>products/deleteProducts',
					data:{pro_id:pro_id},
					success: function(data){
						if(data == 1){
							alert('Products not deleted. Kindly try again!');
							location.reload();
						}else{
							alert('Products has been deleted successfully.');
							location.reload();							
						}
					}
				});
			}
		});

		$(document).on('click', '.activeProduct', function(){
			if (confirm('Are you sure want to do this action?')){
				var pro_id = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>products/updatestatusProduct',
					data:{pro_id:pro_id, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Product status not updated!');
							location.reload();
						} else {
							alert('Product status updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.blockProduct', function(){
			if (confirm('Are you sure want to do this action?')){
				var pro_id = $(this).attr("id");
				var relval = $(this).attr("rel");				
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>products/blockstatusProduct',
					data:{pro_id:pro_id, relval:relval},
					success: function(data){
						if(data == 1){
							alert('Product block not updated!');
							location.reload();
						} else {
							alert('Product block updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		$(document).on('click', '.reportAbuseblock', function(){
			if (confirm('Are you sure want to do this action?')){
				var pro_id = $('#reportAbuseproId').val();	
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>products/reportAbuseblocksts',
					data:{pro_id:pro_id},
					success: function(data){
						if(data == 1){
							alert('Product block not updated!');
							location.reload();
						} else {
							alert('Product block updated successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
	});
</script>
	  