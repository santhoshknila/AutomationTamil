<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Twitter -->
		<meta name="twitter:site" content="@themepixels">
		<meta name="twitter:creator" content="@themepixels">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="Bracket">
		<meta name="twitter:description" content="Premium Quality and Responsive UI for Dashboard.">
		<meta name="twitter:image" content="http://themepixels.me/bracket/img/bracket-social.png">

		<!-- Facebook -->
		<meta property="og:url" content="http://themepixels.me/bracket">
		<meta property="og:title" content="Bracket">
		<meta property="og:description" content="Premium Quality and Responsive UI for Dashboard.">

		<meta property="og:image" content="http://themepixels.me/bracket/img/bracket-social.png">
		<meta property="og:image:secure_url" content="http://themepixels.me/bracket/img/bracket-social.png">
		<meta property="og:image:type" content="image/png">
		<meta property="og:image:width" content="1200">
		<meta property="og:image:height" content="600">

		<!-- Meta -->
		<meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
		<meta name="author" content="ThemePixels">

		<title><?PHP echo $title; ?></title>
		
		<?PHP echo $_scripts; ?> 
		<?PHP echo $_styles; ?>
	</head>
	<body>
		<div class="d-flex align-items-center justify-content-center ht-100v">
			<div class="wd-500 wd-xs-550 pd-25 pd-xs-30 bg-black rounded shadow-base">
				<div class="signin-logo tx-center tx-28 mg-b-30 tx-bold tx-inverse">
					
					<span >Tamil Ethos</span>
				</div>				
				<?PHP
					$cartTotal = $payVal['package']+$payVal['ads'];
					if($payVal['adstype'] == 'adsOnly'){ 
						$returnUrl = $settings[0]->returnadsUrl; 
						$cancelUrl = $settings[0]->canceladsUrl; 
						$notifyURl = $settings[0]->notifyadsUrl; 
					} else { 
						$returnUrl = $settings[0]->returnUrl; 
						$cancelUrl = $settings[0]->cancelUrl; 
						$notifyURl = $settings[0]->notifyUrl; 
					}
					
					$post = array(
						// Merchant details
						'merchant_id' => $settings[0]->merchantId,
						'merchant_key' => $settings[0]->merchantKey,
						'return_url' => $returnUrl,
						'cancel_url' => $cancelUrl,
						'notify_url' => $notifyURl,
						// Buyer details
						'name_first' => 'First Name',
						'name_last'  => 'Last Name',
						'email_address'=> 'knilaitsolution@gmail.com',
						// Transaction details
						'm_payment_id' => $payVal['proID'], //Unique payment ID to pass through to notify_url
						// Amount needs to be in ZAR
						// If multicurrency system its conversion has to be done before building this array
						'amount' => number_format( sprintf( "%.2f", $cartTotal ), 2, '.', '' ),
						'item_name' => 'Item Name',
						'item_description' => 'Packages & Ads',
						'custom_int1' => '', //custom integer to be passed through           
						'custom_str1' => '',
						'passphrase' => $settings[0]->passphrase,
					);
					
					$pfData = $post;
					$pfParamString = '';
					foreach ( $pfData as $key => $val ){
						if ( $val !='' && $key != 'submit' && $key != 'passphrase' ){
							$pfParamString .= $key .'='. urlencode( stripslashes( trim( $val ) ) ) . '&';
						}
					}					  
					// Remove the last '&' from the Parameter string
					$pfParamString = substr( $pfParamString, 0, -1 );

					// Add the passphrase
					if ( $pfData['passphrase'] ){
						$preSigString = $pfParamString . '&passphrase=' . urlencode( $pfData['passphrase'] );
					} else {
						$preSigString = $pfParamString;
					}
					$signature = md5( $preSigString );
					if($settings[0]->paymentType == 2){
						$testingMode = true;
					} else if($settings[0]->paymentType == 1){
						$testingMode = false;
					}
					$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
					
				?>
				 <form action="https://<?PHP echo $pfHost; ?>/eng/process" method="POST" name="submitpayment" >
					<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
					<div class="bd bd-gray-300 rounded table-responsive">	
					<?php
						foreach ( $pfData as $key => $val ){
							if ( !empty( $val ) && $key != 'submit' && $key != 'passphrase' ){
					?>
								<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>"/>
					<?php
							}
						}
					?>
					<input type="hidden" name="signature" value="<?php echo $signature?>" />
						<table class="table table-striped mg-b-0 table-hover">
							<thead>
								<tr>
									<th>Particulars</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Packages</td>
									<td align="center"><?PHP echo $payVal['package']; ?></td>
								</tr>
								<tr>
									<td>Ads</td>
									<td align="center"><?PHP echo $payVal['ads']; ?></td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td>Total Amount</td>
									<td align="center"><?PHP echo $cartTotal; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<button type="submit" class="btn btn-info btn-block">Confirm to Pay</button>
				</form>
			</div><!-- login-wrapper -->
		</div><!-- d-flex -->
	</body>
</html>