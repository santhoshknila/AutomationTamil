<script src="<?php echo base_url("skin/default/js/ckeditor/ckeditor.js"); ?>"></script>
<script src="<?php echo base_url("skin/default/js/editors.js"); ?>"></script>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>newsfeeds">Newsfeeds</a>
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
					<form name="newsfeedForm" id="newsfeedForm" action="<?php echo site_url("newsfeeds/save"); ?>" method="POST" enctype="multipart/form-data" >
					<?php if(!empty($editnews)){$news = $editnews[0];} ?>
					<?php if(!empty($news) && $news->newsFeedsId != ''){ ?>
						<input type="hidden" name="news[newsFeedsId]" id="newsFeedsId" value="<?php echo $news->newsFeedsId; ?>"/>
						<input type="hidden" name="news[createdBy]" id="createdBy" value="<?php echo $news->createdBy; ?>"/>
						<input type="hidden" name="news[updatedBy]" id="updatedBy" value="<?php echo $news->updatedBy; ?>"/>
					<?php } else {?>
						<input type="hidden" name="news[createdBy]" id="createdBy" value="1"/>
						<input type="hidden" name="news[updatedBy]" id="updatedBy" value="1"/>
						<input type="hidden" name="news[privacyId]" id="privacyId" value="1"/>
					<?PHP } ?>
					
						<div class="row ">
							<div class="col-sm-4 mg-t-10 mg-sm-t-0">
								<div class="row mg-t-10">
									<label class="form-control-label pull-left"><span class="tx-danger">*</span> Title:</label>
									<input type="text" class="form-control" id="newsFormspacetext" name="news[title]" placeholder="" value="<?php echo (!empty($news) && $news->title!='')?$news->title:'';?>" />
								</div>
								<!--<div class="row mg-t-10">
									<label class="form-control-label pull-left">Start Date:</label>
									<input type="text" id="startDate" class="form-control" name="news[startDate]" placeholder="" value="< ?php echo (!empty($news) && $news->startDate!='')?$news->startDate:'';?>" readonly />
								</div>
								<div class="row mg-t-10">
									<label class="form-control-label pull-left">End Date:</label>
									<input type="text" class="form-control" id="endDate" name="news[endDate]" placeholder="" value="< ?php echo (!empty($news) && $news->endDate!='')?$news->endDate:'';?>" readonly />
								</div> -->
								<div class="row mg-t-10">
									<label class="form-control-label pull-left">Description:</label>
									<textarea class="form-control" id="newsFormspacetextarea" data-emoji-placeholder=":grimacing:" name="news[description]" placeholder="" cols="30" rows="3" ><?php echo (!empty($news) && $news->description!='')?$news->description:'';?></textarea>
								</div>
							</div>	
							
							<div class="col-sm-8 mg-t-20 mg-sm-t-0">
								<div style="text-align:right;">
								<a href="javascript:void(0);" class="addNew">Add New</a>
								</div>
								<div class="bd bd-gray-300 rounded table-responsive">
									<table class="table table-striped mg-b-0 table-hover newsUpload">
										<thead>
											<tr>												
												<th class="col-sm-4">Upload Type</th>
												<th class="col-sm-6">Option</th>
												<th class="col-sm-2">Actions</th>
											</tr>
										</thead>
										<tbody>
										<?PHP if(!empty($news)){
											$mediaFiles = getMedia($news->newsFeedsId);
											if(!empty($mediaFiles)){
												$i=1;
												foreach($mediaFiles as $media){
											?>
											<tr id="row<?PHP echo $i; ?>" class="newsUploadfiles">
												<td>
													<input class="form-control" type="hidden" name="newsfeedImageId[<?PHP echo $i; ?>]" value="<?PHP echo $media['newsfeedImageId']; ?>" readonly />
													<select class="form-control fileType" data-placeholder="" name="news[fileType][<?PHP echo $i; ?>]" id="<?PHP echo $i; ?>" required disabled >
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
															<img src="<?PHP echo site_url("uploads/newsfeeds/".$media['imagevideo_url']); ?>" width="100px" height="80px">
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
													<a id="<?PHP echo $i; ?>" href="javascript:void(0);" class="btn_editremoveUp"  rel="<?PHP echo $media['newsfeedImageId']; ?>">Remove</a>
												</td>
											</tr>											
											<?PHP
												$i++;
												} 
											}
										} ?>
										</tbody>
									</table>
								</div>
							</div>
							
						</div>
						<div class="row mg-t-30">
							<div class="col-sm-12 mg-l-auto">
								<div class="form-layout-footer">
									<input type="button" name="cancel" value="Cancel" onclick="window.location.href='<?php echo site_url("newsfeeds"); ?>'" class="btn btn-primary waves-effect pull-left" />
									
									<input type="submit" name="submit" value="<?php if(!empty($news) && $news->newsFeedsId !=''){ echo "Update News"; }else{ echo "Submit"; } ?>" class="btn btn-primary waves-effect pull-right" />
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
		$('#startDate').datepicker({
			dateFormat: "yy-mm-dd",
			minDate: 0,
			onSelect: function (selected) {
				var dt = new Date(selected);
				dt.setDate(dt.getDate() + 1);
				$("#endDate").datepicker("option", "minDate", dt);
			}
		});
		
		$("#endDate").datepicker({
			dateFormat: "yy-mm-dd",
			onSelect: function (selected) {
				var dt = new Date(selected);
				dt.setDate(dt.getDate() - 1);
				$("#startDate").datepicker("option", "maxDate", dt);
			}
		});
		
		$('.addNew').click(function(){
			combined = $('.newsUploadfiles').length;
			var i = $('.newsUploadfiles').length;			
			i++;
			if(combined <= 14){
				$('.newsUpload').append('<tr id="row'+i+'" class="newsUploadfiles"><td><select class="form-control fileType" name="news[fileType]['+i+']" id="'+i+'" required ><option value=""> Choose File Type</option><option value="image'+i+'">Image</option><option value="url'+i+'">Youtube URL</option></select></td><td><div class="uploadImage'+i+'" style="display:none;"><div class="form-group"><input id="filePathImages'+i+'" type="file" name="filePathimages['+i+']" value="" placeholder="" /></div></div><div class="uploadUrl'+i+'" style="display:none;"><div class="form-group"><input class="form-control" type="url" name="news[filePathURl]['+i+']" value="" placeholder="Enter the valid youtube URL" id="filePathUrl'+i+'" /></div></div></td><td><a id="'+i+'" href="javascript:void(0);" class="btn_removedir" >Remove</a></td></tr>');
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
			if (confirm('Are you sure you want delete this row?')){
				var button_id = $(this).attr("id");
				$("#row"+button_id).remove();				
				var j = 1;
				$('.newsUpload tbody tr').each(function () {
					$(this).removeAttr('id');
					$(this).attr('id', 'row' + j);					
					
					$('#row' + j + ' td a').removeAttr('id');
					$('#row' + j + ' td a').attr('id', j);	
					j++;
				});
			}			
			combined = 0;
			var i = $('.newsUploadfiles').length;
		});	
		
		$(document).on('click', '.btn_editremoveUp', function(){
			if (confirm('Are you sure you want delete this row?')){
				var button_id = $(this).attr("id");
				var uploadRowID = $(this).attr("rel");
				$.ajax({
					type:'POST',
					url:'<?php echo base_url(); ?>newsfeeds/removeUploadfiles',
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
		}, "News title must contain alphanumeric.");		
		
		jQuery.validator.addMethod("descriptionRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\_\$\!\@\s]+$/i.test(value);
		}, "Description must contain letters, numbers, space & special character like '&,_,$,@,!' only allowed.");
		
		$("#newsfeedForm").validate({
			rules: {
				'news[title]' : {
					required:true,
					minlength: 1,
					maxlength: 50,
					//nameRegex:true
				},
				'news[startDate]': {
					//required: true,
				},
				'news[endDate]': {
					//required: true,
				},
				'news[description]': {
					minlength: 1,
					maxlength: 280,
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
		
		$("#newsFormspacetext").on("keydown", function(e) {
			var len = $("#newsFormspacetext").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#newsFormspacetext").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#newsFormspacetextarea").on("keydown", function(e) {
			var len = $("#newsFormspacetextarea").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#newsFormspacetextarea").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
		
		$("#newsFormspacetext").emojioneArea({	autoHideFilters: true });
		$("#newsFormspacetextarea").emojioneArea({	autoHideFilters: true });
		
	});
</script>