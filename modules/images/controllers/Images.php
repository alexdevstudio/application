<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Images extends MX_Controller {

	

	public function getImage($data)
	{	

		$src = $data['src'];

		
		if($this->imageUrlExists($src)){
			$this->load->model('images_model');
			return $this->images_model->parseImage($data);
			

		}
		return false;
		

			
	
	}

	public function imageUrlExists($url){
		
	$content = @file_get_contents($url);
	if ($content === false) { return false; } else {return true;}

	}


	public function getFirstImage($sku, $html=false){

		$filename = './images/'.$sku;

			if (file_exists($filename)) {
			   $files = scandir ($filename);
			   $firstFile =$files[2];// because [0] = "." [1] = ".." 

			   if(!$html)
			   	return $firstFile;

			   $src = $filename.'/'.$firstFile;
			   $src = "http://etd.gr/xml".ltrim($src,".");

			   $output ="<img  src='$src' />";
			   return $output;

			} else {
			    $output ="<img  src='".base_url()."assets/images/no-image.png' />";
			   return $output;
			}


	}

	public function getExternalImagesFromUrl($sku=null, $url=null){
		$this->load->model('images_model');
		//echo $this->images_model->getExternalImagesFromUrl('155', 'https://www.amazon.co.uk/Dell-Inspiron-Laptop-Graphics-Anti-Glare/dp/B017URDNS6/ref=sr_1_3?ie=UTF8&qid=1497345644&sr=8-3&keywords=dell++5559');
		echo $this->images_model->getExternalImagesFromUrl('155', 'https://www.amazon.com/Dell-i3567-5185BLK-PUS-Inspiron-Laptop-Graphics/dp/B06X9TH2RX/ref=sr_1_3?ie=UTF8&qid=1497356042&sr=8-3&keywords=dell+3567');
		//echo $this->images_model->getExternalImagesFromUrl('155', 'https://etd.gr');

    }






}




?> 