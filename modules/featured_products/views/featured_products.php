
<div class="col-xs-12">
	<hr>
	<div class="box">
		<div class="box-header">
			<h3 class="box-title">Προϊόντα σε Προσφορά</h3>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-12">
					<table id="FeaturedProductTable" class="table table-striped table-hover dataTable">
						<thead>
							<tr role="row">
							<th>SKU</th>
							<th>Woocommerce ID</th>
							<th>Product Number</th>
							<th>Κατηγορία</th>
							<th>-</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($Featured_products as $Featured_product) {
							?>							
							<tr role="row">
								<td><?= $Featured_product->sku ?></td>
								<td><?= $Featured_product->woo_id ?></td>
								<td><?= $Featured_product->product_number ?></td>
								<td><?= $Featured_product->section ?></td>
								<td><a href="<?= base_url('/featured_products/delete/'.$Featured_product->sku.'/true'); ?>" class="btn btn-danger btn-md col-xs-offset-1 col-xs-5">Διαγραφή</a></td>
							</tr>
							<?php
							}
							?>
						</tbody>

					</table>
				</div>
			</div>
		</div>
	</div>
</div>


