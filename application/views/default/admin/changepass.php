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
			<div class="row">
				<div class="col-xl-12 mg-t-20 mg-xl-t-0">
					<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
					<div class="form-layout form-layout-5">
					<form name="adminpassForm" id="adminpassForm" action="<?php echo site_url("admin/updatepass"); ?>" method="POST">
						<div class="row">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">*</span> New Password:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" name="password" placeholder="Enter New Password" id="password" />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-20">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">*</span> Confirm Password:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" name="conpassword" placeholder="Enter Confirm Password" />
							</div>
						</div><!-- row -->							
						<div class="row mg-t-30">
							<div class="col-sm-8 mg-l-auto">
								<div class="form-layout-footer">
									<button class="btn btn-info">Submit</button>
								</div><!-- form-layout-footer -->
							</div><!-- col-8 -->
						</div>
					</form>
					</div><!-- form-layout -->
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
$(document).ready(function() {		
	/**
	 * Custom validator for contains at least one lower-case letter
	 */
	jQuery.validator.addMethod("atLeastOneLowercaseLetter", function (value, element) {
		return this.optional(element) || /[a-z]+/.test(value);
	}, "Must have at least one lowercase letter");
	 
	/**
	 * Custom validator for contains at least one upper-case letter.
	 */
	jQuery.validator.addMethod("atLeastOneUppercaseLetter", function (value, element) {
		return this.optional(element) || /[A-Z]+/.test(value);
	}, "Must have at least one uppercase letter");
	 
	/**
	 * Custom validator for contains at least one number.
	 */
	jQuery.validator.addMethod("atLeastOneNumber", function (value, element) {
		return this.optional(element) || /[0-9]+/.test(value);
	}, "Must have at least one number");
	 
	/**
	 * Custom validator for contains at least one symbol.
	 */
	jQuery.validator.addMethod("atLeastOneSymbol", function (value, element) {
		return this.optional(element) || /[!@#$%^&*()]+/.test(value);
	}, "Must have at least one symbol");
		
	$("#adminpassForm").validate({
		debug:false,
		rules: {
			'password':{
				required: true,
				minlength: 6,
				maxlength: 20,
				atLeastOneLowercaseLetter: true,
				atLeastOneUppercaseLetter: true,
				atLeastOneNumber: true,
				atLeastOneSymbol: true
			},
			'conpassword':{
				required: true,
				minlength: 6,
				maxlength: 20,
				atLeastOneLowercaseLetter: true,
				atLeastOneUppercaseLetter: true,
				atLeastOneNumber: true,
				atLeastOneSymbol: true,
				equalTo: "#password",
			},	
		},
		messages: { 			
			conpassword: {				
				equalTo:"Please retype the correct new password"
			},
		},			
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
		},
		
	});
		
	$("#password").on("keydown", function(e) {
		var len = $("#password").val().length;
		if (len == 0) {
			return e.which !== 32;
		} else {
			var pos = $("#password").val();
			if (pos == 0) {
				return e.which !== 32;
			}
		}
	});	
	
	$("#conpassword").on("keydown", function(e) {
		var len = $("#conpassword").val().length;
		if (len == 0) {
			return e.which !== 32;
		} else {
			var pos = $("#conpassword").val();
			if (pos == 0) {
				return e.which !== 32;
			}
		}
	});	
});
</script>