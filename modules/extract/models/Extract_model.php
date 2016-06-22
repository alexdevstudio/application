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
    		$query = $this->db->query("SELECT * FROM $table ");
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

            $file = "./files/".$table.".xml";

            if (file_exists($file)) { unlink ($file); }

			if($xml->save($file)){
				return true;
			}
				return false;


		}
        //, i.item_sku, i.image_src
//INNER JOIN images i ON t.sku = i.item_sku
        public function allImport($table){
//
            $query = $this->db->query("
                 SELECT l.product_number, l.category, l.net_price, l.recycle_tax, l.availability, l.supplier, t.*

                 FROM live l

                 INNER JOIN {$table} t ON l.product_number = t.product_number
                 


                 WHERE l.status = 'published' AND l.category = '{$table}' AND t.new_item = 0

                 
                ");
           $i=1;
           $products = array();
           foreach ($query->result_array() as $product) {
                $i=1;
                $this->db->where('item_sku', $product['sku']);
                $images = $this->db->get('images');

                foreach($images->result_array() as $image){
                    $product['image'.$i] = base_url()."/images/".$image['item_sku']."/".$image['image_src'].".jpg";
                    $i++;
                }

                //Price 

                $net_price = $product['net_price'] + $product['recycle_tax'];

                $etd_price = $net_price*1.06;

                $price_tax = $etd_price*1.24;

                $product['price_tax'] = number_format((float)$price_tax, 2, '.', '');

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

            $file = "./files/updates/".$table.".xml";

            if (file_exists($file)) { unlink ($file); }

            if($xml->save($file)){
                echo "<a class='btn btn-md btn-success  btn-block text-center' href='".base_url()."/files/updates/".$table.".xml"."' download target='_blank'>Λήψη XML</a>";
            }
                return false;

         
          /* print_r("<pre >");
            print_r($products);
*/




        }

    }