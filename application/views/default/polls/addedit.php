<script src="<?php echo base_url("skin/default/js/ckeditor/ckeditor.js"); ?>"></script>
<script src="<?php echo base_url("skin/default/js/editors.js"); ?>"></script>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>polls">Polling</a>
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
					<form name="pollingForm" id="pollingForm" action="<?php echo site_url("polls/save"); ?>" method="POST" enctype="multipart/form-data" >
					<?php if(!empty($editpolls)){$polls = $editpolls[0];} ?>
					<?php if(!empty($polls) && $polls->pollingId != ''){ ?>
						<input type="hidden" name="polls[pollingId]" id="pollingId" value="<?php echo $polls->pollingId; ?>"/>
					<?php }?>
					
						<div class="row ">
							<div class="col-sm-6 mg-t-10 mg-sm-t-0">
								<div class="row mg-t-10">
									<label class="form-control-label pull-left"><span class="tx-danger">*</span> Title:</label>
									<input type="text" class="form-control" id="pollFormspacetext" name="polls[pollingQuestion]" placeholder="" value="<?php echo (!empty($polls) && $polls->pollingQuestion!='')?$polls->pollingQuestion:'';?>" />
								</div>
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> Start Date:</label>
										<input type="text" id="startDate" class="form-control" name="polls[startDate]" placeholder="" value="<?php echo (!empty($polls) && $polls->startDate!='')?$polls->startDate:'';?>" readonly />
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> Start Time:</label>
										<input type="text" id="startTime" class="form-control" name="polls[startTime]" placeholder="" value="<?php echo (!empty($polls) && $polls->startTime!='')?$polls->startTime:'';?>" readonly />
									</div>
								</div>
								<div class="row mg-t-10">
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> End Date:</label>
										<input type="text" id="endDate" class="form-control" name="polls[endDate]" placeholder="" value="<?php echo (!empty($polls) && $polls->endDate!='')?$polls->endDate:'';?>" readonly />
									</div>
									<div class="col-sm-6">
										<label class="form-control-label pull-left"><span class="tx-danger">*</span> End Time:</label>
										<input type="text" id="endTime" class="form-control" name="polls[endTime]" placeholder="" value="<?php echo (!empty($polls) && $polls->endTime!='')?$polls->endTime:'';?>" readonly />
									</div>
								</div>
								<div class="row mg-t-10">
									<label class="form-control-label pull-left">Description:</label>
									<textarea class="form-control" id="pollFormspacetextarea" name="polls[description]" placeholder="" cols="30" rows="3" ><?php echo (!empty($polls) && $polls->description!='')?$polls->description:'';?></textarea>
								</div>
							</div>							
							<div class="col-sm-6 mg-t-20 mg-sm-t-0">
								<div style="text-align:right;"><?PHP if(!empty($polls)){ ?> <a href="javascript:void(0);" class="addNew" >Add New</a><?PHP } ?></div>
								<div class="bd bd-gray-300 rounded table-responsive">				
									<table class="table table-striped mg-b-0 table-hover pollsUpload">
										<thead>
											<tr>												
												<th class="col-sm-10">Options</th>
											</tr>
										</thead>
										<tbody>
										<?PHP if(!empty($polls)){ 
											$pollAnswer = getPollsanswer($polls->pollingId);
											if(!empty($pollAnswer)){
												$i=1;
												foreach($pollAnswer as $pollAns){
										?>
											<tr id="row<?PHP echo $i; ?>" class="pollsUploadoption">
												<td>
													<div class="form-group">
														<input class="form-control" type="hidden" name="poll[pollingAnswerId][<?PHP echo $i; ?>]" value="<?PHP echo $pollAns['pollingAnswerId']; ?>" id="options<?PHP echo $i; ?>" />
														
														<input class="form-control pollOptions" type="text" name="poll[options][<?PHP echo $i; ?>]" value="<?PHP echo $pollAns['answer']; ?>" id="options<?PHP echo $i; ?>" />
													</div>
												</td>
												<td> &nbsp; </td>
											</tr>
										<?PHP $i++; } } } else { ?>
											<tr id="row1" class="pollsUploadoption">
												<td>
													<div class="form-group">
														<input class="form-control pollOptions" type="text" name="poll[options][1]" value="" id="options1" pattern="[A-Za-z0-9]+" />
													</div>
												</td>
												<td> &nbsp; </td>
											</tr>
											<tr id="row2" class="pollsUploadoption">
												<td>
													<div class="form-group">
														<input class="form-control pollOptions" type="text" name="poll[options][2]" value="" id="options2" pattern="[A-Za-z0-9]+" />
													</div>
												</td>
												<td> &nbsp; </td>
											</tr>
											<tr id="row3" class="pollsUploadoption">
												<td>
													<div class="form-group">
														<input class="form-control pollOptions" type="text" name="poll[options][3]" value="" id="options3" pattern="[A-Za-z0-9]+" />
													</div>
												</td>
												<td>
													<span class="addNew" style="font-weight:bold; color:#000;">Add</span>
												</td>
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
									<input type="button" name="cancel" value="Cancel" onclick="window.location.href='<?php echo site_url("polls"); ?>'" class="btn btn-primary waves-effect pull-left" />
									
									<input type="submit" name="submit" value="<?php if(!empty($polls) && $polls->pollingId !=''){ echo "Update Polls"; }else{ echo "Add Polls"; } ?>" class="btn btn-primary waves-effect pull-right" />
								</div><!-- form-layout-footer -->
							</div><!-- col-8 -->
						</div>
					</form>
					</div><!-- form-layout -->
				</div><!-- col-6 -->
			</div>
		</div>
	</div>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo site_url("skin/default/css/mdtimepicker.css"); ?>" />

<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>	
<script type="text/javascript" src="<?php echo site_url("skin/default/js/mdtimepicker.js"); ?>"></script>
		
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
			//maxDate: 0,
			beforeShow: function(){				
				var theDate = new Date();
				theDate.setDate(theDate.getDate());
				$(this).datepicker('option','minDate',theDate);
			},
			onClose: function(){       
				var theDate = new Date($(this).datepicker('getDate'));
				theDate.setMonth(theDate.getMonth() + 1);
				//$('#endDate').datepicker('option','maxDate',theDate);
				$('#endDate').datepicker('option','minDate',$(this).datepicker('getDate'));
			}
		});
		
		$('#startTime').datetimepicker({
			datepicker:false,
			format:'H:i',
			step: 01 
		}); 
		
		$("#endDate").datepicker({
			dateFormat: "yy-mm-dd",
			minDate: 0,
		});
		
		$('#endTime').datetimepicker({
			datepicker:false,
			format:'H:i',
			step: 01			
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
		
		jQuery.validator.addMethod("nameRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\?\ \s]+$/i.test(value);
		}, "Questions must contain letters, space & special character like '?' only allowed.");
		
		jQuery.validator.addMethod("optionsRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\_\$\@\ \s]+$/i.test(value);
		}, "Options must contain letters, numbers, space & special character like '&,_,$,@' only allowed.");
		
		jQuery.validator.addMethod("descriptionRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\_\$\!\@\ \s]+$/i.test(value);
		}, "Description must contain letters, numbers, space & special character like '&,_,$,@,!' only allowed.");
		
		
		$('.addNew').click(function(){
			combined = $('.pollsUploadoption').length;
			var i = $('.pollsUploadoption').length;
			i++;
			if(combined <= 4){
				$('.pollsUpload').append('<tr id="row'+i+'" class="pollsUploadoption"><td><div class="form-group"><input class="form-control pollOptions" type="text" name="poll[options]['+i+']" value="" placeholder="" id="options'+i+'" /></div></td><td><a href="javascript:void(0);" class="btn_removedir" id="'+i+'">Remove</a></td></tr>');
				$('input[name="poll[options]['+i+']"]').rules( "add", {
					required:true,
					minlength: 1,
					maxlength: 32,
					//optionsRegex:true
				});
			} else {
				alert('You have picked the maximum limit 5 items.').always(function() { });
			}
			$('#options'+i).prop("required", true);
		});
		
		$(document).on('click', '.btn_removedir', function(){
			if (confirm('Are you sure you want delete this row?')){
				var button_id = $(this).attr("id");
				$("#row"+button_id+"").remove();
				var j = 1;
				$('.pollsUpload tbody tr').each(function () {
					$(this).removeAttr('id');
					$(this).attr('id', 'row' + j);
					
					$('#row' + j + ' td a').removeAttr('id');
					$('#row' + j + ' td a').attr('id', j);
					
					$('#row' + j + ' td input').removeAttr('id');
					$('#row' + j + ' td input').attr('id', 'options'+ j);
					
					$('#row' + j + ' td input').removeAttr('name');
					$('#row' + j + ' td input').attr('name', 'poll[options]['+j+']');
					j++;
				});
				location.reload();
			}
			combined = 0;
			var i = $('.pollsUploadoption').length;
		});
		
		$("#pollingForm").validate({
			debug:false,
			rules: {
				'polls[pollingQuestion]' : {
					required:true,
					minlength: 1,
					maxlength: 200,
					//nameRegex:true
				},
				'polls[startDate]': {
					required: true,
				},
				'polls[startTime]': {
					required: true,
				},
				'polls[endDate]': {
					required: true,
				},
				'polls[endTime]': {
					required: true,
				},
				'polls[description]': {
					minlength: 1,
					maxlength: 280,
					//descriptionRegex: true
				},
				'poll[options][1]': {
					required: true,
					minlength: 1,
					maxlength: 32,
					//optionsRegex:true
				},
				'poll[options][2]': {
					required: true,
					minlength: 1,
					maxlength: 32,
					//optionsRegex:true
				},
				'poll[options][3]': {
					required: true,
					minlength: 1,
					maxlength: 32,
					//optionsRegex:true
				},				
			},	
			highlight: function(element) {
				$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			},
			success: function(element) {
				$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			},
			
		});
		
		$("#pollFormspacetext").on("keydown", function(e) {
			var len = $("#pollFormspacetext").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#pollFormspacetext").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#pollFormspacetextarea").on("keydown", function(e) {
			var len = $("#pollFormspacetextarea").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#pollFormspacetextarea").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$(".pollOptions").on("keydown", function(e) {
			var len = $(".pollOptions").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $(".pollOptions").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
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