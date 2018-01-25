<div style="background:#fff;" class="col-md-12">
			<section class="content-header">
		    	<h1 style="text-align:center;">Σε Απόθεμα</h1>
		      	<br>	
		    </section>

		    <?php

			    $where = array(
                    'supplier'=>'etd',
                    'status'=>'publish'
                    );
                    
                $order_by = array (
                    'category',
                    'ASC'
                );

                    $stock = Modules::run('crud/get','live',$where, $order_by);

	               // print_r($stock->result_array());
	                $cat_array = array();
                    //$out = '';
                    $out = '
                        <div class="header_stock">
                            <div class="col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-1 text-center">
                                SKU
                            </div>
                            <div class="col-sm-7 text-center">
                                ΤΙΤΛΟΣ
                            </div>
                            <div class="col-sm-2 text-center">
                                ΤΙΜΗ SKROUTZ
                            </div>
                            <div class="col-sm-1 text-right">
                                TIMH ETD
                            </div>
                        </div>';

	                foreach ($stock->result_array() as $items) {

	                	$category = $items['category'];
	                	$cat_array[] = $category;
	                	

	                	$product_number = $items['product_number'];
	                	$where =array(
							'product_number'=>$product_number
	                		);

	                	$item = Modules::run('crud/get', $category, $where);
	                	

	                	$sku =  $item->row()->sku;
	                	$title = $item->row()->title;

	                	$etd_prices = Modules::run('crud/get', 'etd_prices', array('sku'=>$sku ));

	                	if($etd_prices){
	                		$sale_price = $etd_prices->row()->sale_price;
	                		$price_tax = $etd_prices->row()->price_tax;
	                		@$price = ($sale_price=='' || $sale_price<0.01) ? $price_tax: $sale_price;
	                	}
	                	if(!$price){
	                		$price = '<span style="color:red;">Δεν υπάρχει τιμή</span>';
                        }/*else{
	                		$price = "€ $price";
	                	}*/



	                	$skroutzPrice = Modules::run('skroutz/getBestPrice',$sku);

						if($skroutzPrice){
							$skroutzPrice = $skroutzPrice->result_array();
							$skroutzPrice = $skroutzPrice[0];
							$best_price = json_decode($skroutzPrice['best_price']);

							$sklogo = $best_price->shopLogo;
							$sktitle = $best_price->shopTitle;
							$skprice = $best_price->shopPrice;
							
							$skroutzPrice = '<img class="instosk-sklogo float-left" src="'.$sklogo.'" /><span class="instock_item_price">€ '.$skprice.' </span>';


						}else{
							$skroutzPrice = $skprice = '';
                        }
                        
                        $price_color = '';

                        if($skprice != '')
                        {
                            if($skprice > $price)
                                $price_color ='stock-price-nice';
                            elseif($skprice == $price)
                                $price_color ='stock-price-same';
                            else 
                                $price_color ='stock-price-bad';
                        }

	                	$img = Modules::run('images/getFirstImage', $sku, false);
						
                        $out.=' 
                                <div class="instock_item clearfix" data-category="'.$category.'">
                                    <div class="col-sm-1">
                                        <img data-original="'.$img.'" class="lazyimg" />
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        <strong style="color:#0fc504;">'.$sku.'</strong>
                                    </div>
                                    <div class="col-sm-7"> 
                                        <a href="http://etd.gr/xml/edit/'.$category.'/'.$sku.'">'.$title.'</a>
                                    </div>
                                    <div class="col-sm-2">
                                        '.$skroutzPrice.'
                                    </div>
                                    <div class="col-sm-1 text-right '.$price_color.'">
                                        € '.$price.' 
                                    </div>
                                </div>
                                ';
                                /*<div class="instock_item clearfix" data-category="'.$category.'">
						                			<img data-original="'.$img.'" class="lazyimg" />
						        <h5><a href="http://etd.gr/xml/edit/'.$category.'/'.$sku.'">'.$title.'</a></h5>
						        <strong style="color:#0fc504;">SKU: '.$sku.'</strong><br />
						        <span class="instock_item_price">'.$skroutzPrice.'
						        Τιμή στο site: '.$price.'
						        </span>


						        </div>*/
	                	
	                	//print_r($item->result_array());
                    }

?>
<div class="instock_filter">
	<span class="active_filter instock_filter_item" id="all">Όλα :<?php echo count($cat_array); ?></span>

<?php
$cats = array_count_values($cat_array);


foreach ($cats as $key => $value) {
	?>

	<span class=" instock_filter_item" id="<?= $key; ?>"><?php echo ucfirst($key).': '. $value; ?></span>

	<?php
}

?>
</div>
<?php
echo $out;

		    ?>


</div>

<script type="text/javascript">
$(document).ready(function(){
	$('.instock_filter_item').on('click',function(){
		var id = $(this).attr('id');
		$('.instock_filter_item').removeClass('active_filter');
		$('#'+id).addClass('active_filter');

		if(id=='all'){
			$('.instock_item').removeClass('to_hide');
		}else{

			$('.instock_item').addClass('to_hide');
			$('.instock_item[data-category="'+id+'"]').removeClass('to_hide');
		}

	});
});
	
</script>