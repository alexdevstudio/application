<br>
<div class="col-md-4">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Προσθήκη νέου διπλού προϊόντος</h3>
            </div>
            <!-- /.box-header -->
            <form class="" action="duplicates" method="post">
                <div class="box-body">
                  <div class="form-group">
                    <label for="sku1" class="text-green">SKU που θα μείνει</label>
                    <input type="number" name="sku_in" class="form-control" id="sku1" placeholder="Εισάγετε το SKU που θα παραμείνει στο site" >
                  </div>
                  <div class="form-group">
                    <label for="sku2" class="text-red">SKU προς αφαίρεση</label>
                    <input type="number" name="sku_out" class="form-control" id="sku2" placeholder="Εισάγετε το SKU προς αφαίρεση από το site" >
                  </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                <button type="submit" class="btn pull-right btn-danger">Προσθήκη</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>

        <div class="col-md-8">
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Διπλά Προϊόντα</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6 text-green">SKU που θα μείνει</div>
                            <div class="col-sm-6 text-red">SKU προς αφαίρεση</div>
            
                            <hr>
                        </div>
                      <?php if ($duplicates['sku_in']): ?>

                        <?php for ($i=0; $i < count($duplicates['sku_in']->result()); $i++) : ?>
                          <div class="row border-bt">
                            <div class="col-sm-6">

                              <?= $duplicates['sku_in']->result()[$i]->sku_in.'<br/>'.$duplicates['sku_in']->result()[$i]->product_number ?>
                            </div>
                            <div class="col-sm-5">
                              <?= $duplicates['sku_out']->result()[$i]->sku_out.'<br/>'.$duplicates['sku_out']->result()[$i]->product_number ?>
                            </div>
                            <div class="col-sm-1">
                              <a href="duplicates/delete/<?= $duplicates['sku_in']->result()[$i]->sku_in.'/'.$duplicates['sku_out']->result()[$i]->sku_out ?>"><i class="fa fa-times text-red"></i></a>
                            </div>
                          </div>
                        <?php  endfor; ?>
                      <?php endif; ?>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
