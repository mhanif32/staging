<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"
                        onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-message').submit() : false;"><i
                            class="fa fa-trash-o"></i></button>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-fname"><?php echo $entry_store_owner; ?></label>
                                <input type="text" name="filter_fname" value="<?php echo $filter_fname; ?>"
                                       placeholder="<?php echo $entry_store_owner; ?>" id="input-store-owner"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-lname"><?php echo $entry_store_name; ?></label>
                                <input type="text" name="filter_lname" value="<?php echo $filter_lname; ?>"
                                       placeholder="<?php echo $entry_store_name; ?>" id="input-lname"
                                       class="form-control"/>
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i
                                        class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-message">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox"
                                                                                   onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
                                </td>
                                <td class="text-left"><?php if ($sort == 'mp.store_owner') { ?>
                                    <a href="<?php echo $sort_seller_name; ?>"
                                       class="<?php echo strtolower($order); ?>"><?php echo $column_seller_name; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_seller_name; ?>"><?php echo $column_seller_name; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'mp.store_name') { ?>
                                    <a href="<?php echo $sort_store_name; ?>"
                                       class="<?php echo strtolower($order); ?>"><?php echo $column_store_name; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_store_name; ?>"><?php echo $column_store_name; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'mps.message') { ?>
                                    <a href="<?php echo $sort_message; ?>"
                                       class="<?php echo strtolower($order); ?>"><?php echo $column_message; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_message; ?>"><?php echo $column_message; ?></a>
                                    <?php } ?></td>
                                <td class="text-right"><?php if ($sort == 'mps.date_added') { ?>
                                    <a href="<?php echo $sort_date_added; ?>"
                                       class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                                    <?php } ?></td>
                                <td class="text-right"><?php echo $column_action; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($dp_messages) { ?>
                            <?php foreach ($dp_messages as $dp_message) { ?>
                            <tr>
                                <td class="text-center"><?php if (in_array($dp_message['delivery_partner_id'], $selected)) { ?>
                                    <input type="checkbox" name="selected[]"
                                           value="<?php echo $dp_message['delivery_partner_id']; ?>" checked="checked"/>
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]"
                                           value="<?php echo $dp_message['delivery_partner_id']; ?>"/>
                                    <?php } ?></td>
                                <td class="text-left"><?php echo $dp_message['firstname']; ?></td>
                                <td class="text-left"><?php echo $dp_message['lastname']; ?></td>
                                <td class="text-left"><?php echo $dp_message['message']; ?></td>
                                <td class="text-right"><?php echo $dp_message['date_added']; ?></td>
                                <td class="text-right">
                                    <label style="position:relative">
                                        <a href="<?php echo $dp_message['edit']; ?>" data-toggle="tooltip"
                                           title="<?php echo $button_view; ?>" class="btn btn-primary">
                                            <?php if($dp_message['total_unreads']) { ?>
                                            <span style="position:absolute;top:-5px;left:-5px; font-size: 13px;"
                                                  class="label label-danger pull-right"><?php echo $dp_message['total_unreads']; ?></span>
                                            <?php } ?>
                                            <i class="fa fa-eye"></i>
                                        </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
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
        $('#button-filter').on('click', function () {
            url = 'index.php?route=mpmultivendor/mpseller_message&user_token=<?php echo $user_token; ?>';

            var filter_store_owner = $('input[name=\'filter_store_owner\']').val();
            if (filter_store_owner) {
                url += '&filter_store_owner=' + encodeURIComponent(filter_store_owner);
            }

            var filter_store_name = $('input[name=\'filter_store_name\']').val();
            if (filter_store_name) {
                url += '&filter_store_name=' + encodeURIComponent(filter_store_name);
            }

            location = url;
        });
        //--></script>
    <script type="text/javascript"><!--
        $('input[name=\'filter_store_owner\']').autocomplete({
            'source': function (request, response) {
                $.ajax({
                    url: 'index.php?route=mpmultivendor/mpseller/autocomplete&user_token=<?php echo $user_token; ?>&filter_store_owner=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                        response($.map(json, function (item) {
                            return {
                                label: item['store_owner'],
                                value: item['mpseller_id']
                            }
                        }));
                    }
                });
            },
            'select': function (item) {
                $('input[name=\'filter_store_owner\']').val(item['label']);
            }
        });

        $('input[name=\'filter_store_name\']').autocomplete({
            'source': function (request, response) {
                $.ajax({
                    url: 'index.php?route=mpmultivendor/mpseller/autocomplete&user_token=<?php echo $user_token; ?>&filter_store_name=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                        response($.map(json, function (item) {
                            return {
                                label: item['store_name'],
                                value: item['mpseller_id']
                            }
                        }));
                    }
                });
            },
            'select': function (item) {
                $('input[name=\'filter_store_name\']').val(item['label']);
            }
        });
        //--></script>
</div>
<?php echo $footer; ?>