<?php

header('Content-Type: text/html; charset=utf-8');

$host = "sapdev";  
$sysnr = "00"; 
$client = "200"; 

$user = "rfc_user"; 
$passwd = "pt@net"; 
$login = array ( "ASHOST"=>"$host",                  
                 "SYSNR"=>"$sysnr",                  
                 "CLIENT"=>"$client",                  
                 "USER"=>"$user",
                 "PASSWD"=>"$passwd");
                 
//$rfc = saprfc_open (array ("ASHOST"=>"10.45.16.56","SYSNR"=>"00","CLIENT"=>"200","USER"=>"rfc_user","PASSWD"=>"pt@net")); 
	
//Try to connect to SAP using our Login array  
 
$rfc = saprfc_open ($login );

if (!$rfc) { echo "The RFC connection failed with the following error:".saprfc_error(); exit; }

//Discover interface for function module YMT_TESTFI
saprfc_set_code_page($rfc, "4110");

$fce = saprfc_function_discover($rfc, "ZRFC014");

if (!$fce )  { echo "Discovering interface of function module failed"; exit;   }

// Call and execute the function

saprfc_import ($fce,"IM_ZSTAFF_ID", "0000310026");
saprfc_import ($fce,"IM_KEYWORD", "陳");
saprfc_table_init($fce,"TAB_OUT");

//$val=array();
//$val['NUM_POST']='000030';
//$val['DIST']='popo';
//$val['FIELD']='TEST1';
//saprfc_table_append ($fce,"IT_TAB", $val);

$rfc_rc = saprfc_call_and_receive ($fce);

if ($rfc_rc != SAPRFC_OK){
    if ($rfc == SAPRFC_EXCEPTION ){
        echo ("Exception raised: ".saprfc_exception($fce));
    } else {
        echo ("Call error: ".saprfc_error($fce));
    }
    echo "failure";
    exit;
}
$stack = array();
$data_row = saprfc_table_rows ($fce,"TAB_OUT");

echo 'Number of Rows : '.$data_row."<br>";

//    $log_msg='';
    if($data_row != 0 || $data_row != '')   
    {
        for ($i=1; $i<=$data_row; $i++)
        {
        $DATA = saprfc_table_read ($fce,"TAB_OUT",$i);
        echo $DATA["ZSTAFF_ID"],$DATA["C_NAME"],$DATA["C_NAME_CHT"], $DATA["TITLE"],$DATA["DEPARTMENT"]."<br>";
		array_push($stack, $DATA);
//        echo json_encode($DATA);
        }
    }
echo json_encode($stack);
//Debug info
//saprfc_function_debug_info($fce);
saprfc_function_free($fce);
saprfc_close($rfc);


?>