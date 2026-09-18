<?php
ob_start();

$methodName = !empty($_GET["method"])?$_GET["method"] : null;

if($methodName != null)
{	
	eval("\$method = \"$methodName\";");
	
	if($method != null) $method();	
}
function request($name)
{
	$value = $_POST[$name];
	if($value == null){
		$value = $_POST[$name];
	}
	return $value;
}
function writeJSON($obj){	
	
	if(is_string($obj)) {
		ob_end_clean();
		print_r($obj);
	}else {
		$json = json_encode($obj);		
		ob_end_clean();
		print_r($json);
	}
}
function gbk2utf8($data){
	if(is_array($data)){
		return array_map('gbk2utf8', $data);
	}
	return iconv('gbk','utf-8',$data);
}
function php_json_decode($str){
	$stripStr = stripslashes($str);
	$json = json_decode($stripStr,true);
	return $json;
}

?>