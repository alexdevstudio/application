
<div class="col-xs-12">
	<br>
	<div class="header_stock">
		<div class="col-md-1 col-sm-2">
			&nbsp;
		</div>
		<div class="col-md-1 col-sm-2 text-center">
			SKU
		</div>
		<div class="col-md-1 col-sm-2 text-center">
			Product Number
		</div>
		<div class="col-md-6 col-sm-6 text-center">
			ΤΙΤΛΟΣ
		</div>
		<div class="col-md-2 col-sm-6 text-center">
			Κατηγορία
		</div>
		<div class="col-md-1 col-sm-6 text-right">
			&nbsp;
		</div>
	</div>
	<div style="background:#fff; padding-bottom: 3px;">
		<?php
		$first = 0;
		$offset = '';
		foreach ($Featured_products as $Featured_product) {

			$where = array(
				'sku'=>$Featured_product->sku
				);
				
			$prod_title = Modules::run('crud/get',$Featured_product->category, $where);
			$prod_title = $prod_title->result()[0]->title;

			//$img = Modules::run('images/getFirstImage', $Featured_product->sku, false);
			$img = 'https://etd.gr/xml/images/'.$Featured_product->sku.'/'.$Featured_product->image.'.jpg';
			
			if ($first == 0 || ($first % 5) == 0)
				$offset = 'col-md-offset-1';
			else
				$offset = '';

			$first ++;
		?>
			<div class="col-md-2 <?= $offset ?>">
				<div class=" col-xs-12 f-prod">
					<div class=" col-xs-12">
						<div class="col-xs-12 f-img">
							<a href="<?= base_url('edit/'.$Featured_product->category.'/'.$Featured_product->sku); ?>" target="_blank" alt="<?= $prod_title ?>">
								<img src="<?= $img ?>"/>
							</a>
						</div>
						<div class=" col-xs-12 f-desc">
						<a href="<?= base_url('edit/'.$Featured_product->category.'/'.$Featured_product->sku); ?>" target="_blank" alt="<?= $prod_title ?>">
							<h4 class="f-desc-text text-center"><?= $prod_title ?></h4>
						</a>
						</div>
					</div>
					<div class="col-xs-12 f-info">
						<!--
						<div class="f-price-wrapper text-center">
							<div class="f-total-price">
								€ 1199.00                          
								<span class="f-regular_price">€ 1378.90</span>                        
							</div>
						</div>
						-->
						<div class="f-secondary-info">
							<div class="f-sku text-center">
								Κωδικός etd.gr:
								<strong><?= $Featured_product->sku ?></strong>
							</div>
						</div>
						<div class="f-delete">
							<a href="<?= base_url('/featured_products/delete/'.$Featured_product->sku.'/true'); ?>" class="btn btn-danger btn-md">Διαγραφή</a> 
						</div>
					</div>
				</div>
			</div>
		<!--
			<div class="instock_item clearfix" data-category="<?= $Featured_product->category ?>">
				<div class="col-md-1 col-sm-2">
					<img data-original="<?= $img ?>" class="lazyimg" />
				</div>
				<div class="col-md-1 col-sm-2 text-center">
					<strong><a href="<?= base_url('edit/'.$Featured_product->category.'/'.$Featured_product->sku); ?>" target="_blank"> <?= $Featured_product->sku ?></a></strong>
				</div>
				<div class="col-md-1 col-sm-2 text-center">
					<?= $Featured_product->product_number ?>
				</div>
				<div class="col-md-6 col-sm-6"> 
					<a href="<?= base_url('edit/'.$Featured_product->category.'/'.$Featured_product->sku); ?>" target="_blank"><?= $prod_title ?></a>
				</div>
				<div class="col-md-2 col-sm-6 text-center">
					<?= $Featured_product->section ?>
				</div>
				<div class="col-md-1 col-sm-6 text-right '.$price_color.'">
					<a href="<?= base_url('/featured_products/delete/'.$Featured_product->sku.'/true'); ?>" class="btn btn-danger btn-md">Διαγραφή</a> 
				</div>
				
			</div>
		-->
		<?php
		}
		?>
	</div>
</div>


