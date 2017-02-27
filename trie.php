<?php
/*
wangxx created this file on Feb 27,2017
unix2dos SpecialNum.txt
unix2dos NumberSeg.txt
*/
error_reporting (E_ALL & ~E_NOTICE);

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
			// print_r($trienode);
			$trienode=& $trienode['children'][$character];
		}
		// print_r($this->trie);
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
		// print_r($find);
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
		// print_r($find);
		$n=count($find)-1;
		return $find[$n];
	}
}
	
	$trie=new Trie();
	$trie->setWord('156');
	$trie->setWord('15618212');
	$trie->setWord('82397');
	$trie->setWord('7283482');
	$trie->setWord('345435');
	$trie->setWord('5656');
	$trie->setWord('2343');
	$trie->setWord('45654');
	
	print_r($trie);
	$maxWord=$trie->searchMax('15618212766');
	print_r($maxWord);
	echo 'maxWord is '.$maxWord['word']."\n";




	// $words=$trie->searchMax('abcdefgh');
	// foreach($words as $word){
	// 	echo 'position:'.$word['position'].'-'.(strlen($word['word'])+$word['position']."\n");
	// 	echo 'word---'.$word['word']."\n";
	// }