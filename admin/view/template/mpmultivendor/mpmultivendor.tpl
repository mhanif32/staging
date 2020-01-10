<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" name="savetype" value="savechanges" form="form-mpmultivendor" data-toggle="tooltip" title="<?php echo $button_savechanges; ?>" class="btn btn-success"><i class="fa fa-refresh"></i> <?php echo $button_savechanges; ?></button>
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
        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $text_edit; ?></h3>
        <div class="pull-right">
          <span><?php echo $text_store; ?></span>
          <button type="button" data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle"><span><?php echo $store_name; ?> &nbsp; &nbsp; </span> <i class="fa fa-angle-down"></i></button>
          <ul class="dropdown-menu pull-right">
            <li><a href="index.php?route=mpmultivendor/mpmultivendor&user_token=<?php echo $user_token; ?>&store_id=0"><?php echo $text_default; ?></a></li>
            <?php foreach($stores as $store) { ?>
            <li><a href="index.php?route=mpmultivendor/mpmultivendor&user_token=<?php echo $user_token; ?>&store_id=<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="panel-body">
        <div class="bs-callout bs-callout-info"> 
          <h4>MODULEPOINTS - MULTI-VENDOR / MULTI-SELLER MARKETPLACE </h4> 
          <p><center><strong>MP - MULTI-VENDOR / MULTI-SELLER MARKETPLACE V-1.0 </strong></center> <br/> 
          Multivendor extension convert opencart store into Multple vendor/supplier/seller marketplace, extension is quite flexible in functionality. Each vendor/supplier/seller has separate profile, rating, features, products, feedback, etc. vendor/supplier/seller for opencart is best suited extension. Every vendor/supplier/seller can setup their own shipping rates based on their own/product locations and many more other features.
           </p> 
        </div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mpmultivendor" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo $tab_general; ?></a></li>
            <li><a href="#tab-itemsperpage" data-toggle="tab"><i class="fa fa-list" aria-hidden="true"></i> <?php echo $tab_itemsperpage; ?></a></li>
            <li><a href="#tab-info" data-toggle="tab"><i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo $tab_info; ?></a></li>
            <li><a href="#tab-image-size" data-toggle="tab"><i class="fa fa-image" aria-hidden="true"></i> <?php echo $tab_image_size; ?></a></li>
            <li><a href="#tab-support" data-toggle="tab"><i class="fa fa-thumbs-up" aria-hidden="true"></i> <?php echo $tab_modulepoints; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_status) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_status" value="1" <?php echo (!empty($mpmultivendor_status)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_enabled; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_status) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_status" value="0" <?php echo (empty($mpmultivendor_status)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_disabled; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_applyseller_page; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_applyseller_page) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_applyseller_page" value="1" <?php echo (!empty($mpmultivendor_applyseller_page)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_yes; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_applyseller_page) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_applyseller_page" value="0" <?php echo (empty($mpmultivendor_applyseller_page)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_no; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_received_commission_status; ?></label>
                <div class="col-sm-10">
                  <select name="mpmultivendor_received_commission_status_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $mpmultivendor_received_commission_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $help_received_commission_status; ?></div>
                </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_restrict_orderstatus; ?>"><?php echo $entry_restrict_orderstatus; ?></span></label>
                  <div class="col-sm-10">
                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <div class="checkbox">
                        <label>
                          <?php if (in_array($order_status['order_status_id'], $mpmultivendor_restrict_orderstatus)) { ?>
                          <input type="checkbox" name="mpmultivendor_restrict_orderstatus[]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
                          <?php echo $order_status['name']; ?>
                          <?php } else { ?>
                          <input type="checkbox" name="mpmultivendor_restrict_orderstatus[]" value="<?php echo $order_status['order_status_id']; ?>" />
                          <?php echo $order_status['name']; ?>
                          <?php } ?>
                        </label>
                      </div>
                      <?php } ?>
                    </div>
                    <?php if ($error_restrict_orderstatus) { ?>
                    <div class="text-danger"><?php echo $error_restrict_orderstatus; ?></div>
                    <?php } ?>
                  </div>
                </div>
              <div class="form-group required">
                <label class="control-label col-sm-2"><?php echo $entry_commission_rate; ?></label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <input type="text" class="form-control" name="mpmultivendor_commission_rate" value="<?php echo $mpmultivendor_commission_rate; ?>" placeholder="<?php echo $entry_commission_rate; ?>" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default">%</button>
                    </span>
                  </div>
                  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $help_commission_rate; ?></div>
                  <?php if($error_commission_rate) { ?>
                  <div class="text-danger"><?php echo $error_commission_rate; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_autoapproved_seller; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_autoapproved_seller) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_autoapproved_seller" value="1" <?php echo (!empty($mpmultivendor_autoapproved_seller)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_yes; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_autoapproved_seller) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_autoapproved_seller" value="0" <?php echo (empty($mpmultivendor_autoapproved_seller)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_no; ?>                            
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_changereview; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_changereview) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_changereview" value="1" <?php echo (!empty($mpmultivendor_seller_changereview)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_yes; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_changereview) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_changereview" value="0" <?php echo (empty($mpmultivendor_seller_changereview)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_no; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="tab-pane" id="tab-info">
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_name; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_name) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_name" value="1" <?php echo (!empty($mpmultivendor_seller_name)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_show; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_name) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_name" value="0" <?php echo (empty($mpmultivendor_seller_name)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_hide; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_email; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_email) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_email" value="1" <?php echo (!empty($mpmultivendor_seller_email)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_show; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_email) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_email" value="0" <?php echo (empty($mpmultivendor_seller_email)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_hide; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_telephone; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_telephone) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_telephone" value="1" <?php echo (!empty($mpmultivendor_seller_telephone)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_show; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_telephone) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_telephone" value="0" <?php echo (empty($mpmultivendor_seller_telephone)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_hide; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_address; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_address) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_address" value="1" <?php echo (!empty($mpmultivendor_seller_address)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_show; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_address) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_address" value="0" <?php echo (empty($mpmultivendor_seller_address)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_hide; ?>                            
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_seller_image; ?></label>
                <div class="col-sm-3">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary <?php echo !empty($mpmultivendor_seller_image) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_image" value="1" <?php echo (!empty($mpmultivendor_seller_image)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_show; ?>                            
                    </label>
                    <label class="btn btn-primary <?php echo empty($mpmultivendor_seller_image) ? 'active' : '';  ?>">
                      <input type="radio" name="mpmultivendor_seller_image" value="0" <?php echo (empty($mpmultivendor_seller_image)) ? 'checked="checked"' : '';  ?> />
                      <?php echo $text_hide; ?>                            
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-itemsperpage">
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_limit_seller; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="mpmultivendor_seller_list" value="<?php echo $mpmultivendor_seller_list; ?>" placeholder="<?php echo $entry_limit_seller; ?>"  class="form-control" />
                  <?php if ($error_limit_seller) { ?>
                  <div class="text-danger"><?php echo $error_limit_seller; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_limit_store; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="mpmultivendor_store_list" value="<?php echo $mpmultivendor_store_list; ?>" placeholder="<?php echo $entry_limit_store; ?>"  class="form-control" />
                  <?php if ($error_limit_store) { ?>
                  <div class="text-danger"><?php echo $error_limit_store; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_limit_store_product; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="mpmultivendor_store_list_product" value="<?php echo $mpmultivendor_store_list_product; ?>" placeholder="<?php echo $entry_limit_store_product; ?>"  class="form-control" />
                  <?php if ($error_limit_store_product) { ?>
                  <div class="text-danger"><?php echo $error_limit_store_product; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_limit_store_review; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="mpmultivendor_store_list_review" value="<?php echo $mpmultivendor_store_list_review; ?>" placeholder="<?php echo $entry_limit_store_review; ?>"  class="form-control" />
                  <?php if ($error_limit_store_review) { ?>
                  <div class="text-danger"><?php echo $error_limit_store_review; ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-image-size">
              <fieldset>
                <legend><?php echo $legend_seller_info; ?></legend>
                <div class="form-group required">
                  <label class="col-sm-2 control-label"><?php echo $entry_store_logo_size; ?></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_store_logo_width" value="<?php echo $mpmultivendor_store_logo_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_store_logo_height" value="<?php echo $mpmultivendor_store_logo_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <?php if ($error_store_logo_size) { ?>
                    <div class="text-danger"><?php echo $error_store_logo_size; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label"><?php echo $entry_main_banner_size; ?></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_main_banner_width" value="<?php echo $mpmultivendor_main_banner_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_main_banner_height" value="<?php echo $mpmultivendor_main_banner_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <?php if ($error_main_banner_size) { ?>
                    <div class="text-danger"><?php echo $error_main_banner_size; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label"><?php echo $entry_profile_image_size; ?></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_profile_image_width" value="<?php echo $mpmultivendor_profile_image_width; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_profile_image_height" value="<?php echo $mpmultivendor_profile_image_height; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <?php if ($error_profile_image_size) { ?>
                    <div class="text-danger"><?php echo $error_profile_image_size; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend><?php echo $legend_seller_listing; ?></legend>
                <div class="form-group required">
                  <label class="col-sm-2 control-label"><?php echo $entry_main_banner_size_listing; ?></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_main_banner_width_listing" value="<?php echo $mpmultivendor_main_banner_width_listing; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_main_banner_height_listing" value="<?php echo $mpmultivendor_main_banner_height_listing; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <?php if ($error_main_banner_size_listing) { ?>
                    <div class="text-danger"><?php echo $error_main_banner_size_listing; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label"><?php echo $entry_profile_image_size_listing; ?></label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_profile_image_width_listing" value="<?php echo $mpmultivendor_profile_image_width_listing; ?>" placeholder="<?php echo $entry_width; ?>" class="form-control" />
                      </div>
                      <div class="col-sm-6">
                        <input type="text" name="mpmultivendor_profile_image_height_listing" value="<?php echo $mpmultivendor_profile_image_height_listing; ?>" placeholder="<?php echo $entry_height; ?>" class="form-control" />
                      </div>
                    </div>
                    <?php if ($error_profile_image_size_listing) { ?>
                    <div class="text-danger"><?php echo $error_profile_image_size_listing; ?></div>
                    <?php } ?>
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="tab-pane" id="tab-support">
              <fieldset>
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Thanks For Choosing Our Extension</h4>
                     <ul class="list-group">
                      <li class="list-group-item clearfix">Installed Version <span class="badge"><i class="fa fa-gg" aria-hidden="true"></i> V.1.0</span></li>
                    </ul>
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-phone" aria-hidden="true"></i> Please Contact Us In Case Any Issue OR Give Feedback!</h4>
                    <ul class="list-group">
                      <li class="list-group-item clearfix">support@modulepoints.com <span class="badge"><a href="mailto:support@modulepoints.com?Subject=Request Support: Multi-Vendor"><i class="fa fa-envelope"></i> Contact Us</a></span></li> 
                    </ul>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php if(VERSION <= '2.2.0.0') { ?>
<?php foreach ($languages as $language) { ?>
<script type="text/javascript"><!--
  $('#input-message_register<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-message_logged<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-success-customer<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-success-guest<?php echo $language['language_id']; ?>').summernote({ height: 300 });
//--></script>
<?php } ?>
<?php } else { ?>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } ?>
</div>
<?php echo $footer; ?>