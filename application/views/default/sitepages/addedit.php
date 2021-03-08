<script src="<?php echo base_url("skin/default/js/ckeditor/ckeditor.js"); ?>"></script>
<script src="<?php echo base_url("skin/default/js/editors.js"); ?>"></script>
<?php if(!empty($editpage)){$page = $editpage[0];} ?>
<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>sitepages">Manage Sitepages</a>
			<span class="breadcrumb-item active"><?PHP echo (!empty($page) && $page->pagename!='')?$page->pagename:'Add Page';; ?></span>
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
					<form name="sitepageForm" id="sitepageForm" action="<?php echo site_url("sitepages/save"); ?>" method="POST" data-parsley-validate>
					
					<?php if(!empty($page) && $page->pageId != ''){ ?>
						<input type="hidden" name="page[pageId]" id="pageId" value="<?php echo $page->pageId; ?>"/>
					<?php }?>
					
						<div class="row mg-t-30">
							<label class="col-sm-2 form-control-label"><span class="tx-danger">*</span> Title:</label>
							<div class="col-sm-10 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="pagenametxt" name="page[pagename]" placeholder="" value="<?php echo (!empty($page) && $page->pagename!='')?$page->pagename:'';?>" />
							</div>
						</div>
						<div class="row mg-t-30">
							<label class="col-sm-2 form-control-label"><span class="tx-danger">*</span> Description:</label>
							<div class="col-sm-10 mg-t-20 mg-sm-t-0">
								<textarea class="form-control "   name="page[description]" placeholder=""  cols="30" rows="5" id="ckeditor" ><?php echo (!empty($page) && $page->description!='')?$page->description:'';?> </textarea>
							</div>
						</div>
						
						<div class="row mg-t-30">
							<div class="col-sm-10 mg-l-auto">
								<div class="form-layout-footer">
								
									<input type="button" name="cancel" value="Cancel" onclick="window.location.href='<?php echo site_url("sitepages"); ?>'" class="btn btn-primary waves-effect pull-left" />
									
									<input type="submit" name="submit" value="<?php if(!empty($page) && $page->pageId !=''){ echo "Update Page"; }else{ echo "Add Page"; } ?>" class="btn btn-primary waves-effect pull-right" />
								
									
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
	$(document).ready(function(){
		jQuery.validator.addMethod("nameRegex", function(value, element) {
			return this.optional(element) || /^[a-z\ \s]+$/i.test(value);
		}, "Name must contain only letters & space");
		$("#sitepageForm").validate({
			ignore: [],
			debug: false,
			rules: {
				'page[pagename]':{
					required: true,
					minlength: 0,
					maxlength: 50,
					nameRegex:true 
				},
				'page[description]':{
					required: true,
				}
			},
			highlight: function(element){
				$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			},
			success: function(element){
				$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			},
			submitHandler: function(form){
				form.submit();
			}
		});
		
		$("#pagenametxt").on("keydown", function(e) {
			var len = $("#pagenametxt").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#pagenametxt").val();
				if (pos == 0) {
					return e.which !== 32;
				}
			}
		});	
	});	
</script>