jQuery(function ($) {
	
	setTimeout(function() {
        $(".success_msg").fadeOut(5000);
		$(".error_msg").fadeOut(5000);
        $(".fail_msg").fadeOut(5000);
        //$(".investorupdatemsg").fadeOut(5000);
    }, 5000);
        
	$.validator.addMethod('regexphone', function(value, element, param) {
		return this.optional(element) ||
		value.match(typeof param == 'string' ? new RegExp(param) : param);
		},
		'Please enter the valid phone number.'
	);	
    
    /*Check Admin Login Form */
    $('#adminloginForm').validate({
        rules: {
            'username':{
                    required: true
            },
            'password':{
                required: true
            },
        },

        highlight: function (input) {
            console.log(input);
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.input-group').append(error);
        }
    });
	
    /*check Admin Forgot Password */
    $('#adminforgotForm').validate({
        rules: {
            'emailid':{
                required: true,
                regexemail : /^([\w-\.]+)@[a-zA-Z0-9-]{1,2}((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
                email:true,
            }
        },

        highlight: function (input) {
            console.log(input);
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.input-group').append(error);
        }
    });
	
	/*Image and video uploads*/
	$('.uploadImage').hide();
	$('.uploadVideo').hide();
	//$('.uploadUrl').hide();
	$('#selfileType').change(function(){ 
		var selVal = $('#selfileType option:selected').val();
		if(selVal == 'image'){
			$('.uploadImage').show();
			$('.uploadVideo').hide();
			$('.uploadUrl').hide();
			
			$('#filePathImages').prop("required", true);
			$('#filePathVideo').prop("required", false);
			//$('#filePathUrl').prop("required", false);
		} else if(selVal == 'video'){
			$('.uploadImage').hide();
			$('.uploadVideo').show();
			$('.uploadUrl').hide();
			
			$('#filePathImages').prop("required", false);
			$('#filePathVideo').prop("required", true);
			//$('#filePathUrl').prop("required", false);
		} /*else if(selVal == 'url'){
			$('.uploadImage').hide();
			$('.uploadVideo').hide();
			$('.uploadUrl').show();
			
			$('#filePathImages').prop("required", false);
			$('#filePathVideo').prop("required", false);
			$('#filePathUrl').prop("required", true);
		}*/
	});
	
});