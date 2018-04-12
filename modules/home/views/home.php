<?php
	$tables = Modules::run('categories/fullCategoriesArray');
?>
<section class="content">
	<div class="row">
		<div class="col-md-9 col-sm-12">
<?php
   			 //flash messages
			    if($this->session->flashdata('flash_message')){

			      echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
			      echo '<a class="close" data-dismiss="alert">&times</a>';
			      echo $this->session->flashdata('flash_message')['Message'];
			      echo '</div>';
			    }
			?>

			<div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Βήμα 1: Ενημέρωση αποθήκης</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              <div class="box-body">
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-orange-active color-palette ">
											<a href="./live/index/oktabit" style="color:#fff;display:block"><br /><span >Oktabit</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-light-blue color-palette ">
											<a href="./live/index/logicom" style="color:#fff;display:block"><br /><span >Logicom - Enet</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-green color-palette ">
											<a href="./live/index/braintrust" style="color:#fff;display:block"><br /><span >BrainTrust</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-red color-palette ">
											<a href="./live/index/ddc" style="color:#fff;display:block"><br /><span >Digital Data</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-navy color-palette ">
											<a href="./live/index/copiers" style="color:#fff;display:block"><br /><span >Copiers</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-red color-palette ">
											<a href="./live/index/cpi" style="color:#fff;display:block"><br /><span >CPI</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-red color-palette ">
											<a href="./live/index/westnet" style="color:#fff;display:block"><br /><span >Westnet</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-orange-active color-palette ">
											<a href="./live/index/partnernet" style="color:#fff;display:block"><br /><span >PartnerNet</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-light-blue color-palette ">
											<a href="./live/index/quest" style="color:#fff;display:block"><br /><span >Quest</span><br /><br /></a>
										</div>
									</div>
								</div>
								<div class="supplier-item col-sm-6 col-md-2">
									<div class="color-palette-set text-center">
										<div class="bg-green color-palette ">
											<a href="./live/index/netconnect" style="color:#fff;display:block"><br /><span>NETCONNECT</span><br /><br /></a>
										</div>
									</div>
								</div>
              </div>
          </div>

					<div class="box box-warning">
		            <div class="box-header with-border">
		              <h3 class="box-title">Βήμα 2: Δημιουργία XML με νέα προϊόντα</h3>
		            </div>
		            <!-- /.box-header -->
		            <!-- form start -->
		              <div class="box-body">
										<div class=" col-sm-12 col-md-4">
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

										<div class="col-sm-12 col-md-3">
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
		              </div>
		          </div>
							<div class="box box-primary">
				            <div class="box-header with-border">
				              <h3 class="box-title">Βήμα 3: Ενημέρωση Χαρακτηριστικών</h3>
				            </div>
				            <!-- /.box-header -->
				            <!-- form start -->
				              <div class="box-body">

												<div class=" col-sm-12 col-md-4">

												<div class="form-group">
													<label>Επιλέξτε τη κατηγορία των προϊόντων που θέλετε να ενημερώσετε</label>

										           	<form class="" id="charUpdate" method="post" action="<?= base_url()?>home/xmlUploadUpdate">
										           		<select id="categories" name="categories" name="categories" class="form-control ">
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
														</div>
													</div>
												</div>

														<div class="box-footer">
															<button type="button" id="uploadSubmit" class="btn pull-right btn-primary">Ενημέρωση</button>
															<img src="<?= base_url()?>/assets/images/loader.gif" id="updateLoader" class="pull-left loader" style="display:none;"/>
							              </div>
													</form>
				          </div>
									<div class="box box-success">
						            <div class="box-header with-border">
						              <h3 class="box-title">Βήμα 4: Δημιουργία XML για All Import</h3>
						            </div>

						            <!-- /.box-header -->
						            <!-- form start -->
						              <div class="box-body">
														<div class="col-sm-6 col-md-3">
														<div class="form-group">
															<label>Πόσα προϊόντα να εξαχθούν;</label>

														<select class='form-control allImport_num_rows'>
																<option value='all' >All</option>
																<option value='5'>5</option>
																<option value='10'>10</option>
																<option value='15'>15</option>
																<option value='20' selected>20</option>
															</select>
															</div>

													</div>
													<div class="col-sm-6 col-md-3">
														<div class="form-group">
															<label>SKU Προϊόντων (με κόμμα):</label>

														<input class='form-control allImport_sku' />

															</div>

													</div>
													<div class="col-sm-12 col-md-3">
														<div class="form-group">
															<label>Κατηγορίες Προϊόντων:</label>
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
													<div class="clearfix"></div>
											<div class="col-sm-12 col-md-3">
														<div class="form-group">

															<input type="checkbox" name='imagesOnly' id='imagesOnly' value='1' class=''/>
															Μόνο Φωτογραφίες
														</div>

													</div>

												    <div class="col-sm-12 col-md-3">
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
						              </div>
						          </div>
											<div class="box box-danger" style="display: none;">
														<div class="box-header with-border">
															<h3 class="box-title">Βήμα 5: Εισαγωγή όλων All Import</h3>
														</div>


														<div class="box-body">
															<div class="col-sm-4">
													    	<a href="<?= base_url(); ?>extract/allImport/all" target="_blank" class="btn pull-left btn-danger" role="button">Ενημέρωση</a>
														</div>
														</div>
											</div>

										</div>
										</div>
								</section>
