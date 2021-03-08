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
	<div class="row col-lg-12">	
		<div class="br-pagebody col-lg-8">	
			<div class="br-section-wrapper">
				<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
				<div class="bd bd-gray-300 rounded table-responsive ">
					<table class="table table-striped mg-b-0 table-hover">
						<thead>
							<tr>
								<th style="width:50%;">Name</th>
								<th style="width:25%;">Color</th>
								<th style="width:25%;">Action</th>
							</tr>
						</thead>
						<tbody>
						<?PHP 
						if(count($mothertongue) > 0){ $i = 1;
						foreach($mothertongue as $row){
						?>
							<tr>
								<td><?PHP echo $row->mothertongueName; ?></td>
								<td ><input class="form-control" style="background:#<?PHP echo $row->mothertongueColor;?>"readonly value="<?PHP echo $row->mothertongueColor;?>" /></td>
								<td><a href="javascript:void(0);" onclick="editmothert(<?php echo $row->mothertongueId ?>);" data-toggle="tooltip"   title="Edit Branch" ><i class="fa fa-edit"id="fa_edit"></i></a></td> 
							</tr>
						<?php $i++; } } else {?>
							<tr align="center">
								<td colspan="10">No Records Found</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div><!-- bd -->
				
				<div id="pagination"><?php //echo $this->pagination->create_links(); ?></div>
				
			</div><!-- br-section-wrapper -->
		</div><!-- br-pagebody -->
		
		<div class="br-pagebody col-lg-4">
			<div class="br-section-wrapper">
			  <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add/Edit Mothertongue</h6>
				<div class="form-layout form-layout-1">				
					<form id="mothertongueForm" method="post" name="mothertongueForm" action="<?php echo site_url('settings/mothertongueAdd');?>">
						<input type="hidden" class="form-control" id="mothertongueId" name="mothertongueId" />
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">Mothertongue Name: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="mothertongueName" value="" placeholder="" id="mothertongueName" />
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">Pick the Color: <span class="tx-danger">*</span></label>
								<input class="form-control jscolor" type="text" name="mothertongueColor" value="" placeholder="" id="mothertongueColor" readonly />
								</div>
							</div><!-- col-4 -->
						</div><!-- row -->

						<div class="form-layout-footer">
							<button type="button" class="btn btn-info distSubmit">Submit</button>
						</div><!-- form-layout-footer -->
					</form>
				</div><!-- form-layout -->
			</div>
		</div>
	</div>
<script type="text/javascript">
    $(document).ready(function() {
		jQuery.validator.addMethod("nameRegex", function(value, element) {
			return this.optional(element) || /^[a-z0-9\@\$\&\ \s]+$/i.test(value);
		}, "Mother tongue must contain letters, space & special character like '@,$,&' only allowed.");
		$('.distSubmit').on('click',function(){
			$("#mothertongueForm").validate({
				debug:false,
				rules: {
					'mothertongueName' : {
						required:true,
						minlength: 0,
						maxlength: 100,
						nameRegex:true
					},	
					'mothertongueColor' : {
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
			
			$("#mothertongueName").on("keydown", function(e) {
				var len = $("#mothertongueName").val().length;
				if (len == 0) {
					return e.which !== 32;
				} else {
					var pos = $("#mothertongueName").val();
					if (pos == 0) {
						return e.which !== 32;
					}
				}
			});	
			
			var isValid = $("#mothertongueForm").valid();
			var mothertongueId = $('#mothertongueId').val();
            var mothertongueVal = $('#mothertongueName').val();
			if(isValid){
				if(mothertongueVal) {
					$.ajax({
						url: '<?php echo base_url();?>settings/mothertongueCheck/',
						type: "POST",
						dataType: "json",
						data:{mothertongueName:mothertongueVal, mothertongueId:mothertongueId},
						success:function(data) {
							console.log(data);
							if(data == 1){
								alert('Mothertongue Name Already Exist.');
								return false;
							} else if(data == 0){
								$("form[name='mothertongueForm']").submit();
							}
						}
					});
				} 
			}
        });
    });
	function editmothert(res){
		var mothertongueId = res;
		$.ajax({
			url : '<?php echo base_url(); ?>settings/editmothert',
			data : {'mothertongueId': mothertongueId},
			type: 'POST',
			success : function(result)
			{
				response = jQuery.parseJSON(result);
				console.log(result);
				$('#mothertongueName').val(response[0]['mothertongueName']);
				$('#mothertongueColor').val(response[0]['mothertongueColor']);
				$('#mothertongueId').val(response[0]['mothertongueId']);
			}
		});
	}
</script>