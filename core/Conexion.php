<?php
date_default_timezone_set('America/Managua');
include_once('../common/include.php');
class Connection
{
    public static function ConnectionOracle()
    {
       
        try
        {
            $MYDB='(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = 10.202.128.147)(PORT = 1521))(CONNECT_DATA =(SID = hydra)))';
            $conn = oci_connect("AIS_ISP_OFFICE", "uEI3Ukys99s2vDLbbVKa", $MYDB, 'AL32UTF8');
            return $conn;
        }
        catch (exception $e)
        {
            $e = oci_error();
            //return $e;
            sendResponse(200,oci_error(),0);
            exit();
        }
    }

    /*
     * Coneccion al Billing (Oracle DB)
     */
    public static function ConnectionOraclePayment()
    {
        try
        {
            $MYDB='(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST = localhost) (PORT = 1521) ) (CONNECT_DATA =(SID = hydra2)))';
            $conn = oci_connect("AIS_PAYMENTS", "read_manual", $MYDB, 'AL32UTF8') or die();
            return $conn;
        }
        catch (exception $e)
        {
            $e = oci_error();
            //return trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            sendResponse(200,trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR),0);
            exit();
        }
    }

    public static function ConnectionPayment()
    {
      try {
        $url    = "https://api.yota.com.ni/paycom/PaycomService.wsdl";
        $client = new SoapClient($url,array("trace" => 1, "exception" => 0, 'encoding'=>'UTF-8'));
        return $client;
      }
      // Do NOT try and catch "Exception" here
      catch (SoapFault $e ) {
        //return $e->getMessage();
        sendResponse(200,$e->getMessage(),0);
        exit();
      }
    }
    
    /*
    * Ruta de la plataforma
    */
    public static function PathSecurity()
    {
        $path='http://app.yota.com.ni/apis/';
        return $path;
    }

} ?>
