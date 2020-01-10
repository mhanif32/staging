<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $text_contact_details; ?></h3>
          </div>
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td style="width: 20%;"><?php echo $entry_name; ?></td>
                <td><?php echo $customer_name; ?></td>
              </tr>
              <tr>
                <td><?php echo $entry_email; ?></td>
                <td><?php echo $customer_email; ?></td>
              </tr>
              <tr>
                <td><?php echo $text_date_added; ?></td>
                <td><?php echo $date_added; ?></td>
              </tr>
              <tr>
                <td><?php echo $text_date_modified; ?></td>
                <td><?php echo $date_modified; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $text_seller_details; ?></h3>
          </div>
          <table class="table">
            <tbody>
              <tr>
                <td style="width: 20%;"><?php echo $entry_seller_name; ?></td>
                <td><?php echo $store_owner; ?></td>
              </tr>
              <tr>
                <td style="width: 20%;"><?php echo $entry_store_name; ?></td>
                <td><?php echo $store_name; ?></td>
              </tr>
              <tr>
                <td style="width: 20%;"><?php echo $entry_seller_email; ?></td>
                <td><?php echo $seller_email; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $text_enquiry_details; ?></h3>
          </div>
          <div class="panel-body"><?php echo $customer_message; ?></div>
        </div>
      </div>

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
  </div>
</div>
<?php echo $footer; ?>