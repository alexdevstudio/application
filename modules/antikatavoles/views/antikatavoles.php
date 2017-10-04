
<div class='col-xs-12'>
	<hr />
<?php
   			 //flash messages
			    if($this->session->flashdata('flash_message')){

			      echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
			      echo '<a class="close" data-dismiss="alert">&times</a>';
			      echo $this->session->flashdata('flash_message')['Message'];   
			      echo '</div>'; 
			    }
			?>
</div>
<div class='col-xs-6'>
<div class="box">
            
            <!-- /.box-header -->
            <div class="box-body">
            <h3>Δωρεάν Στην Αττική</h3>
              <div class="dataTables_wrapper form-inline dt-bootstrap" id="example2_wrapper">
              
              <div class="row">
              <div class="col-sm-12">
              <table  id="profitTable" class="table table-bordered table-hover dataTable">
               
                <tbody>
                <?php
                $i = 1;
                $excludedArr = [];
                foreach ($categories as $category) {
                	if($excluded = Modules::run('crud/get','charge_antikatavoli_cats', ['category_name'=>$category])){
                		$excludedArr[] = $category;
        				continue;
        				

        			}else{
        				/*$excluded = 'include';
        				$class = ' btn-danger ';
        				$text = 'Ενεργοποίηση δωρεάν αντικαταβολής';*/
        				$excluded = 'exclude';
        				$class = ' btn-success ';
        				$text = 'Να μην ισχύει δωρεάν αντικταβολή';
        				
        			}


                	
                	if ($i % 2 == 0) {
					           $class .= ' even ';
					         }else{
					         	$class .= ' odd ';
					         }

        					$i++;
        					/*$category = $item->category;
        					$rate = $item->rate;
                  $rate = $rate*100;
        					$description = $item->description;*/

        			
                  

                /*  if($rate !=6)
                    $class_color='bg-orange-light';*/
					         ?>

                  <tr class="" role="row">
                    
                    <td width="50%"><?= ucfirst ( $category); ?></td>
                    <td>
                      <form id="antikatavoli_<?= $category; ?>" method="post" action="antikatavoles/<?= $excluded; ?>">
                        <input type='hidden' value='<?= $category; ?>' name='category_name'  />
                        
                        <button id="" type="submit" class="btn <?= $class ?> btn-sm col-xs-offset-1 "  ><?= $text; ?></button>
                      </form> 
                    </td>
                  </tr>
			         <?php

                }
		            ?>
                
              </tbody>
                <tfoot>
                <tr role="row">
                 <th colspan="1" rowspan="1" >Όνομα Κατηγορίας</th>
                <th colspan="1" rowspan="1" >Εξαίρεση από τη δωρεάν αντικαταβολή;
</th>
                </tr>
                </tfoot>
              </table>

            </div>
            <!-- /.box-body -->
          </div>

</div>
</div>
</div>
</div>

<div class='col-xs-6'>
<div class="box">
       
            <!-- /.box-header -->
            <div class="box-body">
            <h3>Πάντα Με Χρέωση</h3>
              <div class="dataTables_wrapper form-inline dt-bootstrap" id="example2_wrapper">
              
              <div class="row">
              <div class="col-sm-12">
              <table  id="profitTable" class="table table-bordered table-hover dataTable">
                
                <tbody>
                <?php
                $i = 1;
                if(empty($excludedArr))
                	echo "<p class='text-center'>Δεν υπάρχουν εξαιρέσεις</p>";
                foreach ($excludedArr as $category) {
                	
        				$excluded = 'include';
        				$class = ' btn-danger ';
        				$text = 'Αφαίρεση από τις εξαιρέσεις';
        				
        			


                	
                	if ($i % 2 == 0) {
					           $class .= ' even ';
					         }else{
					         	$class .= ' odd ';
					         }

        					$i++;
        					/*$category = $item->category;
        					$rate = $item->rate;
                  $rate = $rate*100;
        					$description = $item->description;*/

        			
                  

                /*  if($rate !=6)
                    $class_color='bg-orange-light';*/
					         ?>

                  <tr class="" role="row">
                    
                    <td width="50%"><?= ucfirst ( $category); ?></td>
                    <td>
                      <form id="antikatavoli_<?= $category; ?>" method="post" action="antikatavoles/<?= $excluded; ?>">
                        <input type='hidden' value='<?= $category; ?>' name='category_name'  />
                        
                        <button id="" type="submit" class="btn <?= $class ?> btn-sm col-xs-offset-1 "  ><?= $text; ?></button>
                      </form> 
                    </td>
                  </tr>
			         <?php

                }
		            ?>
                
              </tbody>
                
              </table>

            </div>
            <!-- /.box-body -->
          </div>

</div>