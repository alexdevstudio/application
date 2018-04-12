  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery UI 1.11.4 -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" href="<?= base_url()?>/assets/css/bootstrap-colorpicker.min.css">

<script src="<?= base_url() ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?= base_url() ?>assets/bower_components/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?= base_url() ?>assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url() ?>assets/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url() ?>assets/bower_components/moment/min/moment.min.js"></script>
<script src="<?= base_url() ?>assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?= base_url() ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?= base_url() ?>assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= base_url() ?>assets/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url() ?>assets/dist/js/demo.js"></script>


<script src="<?= base_url()?>assets/js/etd.js"></script>
<script src="<?= base_url()?>assets/js/bootstrap-colorpicker.min.js"></script>
<script src="<?= base_url()?>assets/js/lazy.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.2.0/dropzone.css" />
<script src="<?= base_url()?>assets/js/dropzone.js"></script>
<?php if($this->uri->segment(1) == 'edit'): ?>
  <script type="text/javascript">
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#my-dropzone", {
      url: "<?= base_url("images/upload/".$this->uri->segment(2)."/".$this->uri->segment(3)); ?>"
    })

    $(function() {
        $(".radio").on('change', function(){
          var sku = '<?php echo $sku; ?>';
          var imageId =  $(this).attr('id');
          $.post("<?= base_url('images/default'); ?>", {sku: sku, id: imageId}, function(result){
              if(result === 'success'){
                  $('.fa-star').removeClass('fa-star').addClass('fa-star-o');
                  $('#'+imageId+' i.fa-star-o').removeClass('fa-star-o').addClass('fa-star')
                  setTimeout(function(){
                    alert ('Η προεπιλεγμένη φωτογραφία άλλαξε!');
                  },500);
              }else
              alert('Παρουσιάστηκε σφάλμα!');
          });
        });
    });

  </script>


<?php endif; ?>

 <script type="text/javascript">
$(function() {
    $("img.lazyimg").lazyload();
});

        </script>
</body>
</html>
