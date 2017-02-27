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
	

$handle = fopen(dirname(__FILE__)."/logs/"."dns.log", "a+");

$data = file_get_contents("php://input");
$obj = json_decode($data);


$appId = base_convert($obj->appId,16,10);
$mdn = $obj->mdn;

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    mdn is ".$mdn."\nappId is ".$appId."\n");



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

define(DB_HOST, '127.0.0.1');
define(DB_USER, 'root');
define(DB_PASS, 'fdjkd&wi278^@6DGHfyTF');
define(DB_CONF, 'wiphonednsdb');
define(TABLE_USERINFO, 'numberseg');


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_CONF, 11008) or die("Error " . mysqli_error($conn));
$sql = sprintf("select city from %s where numberseg = %s", TABLE_USERINFO, $numberSeg);

fwrite($handle, 'TIME: '.date("Y-m-d_H:i:s")."    sql is ".$sql."\n");
$result = mysqli_query($conn, $sql);
$row=mysqli_fetch_assoc($result);
$city=$row["city"];
fwrite($handle, "city is ".$city."\n");

$strategy_address=null;

if(0==strcmp($city,'212')||0==strcmp($city,'2129'))//0212  SH_CT
{
    $IP = "124.74.76.210";
    $PORT = 8080;
    $a=array("ip"=>"$IP:$PORT");
    $b=json_encode($a);
    $c=array("address"=>"[$b]");
    echo json_encode($c)."\n";
}
else if(0==strcmp($city,'213')||0==strcmp($city,'2139'))//0213  SH_CU
{
    $IP = "211.95.17.26";
    $PORT = 80;
    $a=array("ip"=>"$IP:$PORT");
    $b=json_encode($a);
    $c=array("address"=>"[$b]");
    echo json_encode($c)."\n";
}
else if(0==strcmp($city,'202')||0==strcmp($city,'2029'))//0202  GZ_CT
{
    $IP = "113.108.197.66";
    $PORT = 8080;
    $a=array("ip"=>"$IP:$PORT");
    $b=json_encode($a);
    $c=array("address"=>"[$b]");
    echo json_encode($c)."\n";
}
else if(0==strcmp($city,'572')||0==strcmp($city,'5729'))//0572  ZJ_CT
{
    $IP = "122.229.29.76";
    $PORT = 80;
    $a=array("ip"=>"$IP:$PORT");
    $b=json_encode($a);
    $c=array("address"=>"[$b]");
    echo json_encode($c)."\n";
}
else if(0==strcmp($city,'000')||0==strcmp($city,'0009'))//add by zhangw 2016-07-25
{
    $IP = "120.26.105.187";
    $PORT = 80;
    $a=array("ip"=>"$IP:$PORT");
    $b=json_encode($a);
    $c=array("address"=>"[$b]");
    echo json_encode($c)."\n";
}


mysqli_close($conn);


?>