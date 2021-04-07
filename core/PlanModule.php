<?php
    require_once '../core/Conexion.php';
    include_once('../common/include.php');

    class PlanModule
    {
        
        public function &get_mac_search($ipAddress){
            if (!empty($ipAddress)) {
                
                $ssh_key = '/usr/local/ssh/app.key';
                $ssh_username = 'app';
                $host1 = '10.202.7.19';
                $host2 = '10.202.7.20';
                $command = "ssh -o 'strictHostKeyChecking no' -o 'UserKnownHostsFile=/dev/null' -i $ssh_key $ssh_username@$host1 'dhcp-lease-list --lease /var/lib/dhcp/dhcpd.leases | grep -w \'$ipAddress'' | awk '{print $1}'";
                $result = exec($command);

                if(empty($result)) {
                    $command2 = "ssh -o 'strictHostKeyChecking no' -o 'UserKnownHostsFile=/dev/null' -i $ssh_key $ssh_username@$host2 'dhcp-lease-list --lease /var/lib/dhcp/dhcpd.leases | grep -w \'$ipAddress'' | awk '{print $1}'";
                    $result   = exec($command2);   
                }

                if (!empty($result)) {
                    $mac = $this->mac_to_imsi($result);
                    $this->get_plans_list($mac);
                    exit();
                }
                else{
                    $ErrorMessage = 'MAC no detectado, verifique!!!';
                    $result = 0;

                    //$this->get_plans_list('710700000000017');
                    //exit();
                }
            }
            else{
                $ErrorMessage = 'IP invalida, verifique!!!';
                $result = 0;
            }

            sendResponse(200,$ErrorMessage,$result);
        
        }

        public function &post_plans_add($ipAddress,$Idplan){

            if (!empty($ipAddress) && !empty($Idplan)) {
                
                $ssh_key = '/usr/local/ssh/app.key';
                $ssh_username = 'app';
                $host1 = '10.202.7.19';
                $host2 = '10.202.7.20';
                $command = "ssh -o 'strictHostKeyChecking no' -o 'UserKnownHostsFile=/dev/null' -i $ssh_key $ssh_username@$host1 'dhcp-lease-list --lease /var/lib/dhcp/dhcpd.leases | grep -w \'$ipAddress'' | awk '{print $1}'";
                $result = exec($command);

                if(empty($result)) {
                    $command2 = "ssh -o 'strictHostKeyChecking no' -o 'UserKnownHostsFile=/dev/null' -i $ssh_key $ssh_username@$host2 'dhcp-lease-list --lease /var/lib/dhcp/dhcpd.leases | grep -w \'$ipAddress'' | awk '{print $1}'";
                    $result   = exec($command2);   
                }

                if (!empty($result)) {
                    $mac = $this->mac_to_imsi($result);
                    $this->post_plans_store($mac,$Idplan);
                    exit();
                }
                else{
                    $ErrorMessage = 'MAC no detectado, verifique!!!';
                    $result = 0;

                    //$this->post_plans_store('710700000000017',$Idplan);710700000000004
                    //exit();
                }
            }
            else{
                $ErrorMessage = 'IP invalida, verifique!!!';
                $result = 0;
            }

            sendResponse(200,$ErrorMessage,$result);
        
        }

        function &post_plans_store($Mac,$Idplan)
        {
            $data   = array();
            $sql = "BEGIN YOTA_CONTRACTS_PKG.SUBSCRIPTIONS_LTE_PUT(:P_MAC, :P_NUM_N_SERVICE_ID,:P_BALANCE, :P_SUCCESS, :P_ERROR_MESSAGE); END;";
            $conn = Connection::ConnectionOracle();
            
            if (!$conn) {
                $e = oci_error();
                sendResponse(500,'Server Connection Error: '.htmlentities($e['message'], ENT_QUOTES),false);
                exit();
            }
            else{

                $stid = oci_parse($conn, $sql);
                
                oci_bind_by_name($stid, ':P_MAC', $Mac);
                oci_bind_by_name($stid, ':P_NUM_N_SERVICE_ID', $Idplan);
                oci_bind_by_name($stid, ':P_BALANCE', $P_BALANCE, 500);
                oci_bind_by_name($stid, ':P_SUCCESS', $P_SUCCESS , 2000);
                oci_bind_by_name($stid, ':P_ERROR_MESSAGE', $P_ERROR_MESSAGE, 500);

                oci_execute($stid, OCI_DEFAULT);
                oci_free_statement($stid);
                oci_close($conn);
                
                $data   = array('P_BALANCE' => $P_BALANCE,'P_SUCCESS' => $P_SUCCESS,'P_ERROR_MESSAGE' => $P_ERROR_MESSAGE);
                sendResponse(200,'process successfully',$data);
            }
        }

        function &get_plans_list($Mac)
        {
            $plans  = array();
            $client = array();
            $data   = array();
            $sql = "BEGIN YOTA_CONTRACTS_PKG_S.GET_PLAN_LTE(:P_MAC, :P_NAME, :P_ACCOUNT, :P_BALANCE,:P_SERVICE,:P_D_BEGIN,:P_D_END,:P_EXISTS_SUBS, :P_CURSOR); END;";
            $conn = Connection::ConnectionOracle();
            
            if (!$conn) {
                $e = oci_error();
                sendResponse(500,'Server Connection Error: '.htmlentities($e['message'], ENT_QUOTES),false);
                exit();
            }
            else{

                $stid = oci_parse($conn, $sql);
                $curs = oci_new_cursor($conn);
                
                oci_bind_by_name($stid, ':P_MAC', $Mac);
                oci_bind_by_name($stid, ':P_NAME', $P_NAME , 2000);
                oci_bind_by_name($stid, ':P_ACCOUNT', $P_ACCOUNT, 500);
                oci_bind_by_name($stid, ':P_BALANCE', $P_BALANCE, 500);
                oci_bind_by_name($stid, ':P_SERVICE', $P_SERVICE, 500);
                oci_bind_by_name($stid, ':P_D_BEGIN', $P_D_BEGIN, 500);
                oci_bind_by_name($stid, ':P_D_END', $P_D_END, 500);
                oci_bind_by_name($stid, ':P_EXISTS_SUBS', $P_EXISTS_SUBS, 20);
                oci_bind_by_name($stid, ':P_CURSOR', $curs, -1, OCI_B_CURSOR);

                oci_execute($stid);
                oci_execute($curs); 

                while ($row = oci_fetch_assoc($curs)){
                    $plans[] = $row;
                }

                $client = array('P_NAME' =>  $P_NAME,'P_ACCOUNT' =>  $P_ACCOUNT, 'P_BALANCE' => $P_BALANCE,'P_SERVICE' => $P_SERVICE,'P_D_BEGIN' => $P_D_BEGIN,'P_D_END' =>  $P_D_END,'P_EXISTS_SUBS' =>  $P_EXISTS_SUBS);
                $data   = array('client' => $client , 'plans' => $plans);
                
                oci_free_statement($stid);
                oci_free_statement($curs);

                oci_close($conn);
                sendResponse(200,'plans successfully listed',$data);
            }
        }

        function mac_to_imsi ($device_mac)
        {
            $mcc = "710";
            $mac = substr( "$device_mac", -14 );
            $mac = str_replace ( ":" , "" , "$mac" );
            $result = hexdec("$mac");
            $imsi =  "$mcc" . "$result";
            return $imsi;
        }

    }