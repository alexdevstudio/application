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
              <table  id="msiTable" class="table table-bordered table-hover dataTable">
                <thead>
                <tr role="row">
                <th  colspan="1" rowspan="1" >SKU</th>
                <th colspan="1" rowspan="1" >Κατηγορία</th>
                <th colspan="1" rowspan="1"  >Τίτλος</th>
                <th colspan="1" rowspan="1"  >Επεξεργασία</th>
                

                </tr>
                </thead>
                <tbody>
                <?php 
                $i = 1;
                if($errors){
                  foreach ($errors as $error) {
                   foreach ($error as $key => $value) {
                    if($i>1){
                      $class="even";
                    }else{
                      $class="odd";
                    }
                    //print_r($item);
                    ?>

              <tr class=" <?=  $class; ?> " role="row">
                  <td><?= $key['sku'];?></td>
                  <td><?= $key['sku'];?></td>
                  <td><?= $key['sku'];?></td>
                  <td><?= $key['sku'];?></td>
                  
                 
                </tr>

                    <?php
                  }





                  }
                }
                 ?>
                

              </tbody>
                <tfoot>
                <tr role="row">
                <th  colspan="1" rowspan="1" >SKU</th>
                <th colspan="1" rowspan="1" >Κατηγορία</th>
                <th colspan="1" rowspan="1"  >Τίτλος</th>
                <th colspan="1" rowspan="1"  >Επεξεργασία</th>
                </tr>
                </tfoot>
              </table>

            </div>
            <!-- /.box-body -->
          </div>

</div>