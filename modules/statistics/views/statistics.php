

	<div class="suppliers sections col-sm-12 col-xs-12">

		<?php
   			 //flash messages
            if($this->session->flashdata('flash_message')){

                echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
                echo '<a class="close" data-dismiss="alert">&times</a>';
                echo $this->session->flashdata('flash_message')['Message'];   
                echo '</div>'; 
            }
		?>
		
		<section class="content-header">
	      <h1>Εξαγωγή Σταττιστικών ανα προμηθευτή </h1>  
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
        ?>
		
		<div class="col-sm-12 col-md-12">
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th colspan="5" style="background-color: #aaa;text-align:center;vertical-align: middle; font-size: 26px;" ><?= $Supplier ?></th>
                    </tr>
                </thead>
                <tbody>              
                <?php
                $cat_pre = '';

                foreach ($Supplier_products as $Supplier_product)
                {
                    if( $cat_pre == $Supplier_product->category)
                        $num ++;
                    else
                    {
                        echo '<tr>';
                        echo '<td colspan="5" style="background-color: #ccc; text-align:center;" >';
                        echo strtoupper($Supplier_product->category);
                        echo '</td>';
                        echo '</tr>';
                        $num = 1;
                    }

                    $cat_pre = $Supplier_product->category;

                    echo '<tr>';
                        echo '<td>';
                            echo $num;
                        echo '</td>';
                    foreach ($Supplier_product as $product_column => $product_value)
                    {
                        if ($product_column != 'product_number' && $product_column != 'product')
                            continue;

                        if($product_column != 'product')
                        {
                            echo '<td>';
                                echo $product_value;
                            echo '</td>';
                        }
                        else
                        {   
                            if($product_value)
                            {
                                echo '<td>';
                                    echo $product_value->sku;
                                echo '</td>';
                                echo '<td>';
                                    echo $product_value->brand;
                                echo '</td>';
                                echo '<td>';
                                    echo $product_value->title;
                                echo '</td>';
                            }
                            else
                            {
                                echo '<td colspan="3" style="color: #f90c0c; text-align:center;">';
                                    echo 'ΔΕΝ ΥΠΑΡΧΕΙ ΤΟ ΠΡΟΪΟΝ ΣΤΗΝ ΚΑΤΗΓΟΡΙΑ';
                                echo '</td>';

                            }
                        }
                    }
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>			
		</div>
        <?php
    }
    ?>

    </div>