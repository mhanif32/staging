<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_date_added; ?></td>
        <td class="text-right"><?php echo $column_amount; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php if ($transactions) { ?>
      <?php foreach ($transactions as $transaction) { ?>
      <tr>
        <td class="text-left"><?php echo $transaction['date_added']; ?></td>
        <td class="text-right"><?php echo $transaction['amount']; ?></td>
      </tr>
      <?php } ?>
      <tr>
        <td class="text-right"><b><?php echo $text_paid; ?></b></td>
        <td class="text-right"><label class="label label-success" style="font-size: 14px;"><?php echo $total_paid; ?></label></td>
      </tr>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="2"><?php echo $text_no_results; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
