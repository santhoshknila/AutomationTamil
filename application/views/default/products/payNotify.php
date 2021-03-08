<?PHP 
// Notify PayFast that information has been received - this is required
header( 'HTTP/1.0 200 OK' );
flush();

// Posted variables from ITN -the return variables
$pfData = $_POST;

// Update db
switch( $pfData['payment_status'] ){
  case 'COMPLETE':
     // If complete, update your application                   
     break;
  case 'FAILED':                    
     // There was an error, update your application
     break;
  default:
     // If unknown status, do nothing (safest course of action)
     break;
}
?>