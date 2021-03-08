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
			<a href="<?php echo base_url();?>ads/addedit"><button class="btn btn-primary mg-b-20">Add Ads</button></a>
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">				
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							<th>Title</th>
							<th>Price</th>
							<th>Price Type</th>
							<th>Display Type</th>
							<th>Description</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($ads) > 0){ $i = 1;
					foreach($ads as $row){
					?>
						<tr>
							<td><?PHP echo $row->title; ?></td>
							<td><?PHP echo $row->price; ?></td>
							<td><?PHP if($row->priceType == 1){ echo "Month"; } else if($row->priceType == 2){ echo "Year"; }  ?></td>
							<td><?PHP if($row->displayType == 1){ echo "Home"; } else if($row->displayType == 2){ echo "Category"; } else if($row->displayType == 3){ echo "Both"; } ?></td>
							<td><?PHP echo $row->description; ?></td>
							<td><a href="<?php echo base_url();?>ads/editads/<?php echo $row->adsId;?>" title="Edit" id="edit_<?php echo $row->adsId;?>" rel="<?php echo $row->adsId;?>"><i class="fa fa-pencil"></i></a>
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