<?php
	$tables = Modules::run('categories/fullCategoriesArray');
?>
	<div class="suppliers sections col-sm-8 col-xs-12">
		
		<section class="content-header">
	      <h1>1. Ενημέρωση αποθήκης </h1>  
	      <br>	   
	    </section> 
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-orange-active color-palette ">
					<a href="./live/index/oktabit" style="color:#fff;display:block"><br /><span >Oktabit</span><br /><br /></a>
				</div>
			</div>
		</div>
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-light-blue color-palette ">
					<a href="./live/index/logicom" style="color:#fff;display:block"><br /><span >Logicom - Enet</span><br /><br /></a>
				</div>
			</div>
		</div> 
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-green color-palette ">
					<a href="./live/index/braintrust" style="color:#fff;display:block"><br /><span >BrainTrust</span><br /><br /></a>
				</div>
			</div>
		</div> 
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-red color-palette ">
					<a href="./live/index/ddc" style="color:#fff;display:block"><br /><span >Digital Data</span><br /><br /></a>
				</div>
			</div>
		</div> 
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-navy color-palette ">
					<a href="./live/index/copiers" style="color:#fff;display:block"><br /><span >Copiers</span><br /><br /></a>
				</div>
			</div>
		</div>
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-red color-palette ">
					<a href="./live/index/cpi" style="color:#fff;display:block"><br /><span >CPI</span><br /><br /></a>
				</div>
			</div>
		</div>
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-red color-palette ">
					<a href="./live/index/westnet" style="color:#fff;display:block"><br /><span >Westnet</span><br /><br /></a>
				</div>
			</div>
		</div>
		<div class="supplier-item col-sm-4 col-md-3">
			<div class="color-palette-set text-center">
				<div class="bg-orange-active color-palette ">
					<a href="./live/index/partnernet" style="color:#fff;display:block"><br /><span >PartnerNet</span><br /><br /></a>
				</div>
			</div>
		</div>
		<!-- 
		<div class="supplier-item col-sm-4 col-md-2">
			<div class="color-palette-set text-center">
				<div class="bg-navy color-palette ">
					<a href="./live/index/aci" style="color:#fff;display:block"><br /><span >ACI Supplies</span><br /><br /></a>
				</div>
			</div>
		</div>  -->
	<div class="clearfix"></div>
		<section class="content-header">
	      	<h1>2. Δημιουργία XML με νέα προϊόντα </h1>
	      <br>	
	    </section>

		<div class=" col-sm-4 col-md-2">
			<div class="form-group">
				<label>Κατηγορίες Προϊόντων</label>
				<select id="SelectTable" class="form-control">
				<?php foreach($tables as $table){?>
					<?php
					$this->db->where('new_item', 1); 
                    $query = $this->db->get($table); 

                    $rows = $query->num_rows();

                    if($rows<1)
                    	$rows='';
                    else
                    	$rows ="______($rows)";
					?>

					<option onclick='linkGenerator("<?= $table; ?>")'><?php echo ucfirst($table); echo $rows;  ?></option>
				<?php } ?>
				</select>
				<input type="checkbox" name="all_product" value="all" id="isAllSelected"> Όλα τα Προϊόντα<br>
			</div>
		</div>
		<div class="col-sm-4 col-md-2">              
			<div id='xml_link' style="display:none;">
				<div class="box box-success box-solid">
					<div class="box-header with-border">
				  		<h3 id='xmltitle' class="box-title">Removable</h3>
				  		<div class="box-tools pull-right">
				    		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					  	</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body">
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			<script>
				function linkGenerator(a){
					$('#xml_link').show();
					$("#xmltitle").html('Loading...'); 

					var url = "<?= base_url()?>extract/xml/"+a;

					if($("#isAllSelected").is(':checked'))
						url = "<?= base_url()?>extract/xml/"+a+"/true";

					$.post(url, function(data){
						$("#xmltitle").html(a); 
						$("#xml_link .box-body").html(data);        
				    });
				}
					$('#isAllSelected').change(function() {
						var selected = $('#SelectTable :selected').val().toLowerCase();
				       	linkGenerator(selected);       
				    });
			</script>
		</div>
	
<div class="clearfix"></div>
	
		
			
			
			<section class=" content-header">
			      <h1>3. Ενημέρωση Χαρακτηριστικών</h1>
			      <br>	
			</section>
			<div class=" col-xs-12 col-sm-6">
			<div class="form-group">
				<label>Επιλέξτε τη κατηγορία των προϊόντων που θέλετε να ενημερώσετε</label>

	           	<form class="" id="charUpdate" method="post" action="<?= base_url()?>home/xmlUploadUpdate">
	           		<select id="categories" name="categories" name="categories" class="form-control">
	                  	<option value=''>Κατηγορίες</option>
		   				<?php
						foreach($tables as $table){
						?>
							<option value='<?= $table; ?>'><?= ucfirst($table); ?></option>

						<?php
						}
						?>
					</select>
					</br>
				 	<label for="updateXml">Επιλέξτε XML Αρχείου</label>
				 	<input id="updateXml" name="file" type="file">
					</br>
					
						<button type="button" id="uploadSubmit" class="btn pull-left btn-danger">Ενημέρωση</button>
						<img src="<?= base_url()?>/assets/images/loader.gif" id="updateLoader" class="pull-left loader" style="display:none;"/>
					
				</form>
			</div><!-- <div class="form-group"> -->
			</div>
			<script>
				function category(){
					return $('#categories').val();
				}
						
				function updateToggle(){
					$('#uploadSubmit').toggle();
					$('#updateLoader').toggle();
				}

				$(function(){
					var inputFile = $('#updateXml');
					//var category = $('#categories').val();

					var uloadUri = $('#charUpdate').attr('action');

					$('#uploadSubmit').on('click',function(event){

						updateToggle();

						var fileToUpload = inputFile[0].files[0];

						//check if there is actually file to upload
						var cat = category();
						if(cat == ''){
							alert('Δεν έχετε επιλέξει κατηγορία');
							updateToggle();
							return false;
						}
						if(fileToUpload !='undefined'){

							var formData = new FormData();
							
							formData.append('file', fileToUpload);
							formData.append('cat', cat);

							$.ajax({

								url: uloadUri,
								type: 'post',
								data: formData,
								processData:false,
								contentType:false,
								success: function(data){
									if(data=='ok'){
										$('#updateXml').val('');
										$('#categories').val('');
										alert('Τα προϊόντα ενημερώθηκαν.');
										updateToggle();
									}else{
										alert(data);
										updateToggle();
									}
								}
							});
						}
						else {
							alert('Δεν έχετε επιλέξει αρχείο');
							updateToggle();
						}
					});
				});
			</script>
	<div class="clearfix"></div>
			<section class="content-header">
		    	<h1>4. Δημιουργία XML για All Import</h1>
		      	<br>	
		    </section>
			<div class="col-sm-6 col-md-6">
				<div class="form-group">
					<label>Κατηγορίες Προϊόντων</label>
					<select class="form-control">
						<?php
						foreach($tables as $table){
						?>
							<option onclick='allImportGenerator("<?= $table; ?>")'><?= ucfirst($table); ?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>
		    <div class="col-sm-6 col-md-6">              
		        <div id='xml_link2' style="display:none;">
		          	<div class="box box-success box-solid">
		            	<div class="box-header with-border">
		              		<h3 id='xmltitle2' class="box-title">Removable</h3>
		              		<div class="box-tools pull-right">
		                		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		              		</div><!-- /.box-tools -->
		            	</div><!-- /.box-header -->
			            <div class="box-body">
			            </div><!-- /.box-body -->
					</div><!-- /.box -->
		        </div>
		  	</div>
			<script>
				function allImportGenerator(a){
					$('#xml_link2').show();
					$("#xmltitle2").html('Loading...'); 
					
					var url = "<?= base_url()?>extract/allImport/"+a;
					$.post(url, function(data){
						$("#xmltitle2").html(a); 
						$("#xml_link2 .box-body").html(data);        
				    });
				}
			</script>
		</div>

		<div class="sections col-xs-6" style="display: none;">
			<section class="content-header">
		    	<h1>5. Εισαγωγή όλων All Import</h1>
		      	<br>	
		    </section>

		    <div class="col-sm-4">
		    	<a href="http://etd.gr/xml/extract/allImport/all" target="_blank" class="btn pull-left btn-danger" role="button">Ενημέρωση</a>
			</div>
			
		
</div><!-- EEEE -->

<div style="background:#fff;" class="col-sm-4 col-xs-12">
			<section class="content-header">
		    	<h1 style="text-align:center;">Σε Απόθεμα</h1>
		      	<br>	
		    </section>

		    <?php

		    $where = array(
		    	'supplier'=>'etd'
		    	);

                $stock = Modules::run('crud/get','live',$where);
               // print_r($stock->result_array());
                $cat_array = array();
                $out = '';
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
                	@$price = $items['price_tax'];
                	if(!$price){
                		$price = '<span style="color:red;">Δεν υπάρχει τιμή</span>';
                	}else{
                		$price = "€ $price";
                	}

                	$img = Modules::run('images/getFirstImage', $sku, true);
					
					$out.=' <div class="instock_item clearfix" data-category="'.$category.'">
					                			'.$img.'
					        <h5><a href="http://etd.gr/xml/edit/'.$category.'/'.$sku.'">'.$title.'</a></h5>
					        <strong style="color:#0fc504;">SKU: '.$sku.'</strong>
					        <span class="instock_item_price">
					        Τιμή στο site: '.$price.'
					        </span>


					        </div>';
                	
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
