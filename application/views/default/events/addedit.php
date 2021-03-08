<script src="<?php echo base_url("skin/default/js/ckeditor/ckeditor.js"); ?>"></script>
<script src="<?php echo base_url("skin/default/js/editors.js"); ?>"></script>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>events">Events</a>
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
					<form name="eventsForm" id="eventsForm" action="<?php echo site_url("events/save"); ?>" method="POST" enctype="multipart/form-data" >
					<?php if(!empty($editevents)){$events = $editevents[0];} ?>
					<?php if(!empty($events) && $events->eventId != ''){ ?>
						<input type="hidden" name="event[eventId]" id="eventId" value="<?php echo $events->eventId; ?>" />
					<?php }?>
					
						<div class="row ">
							<div class="col-sm-6 mg-t-10 mg-sm-t-0">
								<div class="row mg-t-10">
									<label class="form-control-label pull-left"><span class="tx-danger">*</span> Title:</label>
									<input type="text" class="form-control" id="eventsFormspacetext" name="event[title]" placeholder="" value="<?php echo (!empty($events) && $events->title!='')?$events->title:'';?>" />
								</div>
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> Start Date:</label>
										<input type="text" id="startDate" class="form-control" name="event[startDate]" placeholder="" value="<?php echo (!empty($events) && $events->startDate!='')?$events->startDate:'';?>" readonly />
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> Start Time:</label>
										<input type="text" id="startTime" class="form-control" name="event[startTime]" placeholder="" value="<?php echo (!empty($events) && $events->startTime!='')?$events->startTime:'';?>" readonly />
									</div>
								</div>
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> End Date:</label>
										<input type="text" id="endDate" class="form-control" name="event[endDate]" placeholder="" value="<?php echo (!empty($events) && $events->endDate!='')?$events->endDate:'';?>" readonly />
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> End Time:</label>
										<input type="text" id="endTime" class="form-control" name="event[endTime]" placeholder="" value="<?php echo (!empty($events) && $events->endTime!='')?$events->endTime:'';?>" readonly />
									</div>
								</div>
								<div class="row mg-t-10">
									<label class="form-control-label pull-left">Description:</label>
									<textarea class="form-control" name="event[description]" id="eventsFormspacetextarea" placeholder="" cols="30" rows="3" ><?php echo (!empty($events) && $events->description!='')?$events->description:'';?></textarea>
								</div>
								
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left">  Location:</label>
										<input type="text" class="form-control" id="eventsFormspaceloc" name="event[locationDetails]" placeholder=""  value="<?php echo (!empty($events) && $events->locationDetails!='')?$events->locationDetails:'';?>" />
									</div>
								
									<div class="col-sm-6">
										<label class="form-control-label pull-left"> Venue:</label>
										<input type="text" class="form-control" id="eventsFormspaceven" name="event[venueDetails]" placeholder=""  value="<?php echo (!empty($events) && $events->venueDetails!='')?$events->venueDetails:'';?>" />
									</div>
								</div>
								
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left">  Phone 1<small class="mg-l-10">(Country code)</small>:</label>
										<select class="form-control select2" data-placeholder="Choose country code" id="ccode_1"  name="event[ccode_1]" >
											<option value="">Choose Code</option>
											<?PHP 
												foreach($country as $cou){ ?>
												<option value="<?PHP echo $cou->code; ?>" <?php if(!empty($events) && $events->ccode_1 == $cou->code){ echo "SELECTED"; } else { echo ""; }?>><?PHP echo $cou->name." (+".$cou->code.")"; ?></option>	
											<?PHP } ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left">  Phone 1:</label>
										<input type="text" class="form-control" id="eventsFormspacepho1" name="event[phone_1]" placeholder=""  value="<?php echo (!empty($events) && $events->phone_1!='')?$events->phone_1:'';?>" />
									</div>
								</div>
								
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left">Phone 2<small class="mg-l-10">(Country code)</small>:</label>
										<select class="form-control select2" data-placeholder="Choose country code" id="ccode_2" name="event[ccode_2]" >
											<option value="">Choose Code</option>
											<?PHP 
												foreach($country as $cou){ ?>
												<option value="<?PHP echo $cou->code; ?>" <?php if(!empty($events) && $events->ccode_2 == $cou->code){ echo "SELECTED"; } else { echo ""; }?>><?PHP echo $cou->name." (+".$cou->code.")"; ?></option>	
											<?PHP } ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left">  Phone 2:</label>
										<input type="text" class="form-control" id="eventsFormspacepho2" name="event[phone_2]" placeholder="" value="<?php echo (!empty($events) && $events->phone_2!='')?$events->phone_2:'';?>" />
									</div>
								</div>
								
								<div class="row mg-t-10">
									<label class="form-control-label pull-left"> Host Organization:</label>
									<input type="text" class="form-control" id="eventsFormspacehost" name="event[hostOrganization]" placeholder="" value="<?php echo (!empty($events) && $events->hostOrganization!='')?$events->hostOrganization:'';?>" />
								</div>
								<?php if(!empty($events) && $events->createdBy != 1){ ?>
								<div class="row mg-t-10">
									<label class="form-control-label pull-left"><span class="tx-danger">*</span> Status:</label><br/>
									<div class="row col-lg-12 mg-t-20 mg-lg-t-0">
										<select class="form-control select2" id="status" name="event[status]" data-placeholder="Choose Status">
											<option value="" >Choose Status</option>
											<option value="1" <?php if(!empty($events) && $events->status == 1){ echo "SELECTED"; } else { echo ""; }?>>Approved</option>
											<option value="4" <?php if(!empty($events) && $events->status == 4){ echo "SELECTED"; } else { echo ""; }?>>Rejected</option>
										</select>
									</div><!-- col-3 -->
									
								</div><!-- row -->
								<input name="event[createdBy]" id="createdBy" type="hidden" value="<?PHP echo $events->createdBy; ?>" />
								<?PHP } else { ?>
									<input name="event[status]" id="status" type="hidden" value="1" />
									<input name="event[createdBy]" id="createdBy" type="hidden" value="1" />
									<input name="event[updatedBy]" id="updatedBy" type="hidden" value="1" />
								<?PHP } ?>
								
							</div>

							<div class="col-sm-6 mg-t-20 mg-sm-t-0">
								<div style="text-align:right;"><a href="javascript:void(0);" class="addNew">Add New</a></div>
								<div class="bd bd-gray-300 rounded table-responsive">
									<table class="table table-striped mg-b-0 table-hover eventsUpload">
										<thead>
											<tr>												
												<th class="col-sm-4">Upload Type</th>
												<th class="col-sm-6">Option</th>
												<th class="col-sm-2">Actions</th>
											</tr>
										</thead>
										<tbody>
										<?PHP if(!empty($events)){
											$mediaFiles = getEventMedia($events->eventId);
											if(!empty($mediaFiles)){
												$i=1;
												foreach($mediaFiles as $media){
											?>
											<tr id="row<?PHP echo $i; ?>" class="eventsUploadfiles">
												<td>
													<input class="form-control" type="hidden" name="eventImageId[<?PHP echo $i; ?>]" value="<?PHP echo $media['eventImageId']; ?>" readonly />
													<select class="form-control fileType" data-placeholder="" name="events[fileType][<?PHP echo $i; ?>]" id="<?PHP echo $i; ?>" required disabled >
														<option value=""> Choose File Type</option>
														<option value="image<?PHP echo $i; ?>" <?PHP if($media['fileType'] == 'image'){ echo "SELECTED"; } ?>>Image</option>
														<option value="url<?PHP echo $i; ?>" <?PHP if($media['fileType'] == 'url'){ echo "SELECTED"; } ?>>Youtube URL</option>
													</select>
												</td>
												<td>
													<?PHP if($media['fileType'] == 'image'){ ?>
													<div class="uploadImage<?PHP echo $i; ?>">
														<div class="form-group">
															<input id="filePathImages<?PHP echo $i; ?>" type="hidden" name="filehiddenPathimages[<?PHP echo $i; ?>]" value="<?PHP echo $media['imagevideo_url']; ?>" placeholder="" />
															<img src="<?PHP echo site_url("uploads/events/".$media['imagevideo_url']); ?>" width="100px" height="80px">
														</div>
													</div><!-- col-4 -->
													<?PHP } else if($media['fileType'] == 'url'){ ?>
													<div class="uploadUrl<?PHP echo $i; ?>">
														<div class="form-group">
															<input class="form-control" type="url" name="filehiddenPathURl[<?PHP echo $i; ?>]" value="<?PHP echo $media['imagevideo_url']; ?>" placeholder="Enter the valid youtube URL" id="filehiddenPathUrl<?PHP echo $i; ?>" readonly />
														</div>
													</div>
													<?PHP } ?>
												</td>
												<td>
													<a href="javascript:void(0);" class="btn_editremoveUp" id="<?PHP echo $i; ?>" rel="<?PHP echo $media['eventImageId']; ?>">Remove</a>
												</td>
											</tr>											
											<?PHP
												$i++;
												} 
											}
										} else { ?>
											<tr id="row1" class="eventsUploadfiles">
												<td>
													<select class="form-control fileType" name="events[fileType][1]" id="1" >
														<option value=""> Choose File Type</option>
														<option value="image1">Image</option>
														<option value="url1">Youtube URL</option>
													</select>
												</td>
												<td>
													<div class="uploadImage1" style="display:none;">
														<div class="form-group">
															<input id="filePathImages1" type="file" name="filePathimages[1]" value="" placeholder="">
														</div>
													</div>
													<div class="uploadUrl1" style="display:none;">
														<div class="form-group">
															<input class="form-control" type="url" name="events[filePathURl][1]" value="" placeholder="Enter the valid youtube URL" id="filePathUrl1">
														</div>
													</div>
												</td>
												<td><a href="javascript:void(0);" class="btn_removedir" id="1">Remove</a></td>
											</tr>
										<?PHP } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="row mg-t-30">
							<div class="col-sm-12 mg-l-auto">
								<div class="form-layout-footer">
									<input type="button" name="cancel" value="Cancel" onclick="window.location.href='<?php echo site_url("events"); ?>'" class="btn btn-primary waves-effect pull-left" />
									
									<input type="submit" name="submit" value="<?php if(!empty($events) && $events->eventId !=''){ echo "Update Events"; }else{ echo "Add Events"; } ?>" class="btn btn-primary waves-effect pull-right" />
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
		$('#startDate').on('change', function(){
			$('#endDate').val('');
		});
		$('#endDate').on('click', function(){
			var startDate = $('#startDate').val();
			if(startDate == ''){
				alert("Select start date and time");
				$('#startDate').focus();
			}
		});
		$('#startDate').datepicker({
			dateFormat: "yy-mm-dd",
			minDate: 0,
			beforeShow: function(){				
				var theDate = new Date();
				theDate.setDate(theDate.getDate());
				$(this).datepicker('option','minDate',theDate);         
			},			
			onClose: function(){       
				var theDate = new Date($(this).datepicker('getDate'));
				theDate.setMonth(theDate.getMonth() + 1);
				$('#endDate').datepicker('option','maxDate',theDate);
				$('#endDate').datepicker('option','minDate',$(this).datepicker('getDate'));
			}
		});		
		
		$('#startTime').datetimepicker({
			datepicker:false,
			format:'H:i',
			step: 01,
			onClose: function (time){
				$("#endTime").datetimepicker("option", "minTime", time);
			}
		}); 
		
		var endDatedefault = $('#endDate').val();
		if(endDatedefault != ''){
			var startDateend = $('#startDate').val();
			var theDateend = new Date(startDateend);
			theDateend.setMonth(theDateend.getMonth() + 1);
			var maxDateend = theDateend;
		}
		
		$("#endDate").datepicker({
			dateFormat: "yy-mm-dd",
			minDate: 0,
			maxDate: maxDateend,
		});
		
		$('#endTime').datetimepicker({
			datepicker:false,
			format:'H:i',
			step: 01,			
		});
				
		$('#endTime').change(function(){
			var startTime = $('#startTime').val();
			var startDate = $('#startDate').val();
            var endTime = $('#endTime').val();
            var endDate = $('#endDate').val();
			
			st = minFromMidnight(startTime);
			et = minFromMidnight(endTime);
			if(startTime>=endTime && startDate==endDate){
				alert("End time must be greater than start time");
				$('#endTime').val('');
			}
		});	
		
		$('.addNew').click(function(){
			combined = $('.eventsUploadfiles').length;
			var i = $('.eventsUploadfiles').length;
			i++;
			if(combined <= 14){
				$('.eventsUpload').append('<tr id="row'+i+'" class="eventsUploadfiles"><td><select class="form-control fileType" name="events[fileType]['+i+']" id="'+i+'" required ><option value=""> Choose File Type</option><option value="image'+i+'">Image</option><option value="url'+i+'">Youtube URL</option></select></td><td><div class="uploadImage'+i+'" style="display:none;"><div class="form-group"><input id="filePathImages'+i+'" type="file" name="filePathimages['+i+']" value="" placeholder="" /></div></div><div class="uploadUrl'+i+'" style="display:none;"><div class="form-group"><input class="form-control" type="url" name="events[filePathURl]['+i+']" value="" placeholder="Enter the valid youtube URL" id="filePathUrl'+i+'" /></div></div></td><td><a href="javascript:void(0);" class="btn_removedir" id="'+i+'">Remove</a></td></tr>');
			} else {
				alert('You have picked the maximum limit 15 items.').always(function() { });
			}
		});
		
		/*Image and video uploads*/
		
		$(document).on('change', '.fileType', function(){
			var row_id = $(this).attr("id");
			var selVal = $('option:selected',this).val();
			if(selVal == 'image'+row_id){
				$('.uploadImage'+row_id).show();
				$('.uploadUrl'+row_id).hide();
				
				$('#filePathImages'+row_id).prop("required", true);
				$('#filePathUrl'+row_id).prop("required", false);
			} else if(selVal == 'url'+row_id){
				$('.uploadImage'+row_id).hide();
				$('.uploadUrl'+row_id).show();
				
				$('#filePathImages'+row_id).prop("required", false);
				$('#filePathUrl'+row_id).prop("required", true);
			}
		});
		
		$(document).on('click', '.btn_removedir', function(){
			if(confirm('Are you sure you want delete this row?')){
				var button_id = $(this).attr("id");
				$("#row"+button_id+"").remove();
				var j = 1;
				$('.eventsUpload tbody tr').each(function () {
					$(this).removeAttr('id');
					$(this).attr('id', 'row' + j);					
					
					$('#row' + j + ' td a').removeAttr('id');
					$('#row' + j + ' td a').attr('id', j);	
					j++;
				});				
			}
			combined = 0;
		});	
		
		$(document).on('click', '.btn_editremoveUp', function(){
			if (confirm('Are you sure you want delete this row?')){
				var button_id = $(this).attr("id");
				var uploadRowID = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>events/removeUploadfiles',
					data:{uploadRowID:uploadRowID},
					success: function(data){
						if(data == 1){
							alert('Upload files not removed!');							
						} else {
							$("#row"+button_id+"").remove();				
							var i = 1;
							alert('Upload files removed successfully.');
						}
					}
				});
			}
			combined = 0;
		});	
		
		jQuery.validator.addMethod("nameRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\s]+$/i.test(value);
		}, "Fields must contain alphanumeric only allowed.");

		jQuery.validator.addMethod("descriptionRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\_\$\!\@\s]+$/i.test(value);
		}, "Description must contain letters, numbers, space & special character like '&,_,$,@,!' only allowed.");		
		
		$("#eventsForm").validate({
			rules: {
				'event[title]' : {
					required: true,
					minlength: 1,
					maxlength: 50,
					//nameRegex: true,
				},
				'event[startDate]': {
					required: true,
				},
				'event[startTime]': {
					required: true,
				},
				'event[endDate]': {
					required: true,
				},
				'event[endTime]': {
					required: true,
				},
				'event[locationDetails]' : {
					//required: true,
					minlength: 1,
					maxlength: 32,
					//descriptionRegex: true,
				},
				'event[venueDetails]' : {
					//required: true,
					minlength: 1,
					maxlength: 32,
					//descriptionRegex: true,
				},
				'event[phone_1]' : {
					//required: true,
					minlength: 5,
					maxlength: 20,
					number: true
				},
				'event[phone_2]' : {
				//	minlength: 5,
					maxlength: 20,
					number: true
				},
				'event[description]': {					
					minlength: 1,
					maxlength: 280,
					//descriptionRegex: true,
				},
				'event[hostOrganization]': {
					minlength: 1,
					maxlength: 32,
					//required: true,
					//nameRegex: true,
				},
				'event[status]': {
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
		
		$("#eventsFormspacetext").on("keydown", function(e) {
			var len = $("#eventsFormspacetext").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspacetext").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#eventsFormspaceloc").on("keydown", function(e) {
			var len = $("#eventsFormspaceloc").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspaceloc").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#eventsFormspaceven").on("keydown", function(e) {
			var len = $("#eventsFormspaceven").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspaceven").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#eventsFormspacepho1").on("keydown", function(e) {
			var len = $("#eventsFormspacepho1").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspacepho1").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#eventsFormspacepho2").on("keydown", function(e) {
			var len = $("#eventsFormspacepho2").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspacepho2").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});
		
		$("#eventsFormspacehost").on("keydown", function(e) {
			var len = $("#eventsFormspacehost").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspacehost").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});
		
		$("#eventsFormspacetextarea").on("keydown", function(e) {
			var len = $("#eventsFormspacetextarea").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#eventsFormspacetextarea").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#eventsFormspacetext").emojioneArea({	autoHideFilters: true });
		$("#eventsFormspacetextarea").emojioneArea({	autoHideFilters: true });
	});
	function minFromMidnight(tm){
		//var ampm= tm.substr(-2)
		var clk = tm.substr(0, 5);
		var m  = parseInt(clk.match(/\d+$/)[0], 10);
		var h  = parseInt(clk.match(/^\d+/)[0], 10);
		//h += (ampm.match(/pm/i))? 12: 0;
		return h*60+m;
	}
</script>