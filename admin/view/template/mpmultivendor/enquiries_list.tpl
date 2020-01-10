<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-enquiry').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
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
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-customer-name"><?php echo $entry_customer_name; ?></label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer_name; ?>" id="input-customer-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-customer-email"><?php echo $entry_customer_email; ?></label>
                <input type="text" name="filter_customer_email" value="<?php echo $filter_customer_email; ?>" placeholder="<?php echo $entry_customer_email; ?>" id="input-customer-email" class="form-control" />
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-enquiry">
          <?php if ($enquiries) { ?>
            <?php foreach ($enquiries as $enquiry) { ?>
              <div class="panel panel-default clearfix">
                <div class="panel-seller">
                  <label><input type="checkbox" name="selected[]" value="<?php echo $enquiry['mpseller_enquiry_id']; ?>" /> <?php echo $column_store_owner; ?></label>
                  <h5><?php echo $enquiry['store_owner']; ?></h5>
                </div>
                <div class="panel-inner">
                  <table class="table table-bordered" style="margin-bottom: 0;">
                    <tr>
                      <th><?php echo $column_customer_name; ?></th>
                      <th><?php echo $column_customer_email; ?></th>
                      <th><?php echo $column_date_added; ?></th>
                      <th><?php echo $column_date_modified; ?></th>
                    </tr>
                    <tr>
                      <td><?php echo $enquiry['name']; ?></td>
                      <td><?php echo $enquiry['email']; ?></td>
                      <td><?php echo $enquiry['date_added']; ?></td>
                      <td><?php echo $enquiry['date_modified']; ?></td>
                      <td class="text-right"><a href="<?php echo $enquiry['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn-sm btn-primary button-info button-info<?php echo $enquiry['mpseller_enquiry_id']; ?>"><i class="fa fa-eye"></i></a></td>
                    </tr>
                  </table>
                  <div class="panel-body"><?php echo $enquiry['message']; ?></div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <?php echo $text_no_results; ?>
          <?php } ?>
        </form>
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
      url: 'index.php?route=mpmultivendor/mpseller/autocomplete&token=<?php echo $token; ?>&filter_store_owner=' +  encodeURIComponent(request),
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

$('input[name=\'filter_customer\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['customer_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter_customer\']').val(item['label']);
  }
});

$('input[name=\'filter_customer_email\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['email'],
            value: item['customer_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter_customer_email\']').val(item['label']);
  }
});
//--></script>
<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
  var url = 'index.php?route=mpmultivendor/enquiries&token=<?php echo $token; ?>';

  var filter_store_owner = $('input[name=\'filter_store_owner\']').val();
  if (filter_store_owner) {
    url += '&filter_store_owner=' + encodeURIComponent(filter_store_owner);
  }

  var filter_customer = $('input[name=\'filter_customer\']').val();
  if (filter_customer) {
    url += '&filter_customer=' + encodeURIComponent(filter_customer);
  }

  var filter_customer_email = $('input[name=\'filter_customer_email\']').val();
  if (filter_customer_email) {
    url += '&filter_customer_email=' + encodeURIComponent(filter_customer_email);
  }


  var filter_date_added = $('input[name=\'filter_date_added\']').val();
  if (filter_date_added) {
    url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
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