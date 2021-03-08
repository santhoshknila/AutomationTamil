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
			<a href="<?php echo base_url();?>locations/addedit"><button class="btn btn-primary mg-b-20">Add Locations</button></a>
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					$CI = & get_instance();
					$CI->load->model("locations_model");
					if(count($location) > 0){ $i = 1;
					foreach($location as $row){
					?>
						<tr>
							<td><?PHP 
								if($row->parentId == 0){
									echo $row->name;
								} else if($row->parentId != 0){
									$wherecat['loc.locationsId'] = $row->parentId;
									$locationmain = $CI->locations_model->get($wherecat);
									
									$wheresubcat['loc.parentId'] = $row->parentId;
									$wheresubcat['loc.locationsId'] = $row->locationsId;
									$locationsub = $CI->locations_model->get($wheresubcat);
									echo $subCategory = $locationmain[0]->name.' >> '.$locationsub[0]->name;
								}
							?></td>							
							<td><a href="<?php echo base_url();?>locations/editcat/<?php echo $row->locationsId;?>" title="Edit" id="edit_<?php echo $row->locationsId;?>" rel="<?php echo $row->locationsId;?>"><i class="fa fa-pencil"></i></a>
						</tr>
					<?php $i++; } } else { ?>
						<tr align="center">
							<td colspan="6">No Records Found</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div><!-- bd -->
			<div id="pagination" class="ht-80 bd d-flex align-items-center justify-content-center"><?php echo $this->pagination->create_links(); ?></div>			
        </div><!-- br-section-wrapper -->
      </div><!-- br-pagebody -->