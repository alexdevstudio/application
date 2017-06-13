<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Images_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
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
		    
		   // $this->image_lib->initialize($config_manip);

		    if (copy($src, $target_path)) {

		    	echo $target_path.' : '.$src.'<br/>';
		       
		    
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

    public function getExternalImagesFromUrl($sku, $url){

    	// Create DOM from URL or file
    	$opts = array(
			'http'=>array(
				/*'proxy'=>'85.72.61.177:80',
				'request_fulluri' => true,*/
			'method'=>"GET",
			'header'=>"User-Agent: User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5\r\n"
			)
			);

			$context = stream_context_create($opts);
		$html = file_get_html($url, 0, $context);
		//$html = file_get_html($url);

		// Find all images
		echo $html;
		$shopData = array();
		$i = 0;

		foreach($html->find('#imageBlock_feature_div script') as $element){
			/*if($i==0)
				continue;*/
			
		    $the_script = $element->innertext;

		    //iconv(mb_detect_encoding($the_script, mb_detect_order(), true), "UTF-8", $the_script);

			$i++;
		}
		//echo $the_script;
		$the_script = ltrim($the_script, "P.when('A').register(\"ImageBlockATF\", function(A){ var data = { 'colorImages': { 'initial':");
				    //$the_script = "{'colorImages'".$the_script;

		$the_script = rtrim($the_script, ", 'colorToAsin': {'initial': {}}, 'holderRatio': 1.0, 'holderMaxHeight': 700, 'heroImage': {'initial': []}, 'heroVideo': {'initial': []}, 'weblabs' : {} }; A.trigger('P.AboveTheFold'); // trigger ATF event. return data; }); ");


	     $the_script = explode('},{', $the_script);

	     foreach ($the_script as $string) {
	     	$string = preg_replace('/"hiRes":/', '', $string);
	     	//$string = ltrim($string, '"hiRes":');
	     	$Arraystring = explode(',', $string);
	     	if($Arraystring[0] != 'null')
			{
				$Arraystring[0] = preg_replace('/\[\{/', '', $Arraystring[0]);
				$NewImage[] = preg_replace('/"/', '', $Arraystring[0]);
	     	}

	     }

	    echo '<pre>';
	    print_r ($NewImage);

    	return "Ψάχνεις για φωτογραφία με ".$sku." από το ".$url;

    }

   

}