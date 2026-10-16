<script type="text/javascript">
    $(function () {
      $('form').parsley().on('field:validated', function() {
      })
      .on('form:submit', function() {
        return false; // Don't submit form for this demo
      });
    });
</script>
