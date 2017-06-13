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






}




?> 