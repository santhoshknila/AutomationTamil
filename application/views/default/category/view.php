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
			<a href="<?php echo base_url();?>category/addedit"><button class="btn btn-primary mg-b-20">Add Category</button></a>
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Image</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					$CI = & get_instance();
					$CI->load->model("category_model");
					if(count($category) > 0){ $i = 1;
					foreach($category as $row){
					?>
						<tr>
							<td>
							<?PHP 
								if($row->parentId == 0){
									echo $row->name;
								} else if($row->parentId != 0){
									$wherecat['cat.categoryId'] = $row->parentId;
									$categorymain = $CI->category_model->get($wherecat);
									
									$wheresubcat['cat.parentId'] = $row->parentId;
									$wheresubcat['cat.categoryId'] = $row->categoryId;
									$categorysub = $CI->category_model->get($wheresubcat);
									echo $subCategory = $categorymain[0]->name.' >> '.$categorysub[0]->name;
								}
							?></td>
							<td><?PHP if($row->image !=''){
								$filepath = base_url()."/uploads/category/".$row->image;
								echo $uploadfile = "<img src='$filepath' width='100' height='100' />";
							} else {
								$filepath = base_url()."/uploads/category/no_image.png";
								echo $uploadfile = "<img src='$filepath' width='100' height='100' />";
							}
							?></td>
							<td><a href="<?php echo base_url();?>category/editcat/<?php echo $row->categoryId;?>" title="Edit" id="edit_<?php echo $row->categoryId;?>" rel="<?php echo $row->categoryId;?>"><i class="fa fa-pencil"></i></a>							
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