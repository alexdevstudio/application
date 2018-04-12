
<br>
<div class="sections col-sm-12 ">
  <div class="box box-primary">

    <div class="box-body">
      <table id="categories" class="table table-bordered table-striped">
        <thead>
          <tr>
            <td>Όνομα</td>
            <td>Σύνολο πρϊόντων στη βάση</td>
            <td><i class="fa fa-circle text-green"></i> Στο Site Online </td>
            <td>Ενέργειες</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($product_count as $key => $value) : ?>
            <tr>
              <td><?= $key ?></td>
              <td><?= $value ?></td>
              <?php $next_object = 'live_'.$key; ?>
              <td><?= $product_count->$next_object ?></td>
              <td class="text-center"><a href="<?= base_url('categories/products/'.$key) ?>" class='btn btn-default '>Προβολή Προϊόντων</a> </td>

            </tr>
            <?php unset($product_count->$next_object); ?>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <td>Όνομα</td>
          <td>Σύνολο πρϊόντων στη βάση</td>
          <td>Σύνολο πρϊόντων στο ETD.gr</td>
          <td>Ενέργειες</td>
        </tfoot>
      </table>
    </div>

</div>
</div>
<script>
  $(function () {
    $('#categories').DataTable({
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
