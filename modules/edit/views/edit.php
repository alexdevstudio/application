
<div class="sections col-xs-12">
	<section class="content-header">
	
		<br>	   
    </section> 

    <div class="col-xs-12 col-md-2">

   
 <?php 

	$sku = $item->row()->sku;
	$pn = $item->row()->product_number;

	$image = Modules::run("images/getFirstImage",$sku,true);

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
 <form method="post" action=''>
 <div class="form-group">
<label>Κανονική Τιμή</label>
<?php
$price = '';
$availability = '';
$sale_price = '';
$supplier = '';
$instock = '';
$outstock = '';
$outstock2 = '';

if($itemLive){
	
	$price = $itemLive->row()->price_tax;
	$sale_price = $itemLive->row()->sale_price; 
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
	}elseif($av=='Κατόπιν παραγγελίας σε 1 εργάσιμη'){
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
	$other = '';

?>
<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
<input class='form-control' type="hidden" name='delete_flag' value='0'>
<input class='form-control' type="hidden" name='status' value='add'>


<div class="input-group">
    <input class='form-control' name='price_tax' id='regular' value='<?= $price; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("regular");' title="Εκκαθάριση τιμής">X</span>
</div>




</div>
<div class="form-group">
	                  	
<label>Μέγιστος Αριθμός Άτοκων Δόσεων</label>

<div class="input-group">
    <input class='form-control' name='installments' type="number" id='installments' value='<?= $installments; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("installments");' title="Εκκαθάριση δόσεων">X</span>
</div>



</div>
<div style=''class="form-group">
	                  	
<label>Τιμή Προσφοράς</label>

<div class="input-group">
    <input class='form-control' name='sale_price' id='sale_price' value='<?= $sale_price; ?>'>
    <span style="cursor:pointer;color:#dd4b39;" class="input-group-addon" id="basic-addon1" onclick='clearPrice("sale_price");' title="Εκκαθάριση τιμής">X</span>
</div>
	

</div>
<div class="form-group">
	                  	
	                  	<label>Προμυθευτής</label>
	                  	
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
	              		  				
	              		  				default:
	              		  					$other = 'selected';
	              		  					
	              		  					break;
	              		  			}
	              		  		

	              		  	?>
	              		  	<option value="etd" <?= $etd ?>>ETD</option>
	              		  	<option value="logicom" <?= $logicom ?>>Logicom</option>
	              		  	<option value="oktabit" <?= $oktabit ?>>Oktabit</option>
	              		  	<option value="braintrust" <?= $braintrust ?>>Braintrust</option>
	              		  	<option value="cpi" <?= $cpi ?>>CPI</option>
	              		  	<option value="westnet" <?= $westnet ?>>WestNet</option>
	              		  	<option value="DDC" <?= $ddc ?>>DDC</option>
	              		  	<option value="partnernet" <?= $partnernet ?>>PartnerNet</option>
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
                    <input name="upcoming_date" type='text' value="<?php echo $upcomingDate; ?>" class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        
       
   

	       

	<button type="submit" class="btn btn-block btn-warning">Ενημέρωση του Eshop</button>

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
    </div>

		<div class=" col-xs-12 col-md-10">
		<h2><?= $item->row()->title; ?></h2>
		<div style='border-top:1px solid #888'></div><br />
		<form  method='post' action="">

   <?php
	$items = $item->result_array();
	foreach ($items[0] as $key => $value) {
		
		//print_r($item);
		if($key=='product_number' || $key=='id' || $key=='sku' || $key=='new_item'){
			continue;
		}
		?>

		<div class="col-xs-12 col-md-6">

		<div class="col-xs-12 col-md-4">
		<label><?= ucfirst(str_replace('_', ' ', $key));  ?></label>
		</div>
		<div class="col-xs-12 col-md-8">
			<input class='form-control' type="hidden" name='new_item' value='0'>
			<input class='form-control' type="hidden" name='status' value='update'>
			<?php 

			if($key == 'description' || $key == 'bonus') {

?>
<textarea  name="<?= $key; ?>" class="form-control edit-form-etd" value="" >
<?php if(isset($_POST[$key])){ echo $_POST[$key]; }else{echo $value; } ?>
</textarea>
</div>
<?php
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
		</form>
		</div>
</div>


<script type="text/javascript">
	/*$(document).ready(function(){*/

			function clearPrice(a){
				$('#'+a).val('');
			}

/*});*/

</script>