<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Categories_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function insertItem($c, $categoryData){

    	$query = $this->db->get_where($c,array('product_number'=>$categoryData['product_number']));
    	if($query->num_rows()<1){


	    	if($this->db->insert($c, $categoryData)){
	    		return true;
	    	}
	    }
	    else
	    {
	    	

			$this->db->set('sku',$categoryData['sku']);
			$this->db->where('id',$query->row()->id);
			if($this->db->update($c)){

			$message = "Το SKU που προστέθηκε δεν ταιριάζει με το SKU που είναι ήδη
			 καταχωρημένο στο ERP για αυτό το Προϊόν:<br /> Κατηγορία: ".$c."<br />Παλιό SKU: ".$query->row()->sku."<br />
			 Τίτλος: ".$query->row()->title."<br/> <p style='color:red;'>Παρακαλούμε όπως προβείτε άμεσα στην αλλαγή του SKU στο ERP ή 
			 διαφορετικά αγνοείστε αυτό το μήνυμα.</p><br />Το νέο SKU είναι: ".$categoryData['sku'];
				
				Modules::run('emails/send','Διπλό SKU',$message);
				return true;
			}

	    }


    	return false;
 
    }


    public function updateItem($filename, $table){

$fa = 0;
    		$xml=simplexml_load_file("./files/uploads/".$filename) or die("Error: Cannot find uploaded XML file");
    		$message = '';
//$asd = 0;
    		foreach ($xml->children() as $product) {
    			$data=array();

    			if(empty($product))
    				continue;

    			foreach ($product as $key => $value) {
    				
    				if($key != 'description' && $key!='shipping_class'){

    					$data[$key]=$value;

    				}


    			}

    			$sku = $data['sku'];

    				/*if($sku == '1313329'){
    					echo "<pre />";
    						print_r($data);
    						exit();
    				}*/
    			$data['new_item']=0;

    			/*if($table=="desktops" || $table == "monitors" || $table == "ups" ){
    			    $data['shipping_class'] = $this->makeShippingClass($data, $table);
    			}
    			else if($table == "printers" || $table == "multifunction_printers"){
    				$data['shipping_class'] = $this->makeShippingClass($data, $table);
    			}*/
    			$data['shipping_class'] = $this->makeShippingClass($data, $table);
    			$vw = $data['volumetric_weight'];

    			if($vw=='' || $vw == 0 ){

    				if($table == "printers" || $table == "multifunction_printers") {

    					$dimensions = $data['dimensions'];
						$data['volumetric_weight'] = $this->volumeWeight($dimensions);
    			   		

						if(!$data['volumetric_weight']){
    						
    						$data['new_item']=1;

						}
    				}
    				else
    				{

    					$data['volumetric_weight'] = $this->getWeight($data['shipping_class']);
    				}
    			}


    			


				
				
				$this->db->where('sku', $sku );
				$query = $this->db->get($table);

				if($query->num_rows()>0){
					$this->db->where('sku',$sku);
					$this->db->update($table, $data);
					//unset($data);

				}else{
					$message.="SKU:$fa $sku δεν βρέθηκε στην κατηγορία $table \n";
					exit($message);
				}

				$fa++;
    		}

    		$message = 'Η ενημέρωση των '.$table.' ολοκληρώθηκε με επιτυχία.';
    		return $message;


    }

public function volumeWeight($dimensions){



	//String sanitize
	$dimensions = str_replace(' ','', $dimensions);
	$dimensions = str_replace('mm','', $dimensions);
	$dimensions = str_replace('.',',', $dimensions);
	$dimensions = str_replace('X','x', $dimensions);

	//Create an array
	$da = explode("x",$dimensions);
	
	if(count($da) != 3){ 

		return false;
	}

	$vweight = (((int) $da[0]/10)*((int) $da[1]/10)*((int) $da[2]/10))/5000;

	$vweight = ceil($vweight);
	

	return $vweight;
	





}

public function makeShippingClass($data, $cat, $dynamic = null){

	$vweight = (float) $data['volumetric_weight'];


	if($vweight > 0 && $vweight!='')
	{
		$shipping_class=$this->shippingByWeight($vweight);
	}
	else
	{
		switch ($cat) {

			case 'laptops':
					$shipping_class= 10063;
					break;
			case 'software':
					$shipping_class= 10646;
					break;
			case 'carrying_cases':
					$shipping_class= 10650;
					break;
			case 'desktops':
					$shipping_class= 10070;
					if($data['type']=='Mini Pc')
						$shipping_class= 10066;
					else
						$shipping_class= 10070;
					break;
			case 'power_bank':
					$shipping_class= 10650;
					break;
			case 'monitors':
			case 'tv':
					$size = (float) $data['screen_size'];
					$pn = $data['product_number'];
					if($data['brand']=='DELL' && $size >= 24 && $size <= 25 && (substr($pn, 0, 1) === 'U' || substr($pn, 0, 1) === 'u') ){
						$shipping_class= 10073;
					}
					else
					{
						if($size >= 42)
							$shipping_class= 4667;
						elseif($size >= 32)
							$shipping_class= 10883;
						elseif($size >= 25)
							$shipping_class= 10870;
						elseif($size >= 22)
							$shipping_class= 10019;
						else
							$shipping_class= 10063;
					}
					break;

			case 'routers':
					$shipping_class= 10650;
					break;
			case 'switches':
					
					$ports=(int) $data['ports'];
					
					if( $ports >= 16)
						$shipping_class= 10063;
					else
						$shipping_class= 10650;
					break;
			case 'ups':
					$strength=(int) $data['strength'];
					
					if( $strength >= 3001)
						$shipping_class= 9974;
					elseif( $strength >= 1501)
						$shipping_class = 10883;
					else
						$shipping_class = 10070;
					break;
			
			case 'servers':
					$server_formfactor = $data['form_factor'];
					if($server_formfactor != 'Rack' && $server_formfactor != 'Tower')
						$shipping_class= 10070;
					else
						$shipping_class= 4669;
					break;
			case 'speakers':
					$shipping_class= 10063;
					break;
			case 'external_hard_drives':
					$shipping_class= 10650;
					break;
			case 'sata_hard_drives':
					$shipping_class= 10650;
					break;
			case 'ssd':
					$shipping_class= 10647;
					break;
			case 'keyboard_mouse':
					if($data['type']=='Mouse')
						$shipping_class = 10648;
					else
						$shipping_class = 10651;
					break;
			case 'tablets':
					$shipping_class= 10650;
					break;
			case 'cartridges':
					$shipping_class= 10647;
					break;
			case 'toners':
					$title = $data['title'];
					if(strpos($title, 'Tri-Pack'))
						$shipping_class = 10652;
					elseif(strpos($title, 'Dual Pack') || strpos($title, '2-pack'))
						$shipping_class = 10650;
					else
						$shipping_class = 10650;
					break;
			case 'smartphones':
					$shipping_class= 10650;
					break;

			case 'cables':
			case 'cable_accessories':
					$shipping_class= 10646;
					break;
			case 'patch_panels':
					$shipping_class= 10650;
					break;
			case 'racks':
					$shipping_class= 9974;
					break;
			case 'optical_drives':
					$shipping_class = 10650;
					break;
			case 'card_readers':
					$shipping_class = 10650;
					break;
			case 'flash_drives':
					$shipping_class = 10646;
					break;
			case 'power_supplies':
					$shipping_class = 10063;
					break;
			case 'cases':
					$shipping_class = 10070;
					break;
			case 'fans':
					$shipping_class = 10650;
					break;
			case 'motherboards':
					$shipping_class = 10063;
					break;
			case 'graphic_cards':
					$shipping_class = 10650;
					break;
			case 'cpu':
					$shipping_class = 10063;
					break;
			case 'memories':
					$shipping_class = 10646;
					break;
			case 'hoverboards':
					$shipping_class = 10070;
					break;
			case 'ip_cards':
					$shipping_class = 10650;
					break;
			case 'ip_gateways':
					$shipping_class = 10063;
					break;
		    case 'ip_phones':
		    case 'docking_stations':
					$shipping_class = 10650;
					break;
			case 'ip_pbx':
					$shipping_class = 10063;
					break;
			case 'printer_fusers':
			case 'printer_drums':
			case 'printer_belts':
					$shipping_class = 10651;
					break;
			case 'cooling_pads':
					$shipping_class = 10651;
					break;
			default:
				return false;
				break;
	
			}
		}
	
		
     
      return $shipping_class;
    }


    public function shippingByWeight($vweight){
    	$vweight = (float) $vweight;


    	switch ($vweight) {
    		case 0.2:
    			$shipping_class = 10646;
    			break;
    		case 0.3:
    			$shipping_class = 10647;
    			break;
    		case 0.5:
    			$shipping_class = 10648;
    			break;
    		case 1:
    			$shipping_class = 10649;
    			break;
    		case 2:
    			$shipping_class = 10650;
    			break;
    		case 3:
    			$shipping_class = 10651;
    			break;
    		case 4:
    			$shipping_class = 10652;
    			break;
    		case 5:
    			$shipping_class = 10063;
    			break;
    		case 6:
    			$shipping_class = 10019;
    			break;
    		case 7:
    			$shipping_class = 10064;
    			break;
    		case 8:
    			$shipping_class = 10065;
    			break;
    		case 9:
    			$shipping_class = 10066;
    			break;
    		case 10:
    			$shipping_class = 10067;
    			break;
    		case 11:
    			$shipping_class = 10068;
    			break;
    		case 12:
    			$shipping_class = 10069;
    			break;
    		case 13:
    			$shipping_class = 10070;
    			break;
    		case 14:
    			$shipping_class = 10071;
    			break;
    		case 15:
    			$shipping_class = 10072;
    			break;
    		case 16:
    			$shipping_class = 10073;
    			break;
    		case 17:
    			$shipping_class = 10074;
    			break;
    		case 18:
    			$shipping_class = 10075;
    			break;
    		case 19:
    			 $shipping_class = 10870;
    			 break;
    		case 20:
    			 $shipping_class = 10871;
    			 break;
    		case 21:
    			 $shipping_class = 10872;
    			 break;
    		case 22:
    			 $shipping_class = 10873;
    			 break;
    		case 23:
    			 $shipping_class = 10874;
    			 break;
    		case 24:
    			 $shipping_class = 10875;
    			 break;
    		case 25:
    			 $shipping_class = 10876;
    			 break;
    		case 26:
    			 $shipping_class = 10877;
    			 break;
    		case 27:
    			 $shipping_class = 10878;
    			 break;
    		case 28:
    			 $shipping_class = 10879;
    			 break;
    		case 29:
    			 $shipping_class = 10880;
    			 break;
    		case 30:
    			 $shipping_class = 10881;
    			 break;
    		case 31:
    			 $shipping_class = 10882;
    			 break;
    		case 32:
    			 $shipping_class = 10883;
    			 break;
    		
    		default:
    			$shipping_class = 9974; //Overload
    			break;
    	}
    			//exit($shipping_class);

    	return $shipping_class;
    }

    public function getWeight($shipping_class){
      $sc_array = array('4636' =>5,
						'4644' =>2,
						'4660' =>9,
						'4661' =>13,
						'4662' =>2,
						'4663' =>2,
						'4664' =>5,
						'4665' =>19,
						'4666' =>32,
						'4667' =>46,
						'4668' =>13,
						'4669' =>40,
						'4670' =>2,
						'4671' =>2,
						'4672' =>2,
						'4673' =>3,
						'4674' =>2,
						'4675' =>2,
						'4676' =>2,
						'4677' =>2,
						'4686' =>4,
						'9393' =>16,
						'10646' => 0.2,
						'10647' => 0.3,
						'10648' => 0.5,
						'10649' => 1,
						'10650' => 2,
						'10651' => 3,
						'10652' => 4,
						'10063' => 5,
						'10019' => 6,
						'10064' => 7,
						'10065' => 8,
						'10066' => 9,
						'10067' => 10,
						'10068' => 11,
						'10069' => 12,
						'10070' => 13,
						'10071' => 14,
						'10072' => 15,
						'10073' => 16,
						'10074' => 17,
						'10075' => 18,
						'10870' => 19,
						'10871' => 20,
						'10872' => 21,
						'10873' => 22,
						'10874' => 23,
						'10875' => 24,
						'10876' => 25,
						'10877' => 26,
						'10878' => 27,
						'10879' => 28,
						'10880' => 29,
						'10881' => 30,
						'10882' => 31,
						'10883' => 32,
						'9974' => 33
						);

    	return $sc_array[$shipping_class];
    }
   

}