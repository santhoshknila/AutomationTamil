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
		<a href="<?php echo base_url();?>sitepages/addedit"><button class="btn btn-primary mg-b-20">Add Pages</button></a>
        <div class="br-section-wrapper">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>							
							<th>Page Name</th>
							<th>Created Date</th>
							<th>Created By</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($sitepages) > 0){ $i = 1;
					foreach($sitepages as $row){					
						if($row->createdBy == 1){
							$createdBy = 'Admin';
						}
					?>
						<tr>							
							<td><?PHP echo $row->pagename; ?></td>
							<td><?PHP echo date("d-m-Y", strtotime($row->createdDate)); ?></td>
							<td><?PHP echo $createdBy; ?></td>
							<td><a href="<?php echo base_url();?>sitepages/editpage/<?php echo $row->pageId;?>" title="Edit" id="edit_<?php echo $row->pageId;?>" rel="<?php echo $row->pageId;?>"><i class="fa fa-pencil"></i></a>
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