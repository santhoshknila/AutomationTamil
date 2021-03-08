<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>locations">Locations</a>
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
					<form name="locationForm" id="locationForm" action="<?php echo site_url("locations/save"); ?>" method="POST">
						<?php if(!empty($editloc)){$loca = $editloc[0];}
							if(!empty($loca) && $loca->locationsId != ''){ ?>
								<input type="hidden" name="location[locationsId]" id="locationsId" value="<?php echo $loca->locationsId; ?>"/>
						 <?PHP } ?>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Name:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="locationName" name="location[name]" placeholder="Enter location name" value="<?php echo (!empty($loca) && $loca->name!='')?$loca->name:'';?>" />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label">Parent:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<select class="form-control" data-placeholder="" name="location[parentId]" >
									<option value=""> Choose Parent location</option>
									<?PHP if(!empty($location)){
										foreach($location as $loc){ ?>
										<option value="<?PHP echo $loc->locationsId; ?>" <?PHP if(!empty($loca) && $loca->parentId == $loc->locationsId){ ?> SELECTED <?PHP } ?>><?PHP echo $loc->name; ?></option>
									<?PHP } } ?>
								</select>
							</div>
						</div><!-- row -->						
						<div class="row mg-t-30">
							<div class="col-sm-8 mg-l-auto">
								<div class="form-layout-footer">
									<button class="btn btn-info locationSubmit" type="button" >Submit</button>
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
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\s]+$/i.test(value);
		}, "Location Name must contain letters, space & special character like '&' only allowed.");
		
		$('.locationSubmit').on('click',function(){
			$("#locationForm").validate({
				rules:{
					'location[name]':{
						required:true,
						minlength: 1,
						maxlength: 32,
						//nameRegex:true
					},
				},	
				highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},
			});
			var locationVal = $('#locationName').val();			
			var locationsId = $('#locationsId').val();
			var isValidtrim = $("#locationName").val($.trim($("#locationName").val()));
			if(isValidtrim){
				$.ajax({
					url: '<?php echo base_url();?>locations/locationCheck',
					type: "POST",
					dataType: "json",
					data:{ name:locationVal, locationsId:locationsId },
					success:function(data){
						console.log(data);
						if(data == 1){
							alert('location Name Already Exist.');
							return false;
						} else if(data == 0){
							$("form[name='locationForm']").submit();
						}
					}
				});
			}
		});
		
		$("#locationName").on("keydown", function(e) {
			var len = $("#locationName").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#locationName").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
	});
</script>