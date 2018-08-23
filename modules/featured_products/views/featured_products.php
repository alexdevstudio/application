<style type="text/css">
.col-md-5ths, .col-lg-5ths, .col-xl-5ths, 
.col-xs-two5ths, .col-sm-two5ths, .col-md-two5ths, .col-lg-two5ths, .col-xl-two5ths, 
.col-xs-three5ths, .col-sm-three5ths, .col-md-three5ths, .col-lg-three5ths, .col-xl-three5ths, 
.col-xs-four5ths, .col-sm-four5ths, .col-md-four5ths, .col-lg-four5ths, .col-xl-four5ths, 
.col-xs-five5ths, .col-sm-five5ths, .col-md-five5ths, .col-lg-five5ths, .col-xl-five5ths
{
    position: relative;
    min-height: 1px;
	padding: 0 3px;
}

@media (min-width: 576px) {
    .col-sm-5ths {width: 20%;float: left;}
    .col-sm-two5ths {width: 40%;float: left;}
    .col-sm-three5ths {width: 60%;float: left;}
    .col-sm-four5ths {width: 80%;float: left;}
}

@media (min-width: 768px) {
    .col-md-5ths {width: 20%;float: left;}
    .col-md-two5ths {width: 40%;float: left;}
    .col-md-three5ths {width: 60%;float: left;}
    .col-md-four5ths {width: 80%;float: left;}
}

@media (min-width: 992px) {
    .col-lg-5ths {width: 20%;float: left;}
    .col-lg-two5ths {width: 40%;float: left;}
    .col-lg-three5ths {width: 60%;float: left;}
    .col-lg-four5ths {width: 80%;float: left;}
}

@media (min-width: 1200px) {
    .col-xl-5ths {width: 20%;float: left;}
    .col-xl-two5ths {width: 40%;float: left;}
    .col-xl-three5ths {width: 60%;float: left;}
    .col-xl-four5ths {width: 80%;float: left;}
}
</style>
<div class="col-xs-12">
	<br>

	<?php
	$first_published = $first_trashed = 0;
	$offset = $out_published = $out_trushed = '';
	$more_than_five = false;

	foreach ($Featured_products as $Featured_products) {

		$where = array(
			'sku'=>$Featured_products->sku
			);
			
		$prod_title = Modules::run('crud/get',$Featured_products->category, $where);
		$prod_title = $prod_title->result()[0]->title;

		//$img = Modules::run('images/getFirstImage', $Featured_products->sku, false);
		$img = 'https://etd.gr/xml/images/'.$Featured_products->sku.'/'.$Featured_products->image.'.jpg';

		if($Featured_products->product_status == 'publish') {

			/*if ($first_published == 0)
				$offset = 'col-md-offset-1';
			elseif(($first_published % 5) == 0)
			{
				$offset = 'col-md-offset-1';
				$more_than_five = true;
			}
			else*/
				$offset = '';

			$first_published ++;
			if($more_than_five)
				$out = '</div><hr><div class="row"><h4 class="f-header">Δεν εμφανίζονται γιατί είναι παραπάνω από 5</h4><div class="col-md-2 '.$offset.'">';
			else
				$out = '<div class="col-md-5ths '.$offset.'">';
		}
		else {
			/*if ($first_trashed == 0 || ($first_trashed % 5) == 0)
				$offset = 'col-md-offset-1';
			else*/
				$offset = '';

			$first_trashed ++;

			$out = '<div class="col-md-5ths '.$offset.'">';
		}
		
			$out .= '<div class=" col-xs-12 f-prod">';
				$out .= '<div class=" col-xs-12">';
					$out .= '<div class="col-xs-12 f-img">';
						$out .= '<a href="'.base_url("edit/".$Featured_products->category."/".$Featured_products->sku) .'" target="_blank" alt="'.$prod_title .'">';
							$out .= '<img src="'.$img .'"/>';
						$out .= '</a>';
					$out .= '</div>';
					$out .= '<div class=" col-xs-12 f-desc">';
						$out .= '<a href="'.base_url("edit/".$Featured_products->category."/".$Featured_products->sku) .'" target="_blank" alt="'.$prod_title .'">';
							$out .= '<h4 class="f-desc-text text-center">'.$prod_title .'</h4>';
						$out .= '</a>';
					$out .= '</div>';
				$out .= '</div>';
				$out .= '<div class="col-xs-12 f-info">';
					$out .= '<div class="f-secondary-info">';
						$out .= '<div class="f-sku text-center">Κωδικός etd.gr:';
							$out .= '<strong>'.$Featured_products->sku .'</strong>';
						$out .= '</div>';
					$out .= '</div>';
					$out .= '<div class="f-delete">';
						$out .= '<a href="'.base_url("/featured_products/delete/".$Featured_products->sku."/true") .'" class="btn btn-danger btn-md">Διαγραφή</a>'; 
					$out .= '</div>';
				$out .= '</div>';
			$out .= '</div>';
		$out .= '</div>';

		if($Featured_products->product_status == 'publish') {

			$out_published .= $out;
		}
		else {

			$out_trushed .= $out;
		}
	}
	
	if($out_published != ''){
	?>
		<div class="container">
			<div class="row">
				<h3 class="f-header">Διαθέσιμα προϊόντα</h3>
				<?= $out_published ?>
			</div>
		</div>
		
	<?php
	}
	if($out_trushed != '') {
		?>
		<br>
		<div class="container">
			<div class="row">
				<h3 class="f-header">Μη διαθέσιμα προϊόντα</h3>
				<?= $out_trushed ?>
			</div>
		</div>
	<?php
	}
	?>

			<!--
				<div class="col-md-2 <?= $offset_published ?>">
					<div class=" col-xs-12 f-prod">
						<div class=" col-xs-12">
							<div class="col-xs-12 f-img">
								<a href="<?= base_url('edit/'.$Featured_products->category.'/'.$Featured_products->sku); ?>" target="_blank" alt="<?= $prod_title ?>">
									<img src="<?= $img ?>"/>
								</a>
							</div>
							<div class=" col-xs-12 f-desc">
							<a href="<?= base_url('edit/'.$Featured_products->category.'/'.$Featured_products->sku); ?>" target="_blank" alt="<?= $prod_title ?>">
								<h4 class="f-desc-text text-center"><?= $prod_title ?></h4>
							</a>
							</div>
						</div>
						<div class="col-xs-12 f-info">
							<div class="f-secondary-info">
								<div class="f-sku text-center">
									Κωδικός etd.gr:
									<strong><?= $Featured_products->sku ?></strong>
								</div>
							</div>
							<div class="f-delete">
								<a href="<?= base_url('/featured_productss/delete/'.$Featured_products->sku.'/true'); ?>" class="btn btn-danger btn-md">Διαγραφή</a> 
							</div>
						</div>
					</div>
				</div>
			-->

<!--
			<div class="instock_item clearfix" data-category="<?= $Featured_products->category ?>">
				<div class="col-md-1 col-sm-2">
					<img data-original="<?= $img ?>" class="lazyimg" />
				</div>
				<div class="col-md-1 col-sm-2 text-center">
					<strong><a href="<?= base_url('edit/'.$Featured_products->category.'/'.$Featured_products->sku); ?>" target="_blank"> <?= $Featured_products->sku ?></a></strong>
				</div>
				<div class="col-md-1 col-sm-2 text-center">
					<?= $Featured_products->product_number ?>
				</div>
				<div class="col-md-6 col-sm-6"> 
					<a href="<?= base_url('edit/'.$Featured_products->category.'/'.$Featured_products->sku); ?>" target="_blank"><?= $prod_title ?></a>
				</div>
				<div class="col-md-2 col-sm-6 text-center">
					<?php 
					if ($Featured_products->section != $Featured_products->category)
						echo $Featured_products->section.' ('.$Featured_products->category.')';
					else
						echo $Featured_products->section;
					?>
				</div>
				<div class="col-md-1 col-sm-6 text-right '.$price_color.'">
					<a href="<?= base_url('/featured_productss/delete/'.$Featured_products->sku.'/true'); ?>" class="btn btn-danger btn-md">Διαγραφή</a> 
				</div>
				
			</div>
-->

	</div>
</div>