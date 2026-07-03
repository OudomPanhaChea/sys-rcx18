<?php $this->load->view('includes/head'); ?>
<style>

body{
  color: #000000;
}

*{ font-family: DejaVu Sans !important;}

</style>
</head>
<body>
  <section class="section">
    <div class="section-body">
      <div class="invoice" style="padding: 0px;">
        <div class="invoice-print">
          <div class="row">
            <div class="col-lg-12">
              <div class="invoice-title">
                <h2><?=$this->lang->line('invoice')?$this->lang->line('invoice'):'Invoice'?></h2>
                <div class="invoice-number"><?=$orders[0]['invoice_id']?></div>
              </div>
              <hr style="margin-top: 40px; margin-bottom: 10px;">
              <div class="row">
                <div class="col-md-6">
                  <address>
                    <h5 class="text-uppercase" style="color: <?=theme_color()?>;" ><?=company_name()?></h5>
                  </address>
                </div>
                <div class="col-md-6" >
                  <address class="float-right">
                    <?=$this->lang->line('billed_to')?$this->lang->line('billed_to'):'Billed To'?>:<br>
                    <strong class="text-uppercase"><?=$orders[0]['first_name']?> <?=$orders[0]['last_name']?></strong>
                  </address>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <address>
                    <strong><?=$this->lang->line('invoice_date')?$this->lang->line('invoice_date'):'Invoice Date'?>:</strong><br>
                    <?=htmlspecialchars(format_date($orders[0]['created'],system_date_format()))?>
                  </address>
                </div>
                <div class="col-md-6 text-md-right text-right" >
                  <address class="float-right">
                  </address>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <tr>
                    <th><?=$this->lang->line('plan')?htmlspecialchars($this->lang->line('plan')):'Plan'?></th>
                    <th><?=$this->lang->line('payment_type')?htmlspecialchars($this->lang->line('payment_type')):'Payment Type'?></th>
                    <th class="text-right"><?=$this->lang->line('price')?htmlspecialchars($this->lang->line('price')):'Price'?></th>
                  </tr>
                  <tr>
                    <td><?=$orders[0]['title']?></td>
                    <td>
                      <?php
                        if($orders[0]["billing_type"] == 'One Time'){
                          echo $this->lang->line('one_time')?htmlspecialchars($this->lang->line('one_time')):'One Time';
                        }elseif($orders[0]["billing_type"] == 'Monthly'){
                          echo $this->lang->line('monthly')?htmlspecialchars($this->lang->line('monthly')):'Monthly';
                        }else{
                          echo $this->lang->line('yearly')?$this->lang->line('yearly'):'Yearly';
                        } 
                      ?>
                    </td>
                    <td class="text-right"><?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=$orders[0]['price']?></td>
                  </tr>
                </table>
              </div>
              <div class="row mt-4">
                <div class="col-lg-7">
                </div>
                <div class="col-lg-5 text-right">
                  <?php if($orders[0]['tax']){ foreach(json_decode($orders[0]['tax'], true) as $tax){ ?>
                    <div class="invoice-detail-item">
                      <div class="invoice-detail-name"><?=htmlspecialchars($tax['tax_name'])?> (<?=htmlspecialchars($tax['tax_per'])?>%):
                      <strong class="text-dark"><?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=htmlspecialchars($tax['tax_amount'])?></strong>
                      </div>
                    </div> 
                  <?php } } ?>
                  <hr class="mt-2 mb-2">
                  <div class="invoice-detail-item">
                    <div class="invoice-detail-value invoice-detail-value-lg"><?=$this->lang->line('total')?$this->lang->line('total'):'Total'?>: <?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=htmlspecialchars($orders[0]['amount_with_tax'])?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
        
<!-- General JS Scripts -->
<script src="<?=base_url('assets/modules/jquery.min.js')?>"></script>
<script src="<?=base_url('assets/modules/bootstrap/js/bootstrap.min.js')?>"></script>
<script src="<?=base_url('assets/js/stisla.js')?>"></script>
 
</body>
</html>
