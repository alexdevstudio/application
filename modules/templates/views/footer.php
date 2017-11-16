  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery UI 1.11.4 -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" href="<?= base_url()?>/assets/css/bootstrap-colorpicker.min.css">


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?= base_url()?>assets/plugins/morris/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?= base_url()?>assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?= base_url()?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?= base_url()?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url()?>assets/plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?= base_url()?>assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?= base_url()?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?= base_url()?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?= base_url()?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= base_url()?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url()?>assets/dist/js/app.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes)
<script src="<?= base_url()?>assets/dist/js/pages/dashboard.js"></script>-->
<!-- AdminLTE for demo purposes -->
<script src="<?= base_url()?>assets/dist/js/demo.js"></script>
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
