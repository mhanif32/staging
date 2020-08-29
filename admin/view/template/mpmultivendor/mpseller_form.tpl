<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-mpseller" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php if($mpseller_id) { ?>
                    <div class="col-sm-2">
                        <div class="seller-links text-left">
                            <a href="<?php echo $seller_products; ?>" target="_blank" class="btn btn-primary btn-block"><i
                                        class="fa fa-tag"></i> <?php echo $button_products; ?></a>
                            <a href="<?php echo $seller_enquiries; ?>" target="_blank"
                               class="btn btn-primary btn-block"><i
                                        class="fa fa-question-circle"></i> <?php echo $button_enquiries; ?></a>
                            <a href="<?php echo $seller_reviews; ?>" target="_blank"
                               class="btn btn-primary btn-block"><i
                                        class="fa fa-star"></i> <?php echo $button_reviews; ?></a>
                            <a href="<?php echo $seller_orders; ?>" target="_blank" class="btn btn-primary btn-block"><i
                                        class="fa fa-shopping-cart"></i> <?php echo $button_orders; ?></a>
                            <a href="<?php echo $seller_commission; ?>" target="_blank"
                               class="btn btn-primary btn-block"><i
                                        class="fa fa-money"></i> <?php echo $button_commission; ?></a>
                            <a href="<?php echo $seller_transaction; ?>" target="_blank"
                               class="btn btn-primary btn-block"><i
                                        class="fa fa-dollar"></i> <?php echo $button_transaction; ?></a>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-sm-10 right-part">
                        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"
                              id="form-mpseller" class="form-horizontal">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab-general"
                                                      data-toggle="tab"><?php echo $tab_general; ?></a></li>
                                <li><a href="#tab-store" data-toggle="tab"><?php echo $tab_store; ?></a></li>
                                <li><a href="#tab-local" data-toggle="tab"><?php echo $tab_local; ?></a></li>
                                <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
                                <li><a href="#tab-social" data-toggle="tab"><?php echo $tab_social_profiles; ?></a></li>
                                <li><a href="#tab-shipping" data-toggle="tab"><?php echo $tab_shipping; ?></a></li>
                                <li><a href="#tab-payment" data-toggle="tab"><?php echo $tab_payment; ?></a></li>
                                <li><a href="#tab-seo" data-toggle="tab"><?php echo $tab_seo; ?></a></li>
                                <li><a href="#tab-commission" data-toggle="tab"><?php echo $tab_commission; ?></a></li>
                                <li><a href="#tab-transaction" data-toggle="tab"><?php echo $tab_transaction; ?></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-general">
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_description; ?></label>
                                        <div class="col-sm-10">
                                            <textarea name="description" rows="5"
                                                      class="form-control"><?php echo $description; ?></textarea>
                                            <?php if ($error_description) { ?>
                                            <div class="text-danger"><?php echo $error_description; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_meta_description; ?></label>
                                        <div class="col-sm-10">
                                            <textarea name="meta_description" id="input-storemeta" rows="5"
                                                      class="form-control"><?php echo $meta_description; ?></textarea>
                                            <?php if ($error_meta_description) { ?>
                                            <div class="text-danger"><?php echo $error_meta_description; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_meta_keyword; ?></label>
                                        <div class="col-sm-10">
                                            <textarea name="meta_keyword" rows="5"
                                                      class="form-control"><?php echo $meta_keyword; ?></textarea>
                                            <?php if ($error_meta_keyword) { ?>
                                            <div class="text-danger"><?php echo $error_meta_keyword; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"
                                               for="input-status"><?php echo $entry_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="status" id="input-status" class="form-control">
                                                <?php if ($status) { ?>
                                                <option value="1"
                                                        selected="selected"><?php echo $text_enabled; ?></option>
                                                <option value="0"><?php echo $text_disabled; ?></option>
                                                <?php } else { ?>
                                                <option value="1"><?php echo $text_enabled; ?></option>
                                                <option value="0"
                                                        selected="selected"><?php echo $text_disabled; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"
                                               for="input-approved"><?php echo $entry_approved; ?></label>
                                        <div class="col-sm-10">
                                            <select name="approved" id="input-approved" class="form-control">
                                                <?php if ($approved) { ?>
                                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                                <option value="0"><?php echo $text_no; ?></option>
                                                <?php } else { ?>
                                                <option value="1"><?php echo $text_yes; ?></option>
                                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-approved_for_delivery">Approve
                                            for Delivery of Product?</label>
                                        <div class="col-sm-10">
                                            <select name="approved_for_delivery" id="input-approved_for_delivery"
                                                    class="form-control">
                                                <?php if ($approved_for_delivery) { ?>
                                                <option value="1" selected="selected">Enable</option>
                                                <option value="0">Disable</option>
                                                <?php } else { ?>
                                                <option value="1">Enable</option>
                                                <option value="0" selected="selected">Disable</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab-seo">
                                    <div class="alert alert-info"><i
                                                class="fa fa-info-circle"></i> <?php echo $text_keyword; ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <td class="text-left"><?php echo $entry_store; ?></td>
                                                <td class="text-left"><?php echo $entry_seo_keyword; ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($seo_stores as $store) { ?>
                                            <tr>
                                                <td class="text-left"><?php echo $store['name']; ?></td>
                                                <td class="text-left"><?php foreach($languages as $language) { ?>
                                                    <div class="input-group"><span class="input-group-addon"><img
                                                                    src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png"
                                                                    title="<?php echo $language['name']; ?>"/></span>
                                                        <input type="text"
                                                               name="seller_seo_url[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]"
                                                               value="<?php echo isset($seller_seo_url[$store['store_id']][$language['language_id']]) ? $seller_seo_url[$store['store_id']][$language['language_id']] : ''; ?>"
                                                               placeholder="<?php echo $entry_seo_keyword; ?>"
                                                               class="form-control"/>
                                                    </div>
                                                    <?php if(isset($error_seo_keyword[$store['store_id']][$language['language_id']])) { ?>
                                                    <div class="text-danger"><?php echo $error_seo_keyword[$store['store_id']][$language['language_id']]; ?></div>
                                                    <?php } ?>
                                                    <?php } ?></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <td class="text-left"><?php echo $entry_store; ?></td>
                                                <td class="text-left"><?php echo $entry_review_seo_keyword; ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($seo_stores as $store) { ?>
                                            <tr>
                                                <td class="text-left"><?php echo $store['name']; ?></td>
                                                <td class="text-left"><?php foreach($languages as $language) { ?>
                                                    <div class="input-group"><span class="input-group-addon"><img
                                                                    src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png"
                                                                    title="<?php echo $language['name']; ?>"/></span>
                                                        <input type="text"
                                                               name="review_seo_url[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]"
                                                               value="<?php echo isset($review_seo_url[$store['store_id']][$language['language_id']]) ? $review_seo_url[$store['store_id']][$language['language_id']] : ''; ?>"
                                                               placeholder="<?php echo $entry_review_seo_keyword; ?>"
                                                               class="form-control"/>
                                                    </div>
                                                    <?php if(isset($error_review_seo_keyword[$store['store_id']][$language['language_id']])) { ?>
                                                    <div class="text-danger"><?php echo $error_review_seo_keyword[$store['store_id']][$language['language_id']]; ?></div>
                                                    <?php } ?>
                                                    <?php } ?></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-store">
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-store-owner"><?php echo $entry_store_owner; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="store_owner" value="<?php echo $store_owner; ?>"
                                                   placeholder="<?php echo $entry_store_owner; ?>"
                                                   id="input-store-owner" class="form-control"/>
                                            <?php if ($error_store_owner) { ?>
                                            <div class="text-danger"><?php echo $error_store_owner; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-store-name"><?php echo $entry_store_name; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="store_name" value="<?php echo $store_name; ?>"
                                                   placeholder="<?php echo $entry_store_name; ?>" id="input-store-name"
                                                   class="form-control"/>
                                            <?php if ($error_store_name) { ?>
                                            <div class="text-danger"><?php echo $error_store_name; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_address; ?></label>
                                        <div class="col-sm-10">
                                            <textarea name="address" class="form-control"
                                                      placeholder="<?php echo $entry_address; ?>"><?php echo $address; ?></textarea>
                                            <?php if ($error_address) { ?>
                                            <div class="text-danger"><?php echo $error_address; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-email"><?php echo $entry_email; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="email" value="<?php echo $email; ?>"
                                                   placeholder="<?php echo $entry_email; ?>" id="input-email"
                                                   class="form-control"/>
                                            <?php if ($error_email) { ?>
                                            <div class="text-danger"><?php echo $error_email; ?></div>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-telephone"><?php echo $entry_telephone; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="telephone" value="<?php echo $telephone; ?>"
                                                   placeholder="<?php echo $entry_telephone; ?>" id="input-telephone"
                                                   class="form-control"/>
                                            <?php if ($error_telephone) { ?>
                                            <div class="text-danger"><?php echo $error_telephone; ?></div>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><?php echo $entry_fax; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="fax"
                                                   placeholder="<?php echo $entry_fax; ?>" value="<?php echo $fax; ?>"/>
                                            <?php if ($error_fax) { ?>
                                            <div class="text-danger"><?php echo $error_fax; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-local">
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_city; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="city"
                                                   placeholder="<?php echo $entry_city; ?>"
                                                   value="<?php echo $city; ?>"/>
                                            <?php if ($error_city) { ?>
                                            <div class="text-danger"><?php echo $error_city; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-country"><?php echo $entry_country; ?></label>
                                        <div class="col-sm-10">
                                            <select name="country_id" id="input-country" class="form-control">
                                                <option value=""><?php echo $text_select; ?></option>
                                                <?php foreach ($countries as $country) { ?>
                                                <?php if ($country['country_id'] == $country_id) { ?>
                                                <option value="<?php echo $country['country_id']; ?>"
                                                        selected="selected"><?php echo $country['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_country) { ?>
                                            <div class="text-danger"><?php echo $error_country; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label"
                                               for="input-zone"><?php echo $entry_zone; ?></label>
                                        <div class="col-sm-10">
                                            <select name="zone_id" id="input-zone" class="form-control">
                                            </select>
                                            <?php if ($error_zone) { ?>
                                            <div class="text-danger"><?php echo $error_zone; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-image">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><?php echo $entry_banner; ?></label>
                                        <div class="col-sm-10">
                                            <a href="" id="thumb-banner" data-toggle="image" class="img-thumbnail"><img
                                                        src="<?php echo $thumb_banner; ?>" alt="" title=""
                                                        data-placeholder="<?php echo $placeholder; ?>"/></a>
                                            <input type="hidden" name="banner" value="<?php echo $banner; ?>"
                                                   id="input-banner"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><?php echo $entry_logo; ?></label>
                                        <div class="col-sm-10">
                                            <a href="" id="thumb-logo" data-toggle="image" class="img-thumbnail"><img
                                                        src="<?php echo $thumb_logo; ?>" alt="" title=""
                                                        data-placeholder="<?php echo $placeholder; ?>"/></a>
                                            <input type="hidden" name="logo" value="<?php echo $logo; ?>"
                                                   id="input-logo"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><?php echo $entry_image; ?></label>
                                        <div class="col-sm-10">
                                            <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img
                                                        src="<?php echo $thumb_image; ?>" alt="" title=""
                                                        data-placeholder="<?php echo $placeholder; ?>"/></a>
                                            <input type="hidden" name="image" value="<?php echo $image; ?>"
                                                   id="input-image"/>
                                        </div>
                                    </div>
                                    <?php if (isset($link_id_proof)) { ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2">ID Proof</label>
                                        <div class="col-sm-10">
                                            <a href="<?php echo $link_id_proof?>" class="btn btn-primary"
                                               target="_blank">Click here to view Id Proof</a>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($link_address_proof)) { ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2">Address Proof</label>
                                        <div class="col-sm-10">
                                            <a href="<?php echo $link_address_proof?>" class="btn btn-primary"
                                               target="_blank">Click here to view Address Proof</a>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="tab-pane" id="tab-social">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_facebook; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-facebook"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control" name="facebook_url"
                                                       placeholder="<?php echo $entry_facebook; ?>"
                                                       value="<?php echo $facebook_url; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_google_plus; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-google-plus"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_google_plus; ?>"
                                                       value="<?php echo $google_plus_url; ?>" name="google_plus_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_twitter; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-twitter"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_twitter; ?>"
                                                       value="<?php echo $twitter_url; ?>" name="twitter_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_pinterest; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-pinterest"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_pinterest; ?>"
                                                       value="<?php echo $pinterest_url; ?>" name="pinterest_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_linkedin; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-linkedin"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_linkedin; ?>"
                                                       value="<?php echo $linkedin_url; ?>" name="linkedin_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_youtube; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-youtube"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_youtube; ?>"
                                                       value="<?php echo $youtube_url; ?>" name="youtube_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_instagram; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-instagram"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_instagram; ?>"
                                                       value="<?php echo $instagram_url; ?>" name="instagram_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_flickr; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-flickr"
                                                                                   aria-hidden="true"></i></span>
                                                <input type="text" class="form-control"
                                                       placeholder="<?php echo $entry_flickr; ?>"
                                                       value="<?php echo $flickr_url; ?>" name="flickr_url">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-shipping">
                                    <div class="form-group mp-buttons required">
                                        <label class="col-sm-2 control-label"><?php echo $entry_shipping_type; ?></label>
                                        <div class="col-sm-4">
                                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                                                <label class="shipping_type btn btn-primary <?php echo $shipping_type == 'order_wise' ? 'active' : '';  ?>">
                                                    <input type="radio" name="shipping_type"
                                                           value="order_wise" <?php echo $shipping_type == 'order_wise' ? 'checked="checked"' : ''; ?>
                                                    /><?php echo $text_order_wise; ?>
                                                </label>
                                                <label class="shipping_type btn btn-primary <?php echo $shipping_type == 'product_wise' ? 'active' : '';  ?>">
                                                    <input type="radio" name="shipping_type"
                                                           value="product_wise" <?php echo $shipping_type == 'product_wise' ? 'checked="checked"' : ''; ?>
                                                    /><?php echo $text_product_wise; ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_shipping_amount; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="shipping_amount"
                                                   value="<?php echo $shipping_amount; ?>"
                                                   placeholder="<?php echo $entry_shipping_amount; ?>"/>
                                            <?php if($error_shipping_amount) { ?>
                                            <div class="text-danger"><?php echo $error_shipping_amount; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-payment">
                                    <div class="form-group mp-buttons required">
                                        <label class="col-sm-2 control-label"><?php echo $entry_payment_type; ?></label>
                                        <div class="col-sm-6">
                                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                                                <label class="payment_type btn btn-primary <?php echo $payment_type == 'paypal' ? 'active' : '';  ?>">
                                                    <input type="radio" name="payment_type"
                                                           value="paypal" <?php echo $payment_type == 'paypal' ? 'checked="checked"' : ''; ?>
                                                    /><?php echo $text_paypal; ?>
                                                </label>
                                                <label class="payment_type btn btn-primary <?php echo $payment_type == 'bank' ? 'active' : '';  ?>">
                                                    <input type="radio" name="payment_type"
                                                           value="bank" <?php echo $payment_type == 'bank' ? 'checked="checked"' : ''; ?>
                                                    /><?php echo $text_bank_transfer; ?>
                                                </label>
                                                <label class="payment_type btn btn-primary <?php echo $payment_type == 'cheque' ? 'active' : '';  ?>">
                                                    <input type="radio" name="payment_type"
                                                           value="cheque" <?php echo $payment_type == 'cheque' ? 'checked="checked"' : ''; ?>
                                                    /><?php echo $text_cheque; ?>
                                                </label>
                                            </div>
                                            <?php if($error_payment_type) { ?>
                                            <div class="text-danger"><?php echo $error_payment_type; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required payment_paypal <?php echo $payment_type == 'paypal' ? '' : 'hide'; ?>">
                                        <label class="control-label col-sm-2"><?php echo $entry_paypal_email; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="paypal_email"
                                                   value="<?php echo $paypal_email; ?>"
                                                   placeholder="<?php echo $entry_paypal_email; ?>"/>
                                            <?php if($error_paypal_email) { ?>
                                            <div class="text-danger"><?php echo $error_paypal_email; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required payment_bank <?php echo $payment_type == 'bank' ? '' : 'hide'; ?>">
                                        <label class="control-label col-sm-2"><?php echo $entry_bank_details; ?></label>
                                        <div class="col-sm-10">
                                            <textarea name="bank_details" rows="8" class="form-control"
                                                      placeholder="<?php echo $entry_bank_details; ?>"><?php echo $bank_details; ?></textarea>
                                            <?php if($error_bank_details) { ?>
                                            <div class="text-danger"><?php echo $error_bank_details; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required payment_cheque <?php echo $payment_type == 'cheque' ? '' : 'hide'; ?>">
                                        <label class="control-label col-sm-2"><?php echo $entry_cheque_payee; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="cheque_payee_name"
                                                   value="<?php echo $cheque_payee_name; ?>"
                                                   placeholder="<?php echo $entry_cheque_payee; ?>"/>
                                            <?php if($error_cheque_payee_name) { ?>
                                            <div class="text-danger"><?php echo $error_cheque_payee_name; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-commission">
                                    <div class="form-group required">
                                        <label class="control-label col-sm-2"><?php echo $entry_commission_rate; ?></label>
                                        <div class="col-sm-4 col-md-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="commission_rate"
                                                       value="<?php echo $commission_rate; ?>"
                                                       placeholder="<?php echo $entry_commission_rate; ?>"/>
                                                <span class="input-group-btn">
                        <button type="button" class="btn btn-default">%</button>
                        </span>
                                            </div>
                                            <?php if($error_commission_rate) { ?>
                                            <div class="text-danger"><?php echo $error_commission_rate; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-transaction">
                                    <div class="row">
                                        <div class=" col-sm-4">
                                            <div class="mv-ovrvw">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <div class="title"><?php echo $text_total_commission; ?></div>
                                                        <div class="count highlight"><?php echo $total_commission; ?></div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mv-ovrvw">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <div class="title"><?php echo $text_total_paid; ?></div>
                                                        <div class="count highlight"><?php echo $total_paid; ?></div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mv-ovrvw">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <div class="title"><?php echo $text_total_balance; ?></div>
                                                        <div class="count highlight"><?php echo $total_balance; ?></div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="transaction"></div>
                                    <br/>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"
                                               for="input-amount"><?php echo $entry_amount; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="amount" value=""
                                                   placeholder="<?php echo $entry_amount; ?>" id="input-amount"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" id="button-transaction"
                                                data-loading-text="<?php echo $text_loading; ?>"
                                                class="btn btn-primary"><i
                                                    class="fa fa-plus-circle"></i> <?php echo $button_transaction_add; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"><!--
        $('select[name=\'country_id\']').on('change', function () {
            $.ajax({
                url: 'index.php?route=localisation/country/country&user_token=<?php echo $user_token; ?>&country_id=' + this.value,
                dataType: 'json',
                beforeSend: function () {
                    $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                complete: function () {
                    $('.fa-spin').remove();
                },
                success: function (json) {
                    html = '<option value=""><?php echo $text_select; ?></option>';

                    if (json['zone'] && json['zone'] != '') {
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                            if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                                html += ' selected="selected"';
                            }

                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }
                    } else {
                        html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
                    }

                    $('select[name=\'zone_id\']').html(html);
                }
            });
        });

        $('select[name=\'country_id\']').trigger('change');
        //--></script>
    <script type="text/javascript">
        $('.payment_type').click(function () {
            var payment_type = $(this).find('input').val();

            $('.payment_paypal, .payment_bank, .payment_cheque').addClass('hide');
            if (payment_type == 'paypal') {
                $('.payment_paypal').removeClass('hide');
            }

            if (payment_type == 'bank') {
                $('.payment_bank').removeClass('hide');
            }

            if (payment_type == 'cheque') {
                $('.payment_cheque').removeClass('hide');
            }
        });
    </script>
    <script type="text/javascript"><!--
        $('#transaction').delegate('.pagination a', 'click', function (e) {
            e.preventDefault();

            $('#transaction').load(this.href);
        });

        $('#transaction').load('index.php?route=mpmultivendor/mpseller/transaction&user_token=<?php echo $user_token; ?>&mpseller_id=<?php echo $mpseller_id; ?>');

        $('#button-transaction').on('click', function (e) {
            e.preventDefault();

            $.ajax({
                url: 'index.php?route=mpmultivendor/mpseller/addTransaction&user_token=<?php echo $user_token; ?>&mpseller_id=<?php echo $mpseller_id; ?>',
                type: 'post',
                dataType: 'json',
                data: 'amount=' + encodeURIComponent($('#tab-transaction input[name=\'amount\']').val()),
                beforeSend: function () {
                    $('#button-transaction').button('loading');
                },
                complete: function () {
                    $('#button-transaction').button('reset');
                },
                success: function (json) {
                    $('.alert').remove();

                    if (json['error']) {
                        $('#tab-transaction').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');
                    }

                    if (json['success']) {
                        $('#tab-transaction').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');

                        $('#transaction').load('index.php?route=mpmultivendor/mpseller/transaction&user_token=<?php echo $user_token; ?>&mpseller_id=<?php echo $mpseller_id; ?>');

                        $('#tab-transaction input[name=\'amount\']').val('');
                    }
                }
            });
        });
        //--></script>
</div>
<?php echo $footer; ?>