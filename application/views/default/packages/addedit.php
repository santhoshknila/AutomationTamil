<?PHP $user = getUserRole(); ?>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>packages">Packages</a>
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
					<div class="form-layout form-layout-5">
					<form name="packagesForm" id="packagesForm" action="<?php echo site_url("packages/save"); ?>" method="POST" enctype="multipart/form-data" >
						<?php if(!empty($editpack)){$pack = $editpack[0];}
							if(!empty($pack) && $pack->packageId != ''){ ?>
								<input type="hidden" name="package[packageId]" id="packageId" value="<?php echo $pack->packageId; ?>"/>
								<input type="hidden" name="package[updatedBy]" id="updatedBy" value="<?PHP echo $user['userid']; ?>"/>
						<?PHP } else { ?>
							<input type="hidden" name="package[createdBy]" id="createdBy" value="<?PHP echo $user['userid']; ?>"/>
							<input type="hidden" name="package[updatedBy]" id="updatedBy" value="<?PHP echo $user['userid']; ?>"/>
						<?PHP } ?>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Title:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="packageTitle" name="package[title]" placeholder="Enter Package title" value="<?php echo (!empty($pack) && $pack->title!='')?$pack->title:'';?>" />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Price:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="packagePrice" name="package[price]" placeholder="Enter Package price" value="<?php echo (!empty($pack) && $pack->price!='')?$pack->price:'';?>" />
							</div>
						</div>
						<!-- row -->
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Total Products Upload:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="packageProTotal" name="package[packageProTotal]" placeholder="" value="<?php echo (!empty($pack) && $pack->packageProTotal!='')?$pack->packageProTotal:'';?>" />
							</div>
						</div><!-- row -->	
						<?PHP /*<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Price Type:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<select class="form-control" data-placeholder="" name="package[priceType]" >
									<option value=""> Choose Price Type</option>
									<option value="1" <?PHP if(!empty($pack) && $pack->priceType == 1){ ?> SELECTED <?PHP } ?> >Month</option>
									<option value="2" <?PHP if(!empty($pack) && $pack->priceType == 2){ ?> SELECTED <?PHP } ?> >Year</option>
								</select>
							</div>
						</div><!-- row --> */ ?>
						<input type="hidden" name="package[priceType]" id="priceType" value="1"/>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label">Description:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
							<textarea class="form-control" id="packageFormspacetextarea" name="package[description]" placeholder="" cols="30" rows="3" ><?php echo (!empty($pack) && $pack->description!='')?$pack->description:'';?></textarea>
							</div>
						</div>
						
						<?PHP /*<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"> Sequence Order:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="sequenceID" name="package[sequenceID]" placeholder="Enter sequence order" value="<?php echo (!empty($pack) && $pack->sequenceID!='')?$pack->sequenceID:'';?>" />
							</div>
						</div><!-- row -->	*/ ?>
						<div class="row mg-t-30">
							<div class="col-sm-8 mg-l-auto">
								<div class="form-layout-footer">
									<button class="btn btn-info packagesubmit" type="button" >Submit</button>
								</div><!-- form-layout-footer -->
							</div><!-- col-8 -->
						</div>
					</form>
					</div><!-- form-layout -->
				</div><!-- col-6 -->
			</div>
		</div>
	</div>
<script type="text/javascript">
	$(document).ready(function() {
		jQuery.validator.addMethod("nameRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z]{1}[a-zA-Z0-9\&\s]+$/i.test(value);
		}, "Title must contain letters, space & special character like '&' only allowed. First letter not allowed special characters.");
		
		jQuery.validator.addMethod("numberzeroRegex", function(value, element) {
			return this.optional(element) || /^[1-9]+/i.test(value);
		}, "Zero not allowed");
		
		jQuery.validator.addMethod("descriptionRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9]{1}[a-zA-Z0-9\&\@\s]+$/i.test(value);
		}, "Description must contain letters, numbers, space & special character like '&,@' only allowed. First letter not allowed special characters.");
		
		$('.packagesubmit').on('click',function(){
			$("#packagesForm").validate({
				rules: {
					'package[title]':{
						required:true,
						minlength: 1,
						maxlength: 32,
						//nameRegex:true,
					},
					'package[price]':{
						required:true,
						number:true,
						minlength: 1,
						maxlength: 4,
						numberzeroRegex:true,
					},									
					'package[packageProTotal]':{
						required:true,
						number:true,
						minlength: 1,
						maxlength: 4,
						numberzeroRegex:true,
					},
					'package[description]': {
						minlength: 1,
						maxlength: 50,
						//descriptionRegex: true,
					},
				},	
				highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},
			});
			
			var packageVal = $('#packageTitle').val();
			var packageId = $('#packageId').val();
			var isValidtrim = $("#packageTitle").val($.trim($("#packageTitle").val()));
			if(isValidtrim){
				$.ajax({
					url: '<?php echo base_url();?>packages/productCheck',
					type: "POST",
					dataType: "json",
					data:{ title:packageVal, packageId:packageId },
					success:function(data) {
						if(data == 1){
							alert('package Name Already Exist.');
							return false;
						} else if(data == 0){
							$("form[name='packagesForm']").submit();
						}
					}
				});
			}
		});
		
		$("#packageTitle").on("keydown", function(e) {
			var len = $("#packageTitle").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#packageTitle").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#packagePrice").on("keydown", function(e) {
			var len = $("#packagePrice").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#packagePrice").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#packageFormspacetextarea").on("keydown", function(e) {
			var len = $("#packageFormspacetextarea").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#packageFormspacetextarea").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});

		$("#sequenceID").on("keydown", function(e) {
			var len = $("#sequenceID").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#sequenceID").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#packageProTotal").on("keydown", function(e) {
			var len = $("#packageProTotal").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#packageProTotal").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
	});
</script>