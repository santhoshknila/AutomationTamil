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
			<a href="<?php echo base_url();?>notifications/addnew"><button class="btn btn-primary mg-b-20">Add Notifications</button></a>
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Sno</th>
							<th>Subject</th>
							<th>Messages</th>
							<th>Created Date</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($notify) > 0){ $i = 1;
					foreach($notify as $row){
					?>
						<tr>
							<th scope="row"><?PHP echo $i; ?></th>
							<td><?PHP echo $row->subject; ?></td>
							<td><?PHP echo $row->messages; ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td><a onclick="return confirm('Are you sure want to Delete?')" href="<?php echo base_url();?>notifications/deletenotify/<?php echo $row->notifyId;?>" title="Edit" id="edit_<?php echo $row->notifyId;?>" rel="<?php echo $row->notifyId;?>"><i class="fa fa-trash"></i></a>
						</tr>
					<?php $i++; } } else {?>
						<tr align="center">
							<td colspan="6">No Records Found</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div><!-- bd -->
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->