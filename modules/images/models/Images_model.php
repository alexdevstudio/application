<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Images_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function default($sku, $image_id){

      $where = ['item_sku'=>$sku];
      //Disable current default image
      $this->db->where(['item_sku'=>$sku, 'default'=>1]);
      $this->db->set(['default'=>0]);
      $this->db->update('images');
      //Enable the new one
      $this->db->where(['item_sku'=>$sku, 'id'=>$image_id]);
      $this->db->set(['default'=>1]);
      $this->db->update('images');

      return true;
	}

	public function parsePdf($data){

		$src = $data['src'];
		$product_code = $data['code'];

		$target_path = '/var/www/vhosts/etd.gr/httpdocs/wp-content/uploads/'.$product_code.'.pdf';

		if (file_exists($target_path))
			return 'https://etd.gr/wp-content/uploads/'.$product_code.'.pdf';
		else
		{
			if (copy($src, $target_path))
				return 'https://etd.gr/wp-content/uploads/'.$product_code.'.pdf';
		}

		return false;		
	}
	
    public function parseImage($data){

    	$this->load->library('image_lib');

    	$src = $data['src'];
		$sku = $data['sku'];
		$brand = $data['brand'];
		//$pn = $data['part_number'];
		$pn = str_replace("/","-",$data['part_number']);
		$tail = $data['tail'];

		if (!file_exists('images')) {
		    mkdir('images', 0777, true);
		}

		if (!file_exists('images/'.$sku)) {
		    mkdir('images/'.$sku, 0777, true);
		}

		$newFileName = $brand.'_'.$pn.''.$tail;

		$target_path = './images/'.$sku.'/'.$newFileName.'.jpg';

		/* $config_manip = array(
			'image_library' => 'ImageMagick',
			'source_image' => $target_path,
			'new_image' => $target_path,
			'maintain_ratio' => TRUE,
			'create_thumb' => TRUE,
			'thumb_marker' => '',
			'allowed_types' => 'jpg|jpeg|gif|png',
			'width' => 600,
			'height' => 600
		);*/
		//$this->load->library('image_lib', $config);

		//$this->image_lib->resize();
		// $this->image_lib->initialize($config_manip);

		if (copy($src, $target_path)) {

			echo $target_path.' : '.$src.'<br/>';

			//Test with Imagick
			//600x600 Image
			$target_img_path = './images/'.$sku.'/'.$newFileName.'_600.jpg';

			$config_manip = array(
				'image_library' => 'ImageMagick',
				'library_path' => '/usr/bin',
				'source_image' => $target_path,
				'new_image' => $target_img_path,
				'maintain_ratio' => TRUE,
				'create_thumb' => TRUE,
				'thumb_marker' => '',
				'allowed_types' => 'jpg|jpeg|gif|png',
				'width' => 600,
				'height' => 600
			);

			//$this->load->library('image_lib', $config_manip);
			$this->image_lib->initialize($config_manip);

			if ( ! $this->image_lib->resize())
				echo $this->image_lib->display_errors();

			$this->image_lib->clear();
			unset ($config_manip);

			//300x300 Image
			$target_img_path = './images/'.$sku.'/'.$newFileName.'_300.jpg';

			$config_manip = array(
				'image_library' => 'ImageMagick',
				'library_path' => '/usr/bin',
				'source_image' => $target_path,
				'new_image' => $target_img_path,
				'maintain_ratio' => TRUE,
				'create_thumb' => TRUE,
				'thumb_marker' => '',
				'allowed_types' => 'jpg|jpeg|gif|png',
				'width' => 300,
				'height' => 300
			);

			//$this->load->library('image_lib', $config_manip);
			$this->image_lib->initialize($config_manip);

			if ( ! $this->image_lib->resize())
				echo $this->image_lib->display_errors();

			$this->image_lib->clear();
			unset ($config_manip);

			//THUMB Image
			$target_thumb_path = './images/'.$sku.'/'.$newFileName.'_thumb.jpg';

			$config_manip = array(
				'image_library' => 'ImageMagick',
				'library_path' => '/usr/bin',
				'source_image' => $target_path,
				'new_image' => $target_thumb_path,
				'maintain_ratio' => TRUE,
				'create_thumb' => TRUE,
				'thumb_marker' => '',
				'allowed_types' => 'jpg|jpeg|gif|png',
				'width' => 70,
				'height' => 70
			);
			//$this->load->library('image_lib', $config_manip);
			$this->image_lib->initialize($config_manip);

			if ( ! $this->image_lib->resize())
				echo $this->image_lib->display_errors();

			$this->image_lib->clear();
			unset ($config_manip);

			//End of Test with Imagick

			// clear //
			//  $this->image_lib->clear();

			$data = array(
				'item_sku' => $sku,
				'image_src' => $newFileName
				);

			$this->db->insert('images', $data);

			return true;
		}
		return false;
    }

public function getExternalImagesFromUrl($url){
	if (strpos($url, 'www.amazon') !== false) {
    	return $this->getImagesFromAmazon($url);
	}else{
		return $this->getNewImageByUrl($url);
	}

	return;
}

private function getNewImageByUrl($url){
	if($this->validUrl($url)){
		$newImage[] = $url;
		return	$newImage;
	}
	return;
}

private function getImagesFromAmazon($url){

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_REFERER, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$str = curl_exec($curl);
curl_close($curl);

// Create a DOM object
$html_base = new simple_html_dom();
// Load HTML from a string
$html_base->load($str);

foreach($html_base->find('#imageBlock_feature_div script') as $element){


		    $the_script = $element->innertext;

		    //iconv(mb_detect_encoding($the_script, mb_detect_order(), true), "UTF-8", $the_script);


		}
		//echo $the_script;

		//$the_script = ltrim($the_script, "maintainHeight = function(){ var mainHolder = document.getElementById(\"main-image-container\"); var imgTagWrapperId = document.getElementById(\"imgTagWrapperId\"); if(mainHolder && typeof mainHolder != 'undefined'){ var ratio = 0.77; var shouldAutoPlay = false; var naturalMainImageSize = false; var videoSizes = [[342, 196], [385, 221], [425, 244], [466, 267], [522, 300]]; var width = mainHolder.offsetWidth; var containerHeight = width/ratio; containerHeight = Math.min(containerHeight, 700); var aspectRatio = 522/300 var landingImage = document.getElementById(\"landingImage\"); var imageHeight = containerHeight; var imageWidth = width; if(!shouldAutoPlay) { imageHeight = Math.min(imageHeight, 300); imageWidth = Math.min(imageWidth, 522); } var imageWidthBasedOnHeight = imageHeight * aspectRatio; var imageHeightBasedOnWidth = imageWidth / aspectRatio; imageHeight = Math.min(imageHeight, imageHeightBasedOnWidth); imageWidth = Math.min(imageWidth, imageWidthBasedOnHeight); if(typeof mainImgMaxHeight !== 'undefined' && mainImgMaxHeight){ containerHeight = Math.min(mainImgMaxHeight, containerHeight); } mainHolder.style.height = containerHeight + \"px\"; if(imgTagWrapperId && typeof imgTagWrapperId !== 'undefined' ){ imgTagWrapperId.style.height = containerHeight + \"px\"; } if(landingImage && !naturalMainImageSize) { landingImage.style.maxHeight = imageHeight + \"px\"; landingImage.style.maxWidth = imageWidth + \"px\"; } if(shouldAutoPlay){ if(landingImage){ landingImage.style.height = imageHeight + \"px\"; landingImage.style.width = imageWidth + \"px\"; } } } }; maintainHeight(); window.onresize = function(){ maintainHeight(); };");

		$where = strpos($the_script, "P.when('A').register(\"ImageBlockATF\", function(A){ var data = { 'colorImages': { 'initial':");

		$the_script = substr($the_script, $where);
		$the_script = ltrim($the_script, "P.when('A').register(\"ImageBlockATF\", function(A){ var data = { 'colorImages': { 'initial':");
				    //$the_script = "{'colorImages'".$the_script;

		$the_script = rtrim($the_script, ", 'colorToAsin': {'initial': {}}, 'holderRatio': 1.0, 'holderMaxHeight': 700, 'heroImage': {'initial': []}, 'heroVideo': {'initial': []}, 'weblabs' : {} }; A.trigger('P.AboveTheFold'); // trigger ATF event. return data; }); ");


	     $the_script = explode('},{', $the_script);

	     foreach ($the_script as $string) {



	     	$stringHiRes= preg_replace('/"hiRes":/', '', $string);
	     	$stringHiRes= preg_replace('/"large":/', '', $stringHiRes);
	     	//$stringLargeRes = preg_replace('/"hiRes":/', '', $string);
	     	//$string = ltrim($string, '"hiRes":');
	     	$ArrayHiRes = explode(',', $stringHiRes);
	     	/*echo '<pre>';
	     	print_r($ArrayHiRes);
	     	exit();*/

	     	$the_key = (strpos($ArrayHiRes[0], 'null') === FALSE ? 0 : 2);


				//exit('nooooot');
				$ArrayHiRes[$the_key] = preg_replace('/\[\{/', '', $ArrayHiRes[$the_key]);
				//var_dump( $ArrayHiRes[$the_key]);
				$tmpUrl = preg_replace('/"/', '', $ArrayHiRes[$the_key]);
				if($this->validUrl($tmpUrl)){
					$NewImage[] = $tmpUrl;
				}


	     }

	    /*echo '<pre>';
	    print_r($NewImage);
	    exit ();
*/
    	return $NewImage;




		}

		private function validUrl($url){
			$handle = curl_init($url);
			curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

			/* Get the HTML or whatever is linked in $url. */
			$response = curl_exec($handle);

			/* Check for 404 (file not found). */
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);
			if($httpCode == 200) {

			    return true;
			}

			return;

		}




}
