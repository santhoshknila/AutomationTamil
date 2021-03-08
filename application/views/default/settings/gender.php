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
						if(count($gender) > 0){ $i = 1;
						foreach($gender as $row){
						?>
							<tr>
								<td><?PHP echo $row->genderName; ?></td>
								<td ><input class="form-control" style="background:#<?PHP echo $row->genderColor;?>"readonly value="<?PHP echo $row->genderColor;?>" /></td>
								<td><a href="javascript:void(0);" onclick="editgender(<?php echo $row->genderId ?>);" data-toggle="tooltip" title="Edit Gender" ><i class="fa fa-edit"id="fa_edit"></i></a></td> 
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
			  <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add/Edit Gender</h6>
				<div class="form-layout form-layout-1">				
					<form id="genderForm" method="post" name="genderForm" action="<?php echo site_url('settings/genderAdd');?>">
						<input type="hidden" class="form-control" id="genderId" name="genderId" />
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">Gender Name: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="genderName" value="" placeholder="" id="genderName" />
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">Pick the Color: <span class="tx-danger">*</span></label>
								<input class="form-control jscolor" type="text" name="genderColor" value="" placeholder="" id="genderColor" readonly />
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
		}, "Gender must contain letters, space & special character like '@,$,&' only allowed.");
		$('.distSubmit').on('click',function(){
			$("#genderForm").validate({
				debug:false,
				rules: {
					'genderName' : {
						required:true,
						minlength: 0,
						maxlength: 100,
						nameRegex:true
					},	
					'genderColor' : {
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
			
			$("#genderName").on("keydown", function(e) {
				var len = $("#genderName").val().length;
				if (len == 0) {
					return e.which !== 32;
				} else {
					var pos = $("#genderName").val();
					if (pos == 0) {
						return e.which !== 32;
					}
				}
			});	
			
			var isValid = $("#genderForm").valid();
			var genderId = $('#genderId').val();
            var genderVal = $('#genderName').val();
			if(isValid){
				if(genderVal) {
					$.ajax({
						url: '<?php echo base_url();?>settings/genderCheck/',
						type: "POST",
						dataType: "json",
						data:{genderName:genderVal, genderId:genderId},
						success:function(data) {
							console.log(data);
							if(data == 1){
								alert('Gender Name Already Exist.');
								return false;
							} else if(data == 0){
								$("form[name='genderForm']").submit();
							}
						}
					});
				} 
			}
        });
    });
	function editgender(res){		
		var genderId = res;
		$.ajax({
			url : '<?php echo base_url(); ?>settings/editgender',
			data : {'genderId': genderId},
			type: 'POST',
			success : function(result)
			{
				response = jQuery.parseJSON(result);
				console.log(result);
				$('#genderName').val(response[0]['genderName']);
				$('#genderColor').val(response[0]['genderColor']);
				$('#genderId').val(response[0]['genderId']);
			}
		});
	}
</script>