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
				<div class="bd bd-gray-300 rounded table-responsive">	
					<table class="table table-striped mg-b-0 table-hover">
						<thead>
							<tr>								
								<th>Province Name</th>
								<th>City Name</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?PHP 
						if(count($district) > 0){ $i = 1;
						foreach($district as $row){
							$provinceNme = getProvincename($row->provinceId);
						?>
							<tr>								
								<td><?PHP echo $provinceNme[0]->provinceName; ?></td>
								<td><?PHP echo $row->districtName; ?></td>
								<td><a href="javascript:void(0);" onclick="editdis(<?php echo $row->districtId ?>);" data-toggle="tooltip"   title="Edit Branch" ><i class="fa fa-edit"id="fa_edit"></i></a></td> 
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
			  <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add New City</h6>
			  
				<div class="form-layout form-layout-1">				
					<form id="districtForm" method="post" name="districtForm" action="<?php echo site_url('settings/districtadd');?>" data-parsley-validate >	
						<input type="hidden" class="form-control" id="districtId" name="districtId" />
						<div class="row mg-b-25 ">
							<div class="col-lg-12">
								<div class="form-group mg-b-10-force">
									<label class="form-control-label">Country: <span class="tx-danger">*</span></label>
									<select class="form-control select2" data-placeholder="Choose country" id="countryName" required name="countryName" >
										<option value="">Choose country</option>
										<?PHP 
											foreach($countries as $country){ ?>
											<option value="<?PHP echo $country->id; ?>"><?PHP echo $country->name; ?></option>	
										<?PHP } ?>
									</select>
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group mg-b-10-force">
									<label class="form-control-label">Province: <span class="tx-danger">*</span></label>
									<select class="form-control select2" data-placeholder="Choose Province" required name="provinceName" id="provinceName">
										<option value="">Choose Province</option>
									</select>
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">City Name: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="districtName" value="" placeholder="" required id="districtName" />
								</div>
							</div><!-- col-4 -->
						</div><!-- row -->

						<div class="form-layout-footer">
							<button type="submit" class="btn btn-info distSubmit">Submit</button>
						</div><!-- form-layout-footer -->
					</form>
				</div><!-- form-layout -->
			</div>
		</div>
	</div>
		  
    
<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="countryName"]').on('change', function() {
            var countryID = $(this).val();
            if(countryID) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/getProvinceval/'+countryID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('select[name="provinceName"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="provinceName"]').append('<option value="'+ value.provinceId +'">'+ value.provinceName +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="provinceName"]').empty();
            }
        });
		
		$('#districtName').on('change', function() {
            var provinceVal = $('#provinceName').val();
            var dtVal = $(this).val();
            if(dtVal) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/districtCheck/',
                    type: "POST",
                    dataType: "json",
					data:{province:provinceVal, distName:dtVal},
                    success:function(data) {
						console.log(data);
                        if(data == 1){
							alert('District Name Already Exist.');
							$('.distSubmit').attr("disabled", true);
						} else if(data == 0){
							$('.distSubmit').prop("disabled", false);
						}
                    }
                });
            }
        });
    });
	
function editdis(res){
	var districtId = res;
	$.ajax({
	 	url : '<?php echo base_url(); ?>settings/editdistrict',
	 	data : {'districtId': districtId},
	 	type: 'POST',
	 	success : function(result){
	 		response = jQuery.parseJSON(result);
	 		console.log(response);
	 		$('#districtName').val(response[0]['districtName']);
	 		$('#districtId').val(response[0]['districtId']);
	 		$('#countryName').val(response[0]['countryId']);
	 		
	 		var countryID = response[0]['countryId'];
            if(countryID) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/getProvinceval/'+countryID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('select[name="provinceName"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="provinceName"]').append('<option value="'+ value.provinceId +'">'+ value.provinceName +'</option>');
                            $('#provinceName').val(response[0]['provinceId']);
                        });
                    }
                });
            }else{
                $('select[name="provinceName"]').empty();
            }
	 	}
	});
}
</script>