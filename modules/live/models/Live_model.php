<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Live_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

 
    private function xml($url){

		$xml = simplexml_load_file($url) or die("Error: Cannot create object");

    	return $xml;
	}
	
    public function oktabit(){

		if($xml = $this->xml("https://www.oktabit.gr/times_pelatwn/prices_xml.asp?customercode=012348&logi=evansmour")){
			if($desc_xml = $this->xml("https://www.oktabit.gr/times_pelatwn/perigrafes_xml.asp?customercode=012348&logi=evansmour")){
				if($char_xml = $this->xml("https://www.oktabit.gr/times_pelatwn/chars_xml.asp?customercode=012348&logi=evansmour")){

					$images = array();

					$this->updateLive('oktabit');


				}
				else
					die("Characteristics XML from Oktabit can not be loaded.");
			}
			else
				die("Description XML from Oktabit can not be loaded.");
		}else
			die("XML from Oktabit can not be loaded.");

			$charsArray = array();
			foreach($char_xml->children() as $chars) {

				$char_prd = strtoupper((string) trim($chars->product[0]));
				$char_att = (string) trim($chars->atribute[0]);
				$char_val = (string) trim($chars->value[0]);

				if ($char_val == 'Yes')
					$char_val = 'ΝΑΙ';
				else if ($char_val == 'No')
					$char_val = 'ΟΧΙ';
				else if ($char_val == '-')
					$char_val = '';

				
				if($char_att == 'Εγγύηση (μήνες)' || $char_att == 'Εγγύηση')
				{
					if($char_val/12 == 1)
						$char_val = (string)($char_val/12).' έτος';
					elseif($char_val/12 > 1)
						$char_val = (string)($char_val/12).' έτη';
				}
	
				$charsArray[$char_prd][$char_att] = $char_val;
			}

		$newProducts = array();
		$f=0;

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'Oktabit Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start Oktabit Update'
		);

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
				case 'Imaging':
					if($sc == 'Projectors LCD')
					{
						$c = 'projectors';
					}else{
						$c = $cat;
					}
					break;
				case 'Computers':
					if($sc == 'Advanced PC' || $sc == 'All In One PC' || $sc == ' All In One PC BTO' || $sc == 'Business PC' || $sc == 'Workstations')
					{
						$c = 'desktops';
					}
					break;
				case 'Energy':
					if($sc == 'Power Bank')
					{
						$c = 'power_bank';
						$c = $cat;
					}
					break;
				case 'Monitors':
					if($sc == 'LCD-TFT PC Monitors')
					{
						$c = 'monitors';
					}
					break;
				case 'Networking':
					//# The product type is set in addProductChars function as following 
					//# $chars_array = array('type' => "Access Point", for the access points
					//# and dynamically from the $char_xml for the rest products.
					if($sc == 'DSL Products' || $sc == 'Routers')
					{
						$c = 'routers';
					}
					elseif($sc == 'Wireless Products')
					{
						if($B2b_sc == 'Routers' || $B2b_sc == 'Access Points')
						{
							$c = 'routers';
						}
					}
					elseif($sc == 'Wireless Products (D)')
					{
						$c = 'routers';
					}
					elseif($sc == 'Powerlines'){
						$c = 'powerlines';
					}
					elseif($sc == 'IP Cameras'){
						$c = 'ip_cameras';
					}
					/*if($sc == 'Routers')
					{
						$c = 'routers';
            			$c = $cat;
					}*/
					elseif($sc == 'Switches'){
						$c = 'switches';
            			$c = $cat;
					}
					break;
				case 'Power Protection':
					/*if($brand != 'CYBERPOWER' && $brand != 'APC'){
						$c = $cat;
					}else{

						if($sc == 'Data Center UPS' || $sc == 'Line Interactive UPS' || $sc == 'On Line UPS'  || $sc == 'Standby UPS')
							{
								$c = 'ups';
							}
					}*/
					if($sc == 'Data Center UPS' || $sc == 'Line Interactive UPS' || $sc == 'On Line UPS'  || $sc == 'Standby UPS')
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
					elseif($sc == 'Antivirus'){
						$c = 'software';
						$sc = 'Antivirus';
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
					elseif ($sc == 'Tower Systems' || $sc == 'Tower Systems BTO' )
					{
						$c = 'servers';
					}
					elseif($sc == 'Server CPU' )
					{
						$c = 'server_cpu';
					}
					elseif($sc == 'Server Memory' )
					{
						$c = 'server_memories';
					}
					elseif($sc == 'Server Storage' )
					{
						$c = 'server_hard_drives';
					}
					elseif($sc == 'Storage Controlers' )
					{
						$c = 'server_controllers';
					}
					elseif($sc == 'Server Options' )
					{
						$tmp_title = (string) trim($product->titlos);
						if(strstr ($tmp_title,'PSU'))
						{
							$c = 'server_power_supplies';
						}
						elseif(strstr ($tmp_title,'Memory'))
						{
							$c = 'server_memories';
						}
						elseif(strstr ($tmp_title,'RAID'))
						{
							$c = 'server_controllers';
						}
					}
					break;
				case 'Entertainment':
					/*if($brand != 'LOGITECH'){
						$c = $cat;
					}else{
						if($sc == 'Speakers' )
						{
							$c = 'speakers';
						}
					}*/
					if($sc == 'Speakers' )
					{
						$c = 'speakers';
					}
					break;
				case 'Storage':
					if($sc == 'External Hard Drives' )
					{
						$c = 'external_hard_drives';
            			$c = $cat;
					}
					elseif($sc == 'SATA Hard Drives' )
					{
						$c = 'sata_hard_drives';
            			$c = $cat;

					}
					elseif($sc == 'Solid State Drives' )
					{
						$c = 'ssd';
           				$c = $cat;
					}
					elseif($sc == 'DVD-RW Drives' )
					{
						$c = 'optical_drives';
            			$c = $cat;
					}
					elseif($sc == 'Card Reader' )
					{
						$c = 'card_readers';
            			$c = $cat;
					}
					elseif($sc == 'USB Memory Sticks' )
					{
						$c = 'flash_drives';
            			$c = $cat;
					}
					break;
				case 'Cases-Peripherals':
					if($brand != 'MICROSOFT' && $brand != 'LOGITECH'){
						$c = $cat;
					}else{

						if($sc == 'Combo' || $sc == 'Keyboard' || $sc == 'Mouse')
						{
							$c = 'keyboard_mouse';

						}
						/*elseif($sc == 'Power Supplies')
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
						}*/
          			}
					break;
				case 'Telephony':
					if($brand == 'YEALINK')
					{
						if($sc == 'Phone Device DECT')
						{
							$c = 'ip_phones';
							$sc = 'DECT Phone';
						}
						elseif($sc == 'Phone Device IP')
						{
							$c = 'ip_phones';
							$sc = 'IP Phones';
						}
						elseif($sc == 'Voice Conference')
						{
							$c = 'ip_phones';
							$sc = 'Conference';
						}
						elseif($sc == 'Accessories')
						{
							$c = 'ip_phones';
							$sc = 'Accessory';
						}
					}
					else
						$c = $cat;
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
          			//$c = $cat;
					break;
				case 'Furniture':
					if($sc == 'Gaming Chair')
					{
						$c = 'gaming_chairs';
					}
					else {
						$c = $cat;
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
				else
					$f=1;

				$net_price = str_replace(",", ".", $product->timi);
				$net_price = (string) trim($net_price);

				$recycle_tax = str_replace(",", ".", $product->anakykl);
				$pn = (string) trim($product->part_no);
				if($pn == '')
					continue;
				//$pn = ($pn == '') ? (string) trim($product->code): $pn;
				$description = "";
				$brand = (string) trim($product->brand);
				$title = (string) trim($product->titlos);
				$code = (string) trim($product->code);
				$product_url = "https://www.oktabit.gr/product_details.asp?productid=".$code;


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
				$chars_array = $this->addProductChars($c, $code, $charsArray);

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

				if ($c == 'software')
				{
					$okt_product['type'] = $sc;
					$okt_product['dist_type'] = $dist_type;
					$okt_product['shipping_class'] = 10646;
					$okt_product['volumetric_weight'] = 0.2;

					if (strstr ($title,'DSP'))
						$okt_product['dist_type'] = 'DSP';
					elseif(strstr ($title,'Reseller Option Kit') || strstr ($title,'ROK'))
						$okt_product['dist_type'] = 'ROK';
				}
				elseif($c == 'ip_phones')
				{
					$okt_product['type'] = $sc;
				}
				
				if ($c == 'memories' && $B2b_sc == 'Εξαρτήματα Servers')
				{
					$okt_product['description'] = 'Εξαρτήματα Servers';
				}

				//2. New products for charateristics tables that load Sku module

				$insert = $this->addProduct ($okt_product, $chars_array, $f, $supplier);

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

		$this->sendImportedProductsByMail($newProducts,'Oktabit');

		echo "Finnished Oktabit";
		echo "<h3> NEW PRODUCTS </h3>";
		foreach ($newProducts as $key => $value) {
			echo $key.' -> '.$value.'<br>';
		}

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);

    }


    public function quest(){

    	$this->load->view('upload_quest_xml', array('error' => ' ' ));
    }

    public function import_quest($path){

    	if($xml = $this->xml($path)){

			//$images = array();
			$this->updateLive('quest');
		}

		$newProducts = array();
		$sc = '';   	
		
		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'Quest Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start Quest Update'
		);

		foreach($xml->children() as $product) {
			$type = '';
			$cat = (string) trim($product->Category);
			$title = (string) trim($product->Item);
			switch ($cat) {
				case 'Routers':
					$c = 'routers';
					break;
				case 'Mobile Battery/Power Bank':
					$c = 'power_bank';
					break;
				case 'Tablet & Pads':
					$c = 'tablets';
					break;
				case 'Desktop Branded PCs':
					$c = 'desktops';
					$type = 'Desktop';
					break;
				case 'Memory Modules':
					$c = 'memories';
					break;
				case 'Notebook Power and Batteries':
					$c = 'accessories';
					$type = 'laptops';
					break;
				case 'Switches':
					$c = 'switches';
					break;
				case 'Speakers':
					$c = 'speakers';
					break;
				/*case 'Memory Cards':
					$c = 'Memory Cards';
					break;*/
				case 'Laser Printers':
					$c = 'printers';
					$type = 'Laser';
					break;
				case 'Operating Systems':
					$c = 'software';
					$type = 'Λειτουργικά Συστήματα';
					break;
				case 'SOLID STATE DISKS (SSD)':
					if (strpos($title, 'PORTABLE') !== false || strpos($title, 'MY PASS') !== false)
						$c = 'external_hard_drives';
					else
						$c = 'ssd';
					break;
				case 'Projector':
					$c = 'projectors';
					break;
				case 'Notebook/Netbook  Bags':
					$c = 'carrying_cases';
					break;
				case 'External Hard Disk Drives':
					$c = 'external_hard_drives';
					break;
				case 'Monitor LCD':
					$c = 'monitors';
					break;
				/*case 'Plotters':
					$c = 'Plotters';
					break;*/
				case 'Servers (HW)':
					$c = 'servers';
					break;
				case "Workstation PC's":
					$c = 'desktops';
					$type = 'Workstation';
					break;
				/*case 'Smart Home':
					$c = 'Smart Home';
					break;*/
				case 'Notebook':
					$c = 'laptops';
					break;
				case 'Security & Antivirus':
					$c = 'software';
					$type = 'Antivirus';
					break;
				case 'Networking Storage':
					$c = 'nas';
					break;
				case 'Barcode Scanners':
					$c = 'barcode_scanners';
					break;
				default:
					$c = $cat;
					break;
			}


			if($c!=$cat){

				$availability = $this->makeAvailability((string) trim($product->Availability), 'quest');

				if(!$availability)
					continue;

				$pn = (string) trim($product->Code);
				if(strpos($pn, 'XXX') !== false)
					continue;

				$title = (string) trim($product->Item);
				if(strpos($title, 'ΕΞΑΡΤ') !== false)
					continue;

	    		$description = '';
	    		$net_price = $product->Price;
	    		if($net_price == '0' || $net_price == 0)
	    			continue;
	    		$net_price = str_replace(",", ".", $net_price); //for floatval to work
				$net_price = floatval ($net_price);
	    		$availability = $availability;
	    		$recycle_tax = '';
	    		$code = (string) trim($product->Quest_Code);

				$brand = $this->MakeQuestBrands($title, $cat);
				if ($brand == 'APPLE' || ($brand == 'TOSHIBA' && $cat== 'Notebook') || $brand == 'BENQ' || $brand == 'Unknown' || $brand == '')
					continue;

	    		//1. Live
				$supplier = 'quest';
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c,
						'product_number'=>$pn,
						'net_price'=>$net_price,
						'availability'=>$availability,
						'recycle_tax'=>(string) trim($recycle_tax),
						'supplier' =>$supplier,
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
				$product_url = '';

				$quest_product = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'title' => $title,
					'brand' => $brand,
					'product_url' => $product_url,
					'code' => $code,
					'net_price'=>$net_price
				);

				if ($c == 'software')
				{
					$quest_product['type'] = $type;
					$quest_product['shipping_class'] = 10646;
					$quest_product['volumetric_weight'] = 0.2;

					if (strstr ($title,'DSP'))
						$quest_product['dist_type'] = 'DSP';
					elseif(strstr ($title,'ROK'))
						$quest_product['dist_type'] = 'ROK';
					else
						$quest_product['dist_type'] = '';
				}

				if ($type != '')
					$quest_product['type'] = $type;

				//Image cannot be parsed must import it manually
				$imageUrl = '';

				$insert = $this->addProduct ($quest_product, array(), $imageUrl, 'quest');


				if ($insert)
				{
					if(isset ($newProducts[$c])){
						$newProducts[$c] = $newProducts[$c]+1;
					}
					else{
						$newProducts[$c] = 1;
					}
				}
			}
		}//end foreach

		$this->sendImportedProductsByMail($newProducts,'Quest');
		echo "Finnished updating Quest.";	

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);
	}//End Quest
	
	public function netconnect(){

    	$this->load->view('upload_netconnect_xml', array('error' => ' ' ));
    }

    public function import_netconnect($path){

    	if($xml = $this->xml($path)){

			//$images = array();
			$this->updateLive('netconnect');
		}

		$newProducts = array();
		$sc = '';  

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'NetConnect Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start NetConnect Update'
		);
		
		if (isset($xml->children()->Product[0]->Availiability) && isset($xml->children()->Product[0]->EAN)) // Check if Availability & EAN exist in XML
		{

			foreach($xml->children() as $product) {
				$type = '';
				$cat = (string) trim($product->Category);
				$sc = (string) trim($product->Subcategory);

				switch ($cat) {
					case 'Harddrive Internal':
						if ($sc == 'SSD SATA' || $sc == 'SSD m2-SATA' || $sc == 'SSD m2-PCIe' /*|| $sc == 'Desktop SSHD'*/)
							$c = 'ssd';
						else
							$c = 'sata_hard_drives';
						break;
					case 'Harddrive External':
						$c = 'external_hard_drives';
						break;
					case 'NAS/Cloud':
						$c = 'nas';
						$type = $sc;
						break;
					case 'Monitor':
						$c = 'monitors';
						break;
					case 'Projector':
						$c = 'projectors';
						break;				
					default:
						$c = $cat;
						break;
				}
	/*

	[Code] => 1P1AMD-A4/4000
		[Brand] => Amd
		[Category] => Processor
		[Subcategory] => Processor with sFM2+
		[Description] => AMD A4-4000 3.00/3.20GHz 2C/2T HD7480D/128CORES 1MB 65W FM2
		[Price] =>         24.29
		[Recycle] => 0
		[Image] => http://images.netconnect.gr/get.php?image=1P1AMD-A4%2F4000_b
		[Text] => http://images.netconnect.gr/text.php?page=1P1AMD-A4%2F4000
		[Availiability] => +
		[EAN] => 730143303415
	*/

				if($c!=$cat){

					if($type == 'Accessories')
						continue;

					$brand = (string) trim($product->Brand);
					if ($brand != 'Western Digital' && $brand != 'ViewSonic' && $brand != 'Synology')
						continue;

					$availability = $this->makeAvailability((string) trim($product->Availiability), 'netconnect');
					if(!$availability)
						continue;

					$pn = (string) trim($product->Code);
					$NewPn = '';
					if($brand == 'ViewSonic')
					{
						if($c == 'projectors')
						{
							$NewPn = ltrim ($pn, '1V5VWS-');
						}
						else
						{
							$pos = strpos($pn, '/');
							$pn_len = strlen($pn);

							if($pn_len - $pos <= 4 )
							{
								$pos = strpos($pn, '-');
								$NewPn = substr($pn, $pos+1);
								$NewPn = str_replace("/","-",$NewPn);
							}
							else
							{
								$NewPn = substr($pn, $pos+1);
								$NewPn = str_replace("/","-",$NewPn);
							}
						}
					}
					else
					{
						$pos = strpos($pn, '-');
						$NewPn = substr($pn, $pos+1);
						if($brand == 'Synology')
						{
							$NewPn = str_replace("/PLUS","+",$NewPn);
							$NewPn = str_replace("PLUS","+",$NewPn);
							$NewPn = str_replace("/02GB"," (2GB)",$NewPn);
							$NewPn = str_replace("/04GB"," (4GB)",$NewPn);
							$NewPn = str_replace("/08GB"," (8GB)",$NewPn);
						}
					}

					if ($NewPn != '')
						$pn = $NewPn;

					$title = (string) trim($product->Description);
					if($brand == 'Western Digital')
					{ 
						if(strpos($title, 'WD') === false)
							$title = 'WD '.$title;
					}
					elseif (strpos($title, $brand) === false || strpos($title, strtoupper($brand)) === false)
						$title = strtoupper($brand).' '.$pn.' '.$title;

					$description = '';
					if($product->Text != '')
					{
						//For Getting the Description from the body tag of $product->Text URL
							$file = file_get_contents($product->Text);       
							$dom = new DOMDocument;
							$dom->loadHTML($file);
							$bodies = $dom->getElementsByTagName('body');
							assert($bodies->length === 1);
							$body = $bodies->item(0);

						$description = $body->textContent;
					}

					$net_price = (string) $product->Price;

					$net_price = str_replace(",","",$net_price);

					if($net_price == '0' || $net_price == 0)
						continue;
					$net_price = floatval ($net_price);

					$recycle_tax = $product->Recycle;
					$recycle_tax = floatval ($recycle_tax);

					$availability = $availability;
					$code = (string) trim($product->EAN);
					
	/*				
					echo '<pre>';
					print_r ($product);
					echo '</pre>';
					echo $c.'<br>';
					echo '---------';

					echo '---------<pre>';
					echo $c.'<br>';
					echo $sc.'<br>';
					echo $brand.'<br>';
					echo $availability.'<br>';
					echo $pn.'<br>';
					echo $title.'<br>';
					echo $description.'<br>';
					echo $net_price.'<br>';
					echo $recycle_tax.'<br>';
					echo $code.'<br>';
					echo '</pre><br><br>';
	*/
					

					//1. Live
					$supplier = 'netconnect';
					if($this->checkLiveProduct($pn, $net_price, $supplier)){

						$live = array(
							'category'=>$c,
							'product_number'=>$pn,
							'net_price'=>$net_price,
							'availability'=>$availability,
							'recycle_tax'=>(string) trim($recycle_tax),
							'supplier' =>$supplier,
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
					$product_url = '';
					$brand = strtoupper($brand);

					$netconnect_product = array(
						'category' => $c,
						'product_number' => $pn,
						'description' => $description,
						'title' => $title,
						'brand' => $brand,
						'product_url' => $product_url,
						'code' => $code,
						'net_price'=>$net_price
					);

					if ($type != '')
						$netconnect_product['type'] = $type;


					$imageUrl = $product->Image;

					$insert = $this->addProduct ($netconnect_product, array(), $imageUrl, 'netconnect');


					if ($insert)
					{
						if(isset ($newProducts[$c])){
							$newProducts[$c] = $newProducts[$c]+1;
						}
						else{
							$newProducts[$c] = 1;
						}
					}
				}
			}//end foreach
			
			$this->sendImportedProductsByMail($newProducts,'NETCONNECT');
			echo "Finnished updating NETCONNECT.";
			$log['log_result'] = $newProducts;
		} //end check for availability column
		else
		{
			$log['log_result'] = 'error: Το XML της Netconnect δεν έχει διαθεσιμότητες ή ΕΑΝ';
			echo "Το XML της Netconnect δεν έχει διαθεσιμότητες ή ΕΑΝ";
			echo "</br></br>";
			echo '<a href="'.base_url("live/index/netconnect").'"><button>Πίσω στην εισαγωγή για NetConnect</button></a>';
		}

		$this->MakeLogEntry($log);
	}//End NETCONNECT


    public function partnernet(){

    	if($xml = $this->xml("http://eshop.partnernet.gr/index.php?route=feed/any_feed&name=episilonteledata")){

			$images = array();
			$this->updateLive('partnernet');

		}else{
			die("XML from PartnerNet can not be loaded.");
		}

		$newProducts = array();
		$supplier = "partnernet";

		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'PartnerNet Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start PartnerNet Update'
		);


		foreach($xml->children() as $product) {

			set_time_limit(50);
			//Rename categories for ETD.gr

			$dist_type='';
			$cat = (string) trim($product->categories1);

			$c_array = explode(">", $cat);
			$c_array[0] = trim($c_array[0]);
			if($c_array[0]!='Telephony' && $c_array[0]!='Conferencing'){
				continue;
			}else{
				$cat = trim($c_array[1]);
			}

			if($c_array[0]=='Conferencing'){
				$cat = "Conference";
			}

			switch ($cat) {
				case 'Asterisk Cards':
					$c='ip_cards';
					break;
				case 'Gateways':
					$c='ip_gateways';
					break;
				case 'IP PBX':
					$c='ip_pbx';
					break;
				case 'IP Phones':
				case 'IP Video Phones':
				case 'Conference':
					$c='ip_phones';
					break;
				default:
					$c =  $cat;
					break;
			}


			if($c==$cat){
				continue;
			}

			$availability = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';

			$net_price = str_replace(".", "", $product->price);
			$net_price = str_replace("€", "", $net_price);

			$net_price = str_replace(",", ".", $net_price); //for floatval to work
			$net_price = floatval ($net_price);

			$pn = (string) trim($product->sku);
			$model = (string) trim($product->model);

			if($pn=='' || $pn!=$model)
				continue;


			$description = "";
			$brand = (string) trim($product->manufacturer);
			$title = (string) trim($product->name);

			if($brand == 'Yealink') //added after Evans command on 16-1-2018 (email).
				continue;

			$status = 'publish';
			$flag = 0;

			if (strpos($title, '3CX') !== false) {
				    $status = 'trash';
				    $flag  = 1;
				}
			//$product_url = "";

			//Insert into live

			//1. Live

				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability,
						'recycle_tax'=>'',
						'supplier' =>$supplier,
						'status' => $status,
						'delete_flag'=>$flag
						);

					$this->db->where('product_number', $pn);
					$this->db->where('supplier', $supplier);
					$this->db->delete('live', $live);

					$this->db->insert('live', $live);

					unset($live);
				}

				//Array for categories table

				$product_array = array(
					'category' => $c,
					'product_number' => $pn,
					'description' => $description,
					'brand' => $brand,
					'title' => $title,
					//'product_url' => $product_url,
					//'code' => $code,
					'net_price'=>$net_price
				);

				$chars_array=array();

				$f = array();
				$f[]=trim($product->image);
				$f[]=trim($product->additional_images1);
				$f[]=trim($product->additional_images2);
				$f[]=trim($product->additional_images3);
				$f[]=trim($product->additional_images4);
				$f[]=trim($product->additional_images5);
				$f[]=trim($product->additional_images6);
				$f[]=trim($product->additional_images7);
				$f[]=trim($product->additional_images8);
				$f[]=trim($product->additional_images9);

				foreach ($f as $key => $value) {
					if($value=='')
						unset($f[$key]);
				}

				//2. New products for charateristics tables that load Sku module

				$insert = $this->addProduct ($product_array, $chars_array, $f, $supplier);

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}

			}//foreach($xml->children() as $product)

			$this->sendImportedProductsByMail($newProducts, 'PartnerNet');
			echo "Finnished $supplier";

			$log['log_result'] = $newProducts;
			$this->MakeLogEntry($log);

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

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'Enet-Logicom Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start Enet-Logicom Update'
		);


		foreach($xml->children()->children()->children() as $product) {

			set_time_limit(50);
			//Rename categories for ETD.gr

			$c = $cat = $product->Details->CategoryList->attributes()->{'Name'};
			$sc = '';
			$ManufacturerList = (string) trim($product->Details->ManufacturerList->attributes()->{'Name'});
			$prd = $product->attributes();

			switch ($cat) {

				case 'Computers- / Accessories- / Bags and Cases':
					$c = 'carrying_cases';
					break;
				case 'Computers- / Computers- / All In One':
				case 'Computers- / Computers- / Desktop':
				case 'Computers- / Computers- / Mini PC':
				case 'Computers- / Computers- / Workstations':
					$c = 'desktops';
					break;
				case 'Computers- / Computers- / Notebooks':
					$c = 'laptops';
					break;
				case 'Computers- / Accessories- / Notebook Options':
					$c = 'accessories';
					$sc = 'laptops';
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
				case 'Networking- / Routing':
					$c = 'routers';
					break;
				case 'Networking- / Switching':
					$c = 'switches';
					break;
				case 'Peripherals- / Accessories- / Keyboards':
				case 'Peripherals- / Accessories- / Mouse':
					$c = 'keyboard_mouse';
					break;
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
				case 'Telecoms- / SmartPhones':
					$c = 'smartphones';
					break;
				case strpos($cat,'Peripherals- / Monitors'):
					$c = 'monitors';
					break;
				case 'Computers- / Servers- / Controllers':
					$c='server_controllers';
					break;
				case 'Computers- / Components- / CPUs':
					if($ManufacturerList == 'HPE- / Server Options- / CPUs')
						$c='server_cpu';
					break;
				case 'Computers- / Components- / Internal Hard Drives':
					if($ManufacturerList == 'HPE- / Server Options- / Internal Hard Drives')
						$c='server_hard_drives';
					break;
				case 'Computers- / Components- / Memories Module':
					if($ManufacturerList == 'HPE- / Server Options- / Memories Module')
						$c='server_memories';
					break;
				case 'Computers- / Servers- / Server / Rack Options':
					$tmp_title = (string) trim($prd["Name"]);
					if(strstr($tmp_title,'Power Supply'))
						$c='server_power_supplies';
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

	    		if($c == 'accessories' && $ManufacturerList != 'MICROSOFT- / Accessories- / Notebook Options')
	    			continue;


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
				//For Servers parts
				if($brand == 'HPE')
					$brand = 'HP';


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
					$log_product['shipping_class'] = 10646;
					$log_product['volumetric_weight'] = 0.2;
					if (strstr ($title,'DSP'))
						$log_product['dist_type'] = 'DSP';
					elseif(strstr ($title,'Reseller Option Kit') || strstr ($title,'ROK'))
						$log_product['dist_type'] = 'ROK';
					else
						$log_product['dist_type'] = '';

				}
				else if($c == 'accessories')
				{
					$log_product['type'] = $sc;
				}

				//2. New products for charateristics tables that load Sku module
				//$this->AddProduct ($c, $pn, $description, $brand, $title, $product_url, $newProducts, $i, $imageUrl, 'logicom');
				$insert = $this->addProduct ($log_product, array(), $imageUrl, 'logicom');


				if ($insert)
				{
					if(isset ($newProducts[$c])){
						$newProducts[$c] = $newProducts[$c]+1;
					}
					else{
						$newProducts[$c] = 1;
					}
				}

			}//if $c==$cat

		}//end foreach

		$this->sendImportedProductsByMail($newProducts, 'Logicom-Enet');

		echo "Finnished updating Logicom-Enet.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);

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

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'DDC Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start DDC Update'
		);

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
							if(strpos( $dimensions, ' ')+1)
								$space_where = strpos( $dimensions, ' ')+1;

							if(strpos( $dimensions, ','))
								$comma_where = strpos( $dimensions, ',');

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

		$this->sendImportedProductsByMail($newProducts, 'Digital Data Communication');

		echo "Finnished updating Digital Data Communication.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);

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

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'Braintrust Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start Braintrust Update'
		);

		foreach($xml->children() as $product) {
			$availability=false;
			set_time_limit(50);
			$chars_array = array();
			//Rename categories for ETD.gr

			$cat = (string) trim($product->Category);
			$sc = trim((string)$product->MainCategory);

			$c = $cat;

			$brand = (string) trim($product->Supplier);
			$title = '';
			$size = '';

			switch ($cat) {
				case 'Notebook':
					if($brand == 'MSI' || $brand == 'ACER')
						$c = 'laptops';
					else
						$c = $cat;
				break;
				case 'Notebook Options':
					if($brand == 'COOLERMASTER')
						$c = 'cooling_pads';
					else
						$c = $cat;
				break;
				case 'Desktop/Tower':
					if($brand == 'MSI')
						$c = 'desktops';
					else
						$c = $cat;
				break;
				case 'PC BareBone':
					if($brand == 'MSI')
					{
						$c = 'desktops';
						$sc = 'Mini Pc';
					}
					else
						$c = $cat;
				break;
				
				case 'PC Motherboard':
					if($brand == 'MSI')
						$c = 'motherboards';
					else
						$c = $cat;
				break;
				case 'VGA':
					if($brand == 'MSI' || $brand == 'SAPPHIRE')
						$c = 'graphic_cards';
					else
						$c = $cat;
				break;
				case 'HDD 3,5"':
					$c = 'sata_hard_drives';
					$size = '3.5"' ;
				break;
				case 'HDD 2,5"':
					$c = 'sata_hard_drives';
					$size = '2.5"' ;
				break;
				case 'HDD External':
					$c = 'external_hard_drives';
				break;
				case 'SSD':
					if($brand == 'SILICON POWER')
						$c = $cat;
					else
						$c = 'ssd';
				break;
				case 'Monitor':
				case 'TV/Monitor':
					if($brand == 'LG ELECTRONICS')
					{
						$c = 'monitors';
						$brand = 'LG';
					}
					else
					{
						$c = 'monitors';
						//$c = $cat;
					}
				break;
				case 'TV':
					if($brand == 'LG ELECTRONICS')
					{
						$brand = 'LG';
					}
						$c = 'tv';
				break;
				case 'Memory':
					if($brand == 'CORSAIR MEMORY')
						$c = 'memories';
					else
						$c = $cat;
				break;
				case 'PSU':
					if($brand == 'CORSAIR MEMORY')
						$c = 'power_supplies';
					else
						$c = $cat;
				break;
				case 'Power Line':
					if($brand == 'DEVOLO')
						$c = 'powerlines';
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

				$description = (string) trim($product->Description);
				$pn = (string) trim($product->SKU);

				if ($pn == ''){
					continue;
				}

				if($c == 'laptops')
				{
						$first = strpos($description, 'NB ')+3;
						$last = strpos($description, ', ');
						$diff = $last-$first;

						$title = substr($description, $first, $diff);
					if($brand == 'MSI')
						$title = "MSI ".$title;
					elseif($brand == 'ACER')
						$title = "ACER ".$title;
				}
				elseif($c == 'desktops')
				{
					$last = strpos($description, ',');
					$title = substr($description, 0, $last);
					if($sc == 'Mini Pc')
						$chars_array['type']='Mini Pc';
					else
						$chars_array['type']='Desktop';
				}
				elseif($c == 'powerlines')
				{
					$last = strpos($description, ',');
					$title = substr($description, 0, $last);
				}
				elseif($c == 'motherboards')
				{
					$first = strpos($description, 'MB ')+3;
					$last = strpos($description, ', ');
					$diff = $last-$first;

					$title = substr($description, $first, $diff);
					$title = $brand." ".$title;
				}
				elseif($c == 'graphic_cards')
				{
					$first = strpos($description, 'VGA ')+4;
					$last = strpos($description, ', ');
					$diff = $last-$first;

					$title = substr($description, $first, $diff);
					$title = $brand." ".$title;
				}
				elseif($c == 'sata_hard_drives' || $c == 'external_hard_drives')
				{
					$title = str_replace('"', '', $description);
				}
				elseif($c == 'monitors')
				{
					$title = $description;
				}
				elseif($c == 'memories')
				{
					$title = $description;
				}
				elseif($c == 'power_supplies')
				{
					$title = $description;
				}
				elseif($c == 'tv')
				{
					$title = $description;
				}
				elseif($c == 'cooling_pads')
				{
					if (strpos($description,'NOTEPAL') > 0 || strpos($description,'notepal') > 0)
						$title = $description;
					else
						continue;
				}
				//echo $title.'<br>';
				$net_price = str_replace(",", ".", $product->timi);
				$net_price = (string) trim($net_price);

				//$availability = $availability;
				$imageUrl = (string) trim($product->Image);
				//$brand = (string) trim($product->Supplier);

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
				if($brand == 'SKYWORTH')
				{
					$live = array(
						'status' => 'publish',
						'delete_flag'=>0
					);

					$this->db->where('product_number', $pn);
					$this->db->update('live', $live);
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

				$insert = $this->addProduct ($braintrust_product, $chars_array, $imageUrl, 'braintrust');

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}

			}
		} //end foreach

		$this->sendImportedProductsByMail($newProducts, 'Braintrust');

		echo "Finnished updating Braintrust.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);

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

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'ACI Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start ACI Update'
		);


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
		$this->sendImportedProductsByMail($newProducts, 'ACI');

		echo "Finnished updating ACI.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);
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

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'Copiers Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start Copiers Update'
		);


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

				$title = (string) trim($product->title);
				$Image = (string) trim($product->Image);
				$PRODUCT_URL = (string) trim($product->PRODUCT_URL);
				$PRODUCT_URL_PDF = (string) trim($product->PRODUCT_URL_PDF);
				$URL_SUPPORT = (string) trim($product->URL_SUPPORT);
				$THL__SUPPORT = (string) trim($product->THL__SUPPORT);
				$brand = (string) trim($product->brand);
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
				if($pn==''){
					echo $title.': has empty PN';
					continue;
				}

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
					$this->db->delete('live');

					$this->db->insert('live', $live);
					echo "test:".$live['product_number'].'<br>';

					unset($live);
				}

				//Array for categories table
				$copiers_product = array(
					'category' => $c,
					'product_number'=>$pn ,
					'title' => $title ,
					'PRODUCT_URL' => $PRODUCT_URL ,
					'PRODUCT_URL_PDF' => $PRODUCT_URL_PDF ,
					'URL_SUPPORT' => $URL_SUPPORT ,
					'THL__SUPPORT' => $THL__SUPPORT ,
					'brand' => $brand ,
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
					'shipping_class' => 9974
					//'volumetric_weight'???
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
		}//end foreach

		$this->sendImportedProductsByMail($newProducts, 'KONICA Copiers');
		echo "Finnished updating KONICA Copiers.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);
    }

    public function cpi(){
    	$this->load->view('upload_cpi_xml', array('error' => ' ' ));
    }

    public function import_cpi($path){

		if($xml = $this->xml($path)){

			$images = array();
			$this->updateLive('cpi');
		}

		$newProducts = array();
		$i=0;

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'CPI Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start CPI Update'
		);

		foreach($xml->children() as $product) {
			$availability=false;
			set_time_limit(50);

			$cat = (string) trim($product->Item);
			$pn = (string) trim($product->Code);
			if (strpos($cat, 'OKI ') !== false || strpos($cat, 'ΟΚΙ') !== false)
			{
				if(strpos($pn, 'MF') !== false)
					$c = 'multifunction_printers';
				else
					$c = 'printers';
			}
			elseif (strpos($cat, 'ΡRΟJΕCΤΟR ') !== false)
				$c = 'projectors';
			else
				$c ='';

			if($c!=''){

				$availability = $this->makeAvailability((string) trim($product->Availability), 'cpi');

				if(!$availability){
					continue;
				}

				$code = (string) trim($product->Int_Code);
				//$pn = (string) trim($product->Code);
				$title = (string) trim($product->Item);
				$net_price = trim($product->Price);
				$recycle_tax = trim($product->Recycle_Price);
				$description = '';

				$supplier = 'cpi';
				// For fixing brand
				if (strpos($cat, 'OKI ') !== false || strpos($cat, 'ΟΚΙ') !== false)
					$brand = 'OKI';
				elseif (strpos($cat, 'ΡRΟJΕCΤΟR ') !== false)
					$brand = 'BENQ';
				else
					$brand = '';

				//Image cannot be parsed must import it manually
				$imageUrl = '';

				//1. Live
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>$recycle_tax ,
						'supplier' =>$supplier,
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
				$cpi_product = array(
					'category' => $c,
					'product_number' => $pn,
					'brand' => $brand,
					'title' => $title,
					'description' => $description,
					'product_url' => '',
					'net_price'=>$net_price
				);
				/*echo '<pre>';
				print_r($cpi_product);*/
				//2. New products for charateristics tables that load Sku module
				$insert = $this->addProduct ($cpi_product, array(), $imageUrl, $supplier);

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}
			}
		}//end foreach

		$this->sendImportedProductsByMail($newProducts, 'CPI');
		echo "Finnished updating CPI.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);
    }

    public function westnet(){
    	$this->load->view('upload_westnet_xml', array('error' => ' ' ));
    }

        public function import_westnet($path){

		if($xml = $this->xml($path)){

			$images = array();
			$this->updateLive('westnet');
		}

		$newProducts = array();
		$i=0;

		//For log table
		$log = array(
			'log_type'=>'update supplier',
			'user_id'=>'0',
			'log_description'=>'WestNet Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start WestNet Update'
		);

		foreach($xml->children() as $product) {

			$availability=false;
			$title = $description = '';
			set_time_limit(50);

			$cat = (string) trim($product->Item_Category);
			$sc = (string) trim($product->Description);
			$c = '';
			$type = $colour = '';

			switch ($cat) {
				case "Tablets":
					$c = 'tablets';
					break;
				case "Smartphones":
					$c = 'smartphones';
					break;
				case "Card Reader":
					$c = 'card_readers';
					break;
				case "OTHER GAMING ACC.":
				case "Bags/Cases":
				case "NB CASES":
					$c = 'carrying_cases';
					break;
				case "Inkjet":
					$sc_upper = strtoupper($sc);
					if (strpos($sc_upper, 'INK') !== false || strpos($sc_upper, 'MULTI') !== false || strpos($sc_upper, 'PACK') !== false || strpos($sc_upper, 'CARTRIDGE') !== false)
						$c = $cat;
					else
					{
						$type = 'Inkjet';
						$colour = 'Έγχρωμο';

						if (strpos($sc_upper, 'MFP') !== false)
							$c = 'multifunction_printers';
						else
							$c = 'printers';
					}
					break;
				case "All In One PC":
					$c = 'desktops';
					break;
				case "PC Desktop":
					$c = 'desktops';
					break;
				case "HDD 2.5 External":
					$c = $cat;
					break;
				case "USB-Stick":
					$c = 'flash_drives';
					break;
				case "PCX":
					//$c = 'graphic_cards';
					$c = $cat;
					break;
				case "Mouse":
				case "Mouse for Notebooks":
					$c = 'keyboard_mouse';
					$type = 'Mouse';
					break;
				case "SET KEYBOARD & MOUSE":
					$c = 'keyboard_mouse';
					$type = 'Set mouse / keyboard';
					break;
				case "KEYBOARD":
				case "KEYBOARDS":
					$c = 'keyboard_mouse';
					$type = 'Keyboard';
					break;
				case '12.0':
				case '12.0"':
				case '13.3ΆΆ':
				case '13.3ΆΆ"':
				case '10,2':
				case '10,2"':
				case '12.1':
				case '12.1"':
				case '13,3':
				case '13,3"':
				case '14ΆΆ':
				case '14.0':
				case '14.0"':
				case '14.1':
				case '14.1"':
				case '15.4':
				case '15.4"':
				case '17.0':
				case '17.0"':
				case '17,1':
				case '17,1"':
					$c = 'laptops';
					break;
				case "Ram DDR2":
				case "Μνήμες Desktop Branded":
				case "Μνήμες Notebook Branded":
				case "Ram DDR3":
					//$c = 'memories';
					$c = $cat;
					break;
				case "LCD 17":
				case "LCD 19":
				case "LCD 20":
				case "LCD 21":
				case "LCD 22":
				case "LCD 23":
				case "LCD 24":
				case "LCD 30":
					$c = 'monitors';
					break;
				case "LCD TV 22":
				case "LCD TV 24":
				case "LCD TV 26":
				case "LCD TV 27":
					$c = 'monitors';
					$type = 'Monitor TV';
					break;
				case "POWER BANKS":
					$c = 'power_bank';
					break;
				case "Fuser":
					$c = 'printer_fusers';
					break;
				case "Laser Color":
					$type = 'Laser';
					$colour = 'Έγχρωμο';
					if (strpos($sc, 'MFP') !== false)
						$c = 'multifunction_printers';
					else
						$c = 'printers';
					break;
				case "Laser Mono":
					$type = 'Laser';
					$colour = 'Μονόχρωμο';
					if (strpos($sc, 'MFP') !== false)
						$c = 'multifunction_printers';
					else
						$c = 'printers';
					break;
				case "Router":
				case "Expander":
				case "Access Point":
					$c = 'routers';
					break;
				case "HDD 3.5 SATA":
					$c = 'sata_hard_drives';
					break;
				case "Rackmount":
				case "Tower":
					$c = 'servers';
					break;
				case "Antivirus & Security":
					$c = 'software';
					$type = 'Antivirus';
					break;
				case "Windows for PC":
					$c = 'software';
					$type = 'Λειτουργικά Συστήματα';
					break;
				case "Application":
					$c = 'software';
					$type = 'Εφαρμογές γραφείου';
					break;
				case "Headset":
				case "HEADSETS":
				case "SPEAKERS":
					$c = 'speakers';
					break;
				case "SSD 2,5ΆΆ":
					$c = 'ssd';
					break;
				case "Hub/Switch":
					$c = 'switches';
					break;
				case "Laserjet":
					$c = $cat;
					break;
				case "19''":
				case "22''":
				case "26''":
				case "32''":
				case "40''":
				case "42''":
				case "50''":
				case "55''":
				case "60''":
				case "65''":
					$c = 'tv';
					break;
				case "SMART HOME":
					if(strpos($sc, 'PowerCube') !== false || strpos($sc, 'PowerBank') !== false)
						$c = 'ups';
					else
						$c = $cat;
					break;
				case "UPS":
					$c = 'ups';
					break;
				case 'GADGETS':
					if (strpos($sc, 'SCOOTER') !== false){
						$title = $description = str_replace('MINI SCOOTER ', '', $sc);
						$c = 'hoverboards';
					}
					else
						$c = $cat;
					break;
				case 'COOLING STANDS':
					$c = 'cooling_pads';
					break;
				default:
					$c = $cat;
					break;
			}

			//if($c=='hoverboards'){
			if($c!=$cat){

				$availability = $this->makeAvailability((string) trim($product->Stock_Status), 'westnet');

				if(!$availability)
					continue;

				$code = (string) trim($product->Code);
				$pn = (string) trim($product->Part_Number);

				if(substr($pn, -4)=='-EOL')
					continue;

				if ($title=='')
					$title = (string) trim($product->Description);

				$net_price = trim($product->Final_Price);
				if($net_price <= 1.00 || $net_price == '')
					continue;

				$recycle_tax = '';

				if ($description=='')
					$description = (string) trim($product->Description);

				$brand = (string) trim($product->Manufacturer);
				switch ($brand) {
					case "3C":
						$brand = "3Com";
						break;
					case "AL":
						$brand = "Allocacoc";
						break;
					case "OC":
						$brand = "AOC";
						break;
					case "DX":
						$brand = "Arozzi";
						break;
					case "AS":
						$brand = "Asus";
						break;
					case "AV":
						$brand = "Avocent";
						break;
					case "CA":
						$brand = "Canon";
						break;
					case "CB":
						$brand = "CB";
						break;
					case "CH":
						$brand = "Cipherlab";
						break;
					case "CI":
						$brand = "Cisco";
						break;
					case "CR":
						$brand = "Creative";
						break;
					case "CY":
						$brand = "Cygnett";
						break;
					case "DV":
						$brand = "Devolo";
						break;
					case "DL":
						$brand = "Dlink";
						break;
					case "ES":
						$brand = "eset";
						break;
					case "ET":
						$brand = "eSTAR";
						break;
					case "FL":
						$brand = "Finlux";
						break;
					case "FS":
						$brand = "Fujitsu";
						break;
					case "GE":
						$brand = "Genius";
						break;
					case "LA":
						$brand = "Genius";
						break;
					case "GH":
						$brand = "Ghostek";
						break;
					case "GB":
						$brand = "Gigabyte";
						break;
					case "GS":
						$brand = "G-Style";
						break;
					case "GT":
						$brand = "G-Tech";
						break;
					case "HP":
					case "H0":
					case "HW":
						$brand = "HP";
						break;
					case "H1":
						$brand = "HPE";
						break;
					case "HI":
						$brand = "Hitachi";
						break;
					case "KA":
						$brand = "Kaspersky";
						break;
					case "KN":
						$brand = "Kingston";
						break;
					case "LV":
					case "IB":
						$brand = "Lenovo";
						break;
					case "LL":
						$brand = "Lexgo";
						break;
					case "LX":
						$brand = "Lexmark";
						break;
					case "LG":
						$brand = "LG";
						break;
					case "LN":
						$brand = "Linksys";
						break;
					case "LO":
						$brand = "Logitech";
						break;
					case "MY":
						$brand = "MaCally";
						break;
					case "MC":
						$brand = "Mcab";
						break;
					case "MS":
						$brand = "MicroSoft";
						break;
					case "NG":
						$brand = "Netgear";
						break;
					case "OK":
						$brand = "OKI";
						break;
					case "PA":
						$brand = "Panda";
						break;
					case "PH":
						$brand = "Philips";
						break;
					case "PN":
						$brand = "PNY";
						break;
					case "PO":
						$brand = "Polaroid";
						break;
					case "SM":
						$brand = "Samsung";
						break;
					case "SK":
						$brand = "SkyViper";
						break;
					case "SO":
						$brand = "Sony";
						break;
					case "SU":
						$brand = "Sun";
						break;
					case "TS":
						$brand = "Toshiba";
						break;
					case "TP":
						$brand = "TP-Link";
						break;
					case "TR":
						$brand = "Trust";
						break;
					case "TO":
						$brand = "Tucano";
						break;
					case "TU":
						$brand = "TurtleBeach";
						break;
					case "WD":
						$brand = "Western Digital";
						break;
					case "WO":
						$brand = "WOWEE";
						break;
					case "XR":
						$brand = "Xerox";
						break;
					case "XO":
						$brand = "X-One";
						break;
					default:
						$brand = "";
						break;
				}
				if($brand == "")
					continue;

				$supplier = 'westnet';

				//Image cannot be parsed must import it manually
				$imageUrl = '';

				//1. Live
				if($this->checkLiveProduct($pn, $net_price, $supplier)){

					$live = array(
						'category'=>$c ,
						'product_number'=>$pn ,
						'net_price'=>$net_price ,
						'availability'=>$availability ,
						'recycle_tax'=>$recycle_tax ,
						'supplier' =>$supplier,
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
				$westnet_product = array(
					'category' => $c,
					'product_number' => $pn,
					'brand' => $brand,
					'title' => $title,
					'description' => $description,
					'product_url' => '',
					'net_price'=> $net_price,
					'new_item'=> 1
				);

				if ($type != '')
					$westnet_product['type'] = $type;

				if($c == 'multifunction_printers' || $c == 'printers')
					$westnet_product['colour'] = $colour;

				if($c == 'software')
				{
					$westnet_product['dist_type'] = '';
					$shipping_class = Modules::run('categories/makeShippingClass', $westnet_product, $c);
					$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);
					$westnet_product['shipping_class'] = $shipping_class;
					$westnet_product['volumetric_weight'] = $volumetric_weight;
				}

				//2. New products for charateristics tables that load Sku module
				$insert = $this->addProduct ($westnet_product, array(), $imageUrl, $supplier);

				if ($insert)
				{
					if(isset ($newProducts[$c]))
						$newProducts[$c] = $newProducts[$c]+1;
					else
						$newProducts[$c] = 1;
				}
			}
		}//end foreach

		$this->sendImportedProductsByMail($newProducts, 'Westnet');
		echo "Finnished updating Westnet.";

		$log['log_result'] = $newProducts;
		$this->MakeLogEntry($log);
    }

    private function sendImportedProductsByMail($newProducts, $the_supplier){

    	if (!empty($newProducts))//Send Mail Check
		{
			$message='<h2>Νέα προϊόντα από '.$the_supplier.'</h2>';

			foreach ($newProducts as $key => $value){
				$message .= $key.' : '.$value.' (<a href="'.base_url().'/extract/xml/'.$key.'/new">Προβολή XML)</a><br>';
			}
			$subject = 'Νέα προϊόντα από '.$the_supplier;

			Modules::run('emails/send',$subject, $message);
		}
    }



    private function checkLiveProduct($pn, $price, $supplier){

    	$query = $this->db->get_where('live', array('product_number' => $pn), 1, 0);

    	if($query->num_rows()>0){

    		foreach ($query->result() as $row)
			{
				if($row->category == 'tv')
				{
					$tvs = Modules::run("crud/get",'tv',array('product_number' => $pn));
					$tvs = $tvs->row()->brand;
					if($tvs == 'SKYWORTH')
						return false;
				}

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
    	}
    	else{
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



    public function addProduct($product, $chars_array, $f=null , $supplier){

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

		//Do not forget volumetric_weight if needed!!!!!!!!!!!!!!!!!!

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
				$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);

				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class,
				'volumetric_weight' =>$volumetric_weight

				);
			}elseif($c == 'printers' || $c == 'multifunction_printers' || $c == 'monitors'){

				//$price = array('price'=>$product['net_price']);

				//$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
				//$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				//'shipping_class' => $shipping_class,
				//'volumetric_weight' => $volumetric_weight
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
				'shipping_class' => $product['shipping_class'],
				'volumetric_weight' => $product['volumetric_weight']
				);

			}elseif($c == "copiers"){
				unset($product['category']);
				$product['sku'] = $sku;

				$categoryData = $product;
			}
			elseif($c == 'hoverboards'){
				$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);
				$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'shipping_class' => $shipping_class,
				'volumetric_weight' => $volumetric_weight
				);
			}elseif($supplier == 'partnernet'){

				$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);
				$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=>'',
				'shipping_class' => $shipping_class,
				'volumetric_weight' => $volumetric_weight
				);
			}
			else
			{
				$shipping_class = '';
				if($c == "carrying_cases" || $c == "external_hard_drives" ||
				 $c == "sata_hard_drives" || $c == "ssd" || $c == "speakers" ||
				 $c == "power_bank" || $c == "keyboard_mouse"  || $c == "servers"  ||
				 $c == "routers"  || $c == "switches"  || $c == "laptops"  || $c== "desktops" || $c == "tablets"  || $c == "smartphones" ||
				 $c == "cables" || $c == "patch_panels" || $c == "racks" || $c =="optical_drives" || $c == "card_readers" || $c == "flash_drives" ||
				 $c == "power_supplies" || $c == "projectors" || $c == "cases" || $c == "fans" || $c == "motherboards" || $c == "graphic_cards" || $c == "cpu" ||
				 $c == "memories" || $c == "hoverboards" || $c == "printer_fusers" || $c == "printer_drums" || $c == "printer_belts" || 
				 $c == "ups" || $c =="tv" || $c == "accessories" || $c == "cable_accessories" || $c == "cooling_pads" || $c == "powerlines" || 
				 $c == "ip_phones" || $c == "server_controllers" || $c == "server_cpu" || $c == "server_hard_drives" || $c == "server_memories" || 
				 $c == "server_power_supplies" || $c == 'nas' || $c == 'firewalls' || $c == 'gaming_chairs' || $c == 'ip_cameras'){

					$shipping_class = Modules::run('categories/makeShippingClass', $chars_array, $c);
					$volumetric_weight = Modules::run('categories/getWeight', $shipping_class);
				}
				$categoryData = array(
				'brand'=> $product['brand'],
				'sku'=> $sku,
				'product_number'=> $product['product_number'],
				'title'=> $product['title'],
				'description'=> strip_tags($product['description']),
				'supplier_product_url'=> $product['product_url'],
				'shipping_class' => $shipping_class,
				'volumetric_weight' => $volumetric_weight
				);

				if($c=='nas' || $c == "ip_phones")
					$categoryData['type'] = $product['type'];

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
			if($supplier == 'oktabit')
			{
				if ($c == 'ups'){
					$categoryData['model'] = str_replace('UPS ', '',$product['title']);
				}
				elseif ($c == 'powerlines') {
					$categoryData['model'] = str_replace(' Powerline', '',$product['title']);
					$categoryData['model'] = str_replace(' Powerlin', '',$categoryData['model']);
					$categoryData['model'] = str_replace(' POWERLINE', '',$categoryData['model']);
					$categoryData['model'] = str_replace(' POWERLIN', '',$categoryData['model']);
				}
				if($c == 'ups' || $c == 'routers' || $c == 'gaming_chairs')
				{
					//Add PDF files
					$etd_product_url_pdf = $this->AddProductPdf($product['code']);
					
					if($etd_product_url_pdf != false )
						$categoryData['product_url_pdf'] = $etd_product_url_pdf;
				}
				
				// Make products not new items to parse immediately
				if($c == 'speakers' || $c == 'gaming_chairs' || $c == 'ups' || $c == 'routers' || $c == 'powerlines' || $c == 'ip_cameras')
					$categoryData['new_item'] = 0;
			}
			
			if($supplier == 'braintrust' && $c != "laptops")
			{
				$categoryData ['new_item'] = 1;
			}
			if(isset($product['brand'] )){
			$product['brand'] = strtoupper($product['brand']);
			}else{
			$product['brand'] = strtoupper($product['Brand']);
			}
			switch ($product['brand']) {
				case 'APC':
					$categoryData['support_url'] = 'http://www.schneider-electric.gr/sites/greece/gr/support/contact/we-care.page';
					$categoryData['support_tel'] = '8001162900 (επιλογή 3)';
					break;
				case 'DELL':
					$categoryData['support_url'] = 'http://www1.euro.dell.com/content/topics/topic.aspx/emea/contact/elgr?c=gr&l=el';
					$categoryData['support_tel'] = '80044149518 / 2108129810 / 2108129855';
					break;
				case 'HP':
					$categoryData['support_url'] = 'http://support.hp.com/gr-el/';
					$categoryData['support_tel'] = '80111225547 / 2109696416 ';
					break;
				case 'ACER':
					$categoryData['support_url'] = 'http://www.acer.com/ac/el/GR/content/service-contact';
					$categoryData['support_tel'] = '8015002000';
					break;
				/*case 'INTEL':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '';
					break;*/
				case 'PHILIPS':
					$categoryData['support_url'] = 'http://www.philips.gr/c-m/consumer-support';
					$categoryData['support_tel'] = '80031221223';
					break;
				case 'AOC':
					$categoryData['support_url'] = 'www.aoc-service.com ';
					$categoryData['support_tel'] = '‎2102409150';
					break;
				case 'MICROSOFT':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2105197500';
					break;
				case 'LENOVO':
					$categoryData['support_url'] = 'http://support.lenovo.com/gr/en';
					$categoryData['support_tel'] = '2111984507(Idea) / 2104800499(Think)';
					break;
				case 'LG':
				case 'LG ELECTRONICS':
					$categoryData['support_url'] = 'http://www.lg.com/gr/support';
					$categoryData['support_tel'] = '80111200900';
					break;
				case 'VIEWSONIC':
					$categoryData['support_url'] = 'http://www.alman.gr/el/';
					$categoryData['support_tel'] = '2102409150';
					break;
				case 'ASUS':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '80044142044';
					break;
				case 'GIGASET':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2106619010';
					break;
				case 'GIGASET':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2106619010';
					break;
				case 'MICROSOFT':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2111206000';
					break;
				case 'SAMSUNG':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '801117267864 ή 2106897691';
					break;
				case 'TP-LINK':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2106148834';
					break;
				case 'BENQ':
					$categoryData['support_url'] = '';
					$categoryData['support_tel'] = '2104805800';
					break;
				case 'MSI':
					$categoryData['support_url'] = '';
					//$categoryData['support_tel'] = '2106995825';
					$categoryData['support_tel'] = 'braintrust@support.gr';
					break;
				default:
					break;
			}

			if(isset($product['type']))
			{
				if($categoryData['type'] == '' && $product['type'] != '')
				{
					$categoryData['type'] = $product['type'];
				}
			}

			if(Modules::run("categories/insert", $c, $categoryData)){

				$insert = true;

			}
			else{
				echo 'issue';
			}


			//3. Add Product Images
			if($f){
				$this->AddProductImages($product, $f, $supplier, $sku);
			}		
		}
		//if($sku = Modules::run('sku/checkSku',$skuArray)){
		/*else
		{
			if($c == 'printers' || $c == 'multifunction_printers'){
				$price = array('price'=>(float)$product['net_price']);
				//print_r($price['price']);
				$shipping_class  = Modules::run('categories/makeShippingClass',$price, $c, true);
					//Must add volumetricWeight!!!
				$this->db->set('shipping_class',$shipping_class);
				$this->db->where('sku',$sku);
				$this->db->update($c);
			}
			/*else if($c == 'memories') //Fix for updating image
			{
				$mem_images = $this->AddProductImages($product, $f, $supplier, $sku);
			}
    	}*/

    	return $insert;
    }


    public function AddProductImages($product, $f, $supplier, $sku){

    	if ($supplier == 'oktabit' )
    	{
			$f=0;
			
    		while($f < 5){ // because we want to get max 5 images

				if($f=="0"){
					$tail='';
				}else{
					$tail = '_'.$f;
				}

				$imageData = array(
					'src' => "https://www.oktabit.gr/images/photos/".$product['code']."".$tail.".jpg",
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
    	else if ($supplier == 'ddc' )
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
    	elseif( $supplier == 'etd' || $supplier=='partnernet')
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
							'brand' => $product['brand'] ,
							'part_number' => $product['product_number'] ,
							'tail' => ''
						);


						Modules::run('images/getImage',$imageData);
		}
		elseif( $supplier == 'netconnect')
    	{
    		$imageData = array(
							'src' => $f,
							'sku' => $sku ,
							'brand' => $product['brand'] ,
							'part_number' => $product['product_number'] ,
							'tail' => ''
						);


						Modules::run('images/getImage',$imageData);
    	}//elseif( $supplier == 'konica')
    	/* //For cpi if image are parsable
    	elseif( $supplier == 'cpi')
    	{
    		$imageData = array(
							'src' => $f,
							'sku' => $sku ,
							'brand' => $product['brand'] ,
							'part_number' => $product['product_number'] ,
							'tail' => ''
						);

						Modules::run('images/getImage',$imageData);
    	}
    	*/
	}
	
	public function AddProductPdf($product_code){

		$pdfData = array(
			'src' => "https://www.oktabit.gr/images/pdfs/".$product_code.".pdf",
			'code' =>$product_code
		);

		return Modules::run('images/getPdf',$pdfData);
	}

	private function addProductChars($category, $product_code, $charsArray){

		$chars_array = array();
		if ($category == 'carrying_cases'){

			$chars_array = array(
				'type' => 'Τύπος',
				'size' => 'Μέγεθος οθόνης',
				'material' => 'Υλικό κατασκευής',
				'colour' => 'Χρώμα',
				'dimensions' => 'Διαστάσεις (πλάτος x ύψος x πάχος, σε mm)'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'power_bank'){

			$chars_array = array(
				'battery_capacity' => "Χωρητικότητα Μπαταρίας",
				'recharge_time' => "Χρόνος επαναφόρτισης ως το 90-100% (ώρες)",
				'charging_output' => "Έξοδος φόρτισης",
				'dimensions' => "Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);

			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'powerlines'){

			$chars_array = array(
				'wi-fi' => 'Ασύρματο',
				'passthrough' => 'Passthrough φις',
				'overline_speed' => 'Ταχύτητα (Mbps)',
				'ethernet_ports' => 'Θύρες LAN',
				'packaging' => 'Τεμάχια συσκευασίας',
				'year_warranty' => 'Εγγύηση'
			);
	
			foreach ($chars_array as $key => $value) {
				$prod_char = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';

				if($value == 'Ταχύτητα (Mbps)')
					$chars_array[$key] = $prod_char.' Mbps';
				else	
					$chars_array[$key] = isset($prod_char) ? $prod_char : '';
			}
		}
		elseif ($category == 'routers'){

			$chars_array = array(
				'type' => 'Τύπος',
				'wireless' => 'Wireless',
				'line_type' => 'Τύπος γραμμής',
				'lan_ports' => 'Θύρες LAN',
				'wan_ports' => 'Θύρες WAN',
				'supported_protocols' => 'Υποστηριζόμενα πρωτόκολλα',
				'vpn' => 'VPN',
				'removable_antenna' => 'Αποσπώμενη κεραία',
				'poe' => 'POE',
				'outdoor' => 'Εξωτερικού χώρου',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {

				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';

				if($value == 'Τύπος')
					$chars_array[$key] = 'Access Point';
			}
		}
		elseif ($category == 'ip_cameras'){

			$chars_array = array(
				'video_resolution' => 'Ανάλυση video',
				'pan_tilt' => 'Pan, Tilt',
				'min_brightness' => 'Ελάχιστη φωτεινότητα (Lux)',
				'wireless' => 'Wireless',
				'zoom' => 'Zoom',
				'audio' => 'Ήχος',
				'outdoor' => 'Εξωτερικού χώρου',
				'poe' => 'PoE',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);

			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'switches'){

			$chars_array = array(
				'ports' => "Θύρες",
				'network_speed' => "Ταχύτητα Δικτύου",
				'sfp_ports' => "SFP θύρες",
				'manage' => "Manage",
				'poe' => "POE",
				'layer' => "Layer",
				'rackmount' => "RackMount",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}			
		}
		elseif ($category == 'speakers'){

			$chars_array = array(
				'type' => "Τύπος ηχείων",
				'watt' => "Ισχύς Watt (RMS)",
				'dolby_digital_decoding' => "Dolby Digital Decoding",
				'headphones_input' => "Είσοδος ακουστικών",
				'input' => "Είσοδος",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}						
		}
		elseif ($category == 'external_hard_drives'){

			$chars_array = array(
				'capacity' => "Χωρητικότητα",
				'connection' => "Σύνδεση",
				'size' => "Διάσταση",
				'cache' => "Cache",
				'rpm' => "Στροφές λειτουργίας",
				'colour' => "Χρώμα",
				'weight' => "Βάρος (γραμμάρια)",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}		
		}
		elseif ($category == 'sata_hard_drives'){

			$chars_array = array(
				'capacity' => "Χωρητικότητα",
				'connection' => "Σύνδεση",
				'size' => "Διάσταση",
				'cache' => "Cache",
				'rpm' => "Στροφές λειτουργίας",
				'packaging' => "Συσκευασία",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}	
		}
		elseif ($category == 'ssd'){

			$chars_array = array(
				'capacity_from_to' => "Χωρητικότητα (Από εώς)",
				'capacity' => "Χωρητικότητα",
				'connection' => "Σύνδεση",
				'size' => "Διάσταση",
				'read_speed' => "Ταχύτητα ανάγνωσης (MB/s)",
				'write_speed' => "Ταχύτητα εγγραφής (MB/s)",
				'manufacture_technology' => "Τεχνολογία κατασκευής",
				'mtbf' => "MTBF (ώρες)",
				'packaging' => "Συσκευασία",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}	
		}
		elseif ($category == 'keyboard_mouse'){

			$chars_array = array(
				'type' => "Τύπος συσκευής",
				'usage' => "Χρήση",
				'connection' => "Σύνδεση",
				'dpi' => "Ανάλυση DPI",
				'mouse_buttons' => "Κουμπιά mouse",
				'technology' => "Τεχνολογία",
				'language' => "Γλώσσα πληκτρολογίου",
				'programmable_buttons' => "Προγραμματιζόμενα πλήκτρα",
				'multimedia_buttons' => "Multimedia πλήκτρα",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}	
		}
		elseif ($category == 'servers'){

			$chars_array = array(
				'form_factor' => "Τύπος θήκης",
				'server_size' => "Rack height",
				'power_supply' => "Τροφοδοτικό",
				'cpu' => "Κατασκευαστής επεξεργαστή",
				'cpu_generation' => "Επεξεργαστής",
				'cpu_model' => "",
				'cpu_frequency' => "",
				'cpu_cores	' => "",
				'cpu_cache' => "",
				'memory_size' => "Χωρητικότητα μνήμης",
				'memory_type' => "Τύπος μνήμης",
				'memory_frequency' => "",
				'hdd' => "Σκληρός δίσκος (χωρητικότητα / στροφές λειτουργίας)",
				'hdd_type' => "Τύπος σκληρών δίσκων",
				'controller_raid' => "RAID",
				'ethernet' => "ethernet",
				'optical_drive' => "Οπτικά μέσα",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}	
		}
		elseif ($category == 'optical_drives')
		{
			$chars_array = array(
				'device' => "Συσκευή",
				'type' => "Τύπος",
				'connection' => "Σύνδεση",
				'write_speed' => "Ταχύτητα εγγραφής",
				'read_speed' => "Ταχύτητα ανάγνωσης",
				'color' => "Χρώμα",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}				
		}
		elseif ($category == 'card_readers')
		{
			$chars_array = array(
				'device_type' => "Τύπος συσκευής",
				'card_types' => "Υποστήριξη τύπων μνήμης",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}			
		}
		elseif ($category == 'flash_drives')
		{
			$chars_array = array(
				'capacity' => "Χωρητικότητα",
				'connection' => "Σύνδεση",
				'security' => "Ασφάλεια",
				'read_speed' => "Ταχύτητα ανάγνωσης (MB/sec)",
				'write_speed' => "Ταχύτητα εγγραφής (MB/sec)",
				'color' => "Χρώμα",
				'closing_type' => "Τύπος κλεισίματος",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}				
		}
		elseif ($category == 'power_supplies')
		{
			$chars_array = array(
				'type' => "Τύπος",
				'power' => "Ισχύς (Watt)",
				'energy_efficiency' => "Energy-Efficient",
				'fan_size' => "Ανεμιστήρας",
				'output_connectors' => "Υποδοχές εξόδου",
				'pfc' => "PFC",
				'dimensions' => "Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}	
		}
		elseif ($category == 'cases')
		{
			$chars_array = array(
				'type' => "Τύπος",
				'motherboard_size' => "Υποστήριξη μητρικής",
				'color' => "Χρώμα",
				'external_5_25' => 'Εξωτερικές Θέσεις 5,25"',
				'external_3_5' => 'Εξωτερικές Θέσεις 3,5"',
				'internal_5_25' => 'Εσωτερικές Θέσεις 3,5"',
				'internal_3_5' => 'Εσωτερικές Θέσεις 2,5"',
				'hdd_dock' => "Docking σκληρού δίσκου",
				'installed_fans' => "Εγκατεστημένοι ανεμιστήρες",
				'side_window' => 'Πλαϊνό Παράθυρο',
				'dimensions' => "Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)",
				'weight' => "Βάρος (κιλά)",
				'power_supply' => "Τροφοδοτικό",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'fans')
		{
			$chars_array = array(
				'heat_sink_type' => "Τύπος Ψύκτρας",
				'fan_diameter' => "Μέγεθος ανεμιστήρα (mm)",
				'fan_number' => "Αριθμός ανεμιστήρων",
				'compatibility' => "Συμβατότητα",
				'dimensions' => "Διαστάσεις (μήκος x πλάτος x ύψος, σε mm)",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'motherboards')
		{
			$chars_array = array(
				'cpu_brand' => 'Κατασκευαστής επεξεργαστή',
				'socket' => 'Socket',
				'chipset' => 'Chipset',
				'integrated_cpu' => 'Ενσωματωμένος επεξεργαστής',
				'type' => 'Τύπος μητρικής',
				'ram_type' => 'Υποστηριζόμενη μνήμη',
				'max_ram' => 'Μέγιστο μέγεθος μνήμης',
				'integrated_graphics' => 'Ενσωματωμένη κάρτα γραφικών',

				'crossfire_x' => "Υποστήριξη CrossfireX / SLI",
				'sli' => "",
				
				'sound' => 'Ήχος',
				'sata_2' => 'SATA 3Gb/s',
				'sata_3' => 'SATA 6Gb/s',
				'raid' => 'Raid',
				'pci_express_x8_16' => 'PCI Express (x16, x8)',
				'pci_express_x1_4' => 'PCI Express (x1, x4)',
				'pci' => 'PCI',
				'usb_2' => 'USB 2.0',
				'usb_3' => 'USB 3.0',
				'usb_3_1' => 'USB 3.1',

				'firewire' => "Firewire / eSATA",
				'e_sata' => "",
				
				'network' => 'Δίκτυο',
				
				'serial' => "Σειριακή θύρα / Παράλληλη θύρα",
				'parallel' => "",
				
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			$sli = $esata = $serial = false;
			foreach ($chars_array as $key => $value) {

				if ($sli || $esata || $serial)
					continue;
				
				$prod_char = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';

				if($value == 'Υποστήριξη CrossfireX / SLI')
				{
					$sli = true;
					if($whereIs = strpos( $prod_char, '/'))
					{
						$etd_crossfire_x = trim(substr($prod_char, 0, $whereIs));
						$etd_sli = trim(substr($prod_char, $whereIs+1, strlen($prod_char)));

						if($etd_crossfire_x == 'No' || $etd_crossfire_x == 'ΟΧΙ')
							$chars_array['crossfire_x'] = 'ΟΧΙ';
						elseif($etd_crossfire_x == 'Yes' || $etd_crossfire_x == 'ΝΑΙ')
							$chars_array['crossfire_x'] = 'ΝΑΙ';

						if($etd_sli == 'No' || $etd_sli == 'ΟΧΙ')
							$chars_array['sli'] = 'ΟΧΙ';
						elseif($etd_sli == 'Yes' || $etd_sli == 'ΝΑΙ')
							$chars_array['sli'] = 'ΝΑΙ';
					}
				}
				elseif($value == 'Firewire / eSATA')
				{
					$esata = true;
					if($whereIs = strpos( $prod_char, '/'))
					{
						$etd_firewire = trim(substr($prod_char, 0, $whereIs));
						$etd_e_sata = trim(substr($prod_char, $whereIs+1, strlen($prod_char)));

						if($etd_firewire == 'No' || $etd_firewire == 'ΟΧΙ')
							$chars_array['firewire'] = 'ΟΧΙ';
						elseif($etd_firewire == 'Yes' || $etd_firewire == 'ΝΑΙ')
							$chars_array['firewire'] = 'ΝΑΙ';

						if($etd_e_sata == 'No' || $etd_e_sata == 'ΟΧΙ')
							$chars_array['e_sata'] = 'ΟΧΙ';
						elseif($etd_e_sata == 'Yes' || $etd_e_sata == 'ΝΑΙ')
							$chars_array['e_sata'] = 'ΝΑΙ';
					}
				}
				elseif($value == 'Σειριακή θύρα / Παράλληλη θύρα')
				{
					$serial = true;
					if($whereIs = strpos( $prod_char, '/'))
					{
						$etd_serial = trim(substr($prod_char, 0, $whereIs));
						$etd_parallel = trim(substr($prod_char, $whereIs+1, strlen($prod_char)));

						if($etd_serial == 'No' || $etd_serial == 'ΟΧΙ')
							$chars_array['serial'] = 'ΟΧΙ';
						elseif($etd_serial == 'Yes' || $etd_serial == 'ΝΑΙ')
							$chars_array['serial'] = 'ΝΑΙ';

						if($etd_parallel == 'No' || $etd_parallel == 'ΟΧΙ')
							$chars_array['parallel'] = 'ΟΧΙ';
						elseif($etd_parallel == 'Yes' || $etd_parallel == 'ΝΑΙ')
							$chars_array['parallel'] = 'ΝΑΙ';
					}
				}
				else              
					$chars_array[$key] = isset($prod_char[$value]) ? $prod_char[$value] : '';
			}
		}
		elseif ($category == 'graphic_cards')
		{
			$chars_array = array(
				'chip_brand' => 'Κατασκευαστής chip',
				'gpu' => 'Επεξεργαστής γραφικών',
				'core_frequency' => 'Συχνότητα πυρήνα (MHz)',
				'manufacturer_technology' => 'Τεχνολογία κατασκευής',
				'ram_size' => 'Μέγεθος μνήμης',
				'ram_type' => 'Τύπος μνήμης',
				'ram_frequency' => 'Συχνότητα μνήμης (MHz)',
				'ram_channel' => 'Δίαυλος μνήμης',
				'connection' => 'Σύνδεση',
				'direct_x' => 'DirectX',
				'output_ports' => 'Θύρες εξόδου',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'cpu')
		{
			$chars_array = array(
				'family' => 'Οικογένεια επεξεργαστή',
				'cpu_model' => 'Μοντέλο επεξεργαστή',
				'frequency' => 'Συχνότητα',
				'turbo_core' => 'Turbo Core',
				'turbo_boost' => 'Turbo Boost',
				'socket' => 'Socket',
				'thermal_design_power' => 'Thermal Design Power',
				'cache' => 'Συνολική Μνήμη Cache (L2 + L3)',
				'construction_technology' => 'Τεχνολογία κατασκευής',
				'core_num' => 'Αριθμός πυρήνων',
				'threads' => 'Threads',
				'integrated_graphic' => "Ενσωματωμένη κάρτα/chip γραφικών",
				'heat_sink' => "Ψύκτρα",
				'packaging' => "Συσκευασία",
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'memories')
		{
			$chars_array = array(
				'type' => 'Τύπος μνήμης',
				'capacity' => 'Χωρητικότητα μνήμης',
				'quantity' => 'Τεμάχια',
				'frequency' => 'Συχνότητα λειτουργίας',
				'cas_latency' => 'CAS Latency',
				'voltage' => 'Τάση λειτουργίας',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {
				$chars_array[$key] = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';
			}
		}
		elseif ($category == 'gaming_chairs'){

			$chars_array = array(
				'size' => 'Size',
				'colour' => 'Χρώμα',
				'adjustable_arm' => 'Ρυθμιζόμενος Βραχίονας',
				'adjustable_back_slope'=> 'Ρυθμιζόμενη Κλίση Πλάτης',
				'back_height' => 'Ύψος Πλάτης',
				'shoulders_width' => 'Πλάτος Ώμων',
				'pillows' => 'Μαξιλάρια',
				'wheels' => 'Ρόδες',
				'base' => 'Βάση',
				'mechanism' => 'Μηχανισμός',
				'material' => 'Υλικό Καλύμματος Κάρεκλας',
				'mechanism' => 'Μηχανισμός',
				'max_load' => 'Μέγιστο Φορτίο',
				'lifter_type' => 'Τύπος Ανυψωτήρα',
				'weight' => 'Βάρος',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			foreach ($chars_array as $key => $value) {

				$prod_char = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';

				if($value == 'Ρυθμιζόμενη Κλίση Πλάτης')
				{
					$chars_array[$key] = $prod_char;
					$chars_array[$key] = str_replace("and#7506", "&deg", $chars_array['adjustable_back_slope']);
				}
				else	
					$chars_array[$key] = isset($prod_char) ? $prod_char : '';
			}
		}
		elseif ($category == 'ups'){

			$chars_array = array(
				'type' => 'Τύπος συσκευής',
				'strength_va' => 'Παρεχόμενη ισχύς (VA)',
				'strength_w' => 'Παρεχόμενη ισχύς (Watt)',
				'waveform_output' => 'Waveform output',
				'input_phase' => 'Είσοδος (φάση)',
				'output_phase' => 'Έξοδος (φάση)',
				'battery_endurance_full_load' => 'Αυτονομία σε full load (λεπτά)',
				'battery_endurance_half_load' => 'Αυτονομία σε half load (λεπτά)',
				'recharge_time'=> 'Χρόνος επαναφόρτισης',
				'battery_type' => 'Μπαταρία',
				'connection' => 'Σύνδεση',
				'usb' => "",
				'rack_mount' => 'Rack mount',
				'form_factor' => "",
				'dimensions' => 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)',
				'weight' => 'Βάρος (κιλά)',
				'year_warranty' => 'Εγγύηση (μήνες)',
				'warranty' => 'Τύπος εγγύησης'
			);
			$usb = $rack = false;
			foreach ($chars_array as $key => $value) {

				if($usb || $rack)
					continue;

				$prod_char = isset($charsArray[strtoupper($product_code)][$value]) ? $charsArray[strtoupper($product_code)][$value] : '';

				if($value == 'Χρόνος επαναφόρτισης')
				{
					$time = str_replace(' hours', '', $prod_char);
					$chars_array[$key] = $time;
				}
				elseif($value == 'Σύνδεση')
				{
					$usb = true;
					if (strpos($prod_char, 'USB') !== false) {
						$chars_array['usb']='ΝΑΙ';
					}
					else
						$chars_array['usb']='ΟΧΙ';
		
					$chars_array[$key] = $prod_char;
				}
				elseif($value == 'Rack mount')
				{
					$rack = true;
					$form_factor = $rack_mount = '';

					if($prod_char == 'Yes')
					{
						$rack_mount = 'ΝΑΙ';
						$form_factor = 'Rack';
					}
					elseif($prod_char == 'Yes (optional)')
					{
						$rack_mount = 'ΠΡΟΑΙΡΕΤΙΚΟ';
						$form_factor = 'Tower';
					}
					else
					{
						$rack_mount = 'ΟΧΙ';
						$form_factor = 'Tower';
					}

					$chars_array['form_factor'] = $form_factor;
					$chars_array[$key] = $rack_mount;
				}
				else	
					$chars_array[$key] = isset($prod_char) ? $prod_char : '';
			}
		}
		return $chars_array;
	}
/*
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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
				'type' => "Access Point",
				'wireless' => "",
				'line_type' => "",
				'lan_ports' => "",
				'wan_ports' => "",
				'supported_protocols' => "",
				'vpn' => "",
				'removable_antenna' => "",
				'poe'=> "",
				'outdoor'=> "",
				'year_warranty'=> "",
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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						case 'poe':
							$chars_array['POE']=$chars_value;
							break;
						case 'outdoor':
							$chars_array['Εξωτερικού χώρου']=$chars_value;
							break;
						case 'year_warranty':
							if($chars_value/12 >=1)
							{
								if($chars_value/12 == 1)
									$chars_array['Εγγύηση (μήνες)'] = (string)($chars_value/12).' έτος';
								else
									$chars_array['Εγγύηση (μήνες)'] = (string)($chars_value/12).' έτη';
							}
							else
							$chars_array['Εγγύηση (μήνες)'] = $chars_value;
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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';


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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
						$chars_value = 'ΝΑΙ';
					else if ($chars_value == 'No')
						$chars_value = 'ΟΧΙ';
					else if ($chars_value == '-')
						$chars_value = '';

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
		else if ($category == 'gaming_chairs'){

			$chars_array = array(
				'size'=>"",
				'colour'=>"",
				'adjustable_arm'=>"",
				'adjustable_back_slope'=>"",
				'back_height'=>"",
				'shoulders_width'=>"",
				'pillows'=>"",
				'wheels'=>"",
				'mechanism'=>"",
				'material'=>"",
				'max_load'=>"",
				'lifter_type'=>"",
				'weight'=>""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Size':
							$chars_array['size']=$chars_value;
							break;
						case 'Χρώμα':
							$chars_array['colour']=$chars_value;
							break;
						case 'Ρυθμιζόμενος Βραχίονας':
							$chars_array['adjustable_arm']=$chars_value;
							break;
						case 'Ρυθμιζόμενη Κλίση Πλάτης':
							$chars_array['adjustable_back_slope']=$chars_value;
							$chars_array['adjustable_back_slope'] = str_replace("and#7506", "&deg", $chars_array['adjustable_back_slope']);
							break;
						case 'Ύψος Πλάτης':
							$chars_array['back_height']=$chars_value;
							break;
						case 'Πλάτος Ώμων':
							$chars_array['shoulders_width']=$chars_value;
							break;
						case 'Μαξιλάρια':
							$chars_array['pillows']=$chars_value;
							break;
						case 'Ρόδες':
							$chars_array['wheels']=$chars_value;
							break;
						case 'Βάση':
							$chars_array['base']=$chars_value;
							break;
						case 'Μηχανισμός':
							$chars_array['mechanism']=$chars_value;
							break;
						case 'Υλικό Καλύμματος Κάρεκλας':
							$chars_array['material']=$chars_value;
							break;
						case 'Μηχανισμός':
							$chars_array['mechanism']=$chars_value;
							break;
						case 'Μέγιστο Φορτίο':
							$chars_array['max_load']=$chars_value;
							break;
						case 'Τύπος Ανυψωτήρα':
							$chars_array['lifter_type']=$chars_value;
							break;
						case 'Βάρος':
							$chars_array['weight']=$chars_value;
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
		elseif ($category == 'ups'){

			$chars_array = array(
				'type'=>"",
				'strength_va'=>"",
				'strength_w'=>"",
				'waveform_output'=>"",
				'input_phase'=>"",
				'output_phase'=>"",
				'battery_endurance_full_load'=>"",
				'battery_endurance_half_load'=>"",
				'recharge_time'=>"",
				'battery_type'=>"",
				'usb'=>"",
				'connection'=>"",
				'form_factor'=>"",
				'rack_mount'=>"",
				'dimensions'=>"",
				'weight'=>"",
				'year_warranty'=>"",
				'warranty'=>""
			);

			foreach($char_xml->children() as $chars){

				$okt_chars_code = (string) trim($chars->product[0]);

				if($product_code == $okt_chars_code)
				{
					$is_found = true;
					$chars_title = (string) trim($chars->atribute[0]);
					$chars_value = (string) trim($chars->value[0]);

					switch ($chars_title) {
						case 'Τύπος συσκευής':
							$chars_array['type']=$chars_value;
							break;
						case 'Παρεχόμενη ισχύς (VA)':
							$chars_array['strength_va']=$chars_value;
							break;
						case 'Παρεχόμενη ισχύς (Watt)':
							$chars_array['strength_w']=$chars_value;
							break;
						case 'Waveform output':
							$chars_array['waveform_output']=$chars_value;
							break;
						case 'Είσοδος (φάση)':
							$chars_array['input_phase']=$chars_value;
							break;
						case 'Έξοδος (φάση)':
							$chars_array['output_phase']=$chars_value;
							break;
						case 'Αυτονομία σε full load (λεπτά)':
							$chars_array['battery_endurance_full_load']=$chars_value;
							break;
						case 'Αυτονομία σε half load (λεπτά)':
							$chars_array['battery_endurance_half_load']=$chars_value;
							break;
						case 'Χρόνος επαναφόρτισης':
							$time = str_replace(' hours','',$chars_value);
							$chars_array['recharge_time']=$time;
							break;
						case 'Μπαταρία':
							$chars_array['battery_type']=$chars_value;
							break;
						case 'Σύνδεση':
							if (strpos($chars_value, 'USB') !== false) {
								$chars_array['usb']='ΝΑΙ';
							}
							else
								$chars_array['usb']='ΟΧΙ';

							$chars_array['connection']=$chars_value;
							break;
						case 'Rack mount':
							$form_factor = $rack_mount = '';
							if($chars_value == 'Yes')
							{
								$rack_mount = 'ΝΑΙ';
								$form_factor = 'Rack';
							}
							elseif($chars_value == 'Yes (optional)')
							{
								$rack_mount = 'ΠΡΟΑΙΡΕΤΙΚΟ';
								$form_factor = 'Tower';
							}
							else
							{
								$rack_mount = 'ΟΧΙ';
								$form_factor = 'Tower';
							}

							$chars_array['form_factor']=$form_factor;
							$chars_array['rack_mount']=$rack_mount;
							break;
						case 'Διαστάσεις (πλάτος x ύψος x βάθος, σε mm)':
							$chars_array['dimensions']=$chars_value;
							break;
						case 'Βάρος (κιλά)':
							$chars_array['weight']=$chars_value;
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


		/////////////

	}*/

    private function updateProduct($table, $data, $product_number){

    	$this->db->where('product_number', $product_number);
    	$this->db->set($data);
    	$this->db->update($table);
    	echo 'update '.$product_number.' in table: '.$table.'<br>';
    }


    public function makeAvailability($availability, $supplier){

    	if($supplier == 'edit'){
    		switch ($availability) {
    			case '0':
	    			$av = 'Αναμονή παραλαβής';
	    			break;
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
	    			break;
	    		case '2':
	    			$av = 'Άμεσα Διαθέσιμο';
	    			break;

	    		default:
	    			return false;
	    			break;
	    	}

	    	return $av;
    	}elseif($supplier == 'oktabit'){

/*
1.Διαθεσιμο
2.Οριακο (λιγα κομματια ακομη στις αποθηκες Oktabit)
3 και 4   Αναμενεται    (μηδεν διαθεσιμο αλλα το εχουμε παραγγειλει στον προμηθευτη)
5. Κατοπιν παραγγελιας (μηδεν διαθεσιμο και το παραγγελνουμε στον προμηθευτη εφοσον το παραγγειλετε σε εμας)
6. NOT AVAILABLE
*/

	    	switch ($availability) {
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
	    			break;
	    		case '2':
	    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
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

    	}elseif($supplier == 'quest'){

    		switch ($availability) {
    			case 'X':
    			case 'x':
    				$av="Κατόπιν παραγγελίας σε 1-3 εργάσιμες";
    				break;
    			case '0':
    			case '1':
    			case '2':
    			case '3':
    			case '4':
    			case '5':
    			case '6':
    			case '7':
    			case '8':
    			case '9':
	    			$av = 'Αναμονή παραλαβής';
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

    				$av="Κατόπιν παραγγελίας σε 1-3 εργάσιμες";

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

    	}elseif($supplier == 'ddc' ){

    		switch ($availability) {
	    		case '0':
	    			$av = 'Αναμονή παραλαβής';
	    			return false;
	    			break;
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
	    			break;
	    		default:
	    			return false;
	    			break;

	    		}

	    	return $av;

	    	}elseif( $supplier == 'braintrust'){

    		switch ($availability) {
	    		case '0':
	    			$av = 'Αναμονή παραλαβής';
	    			return false; // On December 12 by Vaggelis because negative on Skroutz.
	    			break;
	    		case '1':
	    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
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
					$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
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
    	elseif($supplier == 'cpi'){

    		if ($availability >= 1 || $availability == 'Αμεσα Διαθέσιμο' || $availability == 'Περιορισμένη Διαθεσιμότητα' || $availability == 'ΑΜΕΣΑ ΔΙΑΘΕΣΙΜΟ' || $availability == 'ΠΕΡΙΟΡΙΣΜΕΝΗ ΔΙΑΘΕΣΙΜΟΤΗΤΑ')
    			$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
    		else
    			$av = false;

    		return $av;
    	}
    	elseif($supplier == 'westnet'){

    		switch ($availability) {
				case 'OK':
					$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
					break;
				/*case 'N/A':
					$av = 'Κατόπιν παραγγελίας χωρίς διαθεσιμότητα';
					break;*/
				default:
	    			return false;
	    			break;
    		}

    		return $av;
		}
		elseif($supplier == 'netconnect'){

    		switch ($availability) {
				case '+':
				case '++':
					$av = 'Κατόπιν παραγγελίας σε 1-3 εργάσιμες';
					break;
				case '!':
	    			$av = 'Αναμονή παραλαβής';
	    			return false;
					break;
				case '-':
	    			$av = 'Μη διαθέσιμο';
	    			return false;
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
		$this->db->where('delete_flag', 30);
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

	private function MakeQuestBrands($title, $category){
		$brand = '';
		if($category == 'Desktop Branded PCs')
		{
			if (strpos($title, 'DELL') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'Gigabyte') !== false)
				$brand = 'Gigabyte';
			elseif (strpos($title, 'HP') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'INTEL') !== false || strpos($title, 'Intel') !== false)
				$brand = 'INTEL';
			elseif (strpos($title, 'LENOVO') !== false || strpos($title, 'LN') !== false)
				$brand = 'LENOVO';
			elseif (strpos($title, 'Mac') !== false || strpos($title, 'MACBOOK') !== false || strpos($title, 'APPLE') !== false || strpos($title, 'Macmini') !== false || strpos($title, 'MacBook') !== false || strpos($title, 'IMAC') !== false)
				$brand = 'APPLE';
		}
		elseif($category == 'External Hard Disk Drives')
		{
			if (strpos($title, 'MAXTOR') !== false)
				$brand = 'SEAGATE';
			elseif (strpos($title, 'TOSHIBA') !== false)
				$brand = 'TOSHIBA';
			elseif (strpos($title, 'WD') !== false)
				$brand = 'WD';
			elseif (strpos($title, 'AIRPORT') !== false)
				$brand = 'APPLE';
		}
		elseif($category == 'Laser Printers')
		{
			if (strpos($title, 'CANON') !== false)
				$brand = 'CANON';
			elseif (strpos($title, 'EPSON') !== false || strpos($title, 'Epson') !== false)
				$brand = 'EPSON';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'LEXMARK') !== false)
				$brand = 'LEXMARK';
			elseif (strpos($title, 'SAMSUNG') !== false)
				$brand = 'SAMSUNG';
			elseif (strpos($title, 'XEROX') !== false)
				$brand = 'XEROX';
		}
		elseif($category == 'Memory Cards')
		{
			if (strpos($title, 'KINGS') !== false || strpos($title, 'KINGST') !== false || strpos($title, 'KINGSTON') !== false)
				$brand = 'KINGSTON';
		}
		elseif($category == 'Memory Modules')
		{
			if (strpos($title, 'BALL') !== false || strpos($title, 'BALLIST') !== false || strpos($title, 'BALLISTI') !== false || strpos($title, 'BALLISTIX') !== false)
				$brand = 'BALLISTIX';
			elseif (strpos($title, 'CORSAIR') !== false || strpos($title, 'COR') !== false)
				$brand = 'CORSAIR';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'CRUCIAL') !== false)
				$brand = 'CRUCIAL';
			elseif (strpos($title, 'HX') !== false)
				$brand = 'HyperX';
			elseif (strpos($title, 'KINGS') !== false || strpos($title, 'KINGST') !== false || strpos($title, 'KINGSTON') !== false)
				$brand = 'KINGSTON';
		}
		elseif($category == 'Mobile Battery/Power Bank')
		{
			if (strpos($title, 'APC') !== false)
				$brand = 'APC';
			elseif (strpos($title, 'Bitmore') !== false)
				$brand = 'Bitmore';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'PURO') !== false)
				$brand = 'PURO';
			elseif (strpos($title, 'Sailing') !== false)
				$brand = 'Sailing';
			elseif (strpos($title, 'TP-LINK') !== false || strpos($title, 'TPLINK') !== false)
				$brand = 'TP-LINK';
		}
		elseif($category == 'Monitor LCD')
		{
			if (strpos($title, 'AOC') !== false)
				$brand = 'AOC';
			elseif (strpos($title, 'BENQ') !== false)
				$brand = 'BENQ';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'DELL') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'LENOVO') !== false || strpos($title, 'ThinkVision') !== false)
				$brand = 'LENOVO';
			elseif (strpos($title, 'LG') !== false)
				$brand = 'LG';
			elseif (strpos($title, 'SAMSUNG') !== false)
				$brand = 'SAMSUNG';
		}
		elseif($category == 'Notebook')
		{
			if (strpos($title, 'DELL') !== false || strpos($title, 'INSP') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'LENOVO') !== false || strpos($title, 'LEN') !== false || strpos($title, 'LN') !== false)
				$brand = 'LENOVO';
			elseif (strpos($title, 'TOSHIBA') !== false)
				$brand = 'TOSHIBA';
			elseif (strpos($title, 'MBAIR') !== false || strpos($title, 'MBP') !== false || strpos($title, 'MB') !== false || strpos($title, 'Mac') !== false || strpos($title, 'MACBOOK') !== false || strpos($title, 'APPLE') !== false || strpos($title, 'Macmini') !== false || strpos($title, 'MacBook') !== false || strpos($title, 'IMAC') !== false)
				$brand = 'APPLE';
		}
		elseif($category == 'Notebook Power and Batteries')
		{
			if (strpos($title, 'Apple') !== false || strpos($title, 'APPLE') !== false)
				$brand = 'APPLE';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'HANTOL') !== false)
				$brand = 'HANTOL';
			elseif (strpos($title, 'MAGSAFE') !== false)
				$brand = 'MAGSAFE';
			elseif (strpos($title, 'TARGUS') !== false)
				$brand = 'TARGUS';
			elseif (strpos($title, 'Xilence') !== false)
				$brand = 'Xilence';
		}
		elseif($category == 'Notebook/Netbook  Bags')
		{
			if (strpos($title, 'Dicota') !== false)
				$brand = 'Dicota';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'Logic') !== false)
				$brand = 'Logic';
			elseif (strpos($title, 'Speck') !== false)
				$brand = 'Speck';
			elseif (strpos($title, 'THULE') !== false)
				$brand = 'THULE';
		}
		elseif($category == 'Operating Systems')
		{
			//if (strpos($title, 'Microsoft') !== false || strpos($title, 'WIN') !== false || strpos($title, 'Win') !== false)
				$brand = 'MICROSOFT';
		}
		elseif($category == 'Plotters')
		{
			//if (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
		}
		elseif($category == 'Projector')
		{
			if (strpos($title, 'CANON') !== false)
				$brand = 'CANON';
			elseif (strpos($title, 'DELL') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'EPSON') !== false || strpos($title, 'Epson') !== false)
				$brand = 'EPSON';
			elseif (strpos($title, 'HITACHI') !== false)
				$brand = 'HITACHI';
			elseif (strpos($title, 'PANASONIC') !== false)
				$brand = 'PANASONIC';
			elseif (strpos($title, 'PHILIPS') !== false)
				$brand = 'PHILIPS';
		}
		elseif($category == 'Routers')
		{
			if (strpos($title, 'CISCO') !== false)
				$brand = 'CISCO';
			elseif (strpos($title, 'DRAYTEK') !== false)
				$brand = 'DRAYTEK';
			elseif (strpos($title, 'TP-LINK') !== false)
				$brand = 'TP-LINK';
		}
		elseif($category == 'Security & Antivirus')
		{
			if (strpos($title, 'BITDEFENDER') !== false)
				$brand = 'BITDEFENDER';
			elseif (strpos($title, 'ESET') !== false)
				$brand = 'ESET';
			elseif (strpos($title, 'KASPERSKY') !== false)
				$brand = 'KASPERSKY';
			elseif (strpos($title, 'NORTON') !== false)
				$brand = 'NORTON';
			elseif (strpos($title, 'Panda') !== false)
				$brand = 'Panda';
		}
		elseif($category == 'Servers (HW)')
		{
			if (strpos($title, 'DELL') !== false || strpos($title, 'PE R') !== false || strpos($title, 'PE T') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
		}
		elseif($category == 'Smart Home')
		{
			if (strpos($title, 'D-LINK') !== false || strpos($title, 'DLINK') !== false)
				$brand = 'DLINK';
			elseif (strpos($title, 'TP-LINK') !== false || strpos($title, 'TPLINK') !== false)
				$brand = 'TP-LINK';
		}
		elseif($category == 'SOLID STATE DISKS (SSD)')
		{
			if (strpos($title, 'CORSAIR') !== false)
				$brand = 'CORSAIR';
			elseif (strpos($title, 'CRUCIAL') !== false)
				$brand = 'CRUCIAL';
			elseif (strpos($title, 'INTEL') !== false || strpos($title, 'Intel') !== false)
				$brand = 'INTEL';
			elseif (strpos($title, 'KINGSTON') !== false)
				$brand = 'KINGSTON';
			elseif (strpos($title, 'MICRON') !== false)
				$brand = 'MICRON';
			elseif (strpos($title, 'OCZ') !== false)
				$brand = 'OCZ';
			elseif (strpos($title, 'SAMSUNG') !== false)
				$brand = 'SAMSUNG';
		}
		elseif($category == 'Speakers')
		{
			if (strpos($title, 'B&W') !== false)
				$brand = 'B&W';
			elseif (strpos($title, 'Beats') !== false)
				$brand = 'Beats';
			elseif (strpos($title, 'CREATIVE') !== false)
				$brand = 'CREATIVE';
			elseif (strpos($title, 'GENIUS') !== false)
				$brand = 'GENIUS';
			elseif (strpos($title, 'HP') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'LOGITECH') !== false)
				$brand = 'LOGITECH';
		}
		elseif($category == 'Switches')
		{
			if (strpos($title, 'CISCO') !== false || strpos($title, 'Catalyst') !== false)
				$brand = 'CISCO';
			elseif (strpos($title, 'DLINK') !== false || strpos($title, 'D-LINK') !== false || strpos($title, 'DGS') !== false || strpos($title, 'DXS') !== false)
				$brand = 'DLINK';
			elseif (strpos($title, 'NETGEAR') !== false)
				$brand = 'NETGEAR';
			elseif (strpos($title, 'TP-LINK') !== false)
				$brand = 'TP-LINK';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
		}
		elseif($category == 'Tablet & Pads')
		{
			if (strpos($title, 'Bitmore') !== false)
				$brand = 'Bitmore';
			elseif (strpos($title, 'ColorTab') !== false)
				$brand = 'ColorTab';
			elseif (strpos($title, 'iPad') !== false)
				$brand = 'APPLE';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
		}
		elseif($category == "Workstation PC's")
		{
			if (strpos($title, 'DELL') !== false || strpos($title, 'INSP') !== false)
				$brand = 'DELL';
			elseif (strpos($title, 'HP') !== false || strpos($title, 'HPE') !== false)
				$brand = 'HP';
			elseif (strpos($title, 'LENOVO') !== false || strpos($title, 'LEN') !== false || strpos($title, 'LN') !== false)
				$brand = 'LENOVO';
		}
		elseif($category == "Barcode Scanners")
		{
			if (strpos($title, 'SYMBOL') !== false)
				$brand = 'SYMBOL';
			elseif (strpos($title, 'MOTOROLA') !== false)
				$brand = 'MOTOROLA';
			elseif (strpos($title, 'ZEBRA') !== false)
				$brand = 'ZEBRA';
			elseif (strpos($title, 'HONEYWELL') !== false)
				$brand = 'HONEYWELL';
			elseif (strpos($title, 'DATALOGIC') !== false)
				$brand = 'DATALOGIC';
		}
		else
		{
			$brand = 'Unknown';
		}
		return $brand;
	}

	private function MakeLogEntry($log_array)
	{
		/*
		// The $log_array table
		$log = array(
			'log_type'=>'update supplier', 
			'user_id'=>'0',
			'log_description'=>'NetConnect Update',
			'log_date'=> date('Y-m-d H:i:s'),
			'log_result'=>'Start NetConnect Update'
		);
		*/

		if (!empty($log_array))
		{
			if(is_array($log_array['log_result']))
			{
				$log_result = 'Succesfull';
				foreach($log_array['log_result'] as $key => $value)
				{
					$log_result .= '<br>' . $key.' : '.$value;
				}
				$log_array['log_result'] = $log_result;
			}

			$this->db->insert('logs', $log_array);
		}
	}
}
?>
