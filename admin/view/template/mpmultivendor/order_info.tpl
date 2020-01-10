<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $invoice; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></a> <a href="<?php echo $shipping; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></a> <a href="<?php echo $edit; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $text_order_detail; ?></h3>
          </div>
          <table class="table">
            <tbody>
              <tr>
                <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_store; ?>" class="btn btn-info btn-xs"><i class="fa fa-shopping-cart fa-fw"></i></button></td>
                <td><a href="<?php echo $store_url; ?>" target="_blank"><?php echo $store_name; ?></a></td>
              </tr>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_date_added; ?>" class="btn btn-info btn-xs"><i class="fa fa-calendar fa-fw"></i></button></td>
                <td><?php echo $date_added; ?></td>
              </tr>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_payment_method; ?>" class="btn btn-info btn-xs"><i class="fa fa-credit-card fa-fw"></i></button></td>
                <td><?php echo $payment_method; ?></td>
              </tr>
              <?php if ($shipping_method) { ?>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_shipping_method; ?>" class="btn btn-info btn-xs"><i class="fa fa-truck fa-fw"></i></button></td>
                <td><?php echo $shipping_method; ?></td>
              </tr>
              <?php } ?>
              <?php if ($order_status) { ?>
              <tr>
                <td><button data-toggle="tooltip" title="<?php echo $text_order_status; ?>" class="btn btn-info btn-xs"><i class="fa fa-tag fa-fw"></i> </button></td>
                <td><button type="button" class="btn btn-warning btn-xs"><?php echo $order_status; ?></button></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-user"></i> <?php echo $text_customer_detail; ?></h3>
          </div>
          <table class="table">
            <tr>
              <td style="width: 1%;"><button data-toggle="tooltip" title="<?php echo $text_customer; ?>" class="btn btn-info btn-xs"><i class="fa fa-user fa-fw"></i></button></td>
              <td><?php if ($customer) { ?>
                <a href="<?php echo $customer; ?>" target="_blank"><?php echo $firstname; ?> <?php echo $lastname; ?></a>
                <?php } else { ?>
                <?php echo $firstname; ?> <?php echo $lastname; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_customer_group; ?>" class="btn btn-info btn-xs"><i class="fa fa-group fa-fw"></i></button></td>
              <td><?php echo $customer_group; ?></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_email; ?>" class="btn btn-info btn-xs"><i class="fa fa-envelope-o fa-fw"></i></button></td>
              <td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
            </tr>
            <tr>
              <td><button data-toggle="tooltip" title="<?php echo $text_telephone; ?>" class="btn btn-info btn-xs"><i class="fa fa-phone fa-fw"></i></button></td>
              <td><?php echo $telephone; ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-info-circle"></i> <?php echo $text_order; ?></h3>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <td style="width: 50%;" class="text-left"><?php echo $text_payment_address; ?></td>
              <?php if ($shipping_method) { ?>
              <td style="width: 50%;" class="text-left"><?php echo $text_shipping_address; ?></td>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-left"><?php echo $payment_address; ?></td>
              <?php if ($shipping_method) { ?>
              <td class="text-left"><?php echo $shipping_address; ?></td>
              <?php } ?>
            </tr>
          </tbody>
        </table>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td class="text-left"><?php echo $column_product; ?></td>
              <td class="text-left"><?php echo $column_seller_name; ?></td>
              <td class="text-left"><?php echo $column_store_name; ?></td>
              <td class="text-left"><?php echo $column_mpseller_order_status; ?></td>
              <td class="text-left"><?php echo $column_model; ?></td>
              <td class="text-right"><?php echo $column_quantity; ?></td>
              <td class="text-right"><?php echo $column_price; ?></td>
              <td class="text-right"><?php echo $column_total; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
            <tr>
              <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                <?php foreach ($product['option'] as $option) { ?>
                <br />
                <?php if ($option['type'] != 'file') { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                <?php } else { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
                <?php } ?>
                <?php } ?></td>
              <td class="text-left"><?php echo $product['product_store_owner']; ?></td>
              <td class="text-left"><?php echo $product['product_store_name']; ?></td>
              <td class="text-left"><?php echo $product['product_order_status']; ?></td>
              <td class="text-left"><?php echo $product['model']; ?></td>
              <td class="text-right"><?php echo $product['quantity']; ?></td>
              <td class="text-right"><?php echo $product['price']; ?></td>
              <td class="text-right"><?php echo $product['total']; ?></td>
            </tr>
            <?php } ?>
            <?php foreach ($vouchers as $voucher) { ?>
            <tr>
              <td class="text-left"><a href="<?php echo $voucher['href']; ?>"><?php echo $voucher['description']; ?></a></td>
              <td class="text-left"></td>
              <td class="text-left"></td>
              <td class="text-left"></td>
              <td class="text-left"></td>
              <td class="text-right">1</td>
              <td class="text-right"><?php echo $voucher['amount']; ?></td>
              <td class="text-right"><?php echo $voucher['amount']; ?></td>
            </tr>
            <?php } ?>
            <?php foreach ($totals as $total) { ?>
            <tr>
              <td colspan="7" class="text-right"><?php echo $total['title']; ?></td>
              <td class="text-right"><?php echo $total['text']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php if ($comment) { ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td><?php echo $text_comment; ?></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $comment; ?></td>
            </tr>
          </tbody>
        </table>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>