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
                        <div class="col-xs-12">
                          <table class="table table-striped" id="table">
                            <thead>
                              <tr>
                                <th colspan="2" class="text-green text-center" style="border-right: 1px solid #cdd0d4;">SKU που θα μείνει</th>
                                <th colspan="2" class="text-red text-center" style="border-right: 1px solid #cdd0d4;">SKU προς αφαίρεση</th>
                                <th>&nbsp;</th>
                              </tr>
                              <tr>
                                <th>SKU</th>
                                <th style="border-right: 1px solid #cdd0d4;">Product nr</th>
                                <th>SKU</th>
                                <th style="border-right: 1px solid #cdd0d4;">Product nr</th>
                                <th class="text-center"><i class="fa fa-times "></i></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($duplicates['sku_in']): ?>
                                <?php for ($i=0; $i < count($duplicates['sku_in']->result()); $i++) : ?>
                                  <tr>
                                    <td>
                                      <?= $duplicates['sku_in']->result()[$i]->sku_in ?>
                                    </td>
                                    <td style="border-right: 1px solid #cdd0d4;">
                                      <?= $duplicates['sku_in']->result()[$i]->product_number ?>
                                    </td>
                                    <td>
                                      <?= $duplicates['sku_out']->result()[$i]->sku_out ?>
                                    </td>
                                    <td style="border-right: 1px solid #cdd0d4;">
                                      <?= $duplicates['sku_out']->result()[$i]->product_number ?>
                                    </td>
                                    <td class="text-center">
                                    <a href="duplicates/delete/<?= $duplicates['sku_in']->result()[$i]->sku_in.'/'.$duplicates['sku_out']->result()[$i]->sku_out ?>"><i class="fa fa-times text-red"></i></a>
                                    </td>
                                  </tr>
                                <?php  endfor; ?>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
                <script>
                  $(function () {
                    $('#table').DataTable({
                      'paging'      : true,
                      'lengthChange': true,
                      'searching'   : true,
                      'ordering'    : true,
                      'info'        : true,
                      'autoWidth'   : false,
                      "pageLength": 25
                    })
                  })
                </script>
