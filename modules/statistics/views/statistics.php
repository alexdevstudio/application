<div class="suppliers sections col-sm-12 col-xs-12">

	<?php
		$this->load->model('extract/extract_model');
			//flash messages
		if($this->session->flashdata('flash_message')){

			echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
			echo '<a class="close" data-dismiss="alert">&times</a>';
			echo $this->session->flashdata('flash_message')['Message'];   
			echo '</div>'; 
		}
	?>
	
	<section class="content-header">
		<h1>Εξαγωγή Στατιστικών ανα προμηθευτή </h1>  
		<br>	   
	</section> 
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-orange-active color-palette ">
				<a href="<?= base_url('/statistics/index/oktabit'); ?>" style="color:#fff;display:block"><br /><span >Oktabit</span><br /><br /></a>
			</div>
		</div>
	</div>
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-light-blue color-palette ">
				<a href="<?= base_url('/statistics/index/logicom'); ?>" style="color:#fff;display:block"><br /><span >Logicom - Enet</span><br /><br /></a>
			</div>
		</div>
	</div> 
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-green color-palette ">
				<a href="<?= base_url('/statistics/index/braintrust'); ?>" style="color:#fff;display:block"><br /><span >BrainTrust</span><br /><br /></a>
			</div>
		</div>
	</div> 
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-red color-palette ">
				<a href="<?= base_url('/statistics/index/ddc'); ?>" style="color:#fff;display:block"><br /><span >Digital Data</span><br /><br /></a>
			</div>
		</div>
	</div> 
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-navy color-palette ">
				<a href="<?= base_url('/statistics/index/copiers'); ?>" style="color:#fff;display:block"><br /><span >Copiers</span><br /><br /></a>
			</div>
		</div>
	</div>
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-red color-palette ">
				<a href="<?= base_url('/statistics/index/cpi'); ?>" style="color:#fff;display:block"><br /><span >CPI</span><br /><br /></a>
			</div>
		</div>
	</div>
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-red color-palette ">
				<a href="<?= base_url('/statistics/index/westnet'); ?>" style="color:#fff;display:block"><br /><span >Westnet</span><br /><br /></a>
			</div>
		</div>
	</div>
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-orange-active color-palette ">
				<a href="<?= base_url('/statistics/index/partnernet'); ?>" style="color:#fff;display:block"><br /><span >PartnerNet</span><br /><br /></a>
			</div>
		</div>
	</div>
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-light-blue color-palette ">
				<a href="<?= base_url('/statistics/index/quest'); ?>" style="color:#fff;display:block"><br /><span >Quest</span><br /><br /></a>
			</div>
		</div>
	</div>
	<!-- 
	<div class="supplier-item col-sm-4 col-md-2">
		<div class="color-palette-set text-center">
			<div class="bg-navy color-palette ">
				<a href="<?= base_url('/statistics/index/aci'); ?>" style="color:#fff;display:block"><br /><span >ACI Supplies</span><br /><br /></a>
			</div>
		</div>
	</div>  -->

<?php
if(isset($Supplier_products))
{
	$products_quantity= $Supplier_products['TotalProducts'];
	unset($Supplier_products['TotalProducts']);
	
	?>
	
	<div class="col-sm-12 col-md-12">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th colspan="6" style="background-color: #aaa;text-align:center;vertical-align: middle; font-size: 26px;" ><?= $Supplier.' <span style="font-size: 14px;">Αριθ. προϊόντων: '.$products_quantity.'</span>' ?></th>
				</tr>
			</thead>
			<tbody>              
			<?php

			foreach ($Supplier_products as $category=>$category_products)
			{			
				echo '<tr>';
					echo '<th colspan="6" style="background-color: #ccc; text-align:center;" >';
						echo strtoupper($category);
					echo '</th>';
				echo '</tr>';
				echo '<tr>';
					echo '<th align="center">';
						echo 'Product #';
					echo '</th>';
					echo '<th align="center">';
						echo 'SKU';
					echo '</th>';
					echo '<th align="center" width="75px">';
						echo 'Αγορά';
					echo '</th>';
					echo '<th align="center" width="75px">';
						echo 'Πώληση';
					echo '</th>';
					echo '<th align="center">';
						echo 'Μάρκα';
					echo '</th>';
					echo '<th align="center">';
						echo 'Τίτλος';
					echo '</th>';
				echo '<tr>';
				echo '<tr>';
					echo '<th colspan="2">';
						echo '&nbsp;';
					echo '</th>';
					echo '<th colspan="2" align="center" style="color: #ff0000;">';
						echo '(Τιμές χωρίς Φ.Π.Α.)';
					echo '</th>';
					echo '<th colspan="2">';
						echo '&nbsp;';
					echo '</th>';
				echo '<tr>';


				foreach ($category_products as $array => $product)
				{
					$RowColor = $new_product = '';
					if(isset($product->new_item))
					{
						if($product->new_item == '1')
						{
							$RowColor = 'style="background-color: #ffe3be;"';
							$new_product = ' <span style="color: #ff0000;">(ΝΕΟ ΠΡΟΪΟΝ)</span>';
						}
					}
					
					echo '<tr '.$RowColor.'>';
						echo '<td>';
							echo $product->product_number;
						echo '</td>';
						echo '<td>';
							echo '<a href="https://etd.gr/?product_cat=0&s=%22'.$product->sku.'%22&post_type=product" target="_blank">'.$product->sku.'</a>';
						echo '</td>';
						echo '<td align="right">';
							echo $product->net_price.' &euro;';
						echo '</td>';
						echo '<td align="right">';

							$product_for_price['net_price'] = $product->net_price;
							$product_for_price['recycle_tax'] = $product->recycle_tax;
							$product_for_price['category'] = $product->category;
							$product_for_price['brand'] = $product->brand;
							
							$Product_price =  number_format((float)$this->extract_model->priceTax($product_for_price)/1.24, 3, '.', '');
							echo $Product_price.' &euro;';
							//echo $product->net_price.' &euro;';
						echo '</td>';
						echo '<td align="center">';
							echo $product->brand;
						echo '</td>';
						echo '<td>';
							echo $product->title . $new_product;
						echo '</td>';
					echo '</tr>';
				}
			}
			?>
			</tbody>
		</table>			
	</div>
	<?php
}
?>

</div>