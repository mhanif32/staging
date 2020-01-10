<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-review').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-store-owner"><?php echo $entry_store_owner; ?></label>
                <input type="text" name="filter_store_owner" value="<?php echo $filter_store_owner; ?>" placeholder="<?php echo $entry_store_owner; ?>" id="input-store-owner" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-author"><?php echo $entry_author; ?></label>
                <input type="text" name="filter_author" value="<?php echo $filter_author; ?>" placeholder="<?php echo $entry_author; ?>" id="input-author" class="form-control" />
              </div>
            </div>
            <div class="col-sm-6">
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
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-review">
          <?php if ($reviews) { ?>
            <?php foreach ($reviews as $review) { ?>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <div class="row">
                    <div class="col-sm-6">
                      <?php if (in_array($review['mpseller_review_id'], $selected)) { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $review['mpseller_review_id']; ?>" checked="checked" />
                      <?php } else { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $review['mpseller_review_id']; ?>" />
                      <?php } ?>
                      <span class="rating">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <?php if ($review['rating'] < $i) { ?>
                        <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } else { ?>
                        <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                        <?php } ?>
                        <?php } ?>
                      </span>
                      <span class="review-title"> <?php echo $review['title']; ?></span>
                    </div>
                    <div class="col-sm-6 pull-right">
                      <span class="cust">by <b><?php echo $review['author']; ?></b></span>
                      <span class="date">on <b><?php echo $review['date_added']; ?></b></span>
                      <div class="review-btns">
                        <?php if($review['status_int']) { ?>
                        <label class="btn btn-danger"><?php echo $review['status']; ?></label>
                        <?php } else { ?>
                        <label class="btn btn-success"><?php echo $review['status']; ?></label>
                        <?php } ?>
                        <a href="<?php echo $review['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel-body">
                  <?php echo $review['description']; ?>
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
$('#button-filter').on('click', function() {
	url = 'index.php?route=mpmultivendor/review&user_token=<?php echo $user_token; ?>';
	
	var filter_store_owner = $('input[name=\'filter_store_owner\']').val();
	
	if (filter_store_owner) {
		url += '&filter_store_owner=' + encodeURIComponent(filter_store_owner);
	}
	
	var filter_author = $('input[name=\'filter_author\']').val();
	
	if (filter_author) {
		url += '&filter_author=' + encodeURIComponent(filter_author);
	}
	
	var filter_status = $('select[name=\'filter_status\']').val();
	
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status); 
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
//--></script> 
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>