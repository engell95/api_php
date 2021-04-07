<?php
    require_once '../core/Conexion.php';
    include_once('../common/include.php');

    class PaymentModule
    {
    	public function &get_account_search($Search){

    		$result = '';
    		$Message = '';

    		if (!empty($Search)) {
				
				$Connection = '';
			    $Query = [];
			    $Parameters = [];

				$Connection  = Connection::ConnectionPayment();
		      	$Parameters  = array('data' => $Search);
		      	$Query = $Connection->__soapCall('Consult', $Parameters);
			    if ($Query->Reg_Is_Found > 0) {
			        if ($Query->Reg_Is_Found == 1 & $Query->CustomerBase > 0 & $Query->AccountNumber > 0) {
			          $Message = 'Información listada satisfactoriamente.';
			          $result = $Query;
			        }
			        elseif ($Query->CustomerBase > 0 & $Query->AccountNumber == '' ){
			          $Message = 'Información listada satisfactoriamente.';
			          $result = $Query;
			        }
			        else if(!empty($Query->return_error)){
			        	$Message = $Query->return_error;
			        }
			        else{
			            $Message = 'No se pudo encontrar la cuenta.';
			        }
			    }
			    else{
			        $Message = 'No se pudo encontrar la cuenta.';
			    }
    		}
    		else{
				$Message = 'Parámetro de búsqueda vacío, verifique!!!';
    		}
    		sendResponse(200,$Message,$result);
    	}

    	public function &post_payment_save($Parameters){
    		$result = '';
    		$Message = '';
    		if (!empty($Parameters)) {
    			$Connection = '';
			    $Query = [];
			    $Data = [];
			    $Connection  = Connection::ConnectionPayment();
			    $Data  = array('data' => array ('CustomerType'=>$Parameters['CustomerType'],'Account'=>$Parameters['Account'],'Currency'=>$Parameters['Currency'],'Amount'=>$Parameters['Amount'],'CreditCardNumber'=>$Parameters['CreditCardNumber'],'ExpirationDate'=>$Parameters['ExpirationDate'],'SecurityCode'=>$Parameters['SecurityCode'],'RemoteAddr' => $Parameters['RemoteAddr']));
     	 		
     	 		$Query = $Connection->__soapCall('RequestPayCom', $Data);

     	 		$Message = 'Información listada satisfactoriamente.';
			    $result = $Query;
    		}
    		else{
    			$Message = 'Faltan datos para la transacción, verifique!!!';
    		}
    		sendResponse(200,$Message,$result);
    	}
    }