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





}




?> 