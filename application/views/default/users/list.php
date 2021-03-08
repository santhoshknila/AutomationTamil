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
			<?PHP /*<a href="<?php echo base_url();?>users/addedit"><button class="btn btn-primary mg-b-20">Add New</button></a>*/ ?>
			
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Email Address</th>
							<th>My Friends</th>
							<th>My Posts</th>
							<th>Created Date</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($users) > 0){ $i = 1;
					foreach($users as $row){
						if($row->status == 1){ 
					?>
						<tr>
							<td><?PHP echo $row->firstName; ?></td>
							<td><?PHP echo $row->emailid; ?></td>
							<td><?PHP 
								$CI = get_instance();
								$CI->load->model("friends_model");	
								$CI->load->model("newsfeeds_model");	
								$getfriendsCnt = array( 'loggedUser' => $row->userid );
								$friRequesttot = $CI->friends_model->myConnectionsTotal($getfriendsCnt);
								echo "<span class='tx-info'><b>".count($friRequesttot)."</b></span>";
							?></td>
							<td><?PHP 
								$filter_data = array( 'useridPro' => $row->userid, 'useridlog'=> 0 );		
								$totalFeeds = count($CI->newsfeeds_model->getProfileTotalPostdetails($filter_data));
								echo "<span class='tx-danger'><b>".$totalFeeds."</b></span>"; ?>
							</td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<?PHP /* <td><a onclick="return confirm('Are you sure want to Delete?')" href="<?php echo base_url();?>users/deleteUsers/<?php echo $row->userid;?>" title="Delete" id="delete_<?php echo $row->userid;?>" rel="<?php echo $row->userid;?>"><i class="fa fa-trash "></i></td> */  ?>
							
							<td>
									<a href="javascript:void(0);" title="click to Delete this user" id="<?php echo $row->userid;?>" rel="0" class="btn btn-danger btn-with-icon activeUser">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-trash"></i></span>
											<span class="pd-x-15">Delete</span>
										</div>
									</a>
								<?PHP /*if($row->status == 1){ ?>
								<?PHP } else if($row->status == 0){ ?>
									<a href="javascript:void(0);" title="click to Active this user" id="<?php echo $row->userid;?>" rel="1" class="btn btn-danger btn-with-icon activeUser">
										<div class="ht-40">
											<span class="icon wd-40"><i class="fa fa-toggle-off"></i></span>
											<span class="pd-x-15">InActive</span>
										</div>
									</a>
								<?PHP }*/ ?>
							</td>

						</tr>
					<?php $i++; }} } else {?>
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
		 

		$(document).on('click', '.activeUser', function(){
			if (confirm('Are you sure want to Delete User?')){
				var user_id = $(this).attr("id");
				var relval = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>users/updatestatusUser',
					data:{user_id,relval},
					success: function(data){
						if(data == 1){
							alert('User delete failed!');
							location.reload();
						} else {
							alert('User deleted successfully.');
							location.reload();							
						}
					}
				});
			}
		});
		
		 
		
		 
		
	});
</script>