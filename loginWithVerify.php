<?php
/*
wangxx created this file on Feb 27,2017
unix2dos SpecialNum.txt
unix2dos NumberSeg.txt
*/
error_reporting(0);
date_default_timezone_set("PRC");
ignore_user_abort();
set_time_limit(0);

class Trie {
    private $trie;
    function __construct( ){
        $trie=array('children'=>array( ), 'isword'=>false);
    }
    
    function &setWord($word=''){
        $trienode=&$this->trie;
        for($i=0;$i<strlen($word);$i++){
            $character=$word[$i];
            if(!isset($trienode['children'][$character])){
                $trienode['children'][$character]=array('isword'=>false);
            }
            if($i==strlen($word)-1){
                $trienode['children'][$character]=array('isword'=>true);
            }
            $trienode=& $trienode['children'][$character];
        }
    }
    

    function &isWord($word){
        $trienode=&$this->trie;
        for($i=0;$i<strlen($word);$i++){
            $character=$word[$i];
            if(!isset($trienode['children'][$character])){
                return false;
            }else{
                if($i==(strlen($word)-1)&&$trienode['children'][$character]['isword']==true){
                    return true;
                }
                elseif($i==(strlen($word)-1)&&$trienode['children'][$character]['isword']==false){
                    return false;
                }
                $trienode=&$trienode['children'][$character];
            }
        }
    }
    
    
    function search($text=""){
        $textlen=strlen($text);
        $trienode=$tree=$this->trie;
        $find=array();
        $wordrootposition=0;
        $prenode=false;
        $word='';
        for($i=0;$i<$textlen;$i++){
            if(isset($trienode['children'][$text[$i]])){
                $word=$word.$text[$i];
                $trienode=$trienode['children'][$text[$i]];
                if ($prenode==false){
                    $wordrootposition=$i;
                }
                $prenode=true;
                if($trienode['isword']){
                    $find[]=array('position'=>$wordrootposition,'word'=>$word);
                }
            }else{
                $trienode=$tree;
                $word='';
                if($prenode){
                    $i=$i-1;
                    $prenode=false;
                }
            }
        }
        return $find;
    }


    function searchMax($text=""){
        $textlen=strlen($text);
        $trienode=$tree=$this->trie;
        $find=array();
        $wordrootposition=0;
        $prenode=false;
        $word='';
        for($i=0;$i<$textlen;$i++){
            if(isset($trienode['children'][$text[$i]])){
                $word=$word.$text[$i];
                $trienode=$trienode['children'][$text[$i]];
                if ($prenode==false){
                    $wordrootposition=$i;
                }
                $prenode=true;
                if($trienode['isword']){
                    $find[]=array('position'=>$wordrootposition,'word'=>$word);
                }
            }else{
                $trienode=$tree;
                $word='';
                if($prenode){
                    $i=$i-1;
                    $prenode=false;
                }
            }
        }
        $n=count($find)-1;
        return $find[$n];
    }
}

$handle = fopen(dirname(__FILE__)."/logs/"."loginWithVerify.log", "a+");




$data = file_get_contents("php://input");
$obj = json_decode($data);


$appId = base_convert($obj->appId,16,10);
$mdn = $obj->mdn;
$passWord = base_convert($obj->passWord,16,10);
$secSeq = $obj->secSeq;
$secCode = $obj->secCode;
$seq = 111; //seq not used yet, can be used to verify register requiring authcode and reset password requiring authcode
$request = 1;//0:codeRequest  1:authRequest
$opt_code = 3; //0.user_account  1. user_cancel  2. user_register  3. user_login  4. user_active  5. user_deactive
$termType=$obj->termType;

if(!isset($obj->mcc)||(null==$obj->mcc)){
        $mcc = "460";
}else{
        $mcc = $obj->mcc;
}
if(!isset($obj->mnc)||(null==$obj->mnc)){
        $mnc = "03";
}else{
        $mnc = $obj->mnc;
}



$file = 'SpecialNum.txt';
$content = file_get_contents($file);
$arrWord = explode("\r\n", $content);

$trie=new Trie();
foreach ($arrWord as $k => $v) {
    $trie->setWord("$v");
}

// find keyword
$maxWord=$trie->searchMax($mdn);
$numberSeg=$maxWord['word'];
fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    numberSeg is ".$numberSeg."\n");

$trie=null;


if(null==$numberSeg){

    $file = 'NumberSeg.txt'; 
    $content = file_get_contents($file); 
    $arrWord = explode("\r\n", $content); 
    $trie=new Trie( );
    foreach ($arrWord as $k => $v) {
        $trie->setWord($v);
    }
    $maxWord = $trie->searchMax($mdn);
    $numberSeg=$maxWord['word'];
    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    numberSeg is ".$numberSeg."\n");
    $trie=null;
}

define(MYSQL_HOST, '127.0.0.1');
define(MYSQL_USER, 'root');
define(MYSQL_PASS, 'fdjkd&wi278^@6DGHfyTF');
define(MYSQL_CONF, 'wiphonednsdb');
define(TABLE_NUMSEG, 'numberseg');
define(TABLE_SYSCONFIG, 'sysconfig');



$confdb_conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_CONF, 11008) or die("Error " . mysqli_error($confdb_conn));
$sql = sprintf("select * from %s where name = %s", TABLE_PARAMINFO, "'about wiphone'");

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($confdb_conn, $sql);
$row=mysqli_fetch_assoc($result);
$about_wiphone=$row["value"];
fwrite($handle, "about wiphone is ".$about_wiphone."\n");



$sql = sprintf("select * from %s where name = %s", TABLE_PARAMINFO, "'encryptionFlag'");

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($confdb_conn, $sql);
$row=mysqli_fetch_assoc($result);
$encryptionFlag=$row["value"];
fwrite($handle, "encryptionFlag is ".$encryptionFlag."\n");




$sql = sprintf("select * from %s where name = %s", TABLE_PARAMINFO, "'copy right'");

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($confdb_conn, $sql);
$row=mysqli_fetch_assoc($result);
$copy_right=$row["value"];
fwrite($handle, "copy right is ".$copy_right."\n");

mysqli_free_result($result);
mysqli_close($confdb_conn);





$mysqlconn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_CONF, 11008) or die("Error " . mysqli_error($mysqlconn));
$sql = sprintf("select city from %s where numberseg = %s", TABLE_NUMSEG, $numberSeg);

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($mysqlconn, $sql);
$row=mysqli_fetch_assoc($result);
$city=$row["city"];
fwrite($handle, "city is ".$city."\n");
mysqli_free_result($result);



$sql = sprintf("select * from %s where city = %s", TABLE_SYSCONFIG, $city);
fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($mysqlconn, $sql);
if(mysqli_num_rows($result) > 0){
    $row=mysqli_fetch_assoc($result);
    $Smsc=$row["Smsc"];
    $imsiBindCheckFlag=$row["imsiBindCheckFlag"];
    $coreFirstFlag=$row["coreFirstFlag"];
    $smsNum=$row["smsNum"];
    $smsContentAndroid=$row["smsContentAndroid"];
    $smsContentIos=$row["smsContentIos"];
    $helpWebAddr=$row["helpWebAddr"];
    $totalTime=$row["totalTime"];
    $timeLeft=$row["timeLeft"];
    $ssidFlag=$row["ssidFlag"];
    $ssidNum=$row["ssidNum"];
    $itmsInterval=$row["itmsInterval"];    
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t Smsc is ".$GLOBALS['Smsc']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t "."imsiBindCheckFlag is ".$GLOBALS['imsiBindCheckFlag']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t coreFirstFlag is ".$GLOBALS['coreFirstFlag']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t smsNum is ".$GLOBALS['smsNum']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t "."smsContentAndroid is ".$GLOBALS['smsContentAndroid']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t smsContentIos is ".$GLOBALS['smsContentIos']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t helpWebAddr is ".$GLOBALS['helpWebAddr']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t totalTime is ".$GLOBALS['totalTime']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t timeLeft is ".$GLOBALS['timeLeft']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t ssidFlag is ".$GLOBALS['ssidFlag']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t ssidNum is ".$GLOBALS['ssidNum']."\n");
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t itmsInterval is ".$GLOBALS['itmsInterval']."\n");
}else{
    fwrite($handle, "TIME: ".date("Y-m-d_H:i:s")."\t city ".$GLOBALS['city']." not exist\n");
}
mysqli_free_result($result);


mysqli_close($mysqlconn);



define(DB_HOST, '127.0.0.1');
define(DB_USER, 'root');
define(DB_PASS, 'fdjkd&wi278^@6DGHfyTF');
define(DB_DATABASENAME, 'wiphoneconfdb');
define(DB_STAT, 'wiphonestatdb');
define(DB_TABLENAME, 'app_user_info');


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASENAME, 11008) or die("Error " . mysqli_error($conn));




if(isset($obj->imsi)){//imsi not null
    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."\n     imsi = ".$obj->imsi."\n");
    $sql = sprintf("select imsi from %s where mdn = %s", DB_TABLENAME, $obj->mdn);
    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
    $result = mysqli_query($conn, $sql);
    $row=mysqli_fetch_assoc($result);
    $initial_imsi=$row["imsi"];
    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."\n     initial imsi = ".$initial_imsi."\n");

    if($obj->imsi!=$initial_imsi){
        fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."\n     not the initial imsi, so exit. \n");
        mysqli_free_result($result);
        mysqli_close($conn);
        exit;
    }
}





function diff($day1,$day2)
{
    $a=explode("-",$day1);
    $b=explode("-",$day2);
    if(checkdate($a[1],$a[2],$a[0]) && checkdate($b[1],$b[2],$b[0]))
    {
        $c=mktime(0,0,0,$a[1],$a[2],$a[0]);
        $d=mktime(0,0,0,$b[1],$b[2],$b[0]);
        $f=($d-$c)/3600/24;
        return $f;
    }
    else
    {
        fwrite($handle, "date pattern error!\n");
        exit;
    }
}
//diff("2001-1-1","2001-3-1");





function run()
{
	
        $from = '';
        $port = 0;
        //echo "{$GLOBALS['sock']}\n";
        socket_recvfrom($GLOBALS['sock'], $inMsg, 1024, 0, $from, $port);
        
        
        echo "{$inMsg}";
        $array = explode(",",$inMsg);
        $rsCode = $array[2];
        
        $a=array("carrier"=>"shanghai telecom","rsCode"=>"$rsCode");
        echo json_encode($a);
          
        fwrite($GLOBALS['handle'], 'TIME: '.date("Y-m-d_H:i:s").'	FromAuthServerMsg: '.$inMsg.' Client: '.$from.' Port: '.$port."\n");
        
        socket_close($GLOBALS['sock']);

}


fwrite($handle, "secSeq is ".$secSeq."\n");

if(!isset($secSeq)){
    $rsCode = 11;//0: success, 1: fail(password error), 11: fail(others)
    fwrite($handle, "secSeq is null \n");
    echo "{\"rsCode\":\"".$rsCode."\"}";
    exit;
}else if(!isset($secCode)){
    $rsCode = 11;//0: success, 1: fail(password error), 11: fail(others)
    fwrite($handle, "secCode is null \n");
    echo "{\"rsCode\":\"".$rsCode."\"}";
    exit;
}else if(!isset($seq)){
				$rsCode = 11;//0: success, 1: fail(password error), 11: fail(others)
				fwrite($handle, "seq is null \n");
				echo "{\"rsCode\":\"".$rsCode."\"}";
				exit;
}else if(!isset($request)){
				$rsCode = 11;//0: success, 1: fail(password error), 11: fail(others)
				fwrite($handle, "request is null \n");
				echo "{\"rsCode\":\"".$rsCode."\"}";
				exit;
}


fwrite($handle, "passWord is ".$passWord."\n");
$sql = sprintf("select pwd from %s where mdn = %s", DB_TABLENAME, $obj->mdn);
fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

        $row=mysqli_fetch_assoc($result);
        $pwd=$row["pwd"];
        fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."\n     pwd = ".$pwd."\npassWord = ".$passWord."\n");
        
        if($pwd != $passWord){
            		fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."   passWord error\n");
            		$a=array(
			            "carrier"=>"shanghai telecom",
            		    "rsCode"=>"1",
            		    "Smsc"=>$GLOBALS['Smsc'],
            		    "imsiBindCheckFlag"=>$GLOBALS['imsiBindCheckFlag'],
            		    "coreFirstFlag"=>$GLOBALS['coreFirstFlag'],
            		    "smsNum"=>$GLOBALS['smsNum'],
            		    "smsContentAndroid"=>$GLOBALS['smsContentAndroid'],
            		    "smsContentIos"=>$GLOBALS['smsContentIos'],
            		    "helpWebAddr"=>$GLOBALS['helpWebAddr'],
            		    "totalTime"=>$GLOBALS['totalTime'],
            		    "timeLeft"=>$GLOBALS['timeLeft'],
            		    "ssidFlag"=>$GLOBALS['ssidFlag'],
            		    "ssidNum"=>$GLOBALS['ssidNum'],
            		    "itmsInterval"=>$GLOBALS['itmsInterval'],
                        "about wiphone"=>$GLOBALS['about_wiphone'],
                        "copy right"=>$GLOBALS['copy_right'],
                        "encryptionFlag"=>$GLOBALS['encryptionFlag']
            		);
            		echo json_encode($a);
    	}else{
        		    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."   passWord correct\n");
        		    
        		    
        		    
        		    
        		    $sql = sprintf("select app_id from %s where mdn = %s", DB_TABLENAME, $obj->mdn);
        		    fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
        		    $result = mysqli_query($conn, $sql);
        		    if(mysqli_num_rows($result) > 0){
        		    
            		        $row=mysqli_fetch_assoc($result);
            		        $app_id=$row["app_id"];
            		        fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."\n     app_id = ".$app_id."\n");
        		    
        		    }else{
            		    
            		        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            		        if ( $sock === false ) {
                		            $msg = socket_strerror( socket_last_error() );
                		            fwrite($handle, "socket_create() failed:reason:".$msg."\n");
            		        }
            		    
            		         
            		        $msg = "4,0,".$mdn;
            		        $len = strlen($msg);
            		        socket_sendto($sock, $msg, $len, 0, '127.0.0.1', 20001);
            		         
            		        fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s").'	ToAuthServerMsg: '.$msg."\n");
        		    }
        		    
        		    

					$msg = $seq.",".$request.",".$secSeq.",".$secCode;
	                
	                
	                
	                $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	                if ( $sock === false ) {
	                    $msg = socket_strerror( socket_last_error() );
	                    fwrite($handle, "socket_create() failed:reason:".$msg."\n");
	                }
					
					$len = strlen($msg);
					socket_sendto($sock, $msg, $len, 0, '127.0.0.1', 20001);
											
					fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s").'	ToAuthServerMsg: '.$msg."\n");
											
					//echo "{$sock}\n";
					
					//daemon('run');
					
					
					
					socket_recvfrom($sock, $inMsg, 1024, 0, $from, $port);
					
					//echo "{$inMsg}";
					$array = explode(",",$inMsg);
					$rsCode = $array[2];
						
                    $a=array(
					    "carrier"=>"shanghai telecom",
            		    "rsCode"=>$rsCode,
            		    "Smsc"=>$GLOBALS['Smsc'],
            		    "imsiBindCheckFlag"=>$GLOBALS['imsiBindCheckFlag'],
            		    "coreFirstFlag"=>$GLOBALS['coreFirstFlag'],
            		    "smsNum"=>$GLOBALS['smsNum'],
            		    "smsContentAndroid"=>$GLOBALS['smsContentAndroid'],
            		    "smsContentIos"=>$GLOBALS['smsContentIos'],
            		    "helpWebAddr"=>$GLOBALS['helpWebAddr'],
                        "totalTime"=>$GLOBALS['totalTime'],
                        "timeLeft"=>$GLOBALS['timeLeft'],
                        "ssidFlag"=>$GLOBALS['ssidFlag'],
                        "ssidNum"=>$GLOBALS['ssidNum'],
                        "itmsInterval"=>$GLOBALS['itmsInterval'],
                        "about wiphone"=>$GLOBALS['about_wiphone'],
                        "copy right"=>$GLOBALS['copy_right'],
                        "encryptionFlag"=>$GLOBALS['encryptionFlag']
            		);
					echo json_encode($a);

					
					$sql = sprintf("update %s set app_id = %s where mdn = %s", DB_TABLENAME, $appId, $obj->mdn);
					fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
					$result = mysqli_query($conn, $sql);
					mysqli_close($conn);
					
					
					
					$day1 = "1970-1-1";
					$day2 = date("Y-m-d");
					$gap = diff($day1, $day2);
					$suffix = $gap%90;
					fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."   day2 is ".$day2."\ngap is ".$gap."\nsuffix is ".$suffix."\n");
					
					$table = "active_record_".$suffix;
					
					$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_STAT, 11008) or die("Error " . mysqli_error($conn));
					
					$sql = sprintf("INSERT INTO %s (id, create_time, mdn, imsi, esn, opt_code, opt_result, app_id, ip, mcc, mnc, termType) VALUES (1, NOW(), %s, 0, 0, %s, 0, %s, %s, %s, %s, %s)", $table, $mdn, $opt_code, $appId, "\"".$_SERVER['REMOTE_ADDR']."\"", "\"".$mcc."\"", "\"".$mnc."\"", "\"".$termType."\"");
					
					fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
					mysqli_query($conn, $sql);
					mysqli_close($conn);
					
					

					
					fwrite($GLOBALS['handle'], 'TIME: '.date("Y-m-d_H:i:s").'	FromAuthServerMsg: '.$inMsg.' Client: '.$from.' Port: '.$port."\n");
						
					socket_close($sock);
				
				}
				
				mysqli_free_result($pwd);
}else{
			fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."  mdn  ".$mdn."  not exist\n");
			$a=array(
				"carrier"=>"shanghai telecom",
        	    "rsCode"=>"3",
        	    "Smsc"=>$GLOBALS['Smsc'],
        	    "imsiBindCheckFlag"=>$GLOBALS['imsiBindCheckFlag'],
        	    "coreFirstFlag"=>$GLOBALS['coreFirstFlag'],
        	    "smsNum"=>$GLOBALS['smsNum'],
        	    "smsContentAndroid"=>$GLOBALS['smsContentAndroid'],
        	    "smsContentIos"=>$GLOBALS['smsContentIos'],
        	    "helpWebAddr"=>$GLOBALS['helpWebAddr'],
			    "totalTime"=>$GLOBALS['totalTime'],
			    "timeLeft"=>$GLOBALS['timeLeft'],
			    "ssidFlag"=>$GLOBALS['ssidFlag'],
			    "ssidNum"=>$GLOBALS['ssidNum'],
			    "itmsInterval"=>$GLOBALS['itmsInterval'],
                "about wiphone"=>$GLOBALS['about_wiphone'],
                "copy right"=>$GLOBALS['copy_right'],
                "encryptionFlag"=>$GLOBALS['encryptionFlag']
            );
             echo json_encode($a);
}

mysqli_free_result($result);
mysqli_close($conn);

?>