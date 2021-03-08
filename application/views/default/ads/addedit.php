<?PHP $user = getUserRole(); ?>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>ads">Ads</a>
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
					<form name="adsForm" id="adsForm" action="<?php echo site_url("ads/save"); ?>" method="POST" enctype="multipart/form-data" >
						<?php if(!empty($editads)){$ads = $editads[0];}
							if(!empty($ads) && $ads->adsId != ''){ ?>
								<input type="hidden" name="ads[adsId]" id="adsId" value="<?php echo $ads->adsId; ?>"/>
								<input type="hidden" name="ads[updatedBy]" id="updatedBy" value="<?PHP echo $user['userid']; ?>"/>
						<?PHP } else { ?>
							<input type="hidden" name="ads[createdBy]" id="createdBy" value="<?PHP echo $user['userid']; ?>"/>
							<input type="hidden" name="ads[updatedBy]" id="updatedBy" value="<?PHP echo $user['userid']; ?>"/>
						<?PHP } ?>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Title:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="adsTitle" name="ads[title]" placeholder="Enter Ads title" value="<?php echo (!empty($ads) && $ads->title!='')?$ads->title:'';?>" />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Price:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="adsPrice" name="ads[price]" placeholder="Enter Ads price" value="<?php echo (!empty($ads) && $ads->price!='')?$ads->price:'';?>" />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Display Type:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<select type="text" class="form-control" id="" name="ads[displayType]" >
									<option value="">Please Select Type </option>
									<option value="1" <?PHP if(!empty($ads) && $ads->displayType == 1){ ?> SELECTED <?PHP } ?>>Home </option>
									<option value="2" <?PHP if(!empty($ads) && $ads->displayType == 2){ ?> SELECTED <?PHP } ?>>Category</option>
									<option value="3" <?PHP if(!empty($ads) && $ads->displayType == 3){ ?> SELECTED <?PHP } ?>>Both</option>
								 </select>
							</div>
						</div>
						<?PHP /*<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Price Type:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<select class="form-control" data-placeholder="" name="ads[priceType]" >
									<option value=""> Choose Price Type</option>
									<option value="1" <?PHP if(!empty($ads) && $ads->priceType == 1){ ?> SELECTED <?PHP } ?> >Month</option>
									<option value="2" <?PHP if(!empty($ads) && $ads->priceType == 2){ ?> SELECTED <?PHP } ?> >Year</option>
								</select>
							</div>
						</div><!-- row --> */ ?>
						
						<input type="hidden" name="ads[priceType]" id="priceType" value="1"/>
						<input type="hidden" name="ads[adsTotal]" id="adsTotal" value="1"/>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label">Description:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
							<textarea class="form-control" id="adsFormspacetextarea" name="ads[description]" placeholder="" cols="30" rows="3" ><?php echo (!empty($ads) && $ads->description!='')?$ads->description:'';?></textarea>
							</div>
						</div>
						
						<?PHP /*<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"> Sequence Order:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="sequenceID" name="ads[sequenceID]" placeholder="Enter sequence order" value="<?php echo (!empty($ads) && $ads->sequenceID!='')?$ads->sequenceID:'';?>" />
							</div>
						</div><!-- row -->	*/ ?>
						<div class="row mg-t-30">
							<div class="col-sm-8 mg-l-auto">
								<div class="form-layout-footer">
									<button class="btn btn-info adsubmit" type="button">Submit</button>
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
		
		
		
		$('.adsubmit').on('click',function(){
			$("#adsForm").validate({
				rules: {
					'ads[title]':{
						required:true,
						minlength: 1,
						maxlength: 32,
						//nameRegex:true
					},
					'ads[price]':{
						required:true,
						number:true,
						minlength: 1,
						maxlength: 3,
						numberzeroRegex:true,
					},
					'ads[description]': {
						minlength: 1,
						maxlength: 32,
						//descriptionRegex: true,
					},
					'ads[displayType]': {
						required:true,
					},					
				},	
				highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},
			});
			var adsVal = $('#adsTitle').val();
			var adsId = $('#adsId').val();
			var isValidtrim = $("#adsTitle").val($.trim($("#adsTitle").val()));
			if(isValidtrim){
				$.ajax({
					url: '<?php echo base_url();?>ads/adsCheck',
					type: "POST",
					dataType: "json",
					data:{ title:adsVal, adsId:adsId },
					success:function(data) {
						if(data == 1){
							alert('ads Name Already Exist.');
							return false;
						} else if(data == 0){
							$("form[name='adsForm']").submit();
						}
					}
				});
			}
		});
		
		$("#adsTitle").on("keydown", function(e) {
			var len = $("#adsTitle").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#adsTitle").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#adsPrice").on("keydown", function(e) {
			var len = $("#adsPrice").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#adsPrice").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#adsFormspacetextarea").on("keydown", function(e) {
			var len = $("#adsFormspacetextarea").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#adsFormspacetextarea").val();
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
		
	});
</script>