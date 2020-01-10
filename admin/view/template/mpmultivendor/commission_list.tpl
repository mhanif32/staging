<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-store-owner"><?php echo $entry_store_owner; ?></label>
                <input type="text" name="filter_store_owner" value="<?php echo $filter_store_owner; ?>" placeholder="<?php echo $entry_store_owner; ?>" id="input-store-owner" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <h3 class="text-right"><?php echo $text_final_commission; ?>: <b><?php echo $final_commission; ?></b></h3>
        <br>
        <div id="form-commission">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php if ($sort == 'store_owner') { ?>
                    <a href="<?php echo $sort_store_owner; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_store_owner; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_store_owner; ?>"><?php echo $column_store_owner; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'store_name') { ?>
                    <a href="<?php echo $sort_store_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_store_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_store_name; ?>"><?php echo $column_store_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'mc.order_id') { ?>
                    <a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order_id; ?>"><?php echo $column_order_id; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_product_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'price') { ?>
                    <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'quantity') { ?>
                    <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'total') { ?>
                    <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'mc.amount') { ?>
                    <a href="<?php echo $sort_amount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_amount; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_amount; ?>"><?php echo $column_amount; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'e.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($commissions) { ?>
                <?php foreach ($commissions as $commission) { ?>
                <tr>
                  <td class="text-left"><?php echo $commission['store_owner']; ?></td>
                  <td class="text-left"><?php echo $commission['store_name']; ?></td>
                  <td class="text-right"><?php echo $commission['order_id']; ?></td>
                  <td class="text-right"><?php echo $commission['name']; ?></td>
                  <td class="text-right"><?php echo $commission['price']; ?></td>
                  <td class="text-right"><?php echo $commission['quantity']; ?></td>
                  <td class="text-right"><?php echo $commission['total']; ?></td>
                  <td class="text-right"><?php echo $commission['amount']; ?></td>
                  <td class="text-right"><?php echo $commission['date_added']; ?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--  
$('input[name=\'filter_store_owner\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=mpmultivendor/mpseller/autocomplete&user_token=<?php echo $user_token; ?>&filter_store_owner=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['store_owner'],
            value: item['mpseller_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter_store_owner\']').val(item['label']);
  }
});
//--></script> 
<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
  var url = 'index.php?route=mpmultivendor/commission&user_token=<?php echo $user_token; ?>';

  var filter_store_owner = $('input[name=\'filter_store_owner\']').val();
  if (filter_store_owner) {
    url += '&filter_store_owner=' + encodeURIComponent(filter_store_owner);
  }
  
  var filter_date_start = $('input[name=\'filter_date_start\']').val();
  if (filter_date_start) {
    url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
  }

  var filter_date_end = $('input[name=\'filter_date_end\']').val();
  if (filter_date_end) {
    url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
  }

  location = url;
});
//--></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
  pickTime: false
});
//--></script>
</div>
<?php echo $footer; ?>