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
			<div class="form-layout form-layout-1">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
				<form name="settingForm" id="settingForm" action="<?php echo site_url("settings/sitesettingSave"); ?>" method="POST" enctype="multipart/form-data" data-parsley-validate >
					<?PHP if(!empty($settings)){$setting = $settings[0];} ?>
					<input class="form-control" type="hidden" name="siteId" value="<?PHP echo $setting->siteId; ?>" placeholder="" required />
					<h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Payment Details</h6>
					<div class="row mg-b-25">
						
						<div class="col-lg-4">						
							<div class="form-group mg-b-10-force">
								<label class="form-control-label">Merchant ID: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="merchantId" value="<?PHP echo $setting->merchantId; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Merchant KEY: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="merchantKey" value="<?PHP echo $setting->merchantKey; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Passphrase: <span class="tx-danger">(Not need sandbox account.)</span></label>
								<input class="form-control" type="text" name="passphrase" value="<?PHP echo $setting->passphrase; ?>" placeholder="" />
							</div>
						</div><!-- col-4 -->
					</div>
					<h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Packages & ads redirect Details</h6>
					<div class="row mg-b-25">	
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Return Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="returnUrl" value="<?PHP echo $setting->returnUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Cancel Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="cancelUrl" value="<?PHP echo $setting->cancelUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Notify Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="notifyUrl" value="<?PHP echo $setting->notifyUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
					</div>	
					<h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Product convert to Ads redirect Details</h6>
					<div class="row mg-b-25">	
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Ads Return Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="returnadsUrl" value="<?PHP echo $setting->returnadsUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Ads Cancel Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="canceladsUrl" value="<?PHP echo $setting->canceladsUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
						
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Ads Notify Url: <span class="tx-danger">*</span></label>
								<input class="form-control" type="url" name="notifyadsUrl" value="<?PHP echo $setting->notifyadsUrl; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
					</div>
					<div class="row mg-b-25">	
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Payment mode: <span class="tx-danger">*</span></label>
								<select class="form-control select2" id="status" name="paymentType" data-placeholder="Choose Status">
									<option value="" >Choose payment mode</option>
									<option value="1" <?php if($setting->paymentType == 1){ echo "SELECTED"; } ?>>Live</option>
									<option value="2" <?php if($setting->paymentType == 2){ echo "SELECTED"; } ?>>Sandbox</option>
								</select>
							</div>
						</div><!-- col-4 -->
					</div>
					
					<h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">General Details</h6>
					<div class="row mg-b-25">	
						<div class="col-lg-4">
							<div class="form-group">
								<label class="form-control-label">Page Limit: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="pageLimit" value="<?PHP echo $setting->pageLimit; ?>" placeholder="" required />
							</div>
						</div><!-- col-4 -->
					</div><!-- row -->

					<div class="form-layout-footer">
						<button class="btn btn-info">Submit</button>
					</div><!-- form-layout-footer -->
				</form>
			</div><!-- form-layout -->
		</div>
	</div>