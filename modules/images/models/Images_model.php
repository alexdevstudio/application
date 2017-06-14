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




    	$opts = [
		    "http" => [
		        "method" => "GET",
		        "header" => "Accept-language: en\r\n" .
		            "Cookie: foo=bar\r\n"
		    ]
		];

		$context = stream_context_create($opts);

		// Open the file using the HTTP headers set above
		$html = file_get_contents($url, false, $context);

		if (!file_exists('files/tmp')) {
		    mkdir('files/tmp', 0777, true);
		}


		if (!file_exists('files/tmp/'.$sku)) {
		    mkdir('files/tmp/'.$sku, 0777, true);
		}
		file_put_contents('files/tmp/'.$sku.'/images.html', $html);




		//$context = stream_context_create($opts);
		//$html2 = file_get_contents('files/tmp/'.$sku.'/images');


		$html2 = str_get_html(base_url().'files/tmp/'.$sku.'/images.html');
		$html2 = file_get_html($html2)->plaintext;

		// Find all images
		var_dump ($html2);
		exit();
		$shopData = array();
		$i = 0;

		foreach($html2->find('#imageBlock_feature_div script') as $element){
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

     public function getExternalImagesFromUrl2($sku, $url){

     	// Create a stream
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n"
    ]
];

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents($url, false, $context);

     	//$file = file_get_contents($url);
     	echo $file;

     }

   

}