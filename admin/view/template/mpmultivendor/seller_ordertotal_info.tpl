<div id="Mpseller-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
          <?php echo $text_seller_total_info; ?>
          <hr/>
          <div><small><b><?php echo $text_store_name; ?>: <?php echo $store_name; ?></b></small></div>
          <div><small><b><?php echo $text_store_owner; ?>: <?php echo $store_owner; ?></b></small></div>
          <div><small><b><?php echo $text_email; ?>: <?php echo $email; ?></b></small></div>
        </h4>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <td class="text-left"><?php echo $column_product; ?></td>
              <td class="text-left"><?php echo $column_mpseller_order_status; ?></td>
              <td class="text-left"><?php echo $column_model; ?></td>
              <td class="text-right"><?php echo $column_quantity; ?></td>
              <td class="text-right"><?php echo $column_price; ?></td>
              <td class="text-right"><?php echo $column_total; ?></td>
            </tr>
          </thead>
            <tbody>
              <?php foreach ($mpseller_order_products as $product) { ?>
              <tr>
                <td class="text-left"><?php echo $product['name']; ?></td>
                <td class="text-left"><?php echo $product['order_status']; ?></td>
                <td class="text-left"><?php echo $product['model']; ?></td>
                <td class="text-right"><?php echo $product['quantity']; ?></td>
                <td class="text-right"><?php echo $product['price']; ?></td>
                <td class="text-right"><?php echo $product['total']; ?></td>
              </tr>
              <?php } ?>
              <?php foreach ($totals as $total) { ?>
              <tr>
                <td colspan="5" class="text-right"><?php echo $total['title']; ?></td>
                <td class="text-right"><?php echo $total['text']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#Mpseller-modal').on('hidden.bs.modal', function() {
  $(this).remove();
});
</script>