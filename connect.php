<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name="avdb";
require_once("index.php");
$basedir='https://av.ru/';
if (!mysql_connect($db_host, $db_user, $db_pass)) {
    die('Could not connect: ' . mysql_error());
}

    if (!mysql_select_db($db_name)) {
    die('Could not select db: ' . mysql_error());
}

$query="SELECT URL,checked,Name from catalog ";

$result=mysql_query($query) or die('Ошибка'.mysql_error());


$nrows=mysql_numrows($result); 
for($i=0;$i<$nrows;$i++ ) {
	
		 
		$res []=mysql_fetch_row($result);}
	

$check=0;

for($i=0;$i<$nrows;$i++)
{
	$check=$res[$i][1]+$check;
}


$allcat=new parser();


if($check==$nrows|| $nrows==0)
{ 
		 $allcat->findcat($basedir,'a.b-header-new-menu__level-item-link');
		 $names=$allcat->getnames();$i=0;
		 foreach ($allcat->getrefs() as $k) {
		 	
		 	$query= "INSERT INTO `Catalog`(`URL`, `Name`, `Datet_of_Change`, `Active`, `Checked`) VALUES ('".$basedir.$k."','".$names[$i]."','CURRENT_TIMESTAMP',0,0)";

		 	if(mysql_numrows(mysql_query("SELECT URL from catalog WHERE URL='".$basedir.$k."'")))
		 	{
		 	if(!mysql_query($query)){mysql_error();}
		    }
		 	$i++;
		 }
 }
if($check<$nrows){
	$query= "SELECT `URL`, `Name` FROM `Catalog` WHERE `Checked`=0";
$result=mysql_query($query);
for($i=0;$i<$nrows;$i++ ) {
	
		 
		$res []=mysql_fetch_row($result);}
	
	}









foreach ($res as $k) 
{
parse($k[0]);
$query= "UPDATE `Catalog` SET `Checked`= 1 WHERE URL='".$k."'";
mysql_query($query);
}


mysql_close();

function parse($k){
	$inform=["price"=>'.b-product-basket__sum',
		"name"=>'.b-product-card__title',
		"img"=>'img.b-product-card__img',
		"KGBU"=>'div.b-product-table__col-2',
		"info"=>'div.b-product-table__col-1'];

$allprod=catalog::findprod($k,'a.b-product__photo-wrap');
$i=0;
$basedir='https://av.ru/';


if(!empty($allprod)){
foreach ($allprod as $key ) {

 $s=new product();

 $q="SELECT `Item_URL` FROM `Items` WHERE `Item_URL`='".$basedir.$key."'";

 $result=mysql_query($q)or die('Ошибка'.mysql_error());
$checker=mysql_numrows($result); 

 if($checker==0){
 $s->findpage($basedir.$key);
 $s->finditems($inform);

$query="INSERT INTO `Items`SET`Item_URL`='".$s->getitemurl()."',
 `Catalog`= (select `URL` from `Catalog` where `URL`='".$k."'),
  `Date_of_Change`=CURRENT_TIMESTAMP, `Name`='".$s->getitemname()."',
   `Price`='".$s->getitemprice()."',
    `Calories`='".$s->getitemcal()."',
     `Carbones`='".$s->getitemcorb()."',
      `Protein`='".$s->getitemprot()."',
       `Fats`='".$s->getitemfats()."'" ;


mysql_query($query);
foreach ($s->getitemimg as $img) {
	$qimg="INSERT INTO `IMG`SET`img_URL`='".$img."',`Item_URL`=(select `Item_URL` from `Items` where `Item_URL`='".$s->getitemurl()."')";
	echo $qimg;
	mysql_query($qimg);

}
 $s->show();
}}}

}




?>