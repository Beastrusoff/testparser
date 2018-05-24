<?php

require_once ('phpQuery/phpQuery.php');
echo '<!DOCTYPE html><html><meta charset=UTF-8><head>	<title></title></head><body>'; 
set_time_limit(0);

include ('connect.php');
/*$inform=["price"=>'.b-product-basket__sum',
		"name"=>'.b-product-card__title',
		"img"=>'img.b-product-card__img',
		"KGBU"=>'div.b-product-table__col-2',
		"info"=>'div.b-product-table__col-1'];*/
/*$allcat=new parser();
$allcat->findcat($dasedir,'a.b-header-new-menu__level-item-link');

$i=0;
foreach ($allcat->getrefs() as $k) {
parse($k);
}*/
	
/*
function parse($k){
	$info=["price"=>'.b-product-basket__sum',
		"name"=>'.b-product-card__title',
		"img"=>'img.b-product-card__img',
		"KGBU"=>'div.b-product-table__col-2',
		"info"=>'div.b-product-table__col-1'];

$allprod=catalog::findprod($dasedir.$k,'a.b-product__photo-wrap');
$i=0;

if(!empty($allprod)){
foreach ($allprod as $key ) {

 $s=new product();
 $query="SELECT Item_URL from Items WHERE Item_URL =".$basedir.$key;
 $check=mysql_numrows(mysql_query($query)); 
 if($check){
 $s->findpage($basedir.$key);
 $s->finditems($info);
$query="INSERT INTO `Items`(`Item_URL`, `Catalog`, `Date_of_Change`, `Name`, `Price`, `Calories`, `Carbones`, `Protein`, `Fats`) 
VALUES ('"$s->getitemurl()"','"$basedir.$key"',CURRENT_TIMESTAMP,
'"$s->getitemname()"','"$s->getitemprice()"','"$s->getitemcal()"',
'"$s->getitemcarb()"','"$s->getitemprot()"','"$s->getitemfats()"')";


 $s->show();
}
}}}
*/


 class parser{
	//Найти каталоги из на главной странице
 	private $name=[];
 	private $href=[];

	function findcat($url,$sel)
{
	$page=opensession($url);
$document=phpQuery::newDocument($page);
$catalogs =$document->find($sel);
//выгрузка католога всех ссылок

foreach ($catalogs as $catalog ) {
	$pqcatalog=pq($catalog);
	if($pqcatalog->attr('href')!=="javascript:void(0)")
	{$name[] = $pqcatalog->html();
	$href[] = $pqcatalog->attr('href');}
	$this->name = $name;
	$this->href = $href;


}
phpQuery::unloadDocuments($document);
}
function getrefs(){return $this->href;}
function getnames(){return $this->name;}
}


class catalog
{

	function findprod($url,$sel)
{
	
	$page=opensession($url);
	$document=phpQuery::newDocument($page);
$prod =$document->find($sel);
foreach($prod as $p)
{
	$pq=pq($p);
	$href[]=$pq->attr('href');
}
phpQuery::unloadDocuments($document);
return $href;
}
}





class product
{	private $name;
	private $url;
	private $url_img;
	private $price;
	private $KGBU;
	private $document;
	function __construct()
	{
	$this->name="None";
	$this->url="None";
	$this->url_img="None";
	$this->price="None";
	$this->KGBU=["Энергетическая ценность (ккал/100г.):"=>"None",
				"Белки (г/100г.):"=>"None",
				"Углеводы (г/100г.):"=>"None",
				"Жиры (г/100г.):"=>"None"];		
	}
	
	function findpage($url)
	{
		
		$page=opensession($url);
		$this->document=phpQuery::newDocument($page);
		$this->url=$url;

	}
function KGBU($param,$inf)
{	
	$i=0;
	foreach ($param as $key) {
		if(trim($inf[$i])==="Углеводы (г/100г.):")
			{$this->KGBU["Углеводы (г/100г.):"]=$key;}
		if(trim($inf[$i])==="Жиры (г/100г.):")
			{$this->KGBU["Жиры (г/100г.):"]=$key;}
		if(trim($inf[$i])==="Белки (г/100г.):")
			{$this->KGBU["Белки (г/100г.):"]=$key;}
		if(trim($inf[$i])==="Энергетическая ценность (ккал/100г.):")
			{$this->KGBU["Энергетическая ценность (ккал/100г.):"]=$key;}
		$i++;

	}
	
}

	function finditems($sel)
	{
		
		$rubprice=$this->document->find($sel["price"] .'>b');
		$kopprice=$this->document->find($sel["price"] .'>sup');
		$name =$this->document->find($sel["name"])->text();
		$url_img =$this->document->find($sel["img"]);
		$KGBU =$this->document->find($sel["KGBU"]);
		$info =$this->document->find($sel["info"]);
		foreach ($KGBU as $k ) {
			$pq=pq($k);
			$KGB[] =$pq->html();
		}		
		foreach ($info as $k ) {
			$pq=pq($k);
			$inf[] =$pq->html();
		}
		

		$this->KGBU($KGB,$inf);
			foreach ($url_img as $img ) {
		$pq=pq($img);
		$imghref[]= $pq->attr('src');
		}

		$this->url_img =$imghref;
		$this->name =$name;

		$price =$rubprice.'.'.$kopprice;
		$this->price=$price;
		phpQuery::unloadDocuments($this->document);
	}
	function show()
	{echo 
				$this->name.'<br>'.
				$this->url.'<br>'.
				$this->price.'<br>';
				foreach ($this->KGBU as $key=>$val) {
				echo $key.':'.$val.'<br>';
				}				
				foreach ($this->url_img as $img) {
				echo $img.'<br>';
				}
			
				
	}
	function getitemurl(){return $this->url;}
		function getitemname(){	return $this->name;}
		function getitemimg(){	return $this->url_img;}
		function getitemprice(){ return $this->price;}
		function getitemcal(){	return $this->KGBU["Энергетическая ценность (ккал/100г.):"];}
		function getitemprot(){	return $this->KGBU["Белки (г/100г.):"];}
		function getitemcorb(){	return $this->KGBU["Углеводы (г/100г.):"];}
		function getitemfats(){	return $this->KGBU["Жиры (г/100г.):"];}



}




function opensession($url)
{
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER , true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$page=curl_exec($ch);
	curl_close($ch);
	return $page;
}














//Вывод на экран каталогов и их ссылок

function write($tt)
{
if(!empty($tt)){
foreach ($tt as $t) {
	echo $t["href"].$t["Name"].'<br>';
}}
}


function writep($tt)
{
if(!empty($tt)){
foreach ($tt as $t) {
	echo $t.'<br>';
}}
}


echo'</body></html>';




?>








