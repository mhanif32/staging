<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">

        <div class="row">
          <?php if(!empty($chats)) { ?>
          <div class="col-sm-12">
            <?php foreach($chats as $chat) { ?>
             <div class="reply-wrap reply-chat mpchatting">
                <div class="ticket-header">
                  <img class="img-circle" src="<?php echo $chat['from_image']; ?>" />
                  <div class="ticket-content">
                    <span class="from_name">
                      <b><?php echo $chat['from_name']; ?></b>
                      <?php if($chat['from'] == 'seller') { ?>
                      <div class="namesub"><?php echo $chat['from_store']; ?></div>
                      <?php } ?>
                    </span>
                    <div class="ticket-time"><?php echo $chat['date_added']; ?></div>
                    <div class="cnt"><?php echo $chat['message']; ?></div>
                  </div>
                </div>
              </div>
            <hr/>
            <?php } ?>
          </div>
          <?php } ?>
        </div>

        <div class="row">
          <div class="col-sm-12 text-right"><?php echo $pagination; ?></div>
        </div>

        <div id="form-message" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_message; ?></label>
            <div class="col-sm-10">
              <textarea name="message" rows="8" class="form-control"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">&nbsp;</label>
            <div class="col-sm-10">
                <button type="submit" data-toggle="tooltip" title="<?php echo $button_send; ?>" class="btn btn-success button-submit"><i class="fa fa-paper-plane"></i> <?php echo $button_send; ?></button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.button-submit').click(function() {
  $.ajax({
    url: 'index.php?route=mpmultivendor/mpseller_message/SendMessage&user_token=<?php echo $user_token; ?>&mpseller_id=<?php echo $mpseller_id; ?>',
    type: 'post',
    data: $('#form-message input, #form-message textarea'),
    dataType: 'json',
    beforeSend: function() {
      $('.button-submit').button('loading');
    },
    complete: function() {
      $('.button-submit').button('reset');
    },
    success: function(json) {
      $('#form-message .alert, #form-message .text-danger').remove();

      if (json['redirect']) {
        location = json['redirect'];
      } else if (json['warning']) {
        $('#form-message').prepend('<div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> ' + json['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
    }
  });
});
//--></script>
</div>
<?php echo $footer; ?>