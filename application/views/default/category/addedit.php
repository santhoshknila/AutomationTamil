<div class="br-mainpanel">
	<div class="br-pageheader pd-y-15 pd-l-20">
		<nav class="breadcrumb pd-0 mg-0 tx-12">
			<a class="breadcrumb-item" href="<?php echo base_url();?>admin/dashboard">Home</a>
			<a class="breadcrumb-item" href="<?php echo base_url();?>category">Category</a>
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
					<form name="categoryForm" id="categoryForm" action="<?php echo site_url("category/save"); ?>" method="POST" enctype="multipart/form-data" >
						<?php if(!empty($editcat)){$cate = $editcat[0];}
							if(!empty($cate) && $cate->categoryId != ''){ ?>
								<input type="hidden" name="category[categoryId]" id="categoryId" value="<?php echo $cate->categoryId; ?>"/>
						 <?PHP } ?>
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"><span class="tx-danger">* </span> Name:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="categoryName" name="category[name]" placeholder="Enter Category Name" value="<?php echo (!empty($cate) && $cate->name!='')?$cate->name:'';?>" required />
							</div>
						</div><!-- row -->	
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label">Parent:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<select class="form-control" data-placeholder="" name="category[parentId]" id="categoryIdval" >
									<option value=""> Choose Parent Category</option>
									<?PHP if(!empty($category)){
										foreach($category as $cat){ ?>
										<option value="<?PHP echo $cat->categoryId; ?>" <?PHP if(!empty($cate) && $cate->parentId == $cat->categoryId){ ?> SELECTED <?PHP } ?> ><?PHP echo $cat->name; ?></option>
									<?PHP } } ?>
								</select>
							</div>
						</div><!-- row -->
						<div class="row mg-t-10">
							<label class="col-sm-4 form-control-label">Image:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input id="filePathImages" type="file" name="filePathimages" value="<?php echo (!empty($cate) && $cate->image!='')?$cate->image:'';?>" placeholder="" />
							<br />
							<?php if((!empty($cate) && $cate->image!='')){ ?>
								<img style="margin-top:30px;" src="<?php echo site_url("uploads/category/".$cate->image); ?>" height="100px" width="100px" />
							<?php } ?>
							</div>
						</div><!-- row -->
						<?PHP /* <div class="row mg-t-10">
							<label class="col-sm-4 form-control-label"> Sequence Order:</label>
							<div class="col-sm-8 mg-t-10 mg-sm-t-0">
								<input type="text" class="form-control" id="sequenceID" name="category[sequenceID]" placeholder="Enter sequence order" value="<?php echo (!empty($cate) && $cate->sequenceID!='')?$cate->sequenceID:'';?>" />
							</div>
						</div><!-- row -->	*/ ?>
						<div class="row mg-t-30">
							<div class="col-sm-8 mg-l-auto">
								<div class="form-layout-footer">
									<button class="btn btn-info categorySubmit" id="categorySubmit" type="button">Submit</button>
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
			return this.optional(element) || /^[a-zA-Z0-9]{1}[a-zA-Z0-9\&\s]+$/i.test(value);
		}, "Category Name must contain letters, space & special character like '&' only allowed.");
		
		jQuery.validator.addMethod("numberRegex", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]{1}[0-9\s]+$/i.test(value);
		}, "Sequence allowed only numbers. Number limit 0 to 999.");
		
		$('.categorySubmit').on('click',function(){	
			$("#categoryForm").validate({
				rules: {
					'category[name]':{
						required:true,
						minlength: 1,
						maxlength: 75,
						//nameRegex:true
					},
					'category[sequenceID]':{
						minlength: 0,
						maxlength: 3,
						numberRegex: true,
						number: true,
					},
				},
				highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},
			});		
			
			var categoryVal = $('#categoryName').val();
			<?php 
			if(!empty($cate) && $cate->categoryId != ''){ ?>
				var categoryId = $('#categoryId').val();
			<?PHP } else { ?>
				var categoryId = '';
			<?PHP } ?>
			var categoryIdval = $('#categoryIdval').val();			
			var isValidtrim = $("#categoryName").val($.trim($("#categoryName").val()));
			if(isValidtrim){
				$.ajax({
					url: '<?php echo base_url();?>category/categoryCheck',
					type: "POST",
					dataType: "json",
					data:{ name:categoryVal, categoryId:categoryId, categoryIdval:categoryIdval },
					success:function(data) {
						console.log(data);
						if(data == 1){
							alert('Category Name Already Exist.');
							return false;
						} else if(data == 0){
							$("form[name='categoryForm']").submit();
						}
					}
				});
			}
		});
		
		$("#categoryName").on("keydown", function(e) {
			var len = $("#categoryName").val().length;
			if (len == 0) {
				return e.which !== 32;
			} else {
				var pos = $("#categoryName").val();
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