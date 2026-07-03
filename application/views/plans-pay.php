<?php $this->load->view('includes/head'); ?>
</head>
<body>
  <div id="app">
    <div class="main-wrapper">
      <?php $this->load->view('includes/navbar'); ?>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <div class="section-header-back">
              <a href="javascript:history.go(-1)" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>
            <?=$this->lang->line('order_summary')?htmlspecialchars($this->lang->line('order_summary')):'Order Summary'?>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item active">
              <a href="<?=base_url('plans')?>"><?=$this->lang->line('subscription_plans')?$this->lang->line('subscription_plans'):'Plans'?></a>
              </div>
              <div class="breadcrumb-item">
              <?=$this->lang->line('order_summary')?htmlspecialchars($this->lang->line('order_summary')):'Order Summary'?>
              </div>
            </div>
          </div>
          <div class="section-body">
          
          
            <div class="row">
              <div class="col-md-7 mx-auto paymet-box">
                  <div class="row">
                    <div class="col-md-11 mx-auto">
                      <div class="section-title"><?=$this->lang->line('order_summary')?htmlspecialchars($this->lang->line('order_summary')):'Order Summary'?></div>
                      <p class="section-lead"><?=$this->lang->line('check_your_order_and_select_your_payment_method_from_the_options_below')?htmlspecialchars($this->lang->line('check_your_order_and_select_your_payment_method_from_the_options_below')):'Check your order and select your payment method from the options below.'?></p>
                      <div class="table-responsive">
                        <table class="table table-striped table-hover table-md">
                          <tr>
                            <th><?=$this->lang->line('plan')?htmlspecialchars($this->lang->line('plan')):'Plan'?></th>
                            <th class="text-center"><?=$this->lang->line('payment_type')?htmlspecialchars($this->lang->line('payment_type')):'Payment Type'?></th>
                            <th class="text-right"><?=$this->lang->line('price')?htmlspecialchars($this->lang->line('price')):'Price'?></th>
                          </tr>
                          <tr>
                            <td id="summary_plan"><?=$plan[0]['title']?></td>
                            <td id="summary_payment_type" class="text-center">
                              <?php
                                if($plan[0]["billing_type"] == 'One Time'){
                                  echo $this->lang->line('one_time')?htmlspecialchars($this->lang->line('one_time')):'One Time';
                                }elseif($plan[0]["billing_type"] == 'Monthly'){
                                  echo $this->lang->line('monthly')?htmlspecialchars($this->lang->line('monthly')):'Monthly';
                                }else{
                                  echo $this->lang->line('yearly')?$this->lang->line('yearly'):'Yearly';
                                } 
                              ?>
                            </td>
                            <td id="summary_price" class="text-right"><?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=$plan[0]['price']?></td>
                          </tr>
                        </table>
                      </div>
                      <div class="row">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 text-right">
                          <div class="invoice-detail-item">
                            <?php
                              $tax_pec = 0;
                            if($taxes){
                              foreach($taxes as $key => $tax){ 
                                $tax_pec = $tax_pec+$tax['tax'];
                            ?>
                                <div class="invoice-detail-name font-weight-bold"><?=$tax['title']?> (<?=$tax['tax']?>%)</div>
                                <div class="invoice-detail-value">+ <?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=$plan[0]['price']*$tax['tax']/100?></div>
                              <?php } 
                            }
                            $tax_amount = $plan[0]['price']*$tax_pec/100;
                            $total_amount = $plan[0]['price']+$tax_amount;
                            ?>
                          </div>
                          <hr class="mt-2 mb-2">
                          <div class="invoice-detail-item">
                            <div class="invoice-detail-name font-weight-bold"><?=$this->lang->line('total')?htmlspecialchars($this->lang->line('total')):'Total'?></div>
                            <div class="invoice-detail-value invoice-detail-value-lg"><?=htmlspecialchars(get_saas_currency('currency_symbol'))?><?=$total_amount?></div>
                          </div>
                        </div>
                      </div>

                      
                      <hr>
                      <div class="text-md-right mb-3">
                        <button class="btn btn-primary btn-icon icon-left payment-button" data-amount="<?=$total_amount?>" data-id="<?=htmlspecialchars($plan[0]['id'])?>"><i class="fas fa-credit-card"></i> <?=$this->lang->line('pay_now')?htmlspecialchars($this->lang->line('pay_now')):'Pay Now'?></button>
                      </div>


                    </div>
                  </div>
              </div>
            </div>

            <div class="row d-none" id="payment-div">

              <div id="paypal-button" class="col-md-7 mx-auto paymet-box"></div>
              
              <?php if(get_stripe_secret_key() && get_stripe_publishable_key()){ ?>
                <button id="stripe-button" class="col-md-7 btn mx-auto paymet-box">
                  <img src="<?=base_url('assets/img/stripe.png')?>" width="14%" alt="Stripe">
                </button>
              <?php } ?>
              <?php if(get_razorpay_key_id()){ ?>
                <button id="razorpay-button" class="col-md-7 btn mx-auto paymet-box">
                    <img src="<?=base_url('assets/img/razorpay.png')?>" width="27%" alt="Stripe">
                </button>
              <?php } ?>
              <?php if(get_paystack_public_key()){ ?>
                <button id="paystack-button" class="col-md-7 btn mx-auto paymet-box">
                  <img src="<?=base_url('assets/img/paystack.png')?>" width="24%" alt="Paystack">
                </button>
              <?php } ?>

              <?php if(get_offline_bank_transfer()){ ?>
                <div id="accordion" class="col-md-7 paymet-box mx-auto">
                  <div class="accordion mb-0">
                    <div class="accordion-header text-center" role="button" data-toggle="collapse" data-target="#panel-body-3">
                      <h4><?=$this->lang->line('offline_bank_transfer')?$this->lang->line('offline_bank_transfer'):'Offline / Bank Transfer'?></h4>
                    </div>
                    <div class="accordion-body collapse" id="panel-body-3" data-parent="#accordion">
                      <p class="mb-0"><?=get_bank_details()?></p>

                      <form action="<?=base_url('plans/create-offline-request/')?>" method="POST" id="bank-transfer-form">
                        <div class="card-footer bg-whitesmoke">
                          <div class="form-group">
                            <label><?=$this->lang->line('upload_receipt')?htmlspecialchars($this->lang->line('upload_receipt')):'Upload Receipt'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('supported_formats')?htmlspecialchars($this->lang->line('supported_formats')):'Supported Formats: jpg, jpeg, png'?>" data-original-title="<?=$this->lang->line('supported_formats')?htmlspecialchars($this->lang->line('supported_formats')):'Supported Formats: jpg, jpeg, png'?>"></i> </label>
                            <input type="file" name="receipt" class="form-control">
                            <input type="hidden" name="plan_id" id="plan_id">
                          </div>
                          <button class="btn btn-primary savebtn"><?=$this->lang->line('upload_and_send_for_confirmation')?htmlspecialchars($this->lang->line('upload_and_send_for_confirmation')):'Upload and Send for Confirmation'?></button>
                        </div>
                        <div class="result"></div>
                      </form>

                    </div>
                  </div>
                </div>
              <?php } ?>

            </div>
          </div>
        </section>
      </div>
      <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

<?php $this->load->view('includes/js'); ?>

<script>
paypal_client_id = "<?=get_payment_paypal()?>";
get_stripe_publishable_key = "<?=get_stripe_publishable_key()?>";
razorpay_key_id = "<?=get_razorpay_key_id()?>";
offline_bank_transfer = "<?=get_offline_bank_transfer()?>";
paystack_user_email_id = "<?=$this->session->userdata('email')?>";
paystack_public_key = "<?=get_paystack_public_key()?>";
</script>

<?php if(get_payment_paypal()){ ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?=get_payment_paypal()?>&currency=<?=get_saas_currency('currency_code')?>"></script>
<?php } ?>

<?php if(get_stripe_publishable_key()){ ?>
<script src="https://js.stripe.com/v3/"></script>
<?php } ?>

<?php if(get_paystack_public_key()){ ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<?php } ?>

<?php if(get_razorpay_key_id()){ ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php } ?>

<script src="<?=base_url('assets/js/page/payment.js');?>"></script>

</body>
</html>