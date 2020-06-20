<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-mpseller').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1>Registration Pending Approval</h1>
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
        <!--<div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-store-owner"><?php echo $entry_store_owner; ?></label>
                <input type="text" name="filter_store_owner" value="<?php echo $filter_store_owner; ?>" placeholder="<?php echo $entry_store_owner; ?>" id="input-store-owner" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-store-name"><?php echo $entry_store_name; ?></label>
                <input type="text" name="filter_store_name" value="<?php echo $filter_store_name; ?>" placeholder="<?php echo $entry_store_name; ?>" id="input-store-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-approved"><?php echo $entry_approved; ?></label>
                <select name="filter_approved" id="input-approved" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_approved) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <?php } ?>
                  <?php if (!$filter_approved && !is_null($filter_approved)) { ?>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_no; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>-->
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-mpseller">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'mps.store_owner') { ?>
                    <a href="<?php echo $sort_store_owner; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_store_owner; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_store_owner; ?>"><?php echo $column_store_owner; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'mps.store_name') { ?>
                    <a href="<?php echo $sort_store_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_store_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_store_name; ?>"><?php echo $column_store_name; ?></a>
                    <?php } ?></td>
                   <td class="text-center"><?php if ($sort == 'total_products') { ?>
                    <a href="<?php echo $sort_products; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_products; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_products; ?>"><?php echo $column_products; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($mpsellers) { ?>
                <?php foreach ($mpsellers as $mpseller) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($mpseller['mpseller_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $mpseller['mpseller_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $mpseller['mpseller_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $mpseller['store_owner']; ?></td>
                  <td class="text-left"><?php echo $mpseller['store_name']; ?></td>
                  <td class="text-center"><?php if ($mpseller['total_products'] <= 0) { ?>
                    <span class="label label-warning" style="font-size: 11px;"><?php echo $mpseller['total_products']; ?></span>
                    <?php } else { ?>
                    <span class="label label-success" style="font-size: 11px;"><?php echo $mpseller['total_products']; ?></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $mpseller['email']; ?></td>
                  <td class="text-left"><?php echo $mpseller['status']; ?></td>
                  <td class="text-left"><?php echo $mpseller['date_added']; ?></td>
                  <td class="text-right">
                    <a href="#" data-toggle="tooltip" title="<?php echo $button_message; ?>" class="btn btn-primary"><i class="fa fa-paper-plane"></i></a>

                    <!--<?php if ($mpseller['approve']) { ?>
                    <a href="<?php echo $mpseller['approve']; ?>" data-toggle="tooltip" title="<?php echo $button_approve; ?>" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i></a>
                    <?php } else { ?>
                    <button type="button" class="btn btn-success" disabled><i class="fa fa-thumbs-o-up"></i></button>
                    <?php } ?>

                    <div class="btn-group" data-toggle="tooltip" title="<?php echo $button_login; ?>">
                      <button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="fa fa-lock"></i></button>
                      <ul class="dropdown-menu pull-right">
                        <li><a href="index.php?route=customer/customer/login&user_token=<?php echo $user_token; ?>&customer_id=<?php echo $mpseller['customer_id']; ?>&store_id=0" target="_blank"><?php echo $text_default; ?></a></li>
                        <?php foreach ($stores as $store) { ?>
                        <li><a href="index.php?route=customer/customer/login&user_token=<?php echo $user_token; ?>&customer_id=<?php echo $mpseller['customer_id']; ?>&store_id=<?php echo $store['store_id']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>

                    <a href="<?php echo $mpseller['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>-->
                  </td>
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
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=mpmultivendor/mpseller&user_token=<?php echo $user_token; ?>';

	var filter_store_owner = $('input[name=\'filter_store_owner\']').val();
	if (filter_store_owner) {
		url += '&filter_store_owner=' + encodeURIComponent(filter_store_owner);
	}

	var filter_store_name = $('input[name=\'filter_store_name\']').val();
	if (filter_store_name) {
		url += '&filter_store_name=' + encodeURIComponent(filter_store_name);
	}

	var filter_email = $('input[name=\'filter_email\']').val();
	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}

	var filter_status = $('select[name=\'filter_status\']').val();
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	var filter_approved = $('select[name=\'filter_approved\']').val();
	if (filter_approved != '*') {
		url += '&filter_approved=' + encodeURIComponent(filter_approved);
	}

	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	location = url;
});
//--></script>
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

$('input[name=\'filter_store_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=mpmultivendor/mpseller/autocomplete&user_token=<?php echo $user_token; ?>&filter_store_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['store_name'],
						value: item['mpseller_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_store_name\']').val(item['label']);
	}
});

$('input[name=\'filter_email\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=mpmultivendor/mpseller/autocomplete&user_token=<?php echo $user_token; ?>&filter_email=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['email'],
						value: item['mpseller_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_email\']').val(item['label']);
	}
});
//--></script>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>
