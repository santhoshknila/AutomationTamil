<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>products">Home</a>
			<span class="breadcrumb-item active"><?PHP echo $title; ?></span>
		</nav>
	</div><!-- br-pageheader -->
	<div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
		<h4 class="tx-gray-800 mg-b-5"><?PHP echo $title; ?></h4>
	</div>
	
	<div class="br-pagebody">
        <div class="br-section-wrapper">
			<div class="form-layout form-layout-1">
				<form name="productsForm" id="productsForm" action="<?php echo site_url("notifications/save"); ?>" method="POST" enctype="multipart/form-data" data-parsley-validate >
					<div class="row mg-b-25">						
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-control-label">Subject: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="notify[subject]" value="" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						<div class="col-lg-12">
							<div class="form-group">
								<label class="form-control-label">Attach Image: </label>
								<input class="form-control" type="file" name="imageurl" value="" id="" />
							</div>
						</div><!-- col-4 -->
						<div class="col-lg-12">
							<div class="form-group mg-b-10-force">
								<label class="form-control-label">Messages: <span class="tx-danger">*</span></label>
								<textarea rows="10" class="form-control" name="notify[messages]" placeholder="" required ></textarea>
							</div>
						</div> 
					</div><!-- row -->
					<div class="form-layout-footer">
						<button class="btn btn-info">Submit</button>						
					</div><!-- form-layout-footer -->
				</form>
			</div><!-- form-layout -->
		</div>
	</div>