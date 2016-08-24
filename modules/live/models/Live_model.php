<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Live_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    private function xml($url){

		$xml=simplexml_load_file($url) or die("Error: Cannot create object");

    	return $xml;
    }


    public function oktabit(){

		if($xml = $this->xml("http://www.oktabit.gr/times_pelatwn/prices_xml.asp?customercode=012348&logi=evansmour")){
			if($desc_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/perigrafes_xml.asp?customercode=012348&logi=evansmour")){
				if($char_xml = $this->xml("http://www.oktabit.gr/times_pelatwn/chars_xml.asp?customercode=012348&logi=evansmour")){
 
					$images = array();

					$this->updateLive('oktabit');
				
					
				}
				else{
					die("Characteristics XML from Oktabit can not be loaded.");
				}
			}
			else{
				die("Description XML from Oktabit can not be loaded.");
			}
		}else{
			die("XML from Oktabit can not be loaded.");
		}

		$newProducts = array();
		$f=0;

		foreach($xml->children() as $product) {
			
			set_time_limit(50);
			//Rename categories for ETD.gr

			$dist_type='';
			$cat = (string) trim($product->category);
			$sc = trim((string)$product->subcategory);
			$B2b_sc = trim((string)$product->B2b_subcat);

			$c = $cat;
			

			$brand = (string) trim($product->brand);
			if ($brand == 'VERO' || $brand == 'MAXBALL' || $brand == 'LEXMARK')
			{
				$cat = '';
			}

			switch ($cat) {
				case 'Mobile Computers':
					if (strpos($sc, 'Notebooks up to') !== false)
					{
						$c = 'laptops';
					}elseif($sc == 'Carrying Cases'){
						$c = 'carrying_cases';
					}
					break;
				case 'Computers':
					if($sc == 'Advanced PC' || $sc == 'All In One PC' || $sc == 'Business PC' || $sc == 'Workstations')
					{
						$c = 'desktops';
							
					}
					break;
				case 'Energy':
					if($sc == 'Power Bank')
					{
						$c = 'power_bank';
							
					}
					break;
				case 'Monitors':
					if($sc == 'LCD-TFT PC Monitors')
					{
						$c = 'monitors';
					}
					break;
				case 'Networking':
					if($sc == 'Routers')
					{
						$c = 'routers';
					}
					elseif($sc == 'Switches'){
						$c = 'switches';
					}
					break;
				case 'Power Protection':
					if($sc == 'Line Interactive UPS' || $sc == 'On Line UPS'  || $sc == 'Standby UPS')
					{
						$c = 'ups';
					}
					break;
				case 'Printers':
					if($sc == 'Color Laser Printers' || $sc == 'Inkjet Printers' || $sc == 'B-W Laser Printers' )
					{
						$c = 'printers';	
					}
					elseif($sc == 'Multifunction Printers'){
						$c = 'multifunction_printers';
					}
					break;
				case 'Software':
					if($sc == 'OEM ROK Server'){
						$c = 'software';
						$sc = 'Λογισμικό Server';
					}
					elseif($sc == 'Software Applications'){
						$c = 'software';
						$sc = 'Εφαρμογές γραφείου';
					}
					break;
				case 'Software DSP':
					$dist_type = 'DSP';

					if($sc == 'DSP Licensing (CAL)' || $sc == 'DSP Server Software'){
						$c = 'software';
						$sc = 'Λογισμικό Server';
					}
					elseif($sc == 'DSP Operating Systems'){
						$c = 'software';
						$sc = 'Λειτουργικά Συστήματα';
					}
					break;
				case 'Servers':
					if($sc == 'Rackmount Systems' )
					{
						$c = 'servers';
					}
					break;
				case 'Entertainment':
					if($sc == 'Speakers' )
					{
						$c = 'speakers';
					}
					break;
				case 'Storage':
					if($sc == 'External Hard Drives' )
					{
						$c = 'external_hard_drives';
							
					}
					elseif($sc == 'Sata Hard Drives' )
					{
						$c = 'sata_hard_drives';
							
					}
					elseif($sc == 'Solid State Drives' )
					{
						$c = 'ssd';
					}
					elseif($sc == 'DVD-RW Drives' )
					{
						$c = 'optical_drives';
					}
					elseif($sc == 'Card Reader' )
					{
						$c = 'card_readers';
					}
					elseif($sc == 'USB Memory Sticks' )
					{
						$c = 'flash_drives';
					}
					break;
				case 'Cases-Peripherals':
					if($sc == 'Combo' || $sc == 'Keyboard' || $sc == 'Mouse')
					{
						$c = 'keyboard_mouse';
					}
					elseif($sc == 'Power Supplies')
					{
						$c = 'power_supplies';
					}
					elseif($sc == 'PC Cases')
					{
						$c = 'cases';
					}
					elseif($sc == 'PC Cases Options' && $B2b_sc == 'Συστήματα Ψύξης')
					{
						$c = 'fans';
					}
					break;
				case 'Components':
					if($sc == 'Motherboard for Intel')
					{
						$c = 'motherboards';
					}
					elseif($sc == 'VGA ATI' || $sc == 'VGA Nvidia')
					{
						$c = 'graphic_cards';
					}
					elseif($sc == 'CPU Intel')
					{
						$c = 'cpu';
					}
					elseif($sc == 'Memory Modules' )
					{
						$c = 'memories';
					}
					break;
				default:
					$c = $cat;
					break;
			}

			if($c!=$cat){

				$availability = $this->makeAvailability((string) trim($product->availability), 'oktabit');

				if(!$availability){
					$f=0;
					continue;
				}

				$net_price = str_replace(",", ".", $product->timi);
				$net_price = (string) trim($net_price);

				$recycle_tax = str_replace(",", ".", $product->anakykl);
				$pn = (string) trim($product->part_no);
				$pn = ($pn == '') ? (string) trim($product->code): $pn;
				$description = "";
				$brand = (string) trim($product->brand);
				$title = (string) trim($product->titlos);
				$product_url = "";
				$code = (string) trim($product->code);

				// Get The Description
				foreach($desc_xml->children() as $perigrafes) {
					$okt_desc_code = (string) trim($perigrafes->code);
					
					if($code == $okt_desc_code)
					{
						$description = strip_tags((string) (trim($perigrafes->perigrafi)));
						continue;
					}
				}

				// Get the characteristics
				$chars_array = $this->addProductChars($c, $code, $char_xml);

				//1. Live
				$supplier = 'oktabit';
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability,
						'recycle_tax'=>(string) trim($recycle_tax) ,
						'supplier' =>'oktabit',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'oktabit');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}

				//Array for categories table

				$okt_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => $product_url,
					'code' => $code,
					'net_price'=>$net_price
				);

				if ($c=='software')
				{
					$okt_product['type'] = $sc;
					$okt_product['dist_type'] = $dist_type;
					$okt_product['shipping_class'] = 4644;

					if (strstr ($title,'DSP'))
						$okt_product['dist_type'] = 'DSP';
					elseif(strstr ($title,'Reseller Option Kit') || strstr ($title,'ROK'))
						$okt_product['dist_type'] = 'ROK';
				}

				if ($c == 'memories' && $B2b_sc == 'Εξαρτήματα Servers')
				{
					$okt_product['description'] = 'Εξαρτήματα Servers';
				}

				//2. New products for charateristics tables that load Sku module

				$insert = $this->addProduct ($okt_product, $chars_array, $f, 'oktabit');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}

			}//if $c!=$cat
				
			$f=0;
		
		}//end foreach
		
		$this->sendImportedProductsByMail($newProducts);

		 echo "Finnished Oktabit";

    }

    public function logicom(){

    	$this->load->view('upload_logicom_xml', array('error' => ' ' ));
    }

	public function import_logicom($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('logicom');

		}

		$newProducts = array();
		$i=0;
		$k=0;
		//$f=0;
		

		foreach($xml->children()->children()->children() as $product) {
			
			set_time_limit(50);
			//Rename categories for ETD.gr

			$c = $cat = $product->Details->CategoryList->attributes()->{'Name'};
			$sc = '';

			switch ($cat) {

				/*case 'Computers- / Accessories- / Bags and Cases':
					$c = 'carrying_cases';
					break;*/
				case 'Computers- / Computers- / All In One':
				case 'Computers- / Computers- / Desktop':
				case 'Computers- / Computers- / Mini PC':
				case 'Computers- / Computers- / Workstations':
					$c = 'desktops';
					break;
				case 'Computers- / Computers- / Notebooks':
					$c = 'laptops';
					break;
				case 'Computers- / Computers- / Tablet':
					$c = 'tablets';
					break;
				case 'Computers- / Servers- / Server':
					$c = 'servers';
					break;
				case 'Computers- / Software  /  Security- / Antivirus Solutions':
					$c = 'software';
					$sc = 'Antivirus';
					break;
				case 'Computers- / Software  /  Security- / Applications':
					$c = 'software';
					$sc = 'Εφαρμογές γραφείου';
					break;
				case 'Computers- / Software  /  Security- / Operating Systems':
					$c = 'software';
					$sc = 'Λειτουργικά Συστήματα';
					break;
				case 'Computers- / Software  /  Security- / Server Software':
					$c = 'software';
					$sc = 'Λογισμικό Server';
					break;
				case 'Consumables- / Inkjet Cartridges - / HP':
					$c = 'cartridges';
					break;
				case 'Consumables- / Laserjet Cartridges- / HP':
					$c = 'toners';
					break;
				/*case 'Networking- / Routing':
					$c = 'routers';
					break;
				case 'Networking- / Switching':
					$c = 'switches';
					break;
				case 'Peripherals- / Accessories- / Keyboards':
				case 'Peripherals- / Accessories- / Mouse':
					$c = 'keyboard_mouse';
					break;*/
				case 'Peripherals- / Power Protection- / UPS':
					$c = 'ups';
					break;
				case 'Peripherals- / Printers Office- / Multifunctions':
					$c = 'multifunction_printers';
					break;
				case 'Peripherals- / Printers Office- / Printers':
					$c = 'printers';
					break;
				case 'Telecoms GSM- / SmartPhones':
					$c = 'smartphones';
					break;
				case strpos($cat,'Peripherals- / Monitors'):
					$c = 'monitors';
					break;
				default:
					$c = $cat;
					break;
			}

			if($c!=$cat){

	    		$prd = $product->attributes();  

				$availability = $this->makeAvailability((string) trim($prd["Availability"]), 'logicom');

				if(!$availability){
					continue;
				}

	    		$pn = (string) trim($prd["SKU"]);
	    		$title = (string) trim($prd["Name"]);
	    		$description = (string) trim($prd["Description"]);
	    		$net_price = $prd["Price"];
	    		$availability = $availability;
	    		$recycle_tax = $prd["RT"];
	    		$ManufacturerList = (string) trim($product->Details->ManufacturerList->attributes()->{'Name'});


	    		if(strpos($title, 'Ent') && $c == 'multifunction_printers'){
	    			continue;	
	    		}
	    		 

	    		if($c == 'cartridges' || $c == 'toners')
	    		{
	    			$brand = explode('- /',trim($ManufacturerList))[1];
	    			
	    			if (strpos($brand, 'SUPPLIES'))
	    				$brand = explode('SUPPLIES',trim($brand))[0];
	    		}
	    		else
	    		{
	    			$brand = explode('- /',trim($ManufacturerList))[0];
	    		}

	    		
	    		// IMAGE
	    		$imageUrl = $product->Details->Images->Image[2]->attributes()->{'URL'};
	    		if($imageUrl == "")
	    			$imageUrl = $product->Details->Images->Image[1]->attributes()->{'URL'};
	    		
	    		// PRODUCT URL
	    		$product_url = $product->Details->ProductURL->attributes()->{'URL'};
				
				//1. Live
	    		$supplier = 'logicom';
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>$recycle_tax ,
						'supplier' =>'logicom',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'logicom');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}

				$log_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => (string) trim($product_url),
					'net_price'=>$net_price
				);

				if ($c == 'software')
				{
					$log_product['type'] = $sc;
					$log_product['shipping_class'] = 4644;
					if (strstr ($title,'DSP'))
						$log_product['dist_type'] = 'DSP';
					elseif(strstr ($title,'Reseller Option Kit') || strstr ($title,'ROK'))
						$log_product['dist_type'] = 'ROK';
					else
						$log_product['dist_type'] = '';

				}

				//2. New products for charateristics tables that load Sku module
				//$this->AddProduct ($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $imageUrl, 'logicom');
				$insert = $this->addProduct ($log_product, array(), $imageUrl, 'logicom');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}

			}//if $c==$cat
		
		}//end foreach

		$this->sendImportedProductsByMail($newProducts);

		echo "Finnished updating Logicom-Enet.";
		
    }


    public function ddc(){

    	$this->load->view('upload_ddc_xml', array('error' => ' ' ));
    }

	public function import_ddc($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('ddc');

		}

		$newProducts = array();
		$i=0;
		$length = 0;
		$sc = $cable_cat = $dimensions = $size = $height = $ports = $patch_type = '';

		foreach($xml->children() as $product) {

			$length = 0;
			$sc = $cable_cat = $dimensions = $size = $height = $ports = $patch_type = '';

			$c = $cat = $product->category->attributes()->{'name'};

			switch ($cat) {
				case 'LC / LC SingleMode':
				case 'LC / SC MultiMode':
				case 'LC / SC SingleMode':
				case 'LC / ST MultiMode':
				case 'LC / ST SingleMode':
				case 'ST / SC MultiMode':
				case 'ST / SC SingleMode':
				case 'ST / ST MultiMode':
					$c = 'cables';
					$sc = 'Οπτική ΄Ινα';
					break;
				case '0,25m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 0.25;
					break;
				case '0,50m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 0.50;
					break;
				case '1,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 1.00;
					break;
				case '2,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 2.00;
					break;
				case '3,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 3.00;
					break;
				case '5,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 5.00;
					break;
				case '7,50m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 7.50;
					break;
				case '10,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 10.00;
					break;
				case '15,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 15.00;
					break;
				case '20,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = 20.00;
					break;
				case '15,00 / 20,00m':
				case '15,00m / 20.00m / 30.00m':
				case '30,00m / 40,00m / 50,00m':
					$c = 'cables';
					$sc = 'Patch Cord';
					$length = -1;
					break;
				case 'Cat.7 S/FTP':
					$c = 'cables';
					$sc = 'Καλώδιο Εγκατάστασης (Κουλούρα)';
					$cable_cat = 'Cat.7';
					break;
				case 'Cat..5e':
					$c = 'cables';
					$sc = 'Καλώδιο Εγκατάστασης (Κουλούρα)';
					$cable_cat = 'Cat.5e';
					break;
				case 'Cat..6/6A':
					$c = 'cables';
					$sc = 'Καλώδιο Εγκατάστασης (Κουλούρα)';
					$cable_cat = 'Cat.6 / 6a';
					break;
				case 'Cat. 3 Patch Panels / Outlet':
					$c = 'patch_panels';
					$cable_cat = 'Cat.3';
					break;
				case 'Cat..5e Patch Panel':
					$c = 'patch_panels';
					$cable_cat = 'Cat.5e';
					break;
				case 'Cat..6/6A Patch Panel':
					$c = 'patch_panels';
					$cable_cat = 'Cat.6 / 6a';
					break;
				case 'Patch Panel':
					$c = 'patch_panels';
					break;
				/*case '10” Cabinets – Accessories':
					$c = 'racks';
					break;*/
				case 'DELTA S 600x1000':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '600x1000';
					break;
				case 'DELTA S 600x600':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '600x600';
					break;
				case 'DELTA S 600x800':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '600x800';
					break;
				case 'DELTA S 600x900':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '600x900';
					break;
				case 'DELTA S 800x1000':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '800x1000';
					break;
				case 'DELTA S 800x800':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '800x800';
					break;
				case 'DELTA S 800x900':
					$c = 'racks';
					$sc = 'Free Standing';
					$dimensions = '800x900';
					break;
				case 'Base / Castors':
					$c = 'racks';
					$sc = 'Base / Castors';
					break;
				case 'Flat pack':
					$c = 'racks';
					$sc = 'Wall Mount';
					break;
				case 'One-sectioned':
					$c = 'racks';
					$sc = 'Wall Mount';
					break;
				default:
					$c = $cat;
					break;
			}

			if($c!=$cat){

				$prd = $product->attributes();

				$availability = $this->makeAvailability((string) trim($prd['stock']), 'ddc');

			    if(!$availability){
					continue;
				}

				$pn = (string) trim($prd['code']);
			    $title = (string) trim($prd['name']);
			    $brand = (string) trim($prd['brand']);
			    //$prd['price'];
			    $net_price = (string) trim($prd['finalPrice']);
			    $color = (string) trim($prd['colors']);
			    $product_url = (string) trim($prd['attachment1']);

			    //$prd['attachment2'];
				$description = strip_tags((string) trim($product->description)); //to check if image exist in description
			    
			    $image_array = array(); 

			    for ($i = 0; $i < 4; $i ++)
			    {
			    	$image_nr = 'image'.$i;
			    	if ($prd[$image_nr])
			    		$image_array[$i]=(string) trim($prd[$image_nr]);
			    }

			    //Change the category for some products
			    if ($c == 'racks'){
			    	if(strpos($title, 'Patch Panel'))
			    		$c = 'patch_panels';
			    }

			    //Start Of Fix title
				$trimmed_pn = ltrim($pn, "LL-");
				$trimmed_pn = ltrim($pn, "0");

				$title = ltrim($title, $trimmed_pn);
		    	// End of Fix Title

			    // Make the characteristics
			    if ($c == 'cables' && $length < 0.1){

			    	$whereIs = 0;
			    	if ($sc =='Patch Cord' && $length == -1 ) //length
				    {
				    	if(strpos( $title, '0m '))
				    		$whereIs = strpos( $title, '0m ')+1;
				    	elseif(strpos( $title, 'm '))
				    		$whereIs = strpos( $title, 'm ');
				    	$length = substr($title, 0, $whereIs);
				    	$length = substr($length, strrpos( $length, ' '));
				    }
				    else{

				    	if (strpos( $title, 'Cable '))
				   			$whereIs = strpos( $title, 'Cable ') + strlen('Cable ');

				    	$length = substr($title, $whereIs);

				   		if(strpos( $length, 'm '))
				   			$whereIs = strpos( $length, 'm ');
				   		elseif(strpos( $length, 'm,'))
				   			$whereIs = strpos( $length, 'm,');
				    	elseif(strpos( $length, ' Meter'))
				    		$whereIs = strpos( $length, ' Meter');

					   	$length = substr($length, 0, $whereIs);

				   		if(strlen($length) > 3)
				   			$length = substr($length, strrpos( $length, ' '));
				    }
				    $length = str_replace(",",".",$length);
				    $length = (float)$length;
				}
				elseif($c == 'patch_panels'){
					if($cable_cat == ''){
						if(strpos($title, 'Cat')){
							$cable_cat = substr($title, strpos($title, 'Cat'));
							$cable_cat = substr($cable_cat, 0, strpos( $cable_cat, ' '));
						}
					}

					if(strpos($title, 'port')){
						$ports = substr($title, 0, strpos($title, 'port'));
						$ports = substr($ports, strrpos( $ports, ' ')+1);
					}
					elseif(strpos($title, '-Port')){
						$ports = substr($title, 0, strpos($title, '-Port'));
						$ports = substr($ports, strrpos( $ports, ' ')+1);
					}
					elseif(strpos($title, 'xRJ45')){
						$ports = substr($title, 0, strpos($title, 'xRJ45'));
						$ports = substr($ports, strrpos( $ports, ' ')+1);
					}

					if(strpos($title, '"')){
						$size = substr($title, 0, strpos($title, '"'));
						$size = substr($size, strrpos( $size, ' ')+1);
					}
					elseif(strpos($title, '\'')){
						$size = substr($title, 0, strpos($title, '\''));
						$size = substr($size, strrpos( $size, ' ')+1);
					}
					elseif(strpos($title, ' inch')){
						$size = substr($title, 0, strpos($title, ' inch'));
						$size = substr($size, strrpos( $size, ' ')+1);
					}

					if(strpos($title, 'Unshielded'))
						$patch_type = 'Unshielded';
					elseif(strpos($title, 'Shielded'))
						$patch_type = 'Shielded';
				}
				elseif($c == 'racks' ){

					if ($sc != 'Base / Castors')
					{
						$size = 19;
					
						if(strpos($title, 'U'))
						{
							$height = substr($title, 0, strpos($title, 'U')+1);
							$height = substr($height, strrpos( $height, ' ')+1);
						}
					}

					if($dimensions =='')
					{
						if(strpos($title, '0x'))
						{
							$dimensions = substr($title, strpos($title, '0x')-3);
							$dimensions = trim($dimensions);

							$space_where = $comma_where = 1000;
							if($space_where = strpos( $dimensions, ' ')+1)
							if($comma_where = strpos( $dimensions, ','))

							$where_is = min($space_where, $comma_where);
							$dimensions = substr($dimensions, 0, $where_is);

							$dimensions = trim($dimensions);
						}
					}
				}

				$chars_array = array(); 
				
				if($sc)
					$chars_array['subcategory'] = $sc;
				if($length)
					$chars_array['length'] = number_format((float)$length, 2, '.', '');
				if($cable_cat)
					$chars_array['cable_category'] = $cable_cat;
				if($dimensions)
					$chars_array['dimensions'] = $dimensions;
				if($size)
					$chars_array['size'] = $size;
				if($height)
					$chars_array['height'] = $height;
				if($ports)
					$chars_array['ports'] = $ports;
				if($patch_type)
					$chars_array['patch_type'] = $patch_type;

				// End Of Characteristics

			    /////////////////////
			    //1. Live
				$supplier = 'ddc';
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>'' ,
						'supplier' =>'ddc',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'ddc');
					$this->db->delete('live', $live);
					$this->db->insert('live', $live);

					unset($live);
				}

				$ddc_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => $product_url,
					'net_price'=>$net_price
				);

				//2. New products for charateristics tables that load Sku module
				$insert = $this->addProduct ($ddc_product, $chars_array, $image_array, 'ddc');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}
			}
		}

		$this->sendImportedProductsByMail($newProducts);

		echo "Finnished updating Digital Data Communication.";

	}

	public function braintrust(){

    	$this->load->view('upload_braintrust_xml', array('error' => ' ' ));
    }

    public function import_braintrust($path){

    	if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('braintrust');

		}

		$newProducts = array();
		$i=0;


		foreach($xml->children() as $product) {
			$availability=false;
			set_time_limit(50);
			//Rename categories for ETD.gr

			$cat = (string) trim($product->Category);
			$sc = trim((string)$product->MainCategory);

			$c = $cat;
			
			$brand = (string) trim($product->Supplier);

			switch ($cat) {
				case 'Notebook':
					if($brand == 'MSI')
						$c = 'laptops';
					else
						$c = $cat;
				break;
				default :
					$c = $cat;
				break;
			}

			if($c!=$cat){

				$availability = $this->makeAvailability((string) trim($product->availability), 'braintrust');

				if(!$availability){
					continue;
				}

				//$code = (string) trim($product->code);
				//$code = (string) trim($product->SKU);
				$description = (string) trim($product->Description);
				$title = substr($description, strpos($description, 'NB '), strpos($description, ', '));
				$title = "MSI ".$title;
				$net_price = (string) trim($product->timi);
				$availability = $availability;
				$pn = (string) trim($product->SKU);
				$imageUrl = (string) trim($product->Image);
				$brand = (string) trim($product->Supplier);

				//1. Live
				$supplier = 'braintrust';
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'recycle_tax'=>0 ,
						'availability'=>$availability,
						'supplier' =>'braintrust',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'braintrust');
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}

				//Array for categories table

				$braintrust_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					'product_url' => '',
					'net_price'=>$net_price
				);

				//2. New products for charateristics tables that load Sku module

				$insert = $this->addProduct ($braintrust_product, array(), $imageUrl, 'braintrust');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}
			} 		
		} //end foreach

		$this->sendImportedProductsByMail($newProducts);

		echo "Finnished updating Braintrust.";
    }

    public function aci(){
    	$this->load->view('upload_aci_xml', array('error' => ' ' ));
    }

    public function import_aci($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('aci');

		}

		$newProducts = array();
		$i=0;


		foreach($xml->children() as $product) {
			$availability=false;
			set_time_limit(50);
			
			//Rename categories for ETD.gr

			$cat = (string) trim($product->Category);
			$c = $cat;

			switch ($cat) {
				case 'Μελάνια για inkjet εκτυπωτές':
					$c = 'cartridges';
					break;
				case 'Τόνερ':
					$c = 'toners';
					break;
				default :
					$c = $cat;
					break;
			}

			if($c!=$cat){

				$availability = $this->makeAvailability((string) trim($product->Availability), 'aci');

				if(!$availability){
					continue;
				}
				$code = (string) trim($product->Code);
				$pn = (string) trim($product->OEM);
				$title = (string) trim($product->Item);
				$net_price = (string) trim($product->Price);
				$availability = $availability;

				$arr = explode(" ", $title, 2);
				$brand = $arr[0];

				$i++;
				if($i>20)
					continue;

				$imageUrl = 'http://www.acihellas.gr/images/products/originals/' . $pn . '.jpg';
				$content = @file_get_contents($imageUrl);
				if ($content === false) 
					{ 
						echo "NOT";
						//return false; 
					}
					else 
					{
						echo "YES";
						//return true;
					}

				/*
				if (get_headers($imageUrl)[0]!='HTTP/1.1 200 OK')
				{
					$imageUrl = 'http://www.acihellas.gr/images/products/originals/' . $code . '.jpg';
					if (get_headers($imageUrl)[0]!='HTTP/1.1 200 OK')
						$imageUrl = '';
				}
				*/
				echo $imageUrl.'<br>';

				//1. Live
				$supplier = 'aci';

	/*			if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>'' ,
						'supplier' =>'aci',
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', 'aci');
					$this->db->delete('live', $live);
					$this->db->insert('live', $live);

					unset($live);
				}

				//Array for categories table
				$aci_product = array(
					'category' => $c,
					'product_number' => $pn,
					'brand' => $brand,
					'title' => $title,
					'net_price'=>$net_price,
				);

				//2. New products for charateristics tables that load Sku module
				$insert = $this->addProduct ($aci_product, array(), $imageUrl, 'aci');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}
*/
			}
		}
    }

    public function copiers(){
    	$this->load->view('upload_copiers', array('error' => ' ' ));
    }

    public function import_copiers($path){

		if($xml = $this->xml($path)){
			
			$images = array();
			
			$this->updateLive('konica');

		}

		$newProducts = array();
		$i=0;


		foreach($xml->children() as $product) {
			$availability=false;
			set_time_limit(50);
			
			//Rename categories for ETD.gr

			
			$c = 'copiers';

			

			if($c){

				//$availability = $this->makeAvailability((string) trim($product->Availability), 'aci');
				$availability = "Κατόπιν παραγγελίας χωρίς διαθεσιμότητα";

				if(!$availability){
					continue;
				}

				

				$Name = (string) trim($product->Name);
				$Image = (string) trim($product->Image);
				$PRODUCT_URL = (string) trim($product->PRODUCT_URL);
				$PRODUCT_URL_PDF = (string) trim($product->PRODUCT_URL_PDF);
				$URL_SUPPORT = (string) trim($product->URL_SUPPORT);
				$THL__SUPPORT = (string) trim($product->THL__SUPPORT);
				$Brand = (string) trim($product->Brand);
				$copying_process = (string) trim($product->copying_process);
				$Toner_Writing_System = (string) trim($product->Toner_Writing_System);
				$Colour = (string) trim($product->Colour);
				$Speed_Copy_Print_A4_Monochrome = (string) trim($product->Speed_Copy_Print_A4_Monochrome);
				$Speed_Copy_Print_A4_Colour = (string) trim($product->Speed_Copy_Print_A4_Colour);
				$Speed_Copy_Print_A3_Monochrome = (string) trim($product->Speed_Copy_Print_A3_Monochrome);
				$Speed_Copy_Print_A3_Colour = (string) trim($product->Speed_Copy_Print_A3_Colour);
				$Duplex_A4 = (string) trim($product->Duplex_A4);
				$Duplex_A4_Colour = (string) trim($product->Duplex_A4_Colour);
				$First_copy_time_sec_ = (string) trim($product->First_copy_time_sec_);
				$First_copy_time_sec__Colour = (string) trim($product->First_copy_time_sec__Colour);
				$Warm_up_time_sec_ = (string) trim($product->Warm_up_time_sec_);
				$Copy_resolution_dpi = (string) trim($product->Copy_resolution_dpi);
				$graduations = (string) trim($product->graduations);
				$Multi_copy = (string) trim($product->Multi_copy);
				$original_Size = (string) trim($product->original_Size);
				$A3_Support = (string) trim($product->A3_Support);
				$magnification = (string) trim($product->magnification);
				$copy_features = (string) trim($product->copy_features);
				$Resolution_dpi = (string) trim($product->Resolution_dpi);
				$print_Processor = (string) trim($product->print_Processor);
				$Page_Description_Language = (string) trim($product->Page_Description_Language);
				$Operating_Systems = (string) trim($product->Operating_Systems);
				$fonts_printer = (string) trim($product->fonts_printer);
				$print_features = (string) trim($product->print_features);
				$Mobile_printing = (string) trim($product->Mobile_printing);
				$Scan_Speed_scans___min_Colour = (string) trim($product->Scan_Speed_scans___min_Colour);
				$Scan_Speed_scans___min_M_C = (string) trim($product->Scan_Speed_scans___min_M_C);
				$Scan_Resolution_dpi = (string) trim($product->Scan_Resolution_dpi);
				$scanning_Methods = (string) trim($product->scanning_Methods);
				$file_Types = (string) trim($product->file_Types);
				$scan_destinations = (string) trim($product->scan_destinations);
				$Scan_operations = (string) trim($product->Scan_operations);
				$standard_Fax = (string) trim($product->standard_Fax);
				$Fax_Broadcast = (string) trim($product->Fax_Broadcast);
				$Fax_Resolution_dpi = (string) trim($product->Fax_Resolution_dpi);
				$Compression_methods_Fax = (string) trim($product->Compression_methods_Fax);
				$Fax_modem_Kvps = (string) trim($product->Fax_modem_Kvps);
				$Fax_Destinations = (string) trim($product->Fax_Destinations);
				$Fax_functions = (string) trim($product->Fax_functions);
				$box_mode = (string) trim($product->box_mode);
				$Max__user_boxes = (string) trim($product->Max__user_boxes);
				$Type_box_s_system = (string) trim($product->Type_box_s_system);
				$User_Box_Functions = (string) trim($product->User_Box_Functions);
				$Systems_Memory_MB = (string) trim($product->Systems_Memory_MB);
				$Systems_Memory_MB_optionally = (string) trim($product->Systems_Memory_MB_optionally);
				$HDD_GB = (string) trim($product->HDD_GB);
				$Interfaces_standard = (string) trim($product->Interfaces_standard);
				$Interfaces_optionally = (string) trim($product->Interfaces_optionally);
				$network_Protocols = (string) trim($product->network_Protocols);
				$Frame_types = (string) trim($product->Frame_types);
				$Automatic_document_feeder = (string) trim($product->Automatic_document_feeder);
				$paper_Size = (string) trim($product->paper_Size);
				$Printable_paper_weight = (string) trim($product->Printable_paper_weight);
				$Paper_input_capacity = (string) trim($product->Paper_input_capacity);
				$Standard_paper_cassettes = (string) trim($product->Standard_paper_cassettes);
				$Standard_paper_cassettes_optionally = (string) trim($product->Standard_paper_cassettes_optionally);
				$Automatic_duplexing = (string) trim($product->Automatic_duplexing);
				$Finishing_modes_optional = (string) trim($product->Finishing_modes_optional);
				$Output_capacity_with_finisher = (string) trim($product->Output_capacity_with_finisher);
				$Output_capacity_without_finisher = (string) trim($product->Output_capacity_without_finisher);
				$Staple = (string) trim($product->Staple);
				$Stapling_output_capacity = (string) trim($product->Stapling_output_capacity);
				$Letter_fold = (string) trim($product->Letter_fold);
				$Letter_fold_capacity = (string) trim($product->Letter_fold_capacity);
				$Booklet = (string) trim($product->Booklet);
				$Booklet_output_capacity = (string) trim($product->Booklet_output_capacity);
				$Monthly_production_volume = (string) trim($product->Monthly_production_volume);
				$Max_Monthly_production_volume = (string) trim($product->Max_Monthly_production_volume);
				$Lifetime_Toner_Black = (string) trim($product->Lifetime_Toner_Black);
				$Lifetime_Toner_CMY = (string) trim($product->Lifetime_Toner_CMY);
				$Developer_lifetime_Black = (string) trim($product->Developer_lifetime_Black);
				$Developer_lifetime_CMY = (string) trim($product->Developer_lifetime_CMY);
				$Drum_lifetime_Black = (string) trim($product->Drum_lifetime_Black);
				$Drum_lifetime_CMY = (string) trim($product->Drum_lifetime_CMY);
				$Lifetime_Imaging_Unit_Black = (string) trim($product->Lifetime_Imaging_Unit_Black);
				$Lifetime_Imaging_Unit_CMY = (string) trim($product->Lifetime_Imaging_Unit_CMY);
				$power_consumption = (string) trim($product->power_consumption);
				$Dimensions = (string) trim($product->Dimensions);
				$System_weight_kg = (string) trim($product->System_weight_kg);
				$Safety = (string) trim($product->Safety);
				$Accounts = (string) trim($product->Accounts);
				$Accounts_Software  = (string) trim($product->Accounts_Software);
				$System_weight_kg = (string) trim($product->System_weight_kg);
				$Safety = (string) trim($product->Safety);
				$Accounts = (string) trim($product->Accounts);
				$Accounts_Software = (string) trim($product->Accounts_Software);


				$pn = (string) trim($product->product_number);

				

				$imageUrl = $Image;
				

				
				//1. Live
				$supplier = 'konica';

				if($this->checkLiveProduct($pn, '', $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>'' ,
						'availability'=>$availability ,
						'recycle_tax'=>'' ,
						'supplier' =>$supplier ,
						'status' => 'publish',
						'delete_flag'=>0
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', $supplier);
					$this->db->delete('live', $live);
					$this->db->insert('live', $live);

					unset($live);
				}

				//Array for categories table
				$copiers_product = array(
					'category' => $c,
					'product_number'=>$pn ,
					'Name' => $Name ,
					'PRODUCT_URL' => $PRODUCT_URL ,
					'PRODUCT_URL_PDF' => $PRODUCT_URL_PDF ,
					'URL_SUPPORT' => $URL_SUPPORT ,
					'THL__SUPPORT' => $THL__SUPPORT ,
					'Brand' => $Brand ,
					'copying_process' => $copying_process ,
					'Toner_Writing_System' => $Toner_Writing_System ,
					'Colour' => $Colour ,
					'Speed_Copy_Print_A4_Monochrome' => $Speed_Copy_Print_A4_Monochrome ,
					'Speed_Copy_Print_A4_Colour' => $Speed_Copy_Print_A4_Colour ,
					'Speed_Copy_Print_A3_Monochrome' => $Speed_Copy_Print_A3_Monochrome ,
					'Speed_Copy_Print_A3_Colour' => $Speed_Copy_Print_A3_Colour ,
					'Duplex_A4' => $Duplex_A4 ,
					'Duplex_A4_Colour' => $Duplex_A4_Colour ,
					'First_copy_time_sec_' => $First_copy_time_sec_ ,
					'First_copy_time_sec__Colour' => $First_copy_time_sec__Colour ,
					'Warm_up_time_sec_' => $Warm_up_time_sec_ ,
					'Copy_resolution_dpi' => $Copy_resolution_dpi ,
					'graduations' => $graduations ,
					'Multi_copy' => $Multi_copy ,
					'original_Size' => $original_Size ,
					'A3_Support' => $A3_Support ,
					'magnification' => $magnification ,
					'copy_features' => $copy_features ,
					'Resolution_dpi' => $Resolution_dpi ,
					'print_Processor' => $print_Processor ,
					'Page_Description_Language' => $Page_Description_Language ,
					'Operating_Systems' => $Operating_Systems ,
					'fonts_printer' => $fonts_printer ,
					'print_features' => $print_features ,
					'Mobile_printing' => $Mobile_printing ,
					'Scan_Speed_scans___min_Colour' => $Scan_Speed_scans___min_Colour ,
					'Scan_Speed_scans___min_M_C' => $Scan_Speed_scans___min_M_C ,
					'Scan_Resolution_dpi' => $Scan_Resolution_dpi ,
					'scanning_Methods' => $scanning_Methods ,
					'file_Types' => $file_Types ,
					'scan_destinations' => $scan_destinations ,
					'Scan_operations' => $Scan_operations ,
					'standard_Fax' => $standard_Fax ,
					'Fax_Broadcast' => $Fax_Broadcast ,
					'Fax_Resolution_dpi' => $Fax_Resolution_dpi ,
					'Compression_methods_Fax' => $Compression_methods_Fax ,
					'Fax_modem_Kvps' => $Fax_modem_Kvps ,
					'Fax_Destinations' => $Fax_Destinations ,
					'Fax_functions' => $Fax_functions ,
					'box_mode' => $box_mode ,
					'Max__user_boxes' => $Max__user_boxes ,
					'Type_box_s_system' => $Type_box_s_system ,
					'User_Box_Functions' => $User_Box_Functions ,
					'Systems_Memory_MB' => $Systems_Memory_MB ,
					'Systems_Memory_MB_optionally' => $Systems_Memory_MB_optionally ,
					'HDD_GB' => $HDD_GB ,
					'Interfaces_standard' => $Interfaces_standard ,
					'Interfaces_optionally' => $Interfaces_optionally ,
					'network_Protocols' => $network_Protocols ,
					'Frame_types' => $Frame_types ,
					'Automatic_document_feeder' => $Automatic_document_feeder ,
					'paper_Size' => $paper_Size ,
					'Printable_paper_weight' => $Printable_paper_weight ,
					'Paper_input_capacity' => $Paper_input_capacity ,
					'Standard_paper_cassettes' => $Standard_paper_cassettes ,
					'Standard_paper_cassettes_optionally' => $Standard_paper_cassettes_optionally ,
					'Automatic_duplexing' => $Automatic_duplexing ,
					'Finishing_modes_optional' => $Finishing_modes_optional ,
					'Output_capacity_with_finisher' => $Output_capacity_with_finisher ,
					'Output_capacity_without_finisher' => $Output_capacity_without_finisher ,
					'Staple' => $Staple ,
					'Stapling_output_capacity' => $Stapling_output_capacity ,
					'Letter_fold' => $Letter_fold ,
					'Letter_fold_capacity' => $Letter_fold_capacity ,
					'Booklet' => $Booklet ,
					'Booklet_output_capacity' => $Booklet_output_capacity ,
					'Monthly_production_volume' => $Monthly_production_volume ,
					'Max_Monthly_production_volume' => $Max_Monthly_production_volume ,
					'Lifetime_Toner_Black' => $Lifetime_Toner_Black ,
					'Lifetime_Toner_CMY' => $Lifetime_Toner_CMY ,
					'Developer_lifetime_Black' => $Developer_lifetime_Black ,
					'Developer_lifetime_CMY' => $Developer_lifetime_CMY ,
					'Drum_lifetime_Black' => $Drum_lifetime_Black ,
					'Drum_lifetime_CMY' => $Drum_lifetime_CMY ,
					'Lifetime_Imaging_Unit_Black' => $Lifetime_Imaging_Unit_Black ,
					'Lifetime_Imaging_Unit_CMY' => $Lifetime_Imaging_Unit_CMY ,
					'power_consumption' => $power_consumption ,
					'Dimensions' => $Dimensions ,
					'System_weight_kg' => $System_weight_kg ,
					'Safety' => $Safety ,
					'Accounts' => $Accounts ,
					'Accounts_Software ' => $Accounts_Software  ,
					'System_weight_kg' => $System_weight_kg ,
					'Safety' => $Safety ,
					'Accounts' => $Accounts ,
					'Accounts_Software' => $Accounts_Software,
					'shipping_class' => 4682
				);

				//2. New products for charateristics tables that load Sku module
				$insert = $this->addProduct ($copiers_product, array(), $imageUrl, $supplier);

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}

			}
		}
    }

    private function sendImportedProductsByMail($newProducts){

    	if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα</h2>';
		
			foreach ($newProducts as $key => $value){
				$message .= $key.' : '.$value.' (<a href="'.base_url().'/extract/xml/'.$key.'/new">Προβολή XML)</a><br>';
			}

			Modules::run('emails/send','Νέα προϊόντα',$message);
		}
    }



    private function checkLiveProduct($pn, $price, $supplier){

    	$query = $this->db->get_where('live', array('product_number' => $pn), 1, 0);

    	if($query->num_rows()>0){

    		foreach ($query->result() as $row)

				{	

					if($row->supplier == 'etd' || $row->supplier == 'out' ){

						return false;
					} 


					
					$price = (float) $price;
					// Check if product has lower price from a product already is stock and supplier = etd.
				        
				    if($row->net_price >= $price || $row->supplier == $supplier){

				        $this->db->where('id',$row->id);
				        $this->db->delete('live');
				        return true;
				    }else{
				        return false;
				    }

				}

    	}else{
    		return true;
    	}
	
	}//private function checkLiveProduct($pn, $price){


	/*private function insertProductCategory($table, $pn, $data){

		echo $table.'<br />';

		if ($this->db->table_exists($table))
		{

			$query = $this->db->get_where($table, $pn);

	    	if($query->num_rows()<1){

	    		$this->db->insert($table, $data);
	    		return true;
	    		
	    	}

	    	return false;

		} 	
		return false;
    }//private function insertProductCategory(){*/



    public function addProduct($product, $chars_array, $f , $supplier){


    	$insert = false;
    	$c = $product['category'];

		$skuArray = array(
			'category'=> $c,
			'product_number' => $product['product_number'],
			);

				
		// Only For Update
/*
		
		if($c == "power_bank" || $c == "routers" ||$c == "switches" ||$c == "speakers" ||$c == "external_hard_drives" ||$c == "sata_hard_drives" ||$c == "ssd" ||$c == "keyboard_mouse" ||$c == "optical_drives" ||$c == "card_readers" ||$c == "flash_drives" ||$c == "power_supplies" ||$c == "cases" ||$c == "fans" ||$c == "motherboards" ||$c == "graphic_cards" ||$c == "cpu" ||$c == "memories")

		{
			
			if(!$chars_array){
			$chars_array=array();
			}


			//$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);
			//$chars_array = array_merge($chars_array, array("shipping_class"=>$shipping_class));
			//$chars_array = array_merge($chars_array, array("description"=>$product['description']));
			$this->updateProduct($c, $chars_array, $product['product_number']);
			
		}*/
				// End Only for Update
		
		$newSku = Modules::run('sku/checkSku',$skuArray);
		$sku = $newSku['sku'];


		if($newSku['new']){

			
			if($c == 'cartridges' || $c == 'toners'){
				$shipping_class = Modules::run('categories/makeShippingClass', $product, $c);
				
				$categoryData = array(
				'brand'=> $product['brand'], 
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class

				);
			}elseif($c == 'printers' || $c == 'multifunction_printers'){
				
				$price = array('price'=>$product['net_price']);
				
				$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class
				);
			}
			elseif($c == 'software'){

				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'type'=> $product['type'],
				'dist_type'=> $product['dist_type'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $product['shipping_class']
				);

			}elseif($c == "copiers"){
				unset($product['category']);
				$product['sku'] = $sku;

				$categoryData = $product;
			}
			else
			{
				$shipping_class = '';
				if($c == "carrying_cases" || $c == "external_hard_drives" ||
				 $c == "sata_hard_drives" || $c == "ssd" || $c == "speakers" || 
				 $c == "power_banks" || $c == "keyboard_mouse"  || 
				 $c == "routers"  || $c == "switches"  || $c == "laptops"  || $c == "tablets"  || $c == "smartphones" ||
				 $c == "cables" || $c == "patch_panels" || $c == "racks" || $c =="optical_drives" || $c == "card_readers" || $c == "flash_drives" || 
				 $c == "power_supplies" || $c == "cases" || $c == "fans" || $c == "motherboards" || $c == "graphic_cards" || $c == "cpu" || 
				 $c == "memories")		
				$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);


				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class
				);

				if($c == "cables" || $c == "patch_panels" || $c == "racks"){

					$etd_product_url_pdf = '../wp-content/uploads/'.$sku.'.pdf';

					if (!file_exists($etd_product_url_pdf) && $product['product_url']!='')
					{
			    		if(!copy ($product['product_url'], $etd_product_url_pdf))
			    			echo $etd_product_url_pdf.'file not saved';
			    		else
			    		{
			    			$etd_product_url_pdf = 'https://etd.gr/wp-content/uploads/'.$sku.'.pdf';
							$categoryData['product_url_pdf'] = $etd_product_url_pdf;
			    		}
					}

			    	unset($categoryData['supplier_product_url']);
				}

				if($chars_array)
				{
					$categoryData = array_merge($categoryData, $chars_array);
				}
			}

			if(Modules::run("categories/insert", $c, $categoryData)){
				
				$insert = true;


			}
			else{
				echo 'issue';
			}

			
			//3. Add Product Images
			$this->AddProductImages($product, $f, $supplier, $sku);
			
		}//if($sku = Modules::run('sku/checkSku',$skuArray)){
		else
		{
			if($c == 'printers' || $c == 'multifunction_printers'){
				$price = array('price'=>(float)$product['net_price']);
				//print_r($price['price']);
				$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
					
				$this->db->set('shipping_class',$shipping_class);
				$this->db->where('sku',$sku);
				$this->db->update($c);
			}
			/*else if($c == 'memories') //Fix for updating image 
			{
				$mem_images = $this->AddProductImages($product, $f, $supplier, $sku);
			}*/
    	}

    	return $insert;
    }


    private function AddProductImages($product, $f, $supplier, $sku){

    	

    	if ($supplier == 'oktabit' )
    	{
    		while($f < 5){ // because we want to get max 5 images

				if($f=="0"){
					$tail='';
				}else{
					$tail = '_'.$f;
				}

				$imageData = array(
					'src' => "http://oktabit.gr/images/photos/".$product['code']."".$tail.".jpg",
					'sku' => $sku ,
					'brand' => $product['brand'] ,
					'part_number' => $product['product_number'] ,
					'tail' => $tail
				);
				
				if(!$exists=Modules::run('images/getImage',$imageData)){
					$f=5;
				}else{
					
					$f++;
				}
			}
    	}
    	else if ($supplier == 'logicom' || $supplier == 'braintrust')
    	{
    		$imageData = array(
					'src' => $f,
					'sku' => $sku ,
					'brand' => $product['brand'] ,
					'part_number' => $product['product_number'] ,
					'tail' => ""
				);
				
				Modules::run('images/getImage',$imageData);
    	}
    	else if ($supplier == 'ddc')
    	{

    		$image_size = count($f);
	    	for ($i = 0; $i < $image_size; $i++)
	    	{
	    		$imageData = array(
					'src' => $f[$i+1],
					'sku' => $sku ,
					'brand' => $product['brand'] ,
					'part_number' => $product['product_number'] ,
					'tail' => ""
				);
				Modules::run('images/getImage',$imageData);
    		}
    	}
    	elseif( $supplier == 'etd')
    	{
    		$i=0;
    		foreach($f as $image){
		    				

						if($i=="0"){
							$tail='';
						}else{
							$tail = '_'.$i;
						}

						$imageData = array(
							'src' => $image,
							'sku' => $sku ,
							'brand' => $product['brand'] ,
							'part_number' => $product['product_number'] ,
							'tail' => $tail
						);
						
						
						if(!$exists=Modules::run('images/getImage',$imageData)){
							break;
						}

					$i++;
    		}//foreach($f as $image){
    	}//elseif( $supplier == 'etd')
    	elseif( $supplier == 'konica')
    	{
    		$imageData = array(
							'src' => $f,
							'sku' => $sku ,
							'brand' => $product['Brand'] ,
							'part_number' => $product['product_number'] ,
							'tail' => ''
						);
						
						
						Modules::run('images/getImage',$imageData);
    	}//elseif( $supplier == 'etd')
    }


    private function addProductChars($category, $product_code, $char_xml){

    	$is_found = false;

		if ($category == 'carrying_cases'){

			$chars_array = array(
				'type'=>"",
				'size'=>"",
				'material'=>"",
				'colour'=>"",
				'dimensions'=>""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Τύπος':
							$chars_array['type']=$chars_value;
							break;
						case 'Μέγεθος οθόνης':
							$chars_array['size']=$chars_value;
							break;
						case 'Υλικό κατασκευής':
							$chars_array['material']=$chars_value;
							break;
						case 'Χρώμα':
							$chars_array['colour']=$chars_value;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x πάχος, σε mm)':
							$chars_array['dimensions']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'power_bank'){

			$chars_array = array(
				'battery_capacity' => "",
				'recharge_time' => "",
				'charging_output' => "",
				'dimensions' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Χωρητικότητα Μπαταρίας':
							$chars_array['battery_capacity']=$chars_value;
							break;
						case 'Χρόνος επαναφόρτισης ως το 90-100% (ώρες)':
							$chars_array['recharge_time']=$chars_value;
							break;
						case 'Έξοδος φόρτισης':
							$chars_array['charging_output']=$chars_value;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)':
							$chars_array['dimensions']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'routers'){

			$chars_array = array(
				'type' => "",
				'wireless' => "",
				'line_type' => "",
				'lan_ports' => "",
				'wan_ports' => "",
				'supported_protocols' => "",
				'vpn' => "",
				'removable_antenna' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);
					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος':
							$chars_array['type']=$chars_value;
							break;
						case 'Wireless':
							$chars_array['wireless']=$chars_value;
							break;
						case 'Τύπος γραμμής':
							$chars_array['line_type']=$chars_value;
							break;
						case 'Θύρες LAN':
							$chars_array['lan_ports']=$chars_value;
							break;
						case 'Θύρες WAN':
							$chars_array['wan_ports']=$chars_value;
							break;
						case 'Υποστηριζόμενα πρωτόκολλα':
							$chars_array['supported_protocols']=$chars_value;
							break;
						case 'VPN':
							$chars_array['vpn']=$chars_value;
							break;
						case 'Αποσπώμενη κεραία':
							$chars_array['removable_antenna']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'switches'){

			$chars_array = array(
				'ports' => "",
				'network_speed' => "",
				'sfp_ports' => "",
				'manage' => "",
				'poe' => "",
				'layer' => "",
				'rackmount' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Θύρες':
							$chars_array['ports']=$chars_value;
							break;
						case 'Ταχύτητα Δικτύου':
							$chars_array['network_speed']=$chars_value;
							break;
						case 'SFP θύρες':
							$chars_array['sfp_ports']=$chars_value;
							break;
						case 'Manage':
							$chars_array['manage']=$chars_value;
							break;
						case 'POE':
							$chars_array['poe']=$chars_value;
							break;
						case 'Layer':
							$chars_array['layer']=$chars_value;
							break;
						case 'RackMount':
							$chars_array['rackmount']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'speakers'){

			$chars_array = array(
				'type' => "",
				'watt' => "",
				'dolby_digital_decoding' => "",
				'headphones_input' => "",
				'input' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';


					switch ($chars_title) {
						case 'Τύπος ηχείων':
							$chars_array['type']=$chars_value;
							break;
						case 'Ισχύς Watt (RMS)':
							$chars_array['watt']=$chars_value;
							break;
						case 'Dolby Digital Decoding':
							$chars_array['dolby_digital_decoding']=$chars_value;
							break;
						case 'Είσοδος ακουστικών':
							$chars_array['headphones_input']=$chars_value;
							break;
						case 'Είσοδος':
							$chars_array['input']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'external_hard_drives'){

			$chars_array = array(
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'cache' => "",
				'rpm' => "",
				'colour' => "",
				'weight' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					if (strpos($chars_value, '"'))
						$chars_value = str_replace('"', '', $chars_value);

					switch ($chars_title) {
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Cache':
							$chars_array['cache']=$chars_value;
							break;
						case 'Στροφές λειτουργίας':
							$chars_array['rpm']=$chars_value;
							break;
						case 'Χρώμα':
							$chars_array['colour']=$chars_value;
							break;
						case 'Βάρος (γραμμάρια)':
							if($chars_value != '')
								$chars_array['weight']=(string)$chars_value.' gr.';
							else
								$chars_array['weight']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'sata_hard_drives'){

			$chars_array = array(
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'cache' => "",
				'rpm' => "",
				'colour' => "",
				'weight' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					if (strpos($chars_value, '"'))
						$chars_value = str_replace('"', '', $chars_value);

					switch ($chars_title) {
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Cache':
							$chars_array['cache']=$chars_value;
							break;
						case 'Στροφές λειτουργίας':
							$chars_array['rpm']=$chars_value;
							break;
						case 'Συσκευασία':
							$chars_array['packaging']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'ssd'){

			$chars_array = array(
				'capacity_from_to' => "",
				'capacity' => "",
				'connection' => "",
				'size' => "",
				'read_speed' => "",
				'write_speed' => "",
				'manufacture_technology' => "",
				'mtbf' => "",
				'packaging' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if (strpos($chars_value, '"'))
						$chars_value = str_replace('"', '', $chars_value);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Χωρητικότητα (Από εώς)':
							$chars_array['capacity_from_to']=$chars_value;
							break;
						case 'Χωρητικότητα':
							$chars_array['capacity']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Διάσταση':
							$chars_array['size']=$chars_value;
							break;
						case 'Ταχύτητα ανάγνωσης (MB/s)':
							$chars_array['read_speed']=$chars_value;
							break;
						case 'Ταχύτητα εγγραφής (MB/s)':
							$chars_array['write_speed']=$chars_value;
							break;
						case 'Τεχνολογία κατασκευής':
							$chars_array['manufacture_technology']=$chars_value;
							break;
						case 'MTBF (ώρες)':
							$chars_array['mtbf']=$chars_value;
							break;
						case 'Συσκευασία':
							$chars_array['packaging']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'keyboard_mouse'){

			$chars_array = array(
				'type' => "",
				'usage' => "",
				'connection' => "",
				'dpi' => "",
				'mouse_buttons' => "",
				'technology' => "",
				'language' => "",
				'programmable_buttons' => "",
				'multimedia_buttons' => "",
				'warranty' => ""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος συσκευής':
							$chars_array['type']=$chars_value;
							break;
						case 'Χρήση':
							$chars_array['usage']=$chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection']=$chars_value;
							break;
						case 'Ανάλυση DPI':
							$chars_array['dpi']=$chars_value;
							break;
						case 'Κουμπιά mouse':
							$chars_array['mouse_buttons']=$chars_value;
							break;
						case 'Τεχνολογία':
							$chars_array['technology']=$chars_value;
							break;
						case 'Γλώσσα πληκτρολογίου':
							$chars_array['language']=$chars_value;
							break;
						case 'Προγραμματιζόμενα πλήκτρα':
							$chars_array['programmable_buttons']=$chars_value;
							break;
						case 'Multimedia πλήκτρα':
							$chars_array['multimedia_buttons']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'servers'){

			$chars_array = array(
				'form_factor' => "",
				'server_size' => "",
				'power_supply' => "",
				'cpu' => "",
				'cpu_generation' => "",
				'cpu_model' => "",
				'cpu_frequency' => "",
				'cpu_cores	' => "",
				'cpu_cache' => "",
				'memory_size' => "",
				'memory_type' => "",
				'memory_frequency' => "",
				'hdd' => "",
				'hdd_type' => "",
				'controller_raid' => "",
				'ethernet' => "",
				'optical_drive' => "",
				'warranty' => "",
				'year_warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος θήκης':
							$chars_array['form_factor'] = $chars_value;
							break;
						case 'Rack height':
							$chars_array['server_size'] = $chars_value;
							break;
						case 'Κατασκευαστής επεξεργαστή':
							$chars_array['cpu'] = ucfirst($chars_value);
							break;
						case 'Επεξεργαστής':
							$chars_array['cpu_generation']=$chars_value;
								break;
						case 'Τύπος μνήμης':
							$chars_array['memory_type']=$chars_value;
								break;
						case 'Χωρητικότητα μνήμης':
							$chars_array['memory_size']=$chars_value;
							break;
						case 'Σκληρός δίσκος (χωρητικότητα / στροφές λειτουργίας)':
							$chars_array['hdd']=$chars_value;
								break;
						case 'Τύπος σκληρών δίσκων':
							$chars_array['hdd_type']=$chars_value;
							break;
						case 'RAID':
							$chars_array['controller_raid']=$chars_value;
								break;
						case 'Οπτικά μέσα':
							$chars_array['optical_drive']=$chars_value;
							break;
						case 'Δίκτυο  ':
							$chars_array['ethernet']=$chars_value;
							break;
						case 'Τροφοδοτικό':
							$chars_array['power_supply']=$chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['year_warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['year_warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['year_warranty'] = $chars_value;
							break;
						case 'Τύπος εγγύησης':
							$chars_array['warranty']=$chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'optical_drives')
		{
			$chars_array = array(
				'device' => "",
				'type' => "",
				'connection' => "",
				'write_speed' => "",
				'read_speed' => "",
				'color' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Συσκευή':
							$chars_array['device'] = $chars_value;
							break;
						case 'Τύπος':
							$chars_array['type'] = $chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection'] = $chars_value;
							break;
						case 'Ταχύτητα εγγραφής':
							$chars_array['write_speed'] = $chars_value;
							break;
						case 'Ταχύτητα ανάγνωσης':
							$chars_array['read_speed'] = $chars_value;
							break;
						case 'Χρώμα':
							$chars_array['color'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'card_readers')
		{
			$chars_array = array(
				'device_type' => "",
				'card_types' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος συσκευής':
							$chars_array['device_type'] = $chars_value;
							break;
						case 'Υποστήριξη τύπων μνήμης':
							$chars_array['card_types'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'flash_drives')
		{
			$chars_array = array(
				'capacity' => "",
				'connection' => "",
				'security' => "",
				'read_speed' => "",
				'write_speed' => "",
				'color' => "",
				'closing_type' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Χωρητικότητα':
							$chars_array['capacity'] = $chars_value;
							break;
						case 'Σύνδεση':
							$chars_array['connection'] = $chars_value;
							break;
						case 'Ασφάλεια':
							$chars_array['security'] = $chars_value;
							break;
						case 'Ταχύτητα ανάγνωσης (MB/sec)':
							$chars_array['read_speed'] = $chars_value;
							break;
						case 'Ταχύτητα εγγραφής (MB/sec)':
							$chars_array['write_speed'] = $chars_value;
							break;
						case 'Χρώμα':
							$chars_array['color'] = $chars_value;
							break;
						case 'Τύπος κλεισίματος':
							$chars_array['closing_type'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'power_supplies')
		{
			$chars_array = array(
				'type' => "",
				'power' => "",
				'energy_efficiency' => "",
				'fan_size' => "",
				'output_connectors' => "",
				'pfc' => "",
				'dimensions' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος':
							$chars_array['type'] = $chars_value;
							break;
						case 'Ισχύς (Watt)':
							$chars_array['power'] = $chars_value;
							break;
						case 'Energy-Efficient':
							$chars_array['energy_efficiency'] = $chars_value;
							break;
						case 'Ανεμιστήρας':
							$chars_array['fan_size'] = $chars_value;
							break;
						case 'Υποδοχές εξόδου':
							$chars_array['output_connectors'] = $chars_value;
							break;
						case 'PFC':
							$chars_array['pfc'] = $chars_value;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)': 
							$chars_array['dimensions'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'cases')
		{
			$chars_array = array(
				'type' => "",
				'motherboard_size' => "",
				'color' => "",
				'external_5_25' => "",
				'external_3_5' => "",
				'internal_5_25' => "",
				'internal_3_5' => "",
				'hdd_dock' => "",
				'installed_fans' => "",
				'side_window' => "",
				'dimensions' => "",
				'weight' => "",
				'power_supply' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος':
							$chars_array['type'] = $chars_value;
							break;
						case 'Υποστήριξη μητρικής':
							$chars_array['motherboard_size'] = $chars_value;
							break;
						case 'Χρώμα':
							$chars_array['color'] = $chars_value;
							break;
						case 'Εξωτερικές Θέσεις 5,25"':
							$chars_array['external_5_25'] = $chars_value;
							break;
						case 'Εξωτερικές Θέσεις 3,5"':
							$chars_array['external_3_5'] = $chars_value;
							break;
						case 'Εσωτερικές Θέσεις 3,5"':
							$chars_array['internal_5_25'] = $chars_value;
							break;
						case 'Εσωτερικές Θέσεις 2,5"':
							$chars_array['internal_3_5'] = $chars_value;
							break;
						case 'Docking σκληρού δίσκου':
							$chars_array['hdd_dock'] = $chars_value;
							break;
						case 'Εγκατεστημένοι ανεμιστήρες':
							$chars_array['installed_fans'] = $chars_value;
							break;
						case 'Πλαϊνό Παράθυρο':
							$chars_array['side_window'] = $chars_value;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)':
							$chars_array['dimensions'] = $chars_value;
							break;
						case 'Βάρος (κιλά)':
							$chars_array['weight'] = $chars_value;
							break;
						case 'Τροφοδοτικό':
							$chars_array['power_supply'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'fans')
		{
			$chars_array = array(
				'heat_sink_type' => "",
				'fan_diameter' => "",
				'fan_number' => "",
				'compatibility' => "",
				'dimensions' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος Ψύκτρας':
							$chars_array['heat_sink_type'] = $chars_value;
							break;
						case 'Μέγεθος ανεμιστήρα (mm)':
							$chars_array['fan_diameter'] = $chars_value;
							break;
						case 'Αριθμός ανεμιστήρων':
							$chars_array['fan_number'] = $chars_value;
							break;
						case 'Συμβατότητα':
							$chars_array['compatibility'] = $chars_value;
							break;
						case 'Διαστάσεις (μήκος x πλάτος x ύψος, σε mm)':
							$chars_array['dimensions'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'motherboards')
		{
			$chars_array = array(
				'cpu_brand' => "",
				'socket' => "",
				'chipset' => "",
				'integrated_cpu' => "",
				'type' => "",
				'ram_type' => "",
				'max_ram' => "",
				'integrated_graphics' => "",
				'crossfire_x' => "",
				'sli' => "",
				'sound' => "",
				'sata_2' => "",
				'sata_3' => "",
				'raid' => "",
				'pci_express_x8_16' => "",
				'pci_express_x1_4' => "",
				'pci' => "",
				'usb_2' => "",
				'usb_3' => "",
				'usb_3_1' => "",
				'firewire' => "",
				'e_sata' => "",
				'network' => "",
				'serial' => "",
				'parallel' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Κατασκευαστής επεξεργαστή':
							$chars_array['cpu_brand'] = $chars_value;
							break;						
						case 'Socket':
							$chars_array['socket'] = $chars_value;
							break;						
						case 'Chipset':
							$chars_array['chipset'] = $chars_value;
							break;						
						case 'Ενσωματωμένος επεξεργαστής':
							$chars_array['integrated_cpu'] = $chars_value;
							break;						
						case 'Τύπος μητρικής':
							$chars_array['type'] = $chars_value;
							break;						
						case 'Υποστηριζόμενη μνήμη':
							$chars_array['ram_type'] = $chars_value;
							break;						
						case 'Μέγιστο μέγεθος μνήμης':
							$chars_array['max_ram'] = $chars_value;
							break;						
						case 'Ενσωματωμένη κάρτα γραφικών':
							$chars_array['integrated_graphics'] = $chars_value;
							break;						
						case 'Υποστήριξη CrossfireX / SLI':

							if($whereIs = strpos( $chars_value, '/'))
							{
								$etd_crossfire_x = trim(substr($chars_value, 0, $whereIs));
								$etd_sli = trim(substr($chars_value, $whereIs+1, strlen($chars_value)));

								if($etd_crossfire_x == 'No')
									$chars_array['crossfire_x'] = 'ΟΧΙ';
								elseif($etd_crossfire_x == 'Yes')
									$chars_array['crossfire_x'] = 'ΝΑΙ';

								if($etd_sli == 'No')
									$chars_array['sli'] = 'ΟΧΙ';
								elseif($etd_sli == 'Yes')
									$chars_array['sli'] = 'ΝΑΙ';
							}
							break;						
						case 'Ήχος':
							$chars_array['sound'] = $chars_value;
							break;						
						case 'SATA 3Gb/s':
							$chars_array['sata_2'] = $chars_value;
							break;						
						case 'SATA 6Gb/s':
							$chars_array['sata_3'] = $chars_value;
							break;						
						case 'Raid':
							$chars_array['raid'] = $chars_value;
							break;						
						case 'PCI Express (x16, x8)':
							$chars_array['pci_express_x8_16'] = $chars_value;
							break;						
						case 'PCI Express (x1, x4)':
							$chars_array['pci_express_x1_4'] = $chars_value;
							break;						
						case 'PCI':
							$chars_array['pci'] = $chars_value;
							break;						
						case 'USB 2.0':
							$chars_array['usb_2'] = $chars_value;
							break;						
						case 'USB 3.0':
							$chars_array['usb_3'] = $chars_value;
							break;						
						case 'USB 3.1':
							$chars_array['usb_3_1'] = $chars_value;
							break;						
						case 'Firewire / eSATA':

							if($whereIs = strpos( $chars_value, '/'))
							{
								$etd_firewire = trim(substr($chars_value, 0, $whereIs));
								$etd_e_sata = trim(substr($chars_value, $whereIs+1, strlen($chars_value)));

								if($etd_firewire == 'No')
									$chars_array['firewire'] = 'ΟΧΙ';
								elseif($etd_crossfire_x == 'Yes')
									$chars_array['firewire'] = 'ΝΑΙ';

								if($etd_e_sata == 'No')
									$chars_array['e_sata'] = 'ΟΧΙ';
								elseif($etd_e_sata == 'Yes')
									$chars_array['e_sata'] = 'ΝΑΙ';
							}

							break;						
						case 'Δίκτυο':
							$chars_array['network'] = $chars_value;
							break;						
						case 'Σειριακή θύρα / Παράλληλη θύρα':

							if($whereIs = strpos( $chars_value, '/'))
							{
								$etd_serial = trim(substr($chars_value, 0, $whereIs));
								$etd_parallel = trim(substr($chars_value, $whereIs+1, strlen($chars_value)));

								if($etd_serial == 'No')
									$chars_array['serial'] = 'ΟΧΙ';
								elseif($etd_serial == 'Yes')
									$chars_array['serial'] = 'ΝΑΙ';

								if($etd_parallel == 'No')
									$chars_array['parallel'] = 'ΟΧΙ';
								elseif($etd_parallel == 'Yes')
									$chars_array['parallel'] = 'ΝΑΙ';
							}

							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;					
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'graphic_cards')
		{
			$chars_array = array(
				'chip_brand' => "",
				'gpu' => "",
				'core_frequency' => "",
				'manufacturer_technology' => "",
				'ram_size' => "",
				'ram_type' => "",
				'ram_frequency' => "",
				'ram_channel' => "",
				'connection' => "",
				'direct_x' => "",
				'output_ports' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Κατασκευαστής chip':
							$chars_array['chip_brand'] = $chars_value;
							break;
						case 'Επεξεργαστής γραφικών':
							$chars_array['gpu'] = $chars_value;
							break;
						case 'Συχνότητα πυρήνα (MHz)':
							$chars_array['core_frequency'] = $chars_value;
							break;
						case 'Τεχνολογία κατασκευής':
							$chars_array['manufacturer_technology'] = $chars_value;
							break;
						case 'Μέγεθος μνήμης':
							$chars_array['ram_size'] = $chars_value;
							break;
						case 'Τύπος μνήμης':
							$chars_array['ram_type'] = $chars_value;
							break;
						case 'Συχνότητα μνήμης (MHz)':
							$chars_array['ram_frequency'] = $chars_value;
							break;
						case 'Δίαυλος μνήμης':
							$chars_array['ram_channel'] = $chars_value;
							break;
						case 'Σύνδεση':					
							$chars_array['connection'] = $chars_value;
							break;
						case 'DirectX':					
							$chars_array['direct_x'] = $chars_value;
							break;
						case 'Θύρες εξόδου':
							$chars_array['output_ports'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'cpu')
		{
			$chars_array = array(
				'family' => "",
				'cpu_model' => "",
				'frequency' => "",
				'turbo_core' => "",
				'turbo_boost' => "",
				'socket' => "",
				'thermal_design_power' => "",
				'cache' => "",
				'construction_technology' => "",
				'core_num' => "",
				'threads' => "",
				'integrated_graphic' => "",
				'heat_sink' => "",
				'packaging' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Οικογένεια επεξεργαστή':
							$chars_array['family'] = $chars_value;
							break;
						case 'Μοντέλο επεξεργαστή':
							$chars_array['cpu_model'] = $chars_value;
							break;
						case 'Συχνότητα':
							$chars_array['frequency'] = $chars_value;
							break;
						case 'Turbo Core':
							$chars_array['turbo_core'] = $chars_value;
							break;
						case 'Turbo Boost':
							$chars_array['turbo_boost'] = $chars_value;
							break;
						case 'Socket':
							$chars_array['socket'] = $chars_value;
							break;
						case 'Thermal Design Power':
							$chars_array['thermal_design_power'] = $chars_value;
							break;
						case 'Συνολική Μνήμη Cache (L2 + L3)':
							$chars_array['cache'] = $chars_value;
							break;
						case 'Τεχνολογία κατασκευής':
							$chars_array['construction_technology'] = $chars_value;
							break;
						case 'Αριθμός πυρήνων':
							$chars_array['core_num'] = $chars_value;
							break;
						case 'Threads':
							$chars_array['threads'] = $chars_value;
							break;
						case 'Ενσωματωμένη κάρτα/chip γραφικών':
								$chars_array['integrated_graphic'] = $chars_value;
							break;
						case 'Ψύκτρα':
								$chars_array['heat_sink'] = $chars_value;
							break;
						case 'Συσκευασία':
							$chars_array['packaging'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}
		else if ($category == 'memories')
		{
			$chars_array = array(
				'type' => "",
				'capacity' => "",
				'quantity' => "",
				'frequency' => "",
				'cas_latency' => "",
				'voltage' => "",
				'warranty' => ""
				);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{ 
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					if ($chars_value == 'Yes')
						$chars_value = 'NAI';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';

					switch ($chars_title) {
						case 'Τύπος μνήμης':
							$chars_array['type'] = $chars_value;
							break;
						case 'Χωρητικότητα μνήμης':
							$chars_array['capacity'] = $chars_value;
							break;
						case 'Τεμάχια':
							$chars_array['quantity'] = $chars_value;
							break;
						case 'Συχνότητα λειτουργίας':
							$chars_array['frequency'] = $chars_value;
							break;
						case 'CAS Latency':
							$chars_array['cas_latency'] = $chars_value;
							break;
						case 'Τάση λειτουργίας':
							$chars_array['voltage'] = $chars_value;
							break;
						case 'Εγγύηση (μήνες)':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['warranty'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['warranty'] = (string)($chars_value/12).' έτη';
							}
							else
								$chars_array['warranty'] = $chars_value;
							break;
						default :

							break;
					}
				}
				else if ($is_found){
					continue;
				}
			}
			return $chars_array;
		}

		/////////////

    }

    private function updateProduct($table, $data, $product_number){

    	$this->db->where('product_number', $product_number);
    	$this->db->set($data);
    	$this->db->update($table);
    	echo 'update '.$product_number.' in table: '.$table.'<br>';
    }


    public function makeAvailability($availability, $supplier){

    	if($supplier == 'oktabit'){

/*
1.Διαθεσιμο 
2.Οριακο (λιγα κομματια ακομη στις αποθηκες Oktabit) 
3 και 4   Αναμενεται    (μηδεν διαθεσιμο αλλα το εχουμε παραγγειλει στον προμηθευτη) 
5. Κατοπιν παραγγελιας (μηδεν διαθεσιμο και το παραγγελνουμε στον προμηθευτη εφοσον το παραγγειλετε σε εμας) 
6. NOT AVAILABLE
*/

	    	switch ($availability) {
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
	    			break;
	    		case '2':
	    			$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
	    			break;
	    		case '3':
	    		case '4':
	    			$av = 'Αναμονή παραλαβής';
	    			return false;
	    			break;
	    		case '5':
	    			$av = 'Κατόπιν παραγγελίας χωρίς διαθεσιμότητα';
	    			return false;
	    			break;
	    		case '6':
	    			return false;
	    			break;

	    		
	    		default:
	    			return false;
	    			break;
	    	}

	    	return $av;

    	}elseif($supplier == 'logicom'){

    		switch ($availability) {
    			case 'Normal':
    			case 'Limited':
    				
    				$av="Κατόπιν παραγγελίας σε 1 εργάσιμη";
    				
    				break;
    			case 'PreOrder':
    				//$av="Αναμονή παραλαβής";
    				$av= false;
    				//return false;
    				break;
    			default:
    				return false;
    				break;
    		}

    		return $av;

    	}elseif($supplier == 'ddc' || $supplier == 'braintrust'){

    		switch ($availability) {
	    		case '0':
	    			$av = 'Αναμονή παραλαβής';
	    			return false;
	    			break;
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
	    			break;
	    		default:
	    			return false;
	    			break;
	    	}

	    	return $av;
    	}elseif($supplier == 'etd' ){

    		switch ($availability) {
	    		case '0':
	    			$av = 'Αναμονή παραλαβής';
	    			break;
	    		case '1':
	    			$av = 'Άμεσα Διαθέσιμο';
	    			break;
	    		default:
	    			return false;
	    			break;
	    	}

	    	return $av;

    	}elseif($supplier == 'aci'){

    		switch ($availability) {
				case 'Διαθέσιμο':
				case 'Περιορισμένη Διαθ.':
					$av = 'Κατόπιν παραγγελίας σε 1 εργάσιμη';
					break;
				case 'Κατόπιν Παραγγελίας':
					$av = 'Κατόπιν παραγγελίας χωρίς διαθεσιμότητα';
					break;
				case 'Μη Διαθέσιμο':
					$av = false;
					break;
				default:
	    			return false;
	    			break;
    		}

    		return $av;
    	}
    }

	public function updateLive($supplier){

		//1. Delete old trashed entries
		
		$this->db->where('supplier', $supplier);
		$this->db->where('status', 'trash');
		$this->db->where('delete_flag', 10);
		$this->db->delete('live');


		//2. Update all entries to set status=trash and delete_flag +1

		$this->db->where('supplier', $supplier);
		$products = $this->db->get('live');

			foreach ($products->result() as $row) {

				$id = $row->id;
				$flag = $row->delete_flag;
				$flag ++;

				$this->db->where('id', $id);
				$this->db->set('delete_flag',$flag);
				$this->db->set('status','trash');
				$this->db->update('live');				
			}

		return true;
	}
}

?>