<?php $this->load->view('includes/head'); ?>
</head>
<body>
  <div id="app">
    <div class="main-wrapper">
      <?php $this->load->view('includes/navbar'); ?>
        <div class="main-content">
          <section class="section">
            <div class="section-header">
              <div class="section-header-back">
                <a href="javascript:history.go(-1)" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
              </div>
              <h1><?=$this->lang->line('settings')?$this->lang->line('settings'):'Settings'?></h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
                <div class="breadcrumb-item"><?=$this->lang->line('settings')?$this->lang->line('settings'):'Settings'?></div>
              </div>
            </div>

            <div class="section-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="card card-primary">
                    <div class="card-body">
                      <ul class="nav nav-pills flex-column">
                        <li class="nav-item"><a href="<?=base_url('settings')?>" class="nav-link <?=($main_page == 'general')?'active':''?>"><i class="fas fa-cogs"></i> <?=$this->lang->line('general')?$this->lang->line('general'):'General'?></a></li>

                        <?php if (is_module_allowed('payment_gateway')){ ?> 
                          <li class="nav-item"><a href="<?=base_url('settings/payment')?>" class="nav-link <?=($main_page == 'payment')?'active':''?>"><i class="fab fa-paypal"></i> <?=$this->lang->line('payment_gateway')?$this->lang->line('payment_gateway'):'Payment Gateway'?></a></li>
                        <?php } ?>

                        <?php if ($this->ion_auth->in_group(3)){ ?> 
                          <li class="nav-item"><a href="<?=base_url('settings/seo')?>" class="nav-link <?=($main_page == 'seo')?'active':''?>"><i class="fas fa-search"></i> <?=$this->lang->line('seo')?$this->lang->line('seo'):'SEO'?></a></li>
                          <li class="nav-item"><a href="<?=base_url('settings/logins')?>" class="nav-link <?=($main_page == 'logins')?'active':''?>"><i class="fab fa-google"></i> <?=$this->lang->line('social_login')?htmlspecialchars($this->lang->line('social_login')):'Social Login'?></a></li>
                          <li class="nav-item"><a href="<?=base_url('settings/email')?>" class="nav-link <?=($main_page == 'email')?'active':''?>"><i class="fas fa-at"></i> <?=$this->lang->line('email')?$this->lang->line('email'):'Email'?></a></li>
                          <li class="nav-item"><a href="<?=base_url('settings/email-templates')?>" class="nav-link <?=($main_page == 'email-templates')?'active':''?>"><i class="fas fa-mail-bulk"></i> <?=$this->lang->line('email_templates')?$this->lang->line('email_templates'):'Email Templates'?></a></li>
                          <li class="nav-item"><a href="<?=base_url('languages')?>" class="nav-link <?=($main_page == 'languages')?'active':''?>"><i class="fa fa-language"></i> <?=$this->lang->line('languages')?$this->lang->line('languages'):'Languages'?></a></li>

                          
                          <li class="nav-item"><a href="<?=base_url('settings/taxes')?>" class="nav-link <?=($main_page == 'taxes')?'active':''?>"><i class="fas fa-money-bill-alt"></i> <?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>

                          
                          <li class="nav-item"><a href="<?=base_url('settings/update')?>" class="nav-link <?=($main_page == 'update')?'active':''?>"><i class="fas fa-hand-holding-heart"></i> <?=$this->lang->line('update')?$this->lang->line('update'):'Update'?></a></li>

                          <li class="nav-item"><a href="<?=base_url('settings/recaptcha')?>" class="nav-link <?=($main_page == 'recaptcha')?'active':''?>"><i class="fas fa-certificate"></i> <?=$this->lang->line('google_recaptcha')?$this->lang->line('google_recaptcha'):'Google reCAPTCHA'?></a></li>

                          <li class="nav-item"><a href="<?=base_url('settings/custom-code')?>" class="nav-link <?=($main_page == 'custom-code')?'active':''?>"><i class="fas fa-code"></i> <?=$this->lang->line('custom_code')?$this->lang->line('custom_code'):'Custom Code'?></a></li>
                          
                          <li class="nav-item"><a href="<?=base_url('settings/maintenance-mode')?>" class="nav-link <?=($main_page == 'maintenance-mode')?'active':''?>"><i class="fas fa-wrench"></i> <?=$this->lang->line('maintenance_mode_title')?htmlspecialchars($this->lang->line('maintenance_mode_title')):'Maintenance Mode'?></a></li>
                          <li class="nav-item"><a href="<?=base_url('settings/livechat')?>" class="nav-link <?=($main_page == 'livechat')?'active':''?>"><i class="fas fa-comment"></i> <?=$this->lang->line('livechat')?htmlspecialchars($this->lang->line('livechat')):'LiveChat'?></a></li>

                          <li class="nav-item"><a href="<?=base_url('settings/telegram')?>" class="nav-link <?=($main_page == 'telegram')?'active':''?>"><i class="fab fa-telegram-plane"></i> <?=$this->lang->line('telegram')?htmlspecialchars($this->lang->line('telegram')):'Telegram'?></a></li>

                        <?php }else{ ?>
                          <li class="nav-item"><a href="<?=base_url('settings/company')?>" class="nav-link <?=($main_page == 'company')?'active':''?>"><i class="fas fa-copyright"></i> <?=$this->lang->line('company')?$this->lang->line('company'):'Company'?></a></li>

                          <?php if (is_module_allowed('taxes')){ ?> 
                            <li class="nav-item"><a href="<?=base_url('settings/taxes')?>" class="nav-link <?=($main_page == 'taxes')?'active':''?>"><i class="fas fa-money-bill-alt"></i> <?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>
                          <?php } ?> 
                          <?php if (is_module_allowed('user_permissions')){ ?>
                            <li class="nav-item"><a href="<?=base_url('settings/user-permissions')?>" class="nav-link <?=($main_page == 'permissions')?'active':''?>"><i class="fas fa-user-cog"></i> <?=$this->lang->line('user_permissions')?$this->lang->line('user_permissions'):'User Permissions'?></a></li>
                          <?php } ?>

                          <li class="nav-item"><a href="<?=base_url('settings/telegram')?>" class="nav-link <?=($main_page == 'telegram')?'active':''?>"><i class="fab fa-telegram-plane"></i> <?=$this->lang->line('telegram')?htmlspecialchars($this->lang->line('telegram')):'Telegram'?></a></li>
                        <?php } ?>
                        
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="card card-primary" id="settings-card">
                    <?php $this->load->view('setting-forms/'.htmlspecialchars($main_page)); ?>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

<?php $this->load->view('includes/js'); ?>

<?php if($this->uri->segment(2) == 'custom-code' || $this->uri->segment(2) == 'livechat'){ ?>
  <script>
    CodeMirror.fromTextArea(document.getElementById('header_code'), { 
      lineNumbers: true,
      theme: 'duotone-dark',
    }).on('change', editor => {
      $("#header_code").val(editor.getValue());
    });

    CodeMirror.fromTextArea(document.getElementById('footer_code'), { 
      lineNumbers: true,
      theme: 'duotone-dark',
    }).on('change', editor => {
      $("#footer_code").val(editor.getValue());
    });
    
    CodeMirror.fromTextArea(document.getElementById('livechat'), {
      lineNumbers: true,
      theme: 'duotone-dark',
    }).on('change', editor => {
      $("#livechat").val(editor.getValue());
    });



  </script>
<?php } ?>

<?php if($main_page == 'telegram'){ ?>
  <script>
    // Dedicated handlers for the Telegram settings form. Placed here (after
    // includes/js) so jQuery is available, and using its own form id so it
    // does not hit the shared #setting-form handler (which expects a logo
    // "data" object in the response and otherwise leaves the spinner stuck).
    $("#telegram-form").on('submit', function(e) {
      e.preventDefault();
      var form = $(this),
          save_button = form.find('.savebtn'),
          output_status = form.find('.result');

      save_button.addClass('btn-progress').attr('disabled', true);
      output_status.html('');

      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: new FormData(this),
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(result) {
          var cls = (result && result['error'] === false) ? 'alert-success' : 'alert-danger';
          output_status.prepend('<div class="alert ' + cls + '">' + (result ? result['message'] : '') + '</div>');
          output_status.find('.alert').delay(4000).fadeOut();
          save_button.removeClass('btn-progress').attr('disabled', false);
        },
        error: function() {
          output_status.prepend('<div class="alert alert-danger">Something went wrong. Please try again.</div>');
          output_status.find('.alert').delay(4000).fadeOut();
          save_button.removeClass('btn-progress').attr('disabled', false);
        }
      });
    });

    $(document).on('click', '#telegram_test_btn', function() {
      var btn = $(this),
          result = $('#telegram_test_result');
      result.html('');
      btn.attr('disabled', true).addClass('btn-progress');

      $.ajax({
        type: 'POST',
        url: '<?=base_url('settings/test-telegram')?>',
        data: {
          bot_token: $('#telegram_bot_token').val(),
          chat_id: $('#telegram_chat_id').val(),
          thread_id: $('#telegram_thread_id').val()
        },
        dataType: 'json',
        success: function(res) {
          var cls = (res && res['error']) ? 'text-danger' : 'text-success';
          result.html('<span class="' + cls + '">' + (res ? res['message'] : '') + '</span>');
          btn.attr('disabled', false).removeClass('btn-progress');
        },
        error: function() {
          result.html('<span class="text-danger"><?=$this->lang->line('telegram_test_failed')?htmlspecialchars($this->lang->line('telegram_test_failed')):'Could not send. Check the bot token and chat ID.'?></span>');
          btn.attr('disabled', false).removeClass('btn-progress');
        }
      });
    });
  </script>
<?php } ?>

</body>
</html>
