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
			<div class="bd bd-gray-300 rounded table-responsive">
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
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
					if(count($religion) > 0){ $i = 1;
					foreach($religion as $row){
					?>
						<tr>
							<td><?PHP echo $row->religionName; ?></td>
							<td ><input class="form-control" style="background:#<?PHP echo $row->religionColor;?>"readonly value="<?PHP echo $row->religionColor;?>" /></td>
							<td><a href="javascript:void(0);" onclick="editreligion(<?php echo $row->religionId ?>);" data-toggle="tooltip"   title="Edit Branch" ><i class="fa fa-edit"id="fa_edit"></i></a></td> 
						</tr>
					<?php $i++; } } else { ?>
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
          <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add/Edit Religion</h6>
			<div class="form-layout form-layout-1">				
				<form id="religionForm" method="post" name="religionForm" action="<?php echo site_url('settings/religionAdd');?>" >	
					<input type="hidden" class="form-control" id="religionId" name="religionId" />
					<div class="row ">
						<div class="col-lg-12">
							<div class="form-group">
							<label class="form-control-label">Religion Name: <span class="tx-danger">*</span></label>
							<input class="form-control" type="text" name="religionName" value="" placeholder="" id="religionName" />
							</div>
						</div><!-- col-4 -->
						<div class="col-lg-12">
							<div class="form-group">
							<label class="form-control-label">Pick the Color: <span class="tx-danger">*</span></label>
							<input class="form-control jscolor" type="text" name="religionColor" value="" placeholder="" id="religionColor" readonly />
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
		}, "Religion must contain letters, space & special character like '@,$,&' only allowed.");
		
		$('.distSubmit').on('click',function(){
			$("#religionForm").validate({
				debug:false,
				rules: {
					'religionName' : {
						required:true,
						minlength: 0,
						maxlength: 100,
						nameRegex:true
					},
					'religionColor' : {
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
			
			$("#religionName").on("keydown", function(e) {
				var len = $("#religionName").val().length;
				if (len == 0) {
					return e.which !== 32;
				} else {
					var pos = $("#religionName").val();
					if (pos == 0) {
						return e.which !== 32;
					}
				}
			});	
			
			var isValid = $("#religionForm").valid();
            var religionVal = $("#religionName").val();
            var religionId = $("#religionId").val();
			if(isValid){
				if(religionVal) {
					$.ajax({
						url: '<?php echo base_url();?>settings/religionCheck/',
						type: "POST",
						dataType: "json",
						data:{religionName:religionVal, religionId:religionId},
						success:function(data) {
							if(data == 1){
								alert('Religion Name Already Exist.');
								return false;
							} else if(data == 0){
								$("form[name='religionForm']").submit();
							}
						}
					});
				}
			}
        });
    });
	function editreligion(res){
		var religionId = res;
		$.ajax({
			url : '<?php echo base_url(); ?>settings/editreli',
			data : {'religionId': religionId},
			type: 'POST',
			success : function(result)
			{
				response = jQuery.parseJSON(result);
				console.log(response);
				$('#religionName').val(response[0]['religionName']);
				$('#religionColor').val(response[0]['religionColor']);
				$('#religionId').val(response[0]['religionId']);
			}
		});
	}
</script>