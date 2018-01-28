<?php
session_start();
//---------------------------------------------------
if(isset($_GET[usefile])){
   $ca = new DeaCaptcha();
//---------------------------------------------------
//set captcha
   // set image in frame
   $ca->setImgFrame(1);
//---------------------------------------------------
   // set number of char in word; default randomize (5,8)
   //$ca->setNumString(5);
//---------------------------------------------------
   // set crypt word
   // use crypt file
   $ca->setCryptFile('word/file.csv');
   // use array of char
   // use nothing default numbers (0..9)
   //$ca->setCryptBase('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
   $ca->setCryptBase('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
   //$ca->setCryptBase('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
//---------------------------------------------------
   // set width and height image; default w=150 h=60
//   $ca->setWidth(160);
//   $ca->setHeight(70);
   $ca->setWidth(160);
   $ca->setHeight(50);
//---------------------------------------------------
   // set background image
   // use some image with path or array of image (first param)
   // second param (s=stretch,t=tile,o=orginal,r=resize or nothing; default resize)
   // delim between param is ';'
   //$ca->setImageName('image/mda.png;s,image/bda.png;t,image/es.jpg;o,image/sd.jpg;r,image/cs.gif;r,image/wl.gif;t');
//---------------------------------------------------
   // set patern
   // use P=point,L=line,C=circle,E=elipse,CF=fillcircle,EF=fillelipse;T=text
   $ca->setPaternType('p,l,c,e,cf,ef,t');
   $ca->setPaternRandColor(1);
//---------------------------------------------------
   // set font
   // set randomize font if set array of fonts
   $ca->setFontRand(1);
   // set some font with path or array of font or nothing
   // use true type font (.ttf) or graphics data format (.gdf)
   $ca->setFontName('font/gilligan.ttf,font/arialbd.ttf');
   //$ca->setFontName('font/04b.gdf,font/automatic.gdf);
   // set randomize color for each char in word
   $ca->setFontRandColor(1);
   // set shadow on word
   $ca->setFontShadow(1);
//---------------------------------------------------
//create captcha
   $ca->create_captcha();
//---------------------------------------------------
   $_SESSION['key'] = $ca->crypt;
//---------------------------------------------------
// show captcha
   $ca->show_carp($ca->image);
}
//---------------------------------------------------
class DeaCaptcha{
private $imginto;

// set image in frame
function setImgFrame($imgframe = 0){$this->imgframe = $imgframe;}
// set number of char in word
function setNumString($numString){$this->numString = $numString;}
// set crypt word
function setCryptFile($cryptFile){$this->cryptFile = $cryptFile;}
function setCryptBase($cryptBase = ''){$this->cryptBase = $cryptBase;}
// set font
function setFontRand($fontRand){$this->fontRand = $fontRand;}
function setFontName($fontName){$this->fontName = $fontName;}
function setFontRandColor($fontRandColor){$this->fontRandColor = $fontRandColor;}
function setFontShadow($fontShadow){$this->fontShadow = $fontShadow;}
// set width & height
function setWidth($width){$this->width = $width;}
function setHeight($height){$this->height = $height;}
// set background image
function setImageName($imageName){$this->imageName = $imageName;}
// set patern
function setPaternType($paternType){$this->paternType = $paternType;}
function setPaternRandColor($paternRandColor){$this->paternRandColor = $paternRandColor;}

public function create_captcha($imginto = 1){

   $this->imginto = $imginto;
//---------------------------------------------------
// set number of char in word; default randomize (5,9)
   if($this->numString == ''){
      $this->numString = rand(5,8);
   }
//---------------------------------------------------
// set crypt word
   if($this->cryptBase == ''){$this->cryptBase = '0123456789';}
   if(file_exists($this->cryptFile)){
      $myCryptWord = array();
      $file = file($this->cryptFile);
      foreach($file as $line) {
         if(strlen(trim($line)) == $this->numString){
            $myCryptWord[] = trim($line);
         }
      }
      $myCrypt = $myCryptWord[rand(0,count($myCryptWord)-1)];
   }else{
      for ($x = 0; $x <= $this->numString-1; $x++){
         $myChar = substr($this->cryptBase, rand(0,strlen($this->cryptBase)-1), 1);
      	$myCrypt .= $myChar;
      }
   }
//---------------------------------------------------
// set font
   $fName = explode(',', $this->fontName);
   $fntName = $fName[rand(0,count($fName)-1)];
   $fntType = strtolower(pathinfo($fntName, PATHINFO_EXTENSION));
//---------------------------------------------------
// set width and height image; default w=150 h=60
   if($this->width == ''){
      $orgwidth = 150;
      $this->width = $orgwidth;
   }else{
      $orgwidth = $this->width;
   }
   if($this->height == ''){
      $orgheight = 60;
      $this->height = $orgheight;
   }else{
      $orgheight = $this->height;
   }
//---------------------------------------------------
// set background image
   $iNameTypeA = explode(',', $this->imageName);
   $iRand = $iNameTypeA[rand(0,count($iNameTypeA)-1)];
   $iNameType = explode(';', $iRand);
   $imgName = $iNameType[0];
   $imgType = $iNameType[1];
   if($iNameType[1] == ''){$imgType = 'r';};
//---------------------------------------------------
// set patern
   $iPTypeNumA = explode(',', $this->paternType);
   $iPRand = $iPTypeNumA[rand(0,count($iPTypeNumA)-1)];
   $iPTypeNum = explode(';', $iPRand);
   $imgPatType = $iPTypeNum[0];
   $imgPatNum = $iPTypeNum[1];
   if($imgPatNum == ''){
      if(strpos($imgPatType,'c') !== false) $imgPatNum = 50;
      elseif(strpos($imgPatType,'e') !== false) $imgPatNum = 50;
      elseif($imgPatType == 't') $imgPatNum = 500;
      elseif($imgPatType == 'p') $imgPatNum = 2;
      elseif($imgPatType == 'l') $imgPatNum = 10;
      else $imgPatNum = 10;
   }
//---------------------------------------------------
// make image
   if(file_exists($imgName)){
      $size = getimagesize($imgName);
      $orgwidth = $size[0];
      $orgheight = $size[1];
      $orgtype = $size[mime];
      if($orgtype == 'image/png'){
         $image = imagecreatefrompng($imgName);
      }elseif($orgtype == 'image/jpeg'){
         $image = imagecreatefromjpeg($imgName);
      }elseif($orgtype == 'image/gif'){
         $image = imagecreatefromgif($imgName);
      }else{
         $image = imagecreatetruecolor($orgwidth, $orgheight);
         $white = imagecolorallocate($image, 255, 255, 255);
         imagefill($image, 0, 0, $white);
      }

      if($imgType != 'o'){
         $new_image = imagecreatetruecolor($this->width, $this->height);
         if ($imgType == 's'){
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $this->width, $this->height, $orgwidth, $orgheight);
         }
         elseif ($imgType == 't'){
            imagesettile($new_image, $image);
            imagefill($new_image, 0, 0, IMG_COLOR_TILED);
         }
         elseif ($imgType == 'r'){
            imagecopyresized($new_image, $image, 0, 0, 0, 0, $this->width, $this->height, $orgwidth, $orgheight);
         }
         $image = $new_image;
         $orgwidth = $this->width;
         $orgheight = $this->height;
      }
   }else{
      $image = imagecreatetruecolor($orgwidth, $orgheight);
      $white = imagecolorallocate($image, 255, 255, 255);
      imagefill($image, 0, 0, $white);
   }
//---------------------------------------------------
// make patern
   $bright = imagecolorallocate($image, rand(160,255),  rand(160,255),  rand(160,255));
   for ($x = 0; $x <=$orgwidth*$orgheight/$imgPatNum; $x++){
   	$myX = rand(1,$orgwidth);
   	$myY = rand(1,$orgheight);
      $myZ1 = rand(4,10);
      if(strpos($imgPatType,'e') === false){
         $myZ2 = $myZ1;
      }else{
         $myZ2 = rand(4,10);
      }
      if($imgPatType == 'p'){
         if($this->paternRandColor == 1)
            $bright = imagecolorallocate($image, rand(64,255),  rand(64,255),  rand(64,255));
      	imageline($image, $myX, $myY, $myX, $myY, $bright);
      }
      if($imgPatType == 'l'){
         if($this->paternRandColor == 1)
            $bright = imagecolorallocate($image, rand(64,255),  rand(64,255),  rand(64,255));
      	imageline($image, $myX, $myY, $myX + rand(-5,5), $myY + rand(-5,5), $bright);
      }
      if(strpos($imgPatType,'c') === false ^ strpos($imgPatType,'e') === false){
         if(strpos($imgPatType,'f') === false){
            $bright = imagecolorallocate($image, rand(64,255),  rand(64,255),  rand(64,255));
            imageellipse($image, $myX, $myY, $myZ1, $myZ2, $bright);
         }else{
            $bright = imagecolorallocate($image, rand(160,255),  rand(160,255),  rand(160,255));
            imagefilledellipse($image, $myX, $myY, $myZ1, $myZ2, $bright);
         }
      }
      if($imgPatType == 't'){
         $myChar = substr($this->cryptBase, rand(0,strlen($this->cryptBase)-1), 1);
         $bright = imagecolorallocate($image, rand(192,255),  rand(192,255),  rand(192,255));
         if($this->fontName != ''){
            if($this->fontRand == 1){
               $fntName = $fName[rand(0,count($fName)-1)];
               $fntType = strtolower(pathinfo($fntName, PATHINFO_EXTENSION));
            }
            if($fntType == 'ttf'){
               imagettftext($image, rand($orgheight/3, $orgheight/3), rand(-10, 10), $myX, $myY, $bright, $fntName, $myChar);
            }
            elseif($fntType == 'gdf'){
               $font = imageloadfont($fntName);
               imagestring ($image, $font, $myX, $myY, $myChar, $bright);
            }
         }
      }
   }
//---------------------------------------------------
// make crypt word
   $dark = imagecolorallocate($image, rand(5,192),rand(5,192),rand(5,192));
   // middle
   $xstart = ($orgwidth - (10 * $this->numString)) / 2;
   $ystart = ($orgheight - 15) / 2;
   //imagestring($image, 5, $xstart, $ystart, $myCrypt, $dark);

   for ($x = 0; $x <= $this->numString; $x++){
      if($this->fontRand == 1){
         $fntName = $fName[rand(0,count($fName)-1)];
         $fntType = strtolower(pathinfo($fntName, PATHINFO_EXTENSION));
      }
      if($this->fontRandColor == 1)
       	$dark = imagecolorallocate($image, rand(5,192),rand(5,192),rand(5,192));

      if($fntType == 'ttf'){
       	$myChar = substr($myCrypt, $x, 1);
        	$capString .= $myChar;
         // make possition
         $fs = rand($orgheight/3, $orgheight/3);
         $myX =  5 + ($x * $orgwidth/$this->numString + rand(-5,0));
         $myY = rand($fs + 2, $orgheight-5);
         $angle = rand(-10, 10);
         if($this->fontShadow == 1)
            imagettftext($image, $fs+2, $angle, $myX, $myY, 0x00, $fntName, $myChar);
         imagettftext($image, $fs, $angle, $myX, $myY, $dark, $fntName, $myChar);
      }
      elseif($fntType == 'gdf'){
        	$myChar = substr($myCrypt, $x, 1);
        	$capString .= $myChar;
         $font = imageloadfont($fntName);
         $fontHeight = imagefontheight($font)/2;
         $fontWidth = imagefontwidth($font);
         // make possition
         $fs = $orgheight/3;
         $myX =  5+($x * $orgwidth/$this->numString + rand(-5,0));
         $myY = rand($fontHeight + 2, $orgheight - $fontHeight);
         if($this->fontShadow == 1)
            imagestring ($image, $font, $myX+2, $myY-$fontHeight+2, $myChar, 0x00);
         imagestring ($image, $font, $myX, $myY-$fontHeight, $myChar, $dark);
      }
      else{
         $myChar = substr($myCrypt, $x, 1);
         // make possition
         $x1 = $xstart+($x * 10);
         $y1 = $ystart+($y * 10);
         imagestring($image, 5, rand($x1-5,$x1+5), rand($y1-5,$y1+5), $myChar, $dark);
      }
   }
//---------------------------------------------------
// make image frame
   if ($this->imgframe)
      imagerectangle($image, 0, 0, $orgwidth-1, $orgheight-1, 0x00);
//---------------------------------------------------
// output image
   $this->image = $image;
   $this->crypt = $myCrypt;
}

   public function show_carp($image){
      if($this->imginto){
        	header('Content-type: '.$orgtype);
      }
      if($orgtype == 'image/jpeg'){
         imagejpeg($image, $this->file);
      }elseif($orgtype == 'image/gif'){
         imagegif($image, $this->file);
      }else{
         imagepng($image, $this->file);
      }
      imagedestroy($image);
   }
}
?>
