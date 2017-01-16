<div class='col-xs-12'>
  <hr />

<div class="box">
            <div class="box-header">
              <h3 class="box-title">MSI Laptops</h3>
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
                <th colspan="1" rowspan="1" >Title</th>
                <th colspan="1" rowspan="1"  >PN</th>
                <th colspan="1" rowspan="1"  >Price</th>
                <th colspan="1" rowspan="1"  >Προτεινόμενη Τιμή</th>
                <th colspan="1" rowspan="1"  >Τελευταία ενημέρωση</th>

                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;

                foreach ($laptops->result() as $item) {
                  
                  $class = ' odd ';
                  if ($i % 2 == 0) {
            $class = ' even ';
          }
          $i++;
          $sku = $item->sku;
          $title = $item->title;
          $pn = $item->product_number;
          $price = $item->price;
          $date = $item->updated;

          $date = strtotime( $date );
          $date = date( 'd-m-Y', $date );

          ?>

        <tr class="<?= $class; ?>" role="row">
                  <td><?= $sku; ?></td>
                  <td><?= $title; ?></td>
                  <td><?= $pn; ?></td>
                  <td id="newPrice<?= $sku; ?>"><?= $price; ?></td>
                  <td>
                  <form id="sku<?= $sku; ?>" action="./updatePrice">
                  <input type='hidden' value='<?= $sku; ?>' name='sku' id='sku' />
                  
                  <div class="input-group col-xs-6 pull-left">
                      <div class="input-group-addon">
                        <i class="fa fa-euro"></i>
                      </div>
                      <input class="form-control " type='text'  id='price<?= $sku; ?>' name='price<?= $sku; ?>' />
                  </div>
                  
                   <button id="" type="button" class="btn btn-success btn-md col-xs-offset-1 col-xs-5" onclick="msiPrice('sku<?= $sku; ?>', '<?= $sku; ?>', 'price<?= $sku; ?>');" >Αλλαγή</button>

                   </form> 
                  </td>
                 <td class='text-center'><?= $date; ?></td>
                </tr>


      <?php

                }
    ?>
                

              </tbody>
                <tfoot>
                <tr role="row">
                <th  colspan="1" rowspan="1" >SKU</th>
                <th colspan="1" rowspan="1" >Title</th>
                <th colspan="1" rowspan="1"  >PN</th>
                <th colspan="1" rowspan="1"  >Price</th>
                <th colspan="1" rowspan="1"  >Προτεινόμενη Τιμή</th>
                <th colspan="1" rowspan="1"  >Τελευταία ενημέρωση</th>
                </tr>
                </tfoot>
              </table>

            </div>
            <!-- /.box-body -->
          </div>

</div>