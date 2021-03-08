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
								<th>City Name</th>
								<th>Suburb Name</th>
								<th>Action<th>
							</tr>
						</thead>
						<tbody>
						<?PHP 
						if(count($city) > 0){ $i = 1;
						foreach($city as $row){
							$districtName = getDistrictname($row->districtId);
						?>
							<tr>
								<td><?PHP echo $districtName[0]->districtName; ?></td>
								<td><?PHP echo $row->cityName; ?></td>
								<td><a href="javascript:void(0);" onclick="editcity(<?php echo $row->cityId ?>);" data-toggle="tooltip"   title="Edit city" ><i class="fa fa-edit"id="fa_edit"></i></a></td> 
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
		</div>
	
	
		<div class="br-pagebody col-lg-4">
			<div class="br-section-wrapper">
			  <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add New Suburb</h6>
			  
				<div class="form-layout form-layout-1">				
					<form id="cityForm" method="post" name="cityForm" action="<?php echo site_url('settings/cityadd');?>" data-parsley-validate >	
						<input type="hidden" class="form-control" id="cityId" name="cityId" />
						<div class="row mg-b-25 ">
							<div class="col-lg-12">
								<div class="form-group mg-b-10-force">
									<label class="form-control-label">Province: <span class="tx-danger">*</span></label>
									<select class="form-control select2" data-placeholder="Choose Province" id="provinceName" required name="provinceName" >
										<option value="">Choose Province</option>
										<?PHP 
											foreach($province as $pro){ ?>
											<option value="<?PHP echo $pro->id; ?>"><?PHP echo $pro->name; ?></option>	
										<?PHP } ?>
									</select>
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group mg-b-10-force">
									<label class="form-control-label">City: <span class="tx-danger">*</span></label>
									<select class="form-control select2" data-placeholder="City district" required name="districtId" id="districtId">
										<option value="">Choose City</option>
									</select>
								</div>
							</div><!-- col-4 -->
							<div class="col-lg-12">
								<div class="form-group">
								<label class="form-control-label">Suburb Name: <span class="tx-danger">*</span></label>
								<input class="form-control" type="text" name="cityName" value="" placeholder="" required id="cityName" />
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
        $('select[name="provinceName"]').on('change', function() {
            var provinceID = $(this).val();
            if(provinceID) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/getDistrictval/'+provinceID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('select[name="districtId"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="districtId"]').append('<option value="'+ value.districtId +'">'+ value.districtName +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="districtId"]').empty();
            }
        });
		
		$('#cityName').on('change', function() {
            var districtId = $('#districtId').val();
            var cityVal = $(this).val();
            if(cityVal) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/cityCheck/',
                    type: "POST",
                    dataType: "json",
					data:{districtId:districtId, cityName:cityVal},
                    success:function(data) {
						console.log(data);
                        if(data == 1){
							alert('City Name Already Exist.');
							$('.distSubmit').attr("disabled", true);
						} else if(data == 0){
							$('.distSubmit').prop("disabled", false);
						}
                    }
                });
            }
        });
    });
	
	function editcity(res){
		var cityId = res;
		$.ajax({
			url : '<?php echo base_url(); ?>settings/editcity',
			data : {'cityId': cityId},
			type: 'POST',
			success : function(result){
				response = jQuery.parseJSON(result);
				console.log(response);
				$('#cityName').val(response[0]['cityName']);
				$('#cityId').val(response[0]['cityId']);
				$('#provinceName').val(response[0]['provinceId']);
				
				var provinceID = response[0]['provinceId'];
				if(provinceID) {
					$.ajax({
						url: '<?php echo base_url();?>settings/getDistrictval/'+provinceID,
						type: "GET",
						dataType: "json",
						success:function(data) {
							$('select[name="districtId"]').empty();
							$.each(data, function(key, value) {
								$('select[name="districtId"]').append('<option value="'+ value.districtId +'">'+ value.districtName +'</option>');
								$('#districtId').val(response[0]['districtId']);
							});
						}
					});
				}else{
					$('select[name="districtId"]').empty();
				}
			}
		});
	}
</script>