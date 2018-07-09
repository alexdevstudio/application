
<div class="sections col-xs-12">
	<section class="content-header">

		<br>
    </section>

    <div class="col-xs-12 col-md-2">


 <?php

	$sku = $item->row()->sku;
	$pn = $item->row()->product_number;

	$image = Modules::run("images/getFirstImage",$sku,true);

	//echo Modules::run("images/getAmazonImages", $sku, "https://www.amazon.com/Dell-i5559-7080SLV-Touchscreen-RealSense-Generation/dp/B015JVFE0A");

 ?>

 <div class="col-xs-12 bottom-margin  image-fix">
 <?= $image; ?>
</div>
 <div class="col-xs-12 ">

 <div class="">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h4 style='font-weight:bold;'><?= $sku; ?></h4>

              <p>SKU</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>

          </div>
        </div>

        <div class="">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h4 style='font-weight:bold;'><?= $pn; ?></h4>

              <p>Product Number</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>

          </div>


 </div>
</div>

 <div class="col-xs-12 ">
 <?php
     if($skroutzPrice){


$best_price = json_decode($skroutzPrice['best_price']);

$sklogo = $best_price->shopLogo;
$sktitle = $best_price->shopTitle;
$skprice = $best_price->shopPrice;
$skupdate = strtotime($skroutzPrice['last_update']);
$skupdate = date( 'H:i d M `y ', $skupdate );

//print_r($best_price);
     	?>
 <div class="skroutz_box form-group">

     <label class='bg-orange' ><a target="_blank" style="color:#fff" href="<?= $skroutzUrl; ?>">1η Τιμή Skroutz <i class="fa fa-external-link"  aria-hidden="true"></i></a></label>


     <img src="<?= $sklogo; ?>" />

     <!-- <span class='edit-sktitle'><?= $sktitle; ?></span>   -->

     <span class='edit-skprice'><?= $skprice; ?> €</span>
     <span class='edit-skupdate'><i class="fa fa-calendar"></i> <?= $skupdate; ?></span>


</div>

<?php
}

?>
<?php
$price = '';
$availability = '';
$sale_price = '';
$supplier = '';
$instock = '';
$outstock = '';
$outstock2 = '';
$shipping = '';

if($etd_prices != NULL)
{
	$price = $etd_prices->row()->price_tax;
	$sale_price = $etd_prices->row()->sale_price;
	$shipping = $etd_prices->row()->shipping;
}

if($itemLive){

	//$price = $itemLive->row()->price_tax;
	//$sale_price = $itemLive->row()->sale_price;
	//$shipping = $itemLive->row()->shipping;
	$av = $itemLive->row()->availability;
	$supplier = $itemLive->row()->supplier;
	$upcomingDate = $itemLive->row()->upcoming_date;


	if($itemLive->row()->upcoming_date == ''){
		$upcomingDate = '';
	}else{
		$upcomingDate = date('m/d/Y',strtotime($upcomingDate));
	}


	//date( 'H:i d M `y ', $skupdate );

	if($av=='Άμεσα Διαθέσιμο'){
		$instock = 'selected';
	}elseif($av=='Κατόπιν παραγγελίας σε 1-3 εργάσιμες'){
		$outstock = 'selected';
	}elseif($av=='Αναμονή παραλαβής'){
		$outstock2 = 'selected';
	}



}


	$etd = '';
	$logicom = '';
	$oktabit = '';
	$braintrust = '';
	$cpi = '';
	$westnet = '';
	$ddc = '';
	$partnernet = '';
	$quest= '';
	$netconnect = '';
	$other = '';

?>
 <form method="post" action=''>
 <div>Οι παρακάτω τιμές ενημέρωνουν απευθείας το site όταν το προϊόν υπάρχει στο site. </div>
 <br/>
 <div class="form-group">
<label>Κανονική Τιμή</label>

<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
<input class='form-control' type="hidden" name='delete_flag' value='0'>
<input class='form-control' type="hidden" name='status' value='add'>


<div class="input-group">
    <input class='form-control' name='price_tax' id='regular' value='<?= $price; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("regular");' title="Εκκαθάριση τιμής">X</span>
</div>




</div>
<div style=''class="form-group">

<label>Τιμή Προσφοράς</label>

<div class="input-group">
    <input class='form-control' name='sale_price' id='sale_price' value='<?= $sale_price; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("sale_price");' title="Εκκαθάριση τιμής">X</span>
</div>


</div>
<div style=''class="form-group">

<label>Κόστος Αποστολής</label>

<div class="input-group">
    <input class='form-control' name='shipping' id='shipping' value='<?= $shipping; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("shipping");' title="Εκκαθάριση κόστους αποστολής">X</span>
</div>


</div>
<div class="form-group">

<label>Μέγιστος Αριθμός Άτοκων Δόσεων</label>

<div class="input-group">
    <input class='form-control' name='installments' type="number" id='installments' value='<?= $installments; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("installments");' title="Εκκαθάριση δόσεων">X</span>
</div>



</div>

<div class="form-group">

	                  	<label>Προμηθευτής</label>


	              		  <select class='form-control' name="supplier" id="supplier">

	              		  	<?php


	              		  			switch ($supplier) {
	              		  				case 'etd':
	              		  					$etd = 'selected';
	              		  					break;
	              		  				case 'partnernet':
	              		  					$partnernet = 'selected';
	              		  					break;
	              		  				case 'logicom':
	              		  					$logicom = 'selected';
	              		  					break;
	              		  				case 'oktabit':
	              		  					$oktabit = 'selected';
	              		  					break;
	              		  				case 'braintrust':
	              		  					$braintrust = 'selected';
	              		  					break;
	              		  				case 'cpi':
	              		  					$cpi = 'selected';
	              		  					break;
	              		  				case 'westnet':
	              		  					$westnet = 'selected';
	              		  					break;
	              		  				case 'ddc':
	              		  					$ddc = 'selected';
											break;
										case 'quest':
	              		  					$quest = 'selected';
											break;
										case 'netconnect':
	              		  					$netconnect = 'selected';
	              		  					break;

	              		  				default:
	              		  					$other = 'selected';
	              		  					break;
	              		  			}


	              		  	?>
	              		  	<option value="etd" <?= $etd ?>>ETD</option>
	              		  	<option value="braintrust" <?= $braintrust ?>>Braintrust</option>
	              		  	<option value="cpi" <?= $cpi ?>>CPI</option>
							<option value="DDC" <?= $ddc ?>>DDC</option>
							<option value="logicom" <?= $logicom ?>>Logicom</option>
							<option value="netconnect" <?= $netconnect ?>>Netconnect</option>
							<option value="oktabit" <?= $oktabit ?>>Oktabit</option>
							<option value="partnernet" <?= $partnernet ?>>PartnerNet</option>
							<option value="quest" <?= $quest ?>>Quest</option>
	              		  	<option value="westnet" <?= $westnet ?>>WestNet</option>
	              		  	<option value="none" <?= $other ?>>Δεν υπάρχει σε κανέναν προμηθευτή</option>

	              		  </select>



	        </div>
<div class="form-group">

	                  	<label>Διαθεσιμότητα</label>

	              		  <select class='form-control' name="availability" id="availability">
	              		  	<option onClick='toogleUpcommingDate("false")' value="">----</option>
	              		  	<option onClick='toogleUpcommingDate("false")' value="2" <?= $instock; ?>>Διαθέσιμο στο κατάστημα</option>
	              		  	<option onClick='toogleUpcommingDate("false")' value="1" <?= $outstock; ?>>Μη διαθέσιμο στο κατάστημα / Διαθέσιμο στον προμηθευτή</option>
	              		  	<option onClick='toogleUpcommingDate("true")' value="0" <?= $outstock2; ?>>Αναμονή παραλαβής</option>
	              		  </select>



	        </div>
	        <div class="form-group upcommingDate">


       <label>Ημ/νία Παραλαβής</label>


                <div class='input-group date' id='datetimepicker1'>
                <?php
                	$upcomingDate = (isset($upcomingDate))?$upcomingDate:'';
                ?>
                    <input name="upcoming_date" type='text' value="<?php echo $upcomingDate; ?>" class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>






	<button type="submit" class="btn btn-block btn-info">Ενημέρωση του Eshop</button>

</form>
<br/>
</div>
		<!-- Delete Form -->
		<div class="col-xs-12 ">
	 		<form method="post" action="">
	 			<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
	 			<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
	 			<input class='form-control' type="hidden" name='status' value='delete'>
	 			<input class='form-control' type="hidden" name='supplier' value='out'>

	 			<button type="submit" class="btn btn-block btn-danger">Aφαίρεση από STOCK</button>
	 		</form>
		</div>

		<!-- Total Removal Form -->
		<div class="col-xs-12 ">
			<br/>
	 		<form method="post" action="" onsubmit="return confirm('Το προϊόν θα διαγραφεί από παντού. Είστε σίγουροι;');">
	 			<input class='form-control' type="hidden" name='title' value='<?= $item->row()->title; ?>'>
	 			<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
	 			<input class='form-control' type="hidden" name='sku' value='<?= $sku; ?>'>
	 			<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
	 			<input class='form-control' type="hidden" name='status' value='total_removal'>

	 			<button type="submit" class="btn btn-block btn-warning" disabled>Ολική Διαγραφή</button>
	 		</form>
		</div>

    </div>

		<div class=" col-xs-12 col-md-10">
			<?php
   			 //flash messages
			    if($this->session->flashdata('flash_message')){

			      echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
			      echo '<a class="close" data-dismiss="alert">&times</a>';
			      echo $this->session->flashdata('flash_message')['Message'];
			      echo '</div>';
			    }
			?>
			<?php
			if ($supplier == 'out')
			{ ?>
			<div class="alert alert-danger">
            	<a class="close" data-dismiss="alert">×</a>
            	Το συγκεκριμένο προϊόν έχει επιλεχθεί από εμάς να μήν εμφανίζεται στο site .
            </div>
            <?php
        	}
        	?>
			<div class ="row">
				<h2 class="col-sm-9"><?= $item->row()->title; ?></h2>
				<div class="col-sm-3">
					<label class="checkbox">
						<input id="ShowOnFrontPage" type="checkbox" value="" <?= $ExistInFrontPage ?> onclick="updateFrontPage('<?php echo $sku?>','<?php echo $pn?>', '<?php echo $category?>')">Εμφάνιση στη Αρχική σελίδα
					</label>
					<label class="checkbox">
						<input id="IsFeatured" type="checkbox" value="" <?= $IsFeaturedProduct ?> onclick="updateFeatured('<?php echo $sku?>','<?php echo $pn?>', '<?php echo $category?>')">Προσθήκη στις προσφορές
					</label>
				</div>
			</div>


		<div style='border-top:1px solid #888'></div><br />
		<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Χαρακτηριστικά</a></li>
              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Σχετικά Προϊόντα</a></li>
               <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">Φωτογραφίες</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">


               <form  method='post' id="chars_form" action="">

   <?php
	$items = $item->result_array();
	$sku = $item->row()->sku;
	foreach ($items[0] as $key => $value) {

		//print_r($item);
		if($key=='product_number' || $key=='id' || $key=='sku' || $key=='new_item'){
			continue;
		}

		if($key == 'description') :
		?>
		<div class="col-xs-12 col-md-12" style="margin-bottom:30px">
			<div class="col-xs-12 col-md-2">
			<label><?= ucfirst(str_replace('_', ' ', $key));  ?></label>
			</div>
			<div class="col-xs-12 col-md-10">

		<?php else : ?>
			<div class="col-xs-12 col-md-6">
				<div class="col-xs-12 col-md-4">
				<label><?= ucfirst(str_replace('_', ' ', $key));  ?></label>
				</div>
				<div class="col-xs-12 col-md-8">

		<?php endif;?>


			<input class='form-control' type="hidden" name='new_item' value='0'>
			<input class='form-control' type="hidden" name='status' value='update'>
			<?php

			if($key == 'description' || $key == 'bonus') {

?>
<textarea id="<?= $key; ?>"  name="<?= $key; ?>" class="form-control edit-form-etd" value="" >
<?php if(isset($_POST[$key])){ echo $_POST[$key]; }else{echo $value; } ?>
</textarea>
</div>
<?php
}else if($key == 'volumetric_weight'){

	if($value >= 33)
		$value = 33;

	$selected_value = (isset($_POST[$key]))?$_POST[$key]:$value;
	$volumetric_weight_array = array('0'=>'Δεν έχει οριστεί','0.2'=>'0.2','0.3'=>'0.3','0.5'=>'0.5','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30','31'=>'31','32'=>'32','33'=>'Υπέρβαρο');
	echo form_dropdown($key, $volumetric_weight_array, $selected_value, 'class="form-control"');
	echo '</div>';

}else{
			?>
			<input type="text" name="<?= $key; ?>" class="form-control edit-form-etd" value="<?php if(isset($_POST[$key])){ echo $_POST[$key]; }else{echo $value; } ?>" /></div>


		<?php
		}
		?>

		</div>

		<?php
	}
	?>
	<div class="col-xs-12 col-md-6">

		<div class="col-xs-12 col-md-4">
		<label>Skroutz URL 	<a target="_blank" href="<?= $skroutzUrl; ?>"><i class="fa fa-external-link" aria-hidden="true"></i></a></label>
		</div>
		<div class="col-xs-12 col-md-8">
			<input class="form-control edit-form-etd" value="<?= $skroutzUrl; ?>" name="skroutz_url"/>

		</div>

	</div>
	<button type="submit" class="btn btn-block btn-success">Ενημέρωση</button>
		</form> </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
              <!-- cross_sells start -->
              <?php

              	if(!$cross_sells){
              		$cross_sells = '';
              	}else{
              		$cross_sells = $cross_sells->row()->products;
              	}
               ?>
                <form  method='post' action="">
		<label>Προϊόντα Παράλληλης Πώλησης <small style="font-weight:normal;color:#888">(με κόμμα)</small></label>

                <input type="text" name="cross_sells_products" class="form-control" value="<?= $cross_sells; ?>" placeholder="1315245, 1312558, 1316956" />
                <br />
                <input type="hidden" name="status" value="related">
                <input type="hidden" name="sku" value="<?= $sku; ?>">
<button type="submit" class="btn btn-block btn-success ">Ενημέρωση</button>
		</form>
              </div>
              <!-- /.tab-pane -->
             <div class="tab-pane" id="tab_3">



             <?php
				if($images){
					//print_r($images->num_rows());
					//http://etd.gr/xml/images/1320604/PNY_FD8GBATT4-EF.jpg
					?>
					<div class="col-xs-12">
					<div class='itemImages row '>

					<?php

					$i=0;
					foreach ($images->result() as $image) {
						if($i==6){
						?>
							<div class='clearfix'></div>
						<?php
						}
						?>
						<div class='imageItem col-sm-2 col-xs-4'>
							<div class="radio" id="<?= $image->id ?>" >
							  <label for="s<?= $image->id ?>">
									<i class="fa <?= ($image->default > 0 ? 'fa-star' : 'fa-star-o'); ?>"></i>
								</label>
								<input class="hidden" id="s<?= $image->id ?>" <?= ($image->default > 0 ? 'checked' : ''); ?> type="radio" name="defaultImage" value="">
							</div>
							<img style="width:100%;" src="<?= base_url().'/images/'.$sku.'/'.$image->image_src.'.jpg'; ?>" alt="">
							<div  title='Διαγραφή αυτής της φωτογραφίας!' class='deleteImg' data-src='<?= $image->image_src; ?>' data-sku='<?= $sku; ?>'>
								&times;
							</div>

						</div>
						<?php
						$i++;
					}


					?>

					</div>
					</div>


					<?php

						}
    echo form_open('edit/'.$category.'/'.$sku, 'class="imageForm" id="imageForm"');
             	echo	form_hidden('status', 'deleteAllImages');
             	$data = array(
			        'name'          => 'button',
			        'id'            => 'button',
			        'value'         => '',
			        'type'          => 'submit',
			        'class'			=> 'btn pull-right hidden  btn-danger ',
			        'content'		=> 'Διαγραφή Φωτογραφιών',
			        'onclick'		=> "return confirm('Είστε σίγουροι;')"
			);
				echo form_button($data);
			echo form_close();
				echo form_open('edit/'.$category.'/'.$sku, 'class="imageForm" id="imageForm"');
				echo	form_hidden('status', 'images');


              ?>
             	<?php

             		$data = array(
				        'type'  => 'text',
				        'name'  => 'imageUrl',
				        'id'    => 'hiddenemail',
				        'placeholder' => 'Amazon URL',
				        'class' => 'form-control'
				);
				?>
				<div class="row">


				<div class="col-sm-10">


				<?php
				echo form_input($data);
				?>
</div>
<div class="col-sm-2">


				<?php



							$data = array(
			        'name'          => 'button',
			        'id'            => 'button',
			        'value'         => 'Υποβολή',
			        'type'          => 'submit',
			        'class'			=> 'btn  btn-success btn-block',
			        'content'		=> 'Υποβολή'
			);

			echo form_button($data);
			?>
			</div></div>
			<?php
             	echo form_close();
             	 ?>
							 <br>
							 <div class="div-col-12">
								 <div id="my-dropzone" class="dropzone">

								 </div>

		           </div>
             </div>

            </div>
            <!-- /.tab-content -->
          </div>


		</div>
</div>


<script>

		function clearPrice(a){
			$('#'+a).val('');
		}

		//escape any double quotes or other illigal characters
		$('#chars_form').on('submit',function(e){

			$('#chars_form input').each(function(){
				tmp = $(this).val();
				if(tmp != ''){
				 newTemp = tmp.replace(/"/g, "'");
				 $(this).val(newTemp);
				}
			});
			$('#chars_form').submit();
		});








</script>
