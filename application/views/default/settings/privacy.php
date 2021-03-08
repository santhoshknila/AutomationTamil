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
    <div class="br-pagebody col-lg-12">	
        <div class="br-section-wrapper">
		
			<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
			<div class="bd bd-gray-300 rounded table-responsive">
				<table class="table table-striped mg-b-0 table-hover">
					<thead>
						<tr>
							
							<th>Privacy Name</th>
						</tr>
					</thead>
					<tbody>
					<?PHP 
					if(count($privacy) > 0){ $i = 1;
					foreach($privacy as $row){
					?>
						<tr>
							
							<td><?PHP echo $row->privacyName; ?></td>
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
	
	<?PHP /*<div class="br-pagebody col-lg-4">
        <div class="br-section-wrapper">
          <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">Add Privacy</h6>
			<div class="form-layout form-layout-1">				
				<form id="privacyForm" method="post" name="privacyForm" action="<?php echo site_url('settings/privacyAdd');?>" data-parsley-validate >	
					<div class="row ">
						<div class="col-lg-12">
							<div class="form-group">
							<label class="form-control-label">Privacy Name: <span class="tx-danger">*</span></label>
							<input class="form-control" type="text" name="privacyName" value="" placeholder="" required id="privacyName" />
							</div>
						</div><!-- col-4 -->
					</div><!-- row -->

					<div class="form-layout-footer">
						<button type="submit" class="btn btn-info distSubmit">Submit</button>
					</div><!-- form-layout-footer -->
				</form>
			</div><!-- form-layout -->
		</div>
	</div> */ ?>
	
	</div>
<script type="text/javascript">
    $(document).ready(function() {		
		$('#privacyName').on('change', function() {
            var privacyVal = $(this).val();
            if(privacyVal) {
                $.ajax({
                    url: '<?php echo base_url();?>settings/privacyCheck/',
                    type: "POST",
                    dataType: "json",
					data:{privacyName:privacyVal},
                    success:function(data) {
                        if(data == 1){
							alert('Privacy Name Already Exist.');
							$('.distSubmit').attr("disabled", true);
						} else if(data == 0){
							$('.distSubmit').prop("disabled", false);
						}
                    }
                });
            }
        });
    });
</script>