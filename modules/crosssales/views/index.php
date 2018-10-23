<br>
  <div class="col-xs-12">
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

<div class="col-md-4">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Προσθήκη νέου φίλτρου για παράλληλα προϊόντα</h3>
            </div>
            <!-- /.box-header -->
            <form class="" action="crosssales" method="post">
                <div class="box-body">
                  <div class="form-group">
                    <label for="category" class="">Επιλογή κατηγορίας</label>

                    <select id="category" class="form-control" name="category">
                      <option value=''>Κατηγορίες</option>
                      <?php   foreach($tables as $table)  :  ?>
                        <option value='<?= $table->woo_category_id; ?>'><?= ucfirst($table->category_name); ?></option>
                      <?php endforeach;  ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="filter" >Τμήμα τίτλου</label>
                    <input type="text" name="filter" class="form-control" id="filter" value="<?= set_value('filter') ?>" placeholder="Τμήμα τίτλου...( DELL Inspiron 5)..." >
                  </div>
                  <div class="form-group">
                    <label for="skus" >SKU των προϊόντων (χωρισμένα με κόμμα)</label>
                    <input type="text" name="skus" class="form-control" id="skus" value="<?= set_value('skus') ?>" placeholder="13254848, 1315487, 1328751" >
                  </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                <button type="submit" class="btn pull-right btn-success">Προσθήκη</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>

        <div class="col-md-8">
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Ενεργά φίλτρα για παράλληλες πωλήσεις</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                          <div class="col-xs-12">
                            <table class="table table-striped" id="table">
                            <thead>
                            <tr>
                              <th>Κατηγορία</th>
                              <th>Τμήμα τίτλου</th>
                              <th>SKUs</th>
                              <th><i class="fa fa-times "></i></th>
                              <th>Ενημέρωση</th>
                            </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($filters->result() as $filter): ?>
                                <tr>
                                  <td><?= ucfirst( $filter->category_name ) ?></td>
                                  <td><?= $filter->filter ?></td>
                                  <td><?= $filter->skus ?></td>
                                  <td><a href="crosssales/delete/<?= $filter->cross_sells_similar_id ?>"><i class="fa fa-times text-red"></i></a></td>
                                  <td><a href="#" class="btn btn-warning" onclick="alert('Η βάση διαγράφθηκε. Δεν υπάρχει δυνατότητα αποκατάστασης.')">Ενημέρωση</a></td>
                                </tr>
                              <?php endforeach; ?>

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
