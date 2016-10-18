<div class='col-xs-12'>
	<hr />

<div class="box">
            <div class="box-header">
              <h3 class="box-title">Ποσοστά κατηγοριών</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="dataTables_wrapper form-inline dt-bootstrap" id="example2_wrapper">
              <div class="row">
              <div class="col-sm-6">
              	
              </div>
              <div class="col-sm-6">
              	
              </div>
              </div>
              <div class="row">
              <div class="col-sm-12">
              <table  id="profitTable" class="table table-bordered table-hover dataTable">
                <thead>
                <tr role="row">
                <th colspan="1" rowspan="1" >Περιγραφή</th>
                <th colspan="1" rowspan="1" >Category</th>
                <th colspan="1" rowspan="1" >Ποσοστό</th>
                <th colspan="1" rowspan="1" >Νέο Ποσοστό (Χωρίς το %)</th>

                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;

                foreach ($rates_table->result() as $item) {
                	
                	$class = ' odd ';
                	if ($i % 2 == 0) {
					           $class = ' even ';
					         }
					$i++;
					$category = $item->category;
					$rate = $item->rate;
          $rate = $rate*100;
					$description = $item->description;

          $class_color='';

          if($rate !=6)
            $class_color='bg-orange-light';
           

					?>

 				<tr class="<?= $class; ?>" role="row">
                  <td><?= $description; ?></td>
                  <td><?= $category; ?></td>
                  <td class="<?= $class_color; ?>" id="newRate_<?= $category; ?>"><?= $rate.'%' ?></td>
                  <td>
               		<form id="rate_<?= $category; ?>" action="./updateRate">
               		<input type='hidden' value='<?= $rate; ?>' name='rate' id='rate' />
               		
               		<div class="input-group col-xs-6 pull-left">
		                  <div class="input-group-addon">
		                    <i class="fa fa-percent"></i>
		                  </div>
		                  <input class="form-control " type='text'  id='rate_price<?= $category; ?>' name='rate_price<?= $category; ?>' />
               		</div>
               		
                 	 <button id="" type="button" class="btn btn-success btn-md col-xs-offset-1 col-xs-5" onclick="fixRate('rate_<?= $category; ?>', '<?= $category; ?>', 'rate_price<?= $category; ?>');" >Αλλαγή</button>

                   </form> 
                  </td>
                </tr>


			<?php

                }
		?>
                
              </tbody>
                <tfoot>
                <tr role="row">
                <th colspan="1" rowspan="1" >Περιγραφή</th>
                <th colspan="1" rowspan="1" >Category</th>
                <th colspan="1" rowspan="1" >Ποσοστό</th>
                <th colspan="1" rowspan="1" >Νέο Ποσοστό (Χωρίς το %)</th>
                </tr>
                </tfoot>
              </table>

            </div>
            <!-- /.box-body -->
          </div>

</div>