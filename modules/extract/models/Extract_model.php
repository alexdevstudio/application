<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Extract_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function xml($table){

    		
    		$xml = new DomDocument("1.0","UTF-8");//ISO-8859-7



    		$products = $xml->createElement('products');
    		$products = $xml->appendChild($products);

            //$this->db->where("new_item", "1");
    		//$query = $this->db->query("SELECT * FROM $table");

            $this->db->where('new_item', 1);
            $query = $this->db->get($table); 

            $i=0;
            $prod = $query->result_array();
            foreach($prod as $columns){

    			
    			$product = $xml->createElement('product');
    			$product = $products->appendChild($product);

    			foreach($columns as $key => $value){
    				if($key!='id' && $key!='description' && $key!='new_item' ){
    				$item = $xml->createElement($key, trim(htmlspecialchars($value)));
    				$item = $product->appendChild($item);	
					}
    			}


    		}

            //print_r($query->result_array());

			$xml->FormatOutput = true;
			$string_value = $xml->saveXML(); 

			if (!file_exists('files')) {
		    mkdir('files', 0777, true);
			}

            $file = "./files/".$table."_new_items.xml";

            if (file_exists($file)) { unlink ($file); }

			if($xml->save($file)){
				return true;
			}
				return false;

            }
        //, i.item_sku, i.image_src
//INNER JOIN images i ON t.sku = i.item_sku
        public function allImport($table){

            if($table == 'all'){

                $tables = Modules::run('categories/fullCategoriesArray');

            }else{

                $tables = array($table);

            }
            $f = 0;
            foreach ($tables as $table) {

            

                $query = $this->db->query("
                     SELECT l.product_number, l.category, l.net_price, l.recycle_tax,l.price_tax, l.availability, l.supplier, l.status, l.delete_flag, t.*

                     FROM live l

                     INNER JOIN {$table} t ON l.product_number = t.product_number
                     
                     WHERE l.category = '{$table}' AND t.new_item = 0
                     
                    ");
               $i=1;
               $products = array();
               foreach ($query->result_array() as $product) {

                 //Price 
                    if($product['price_tax']=='' ||  $product['price_tax'] === NULL  ||  $product['price_tax'] == '0.00' ){
                      
                        $net_price = $product['net_price'] + $product['recycle_tax'];

                        $etd_price = $net_price*1.06;

                        $price_tax = $etd_price*1.24;

                        $product['price_tax'] = number_format((float)$price_tax, 2, '.', '');

                    }

                    // Check if Etd product is trashed to increment the delete flag.
                    if($product['supplier']=='etd' && $product['status']=='trash')
                    {
                        
                        $flag = $product['delete_flag'];

                        if ($flag >= 10) //1. Delete old trashed entries if delete_flag >=10
                        {
                            $this->db->where('supplier', 'etd');
                            $this->db->where('status', 'trash');
                            $this->db->where('delete_flag', 10);
                            $this->db->delete('live');
                        }
                        else //2. Update all entries to set status=trash and delete_flag +1
                        {

                            $pn = $product['product_number'];
                            $flag ++;

                            $this->db->where('supplier', 'etd');
                            $this->db->where('product_number', $pn);
                            $this->db->where('status', 'trash');
                            $this->db->set('delete_flag',$flag);
                            $this->db->update('live');
                        }
                    }
                    

                // Title for Skroutz and for ETD.gr

                    $etd_title = $product['etd_title'];
                    $skroutz_title = $product['skroutz_title'];
                
                switch ($table) {
                        case 'laptops':
                        $cpu =   str_replace(' ','',$product['cpu_model']); 
                        $ram =   str_replace(' ','',$product['memory_size']);   
                        $disk =  str_replace(' ','',$product['hdd']); 
                        $os = str_replace(' ','',$product['skroutz_operating_system']); 
                        $color = str_replace(' ','',$product['color']); 
                        $model = trim($product['model']);
                        $pn = str_replace(' ','',$product['product_number']);

                        if($color=='')
                            $color=" ";
                        else
                            $color=" $color ";


                        if($etd_title == ''){
                            $etd_title = $model.$color." ($pn)";
                        }

                        if($skroutz_title == ''){
                            $skroutz_title = $model.$color.$cpu.'/'.$ram.'/'.$disk.'/'.$os;

                        }

                            break;
                         case 'desktops':
                        $cpu =   str_replace(' ','',$product['cpu_model']); 
                        $ram =   str_replace(' ','',$product['memory_size']);   
                        $disk =  str_replace(' ','',$product['hdd']); 
                        $os = str_replace(' ','',$product['skroutz_operating_system']); 
                        $model = trim($product['model']);
                        $pn = str_replace(' ','',$product['product_number']);

                        //for NUC some title modifications

                         if($etd_title == ''){
                            
                             $etd_title = $model." ($pn)";
                        }

                        if($skroutz_title == ''){
                             if(strtoupper($product['brand'])=="INTEL"){
                             $skroutz_title = $model." $pn (".$cpu."/".$ram."/".$disk."/".$os.")";
                            }else{
                                 $skroutz_title = $model.' '.$cpu.'/'.$ram.'/'.$disk.'/'.$os;
                            }
                            

                        }
                       
                       
                        

                            break;
                            case 'monitors':
                        
                        $title =   str_replace(' ','',$product['title']); 
                        $brand =   str_replace(' ','',$product['brand']);   
                        $inches =  str_replace(',','.',$product['screen_size']); 
                        $technology = str_replace(' ','',$product['monitor_technology']); 
                        $pn = str_replace(' ','',$product['product_number']);
                        $ips = '';

                        if (strpos($title, 'IPS') !== false) {
                                $ips = ' IPS ';
                            }

                        if($etd_title == ''){
                            
                        $etd_title = $brand.' '.$inches.' ιντσών '.$technology.' '.$ips.' ('.$pn.')';
                        }

                        if($skroutz_title == ''){
                             
                             $skroutz_title = $brand.' '.$inches.'  '.$pn;
                            
                        }

                            

                            break;
                            case 'printers':
                            case 'multifunction_printers':
                        
                        $title =  $product['title']; 
                        $model =  $product['model'];   
                        $pn    =  $product['product_number'];
                        
                         if($etd_title == ''){
                            
                            $etd_title = "$model ($pn)";
                        }

                        if($skroutz_title == ''){
                             
                             $skroutz_title = $model;
                            
                        }
                        
                       
                        

                            break;    
                        default:
                            $etd_title = $product['title'];
                            $skroutz_title = $product['title'];
                            break;
                    }    





                    $etd_title = str_replace('  ', ' ', $etd_title);
                    $etd_title = str_replace('  ', ' ', $etd_title);

                    $skroutz_title = str_replace('//', '/', $skroutz_title);
                    $skroutz_title = str_replace('//', '/', $skroutz_title);
                    $skroutz_title = str_replace('//)', ')', $skroutz_title);
                    $skroutz_title = str_replace('/)', ')', $skroutz_title);

                    $product['etd_title'] =  $etd_title;
                    $product['skroutz_title'] =  $skroutz_title;



                    $i=1;
                    $this->db->where('item_sku', $product['sku']);
                    $images = $this->db->get('images');

                    foreach($images->result_array() as $image){
                        $product['image'.$i] = base_url()."/images/".$image['item_sku']."/".$image['image_src'].".jpg";
                        $i++;
                    }


                    $products[]=$product;


               }


                $xml = new DomDocument("1.0","UTF-8");//ISO-8859-7



                $items = $xml->createElement('items');
                $items = $xml->appendChild($items);

                //$this->db->where("new_item", "1");
                //$query = $this->db->query("SELECT * FROM $table WHERE new_item=1 ");
                $i=0;
                

                foreach($products as $product){

                    
                    $item = $xml->createElement('item');
                    $item = $items->appendChild($item);

                    foreach($product as $key => $value){
                        if($key!='id' && $key!='new_item' ){
                            $attr = $xml->createElement($key, trim(htmlspecialchars(strip_tags($value))));
                            $attr = $item->appendChild($attr);   
                        }
                    }


                }

                //print_r($query->result_array());

                $xml->FormatOutput = true;
                $string_value = $xml->saveXML(); 

                if (!file_exists('files/updates')) {
                mkdir('files/updates', 0777, true);
                }

                $file = "./files/updates/".$table."_ALL_IMPORT.xml";

                if (file_exists($file)) { unlink ($file); }

                if($xml->save($file)){
                    echo "<a class='btn btn-md btn-success  btn-block text-center' href='".base_url()."/files/updates/".$table."_ALL_IMPORT.xml"."' download target='_blank'>Λήψη XML</a>";
                }
                  //  return false;

             
              /* print_r("<pre >");
                print_r($products);
              */


            }

        }

    }