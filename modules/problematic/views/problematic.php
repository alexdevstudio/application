<div class='col-xs-12'>
  <hr />

<div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
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
             
                <?php 
                $i = 1;
                if($errors && is_array($errors)){
                  ?>
                   <table  id="msiTable" class="table table-bordered table-hover dataTable">
                <thead>
                <tr role="row">
                <th  colspan="1" rowspan="1" >SKU</th>
                <th colspan="1" rowspan="1" >Κατηγορία</th>
                <th colspan="1" rowspan="1"  >Τίτλος</th>
                <th colspan="1" rowspan="1"  >Πρόβλημα</th>
                

                </tr>
                </thead>
                <tbody>
                <?php
                  foreach ($errors as $error) {
                    if($i>1){
                      $class="even";
                      $i=2;
                    }else{
                      $class="odd";
                      $i=1;
                    }
                   // print_r($error);
                    ?>

              <tr class=" <?=  $class; ?> " role="row">
                  <td><a href="<?= base_url().'edit/'.$error['category'].'/'.$error['sku'];?>"><?= $error['sku'];?></a></td>
                  <td><a href="<?= base_url().'edit/'.$error['category'].'/'.$error['sku'];?>"><?= $error['category'];?></a></td>
                  <td><a href="<?= base_url().'edit/'.$error['category'].'/'.$error['sku'];?>"><?= $error['title'];?></a></td>
                  <td><?= $error['type'];?></td>
                  
                 
                </tr>

                    <?php
                  }
                  ?>
 </tbody>
                <tfoot>
                <tr role="row">
                <th  colspan="1" rowspan="1" >SKU</th>
                <th colspan="1" rowspan="1" >Κατηγορία</th>
                <th colspan="1" rowspan="1"  >Τίτλος</th>
                <th colspan="1" rowspan="1"  >Πρόβλημα</th>

                </tr>
                </tfoot>
              </table>

                  <?php
                }else{
                  echo "<h3>Δεν υπάρχουν προβληματικά προϊόντα</h3>";
                  }
                 ?>
                
                

             

            </div>
            <!-- /.box-body -->
          </div>

</div>