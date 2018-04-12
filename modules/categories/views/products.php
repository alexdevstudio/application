
<br>
<div class="sections col-sm-12 ">
  <div class="box box-primary">

    <div class="box-body">
      <table id="categories" class="table table-bordered table-striped">
        <thead>
          <tr>
            <td><i class="fa fa-circle"></td>
            <td>SKU</td>
            <td>Product number</td>
            <td>Title</td>
            <td>Ενέργειες</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product) : ?>
            <tr>
              <td class="text-center"><?= ($product->status == 'publish') ? '<i class="fa fa-circle text-green"><br>On' : '<i class="fa fa-circle text-red"><br> Off' ?></td>
              <td><?= $product->sku ?></td>
              <td><?= $product->product_number ?></td>
              <td><?= $product->title ?></td>
              <td class="text-center"><a href="<?= base_url('edit/'.$table.'/'.$product->sku) ?>" class='btn btn-default '>Επεξεργασία</a> </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <td><i class="fa fa-circle"></td>
          <td>SKU</td>
          <td>Product number</td>
          <td>Title</td>
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
      'autoWidth'   : true,
      "pageLength": 25
    })
  })
</script>
