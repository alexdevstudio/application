<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Extract_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

 
    public function xml($table,$all){


            $xml = new DomDocument("1.0","UTF-8");//ISO-8859-7



            $products = $xml->createElement('products');
            $products = $xml->appendChild($products);

            //$this->db->where("new_item", "1");
            //$query = $this->db->query("SELECT * FROM $table");
            if(!$all || $all=='new')
                $this->db->where('new_item', 1);

            $query = $this->db->get($table);

            $i=0;
            $prod = $query->result_array();
            foreach($prod as $columns){


                $product = $xml->createElement('product');
                $product = $products->appendChild($product);

                foreach($columns as $key => $value){
                    if($key!='id' && $key!='new_item' ){
                        $item = $xml->createElement($key, trim(htmlspecialchars($value)));
                        $item = $product->appendChild($item);
                    }
                }
            }
            $product = $xml->createElement('product');
            $product = $products->appendChild($product);
            //print_r($query->result_array());

            $xml->FormatOutput = true;
            $string_value = $xml->saveXML();

            if (!file_exists('files')) {
            mkdir('files', 0777, true);
            }
            if(!$all || $all=='new')
                $file = "./files/".$table."_new_items.xml";
            else
                $file = "./files/".$table."_all_items.xml";

            if (file_exists($file)) { unlink ($file); }

            if($xml->save($file)){
                return true;
            }
                return false;

            }
        //, i.item_sku, i.image_src
//INNER JOIN images i ON t.sku = i.item_sku
        public function allImport($table, $numrows, $imagesOnly=null, $skus=null){

            if($numrows == 'all'){
                $numrows = 5500;
            }

            $action = '';
            $allProds = array();

            if($table == 'all'){
                $action = 'all';
                $tables = Modules::run('categories/fullCategoriesArray');
            }else{
                if($numrows == 'one'){
                    $action = 'all';
                    $numrows = 1;
                }

                $tables = array($table);

            }

            $f = 0;
            foreach ($tables as $table) {

            if($skus && $skus!=''){

                $skus = str_replace('_', ',', $skus);
                $query = $this->db->query("
                     SELECT l.id,
                            l.product_number,
                            l.category,
                            l.net_price,
                            l.recycle_tax,
                            l.availability,
                            l.upcoming_date,
                            l.supplier,
                            l.status,
                            l.delete_flag,
                            t.*,
                            i.installments_count,
                            e.price_tax,
                            e.shipping,
                            e.sale_price,
                            h.html as auto_descr

                     FROM live l

                     INNER JOIN {$table} t ON l.product_number = t.product_number

                     LEFT JOIN installments i ON t.sku = i.sku
                     LEFT JOIN etd_prices e ON t.sku = e.sku
                     LEFT JOIN descriptions_html h ON h.sku = t.sku

                     WHERE l.category = '{$table}' AND t.new_item = 0 AND t.sku IN ({$skus})


                    ");

            }else{

               $query = $this->db->query("
                     SELECT l.id,
                     l.product_number,
                     l.category,
                     l.net_price,
                     l.recycle_tax,
                     l.availability,
                     l.upcoming_date,
                     l.supplier,
                     l.status,
                     l.delete_flag,
                     t.*,
                     i.installments_count,
                     e.price_tax,
                     e.shipping,
                     e.sale_price,
                     h.html as auto_descr

                     FROM live l

                     INNER JOIN {$table} t ON l.product_number = t.product_number

                     LEFT JOIN installments i ON t.sku = i.sku
                     LEFT JOIN etd_prices e ON t.sku = e.sku
                     LEFT JOIN descriptions_html h ON h.sku = t.sku

                     WHERE l.category = '{$table}' AND t.new_item = 0 ORDER BY t.sku DESC LIMIT {$numrows}


                    ");
            }

               $i=1;
               $products = array();


                    $monitors=array();
                    $laptops=array();


                 foreach ($query->result_array() as $available){

                    if($available['category']=='monitors' && $available['availability']=='Άμεσα Διαθέσιμο'){
                        $monitors[] =  $available['sku'];
                    }
                    if($available['category']=='laptops' && $available['availability']=='Άμεσα Διαθέσιμο'){
                        $laptops[] =  $available['sku'];
                    }


                 }



               foreach ($query->result_array() as $product) {

                    $cat = $product['category'];
                    $supplier = $product['supplier'];
                    $pn = $product['product_number'];
                    $sku = $product['sku'];

                    if(isset($product['brand'])){
                        $brand = $product['brand'];
                    }else{
                        $brand = $product['Brand'];
                    }





                    if(!isset($product['price_tax']) || $product['price_tax'] == '' ||  $product['price_tax'] === NULL  ||  $product['price_tax'] == '0.00' || $product['price_tax'] =='0'){

                        $product['price_tax'] = $this->priceTax($product);

                        if(($supplier=='braintrust' || $supplier=='etd') && $cat == 'laptops' && $brand == 'MSI'){
                            $msi = Modules::run("crud/get",'msi_price',array('sku'=>$sku));

                            if($msi)
                                $msi_price = $msi->row()->price;
                            else
                            {
                                echo $sku.': THIS MSI HAS NO PRICE<br>';
                                break;
                            }


                            if($msi_price!='0.00' && $msi_price!='' && $msi_price!='0'){
                                $product['price_tax'] = $msi_price;
                            }

                        }
                        //Skip product without price....
                        if($product['supplier']!='konica' && !$product['price_tax'] && $product['status']=='publish')
                            continue;
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
                    $cross = '';

                switch ($table) {
                        case 'laptops':
                        $cpu =   str_replace(' ','',$product['cpu_model']);
                        $ram =   str_replace(' ','',$product['memory_size']);
                        $disk =  str_replace(' ','',$product['hdd']);
                        $os = str_replace(' ','',$product['skroutz_operating_system']);
                        $color = str_replace(' ','',$product['color']);
                        $model = trim($product['model']);
                        $pn = str_replace(' ','',$product['product_number']);
                        $description = trim(strip_tags($product['description']));
                        $bonus = trim(strip_tags($product['bonus']));

                        $vga = '';
                        $shared_graphics = trim(strip_tags($product['shared_graphics']));

                        if($shared_graphics != "ΝΑΙ" && $shared_graphics != "NAI" )
                        {
                            $vga = '/' . trim(strip_tags($product['graphics'])) . ' ' . trim(strip_tags($product['graphics_memory'])) . ' ' . trim(strip_tags($product['graphics_memory_type']));
                        }

                        if ($bonus != '')
                        {
                            $description = "<div style='color: red;'>ΔΩΡΟ: ".$bonus."</div><div>".$description."</div>";

                            $product['description'] = $description;
                        }

                        if($color=='')
                            $color=" ";
                        else
                            $color=" $color ";


                        if($etd_title == ''){

                            // $etd_title = $model.$color." ($pn)";
                            $screen_size = str_replace(' ','',$product['screen_size']);
                            $monitor_type = $product['monitor_type'];
                            $ram_type = str_replace(' ','',$product['memory_type']);
                            $hdd_type = str_replace(' ','',$product['hdd_type']);
                            $vga = $product['graphics'];
                            $vga_ram = str_replace(' ','',$product['graphics_memory']);
                            $y_warranty = $product['year_warranty'];

                            $title_color = ($color != '') ? ' '.$color : '';
                            $title_vga_ram = ($vga_ram != '') ? ' '.$vga_ram : '';

                            $etd_title = $model.$title_color.', '.
                            $screen_size.' '.$monitor_type.', '.
                            $cpu.', '.
                            $ram.' '.$ram_type.', '.
                            $disk.' '.$hdd_type.', '.
                            $vga.$title_vga_ram.', '.
                            $os.', '.
                            $y_warranty.' ('.$pn.')';

                        }

                        if($skroutz_title == ''){
                            $skroutz_title = $model.$color.$cpu.'/'.$ram.'/'.$disk. $vga.'/'.$os;

                        }


                        $product['cross_sells'] =  Modules::run("crosssales/auto_laptop",$product['sku'], $product['brand'], $product['screen_size'], $product['price_tax']);



                        $product['up_sells'] = implode(",",$laptops);


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

                        $product['up_sells'] = implode(",",$monitors);

                        if (strpos($title, 'IPS') !== false) {
                                $ips = ' IPS ';
                            }

                        if($etd_title == ''){

                        $etd_title = $brand.' '.$inches.' ιντσών '.$technology.' '.$ips.' ('.$pn.')';
                        }

                        if($skroutz_title == ''){

                             $skroutz_title = $brand.' '.$inches.'  '.$pn;

                        }

                            $product['tags'] = $product['availability'];

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
                            case 'servers':

                        $title =  $product['title'];
                        $model =  $product['model'];
                        $cpu =  $product['cpu'];
                        $cpu_generation =  $product['cpu_generation'];
                        $cpu_generation = rtrim($cpu_generation, " E3");
                        $cpu_generation = rtrim($cpu_generation, " E5");
                        $cpu_model =  $product['cpu_model'];
                        $pn    =  $product['product_number'];

                         if($etd_title == '')
                             $etd_title = "$model $cpu $cpu_generation  $cpu_model ($pn)";


                        if($skroutz_title == '')
                             $skroutz_title = "$model $cpu $cpu_generation  $cpu_model ($pn)";

                           break;
                        case 'cartridges':
                        case 'toners':

                        $title =  $product['title'];
                        $model =  $product['model'];
                        $pn    =  $product['product_number'];

                        if($model=='')
                            $title = ltrim($title, 'Cartridge ' );

                         if($etd_title == '')
                             $etd_title = "$title ($pn)";


                        if($skroutz_title == '')
                             $skroutz_title = "$title ($pn)";

                           break;
                           case 'smartphones':

                        $title =  $product['title'];
                        $color =  $product['color'];
                        $model =  $product['model'];
                        $pn    =  $product['product_number'];

                        if($model=='')
                            $model = ltrim($title, 'Smartphone ' );
                            $model = ltrim($model, 'SMARTPHONE ' );

                         if($etd_title == '')
                             $etd_title = "$model $color ($pn)";


                        if($skroutz_title == '')
                             $skroutz_title = "$model  $color ($pn)";

                           break;
                           /*case 'copiers':


                        $pn    =  $product['product_number'];



                         if($etd_title == '')
                             $etd_title = $product['title'];


                        if($skroutz_title == '')
                             $skroutz_title = $product['Name'];

                           break;*/
                        default:
                          $skroutz_title =  $etd_title = $product['title']." (".$product['product_number'].")";
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
                    $cross_sells = $this->cross_sells($sku);
                    if($cross_sells){
                        $product['cross_sells'] = $cross_sells;
                    }
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

                        $value = trim($value);

                        if($key!='id' && $key!='new_item' ){

                            if($key == 'maximum_resolution' || $key=='screen_resolution'){

                                $value = str_replace(' ','',$value);

                            }

                            if(($key == 'description' && $table == 'laptops') || ($key == 'description' && $table == 'printers') || $key == 'auto_descr' )// for insert the description without strip_tags
                              {  $attr = $xml->createElement($key, trim(htmlspecialchars($value)));}
                            else{
                                $attr = $xml->createElement($key, trim(htmlspecialchars(strip_tags($value))));
                            }

                            $attr = $item->appendChild($attr);


                        }
                    }

                   // $cat_check = ''; //to reset the category for next product
                }

                $item = $xml->createElement('item');
                $item = $items->appendChild($item);




                $allProds = array_merge($allProds, $products);


                //print_r($query->result_array());

                $xml->FormatOutput = true;
                $string_value = $xml->saveXML();

                if (!file_exists('files/updates')) {
                mkdir('files/updates', 0777, true);
                }

                $table = ($imagesOnly==1 ? 'imagesOnly': $table);

                $file = "./files/updates/".$table."_ALL_IMPORT.xml";

                if (file_exists($file)) { unlink ($file); }

                if($xml->save($file)){
                   if($action != 'all')
                    echo "<a class='btn btn-md btn-success  btn-block text-center' href='".base_url()."/files/updates/".$table."_ALL_IMPORT.xml"."' download target='_blank'>Λήψη XML</a>";
                }
                  //  return false;


              /* print_r("<pre >");
                print_r($products);
              */


            }

            if($action == 'all'){
                $xml = new DomDocument("1.0","UTF-8");//ISO-8859-7



                $items = $xml->createElement('items');
                $items = $xml->appendChild($items);

                //$this->db->where("new_item", "1");
                //$query = $this->db->query("SELECT * FROM $table WHERE new_item=1 ");
                $i=0;


                foreach($allProds as $product){


                    $item = $xml->createElement('item');
                    $item = $items->appendChild($item);

                    foreach($product as $key => $value){
                        if($key=='sku' || $key=='availability' || $key=='price_tax' || $key=='product_number' || $key=='status' ){
                            $attr = $xml->createElement($key, trim(htmlspecialchars(strip_tags($value))));
                            $attr = $item->appendChild($attr);
                        }
                    }

                    $this->updateWp($product);



                }

                $item = $xml->createElement('item');
                $item = $items->appendChild($item);


                $xml->FormatOutput = true;
                $string_value = $xml->saveXML();

                $file = "./files/updates/general_ALL_IMPORT.xml";

                if (file_exists($file)) { unlink ($file); }

                $xml->save($file);
            }
                echo "<h1>Price and Availability Update Complete.</h1>";

        }

        private function cross_sells($sku){

            $where = array('sku'=>$sku);

            if($cross_sells = Modules::run('crud/get','cross_sells', $where)){
                return $cross_sells->row()->products;
            }
            return;


        }

        public function priceTax($product)
        {
           //  echo $category_rate;
             $net  = $product['net_price'];
             $recycle = $product['recycle_tax'];
             $category = $product['category'];
             $brand = (isset($product['brand']) ? $product['brand'] : $product['Brand'] );
             $net_price = $net + $recycle;

             $category_brand = $category."_".$brand;
             $category_brand_rate = Modules::run("profit_rates/getCategoryRate",$category_brand);

             if($category_brand_rate && $category_brand_rate!='' && $category_brand_rate!='0'){
                $category_rate = $category_brand_rate;
             }else{
                $category_rate = Modules::run("profit_rates/getCategoryRate",$category);

             }


             $category_rate = number_format((float)$category_rate, 3, '.', '');

             $etd_price = $net_price*(1 + $category_rate);

             //$etd_price = $net_price*1.06;

             $price_tax = $etd_price*1.24;

             //if($price_tax == '' ||  $price_tax === NULL  ||  $price_tax == '0.00' || $price_tax =='0')
             if($price_tax !='0')
                return number_format((float)$price_tax, 2, '.', '');
            else
                return false;

        }

        public function updateWp($product,$customVar=null){

                $installments_import = $product['installments_count'];

                    /*if(!$installments_import){
                        $installments_import = 12;
                    }*/

                    $sku = $product['sku'];

                   /* $where = array('sku'=>$sku);
                    $data = array('new_item'=>0);
                    Modules::run("crud/update",$table, $where, $data); */


                    $where = array('meta_value'=>$sku,"meta_key"=>"_sku");
                    $post_id = Modules::run("crud/getWp","wp_postmeta", $where);




                    if(!is_bool($post_id)){
                        $post_id = $post_id->result();
                        $post_id = $post_id[0]->post_id;

                         $where = array('id'=>$post_id);
                         $post_name = Modules::run("crud/getWp","wp_posts", $where);
                         $post_name = $post_name->result();
                         $post_name = $post_name[0]->post_name;




                        if($product['status']=='publish'){


                             $post_name = rtrim($post_name,'__trashed');


                             //If Sale Price is SET check
                             $price1 = false;
                             $sale_price = trim($product['sale_price']);
                             if($sale_price!='' && $sale_price!=0.00 && $sale_price!=0){
                                $price1 = $sale_price;
                             }elseif($product['price_tax']!='' && $product['price_tax']!='0.00'){

                                $price1 = $product['price_tax'];
                             }

                             $customAvailability = $product['availability'];
                             $upcommingDate = $product['upcoming_date'];

                             if($customAvailability=='Αναμονή παραλαβής' && $upcommingDate!=''){
                                $customAvailability.= "^$upcommingDate";

                             }



                            $where = array('post_id'=>$post_id,'meta_key'=>'_regular_price');
                            $data = array('meta_value'=>$product['price_tax']);
                            Modules::run("crud/updateWp","wp_postmeta",  $where, $data);

                            $where = array('post_id'=>$post_id,'meta_key'=>'_sale_price');
                            $data = array('meta_value'=>$sale_price);
                            Modules::run("crud/updateWp","wp_postmeta",  $where, $data);

                            $where = array('post_id'=>$post_id,'meta_key'=>'_price');
                            $data = array('meta_value'=>$price1);
                            Modules::run("crud/updateWp","wp_postmeta",  $where, $data);

                            $where = array('post_id'=>$post_id,'meta_key'=>'custom_availability');
                            $data = array('meta_value'=>$customAvailability);
                            Modules::run("crud/updateWp","wp_postmeta",  $where, $data);

                            $where = array('post_id'=>$post_id,'meta_key'=>'shipping');
                            $data = array('meta_value'=>$product['shipping']);
                            Modules::run("crud/updateWp","wp_postmeta",  $where, $data);

                            $where = array('post_id'=>$post_id,'meta_key'=>'max_installments');
                            Modules::run("crud/deleteWp","wp_postmeta",  $where);

                            $data = array('post_id'=>$post_id,'meta_key'=>'max_installments','meta_value'=>$installments_import);
                            Modules::run("crud/insertWp","wp_postmeta", $data);



                            //echo  $products_count++;
                            //echo ":$sku:".$product['status']."<br />";


                        }
                        /*$where = array('post_id'=>$post_id,'meta_key'=>'_stock_status');
                        $data = array('meta_value'=>'instock');
                        Modules::run("crud/updateWp","wp_postmeta",  $where, $data);
                        $where = array('post_id'=>$post_id,'meta_key'=>'_manage_stock');
                        $data = array('meta_value'=>'no');
                        Modules::run("crud/updateWp","wp_postmeta",  $where, $data);*/
                                $where = array('ID'=>$post_id);
                                $data = array('post_title'=>$product['etd_title'],"post_status"=>$product['status'],"post_name"=>$post_name);
                                Modules::run("crud/updateWp","wp_posts",  $where, $data);


                       // exit($product['price_tax']);


                    }

                    return true;
        }
    }
