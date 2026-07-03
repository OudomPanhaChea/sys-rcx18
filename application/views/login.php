<?php $google_client_id = get_google_client_id(); ?>
<?php $this->load->view('includes/head'); ?>
</head>
<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <a href="<?=base_url()?>">
              <img src="<?=base_url('assets/uploads/logos/'.full_logo());?>" alt="logo" width="100%">
              </a>
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4><?=$this->lang->line('login')?htmlspecialchars($this->lang->line('login')):'Login'?></h4></div>

              <div class="card-body">
                <form id="login" method="POST" action="<?=base_url('auth/login')?>" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="identity"><?=$this->lang->line('email')?htmlspecialchars($this->lang->line('email')):'Email'?></label>
                    <input id="identity" type="email" class="form-control" name="identity" tabindex="1" required autofocus>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label"><?=$this->lang->line('password')?htmlspecialchars($this->lang->line('password')):'Password'?></label>
                      <div class="float-right">
                        <a href="#" id="modal-forgot-password" class="text-small">
                        <?=$this->lang->line('forgot_password')?$this->lang->line('forgot_password'):'Forgot Password'?>
                        </a>
                      </div>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me"><?=$this->lang->line('remember_me')?htmlspecialchars($this->lang->line('remember_me')):'Remember Me'?></label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="savebtn btn btn-primary btn-lg btn-block" tabindex="4">
                    <?=$this->lang->line('login')?htmlspecialchars($this->lang->line('login')):'Login'?>
                    </button>
                  </div>

                  <?php if($google_client_id){ ?>
                    <div class="form-group d-flex justify-content-center">
                      <div id="g_id_onload"
                          data-client_id="<?=$google_client_id?>"
                          data-callback="handleCredentialResponse">
                      </div>
                      <div id="g_id_onload"
                          data-client_id="<?=$google_client_id?>"
                          data-callback="handleCredentialResponse"
                          data-auto_prompt="false">
                      </div>
                      <div class="g_id_signin"
                          data-type="standard"
                          data-size="large"
                          data-theme="outline"
                          data-text="sign_in_with"
                          data-shape="rectangular"
                          data-logo_alignment="left">
                      </div>
                    </div>
                  <?php } ?>

                  <?php if(!turn_off_new_user_registration()){ ?>
                  <div class="text-muted text-center">
                  <?=$this->lang->line('dont_have_an_account')?$this->lang->line('dont_have_an_account'):"Don't have an account?"?> <a href="<?=base_url('auth/register');?>"><?=$this->lang->line('create_one')?$this->lang->line('create_one'):'Create One'?></a>
                  </div>
                  <?php } ?>

                  <div class="form-group">
                    <div class="result"><?=isset($message)?htmlspecialchars($message):'';?></div>
                  </div>
                  
                </form>
              </div>
            </div>

            

            <div class="simple-footer">
              <?=htmlspecialchars(footer_text())?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <form class="modal-part" id="modal-forgot-password-part" action="<?=base_url('auth/forgot-password')?>" class="needs-validation" novalidate="" data-title="<?=$this->lang->line('forgot_password')?htmlspecialchars($this->lang->line('forgot_password')):'Forgot Password'?>" data-btn="<?=$this->lang->line('send')?htmlspecialchars($this->lang->line('send')):'Send'?>">
    <p><?=$this->lang->line('we_will_send_a_link_to_reset_your_password')?$this->lang->line('we_will_send_a_link_to_reset_your_password'):'We will send a link to reset your password.'?></p>
    <div class="form-group">
      <label><?=$this->lang->line('email')?htmlspecialchars($this->lang->line('email')):'Email'?></label>
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fas fa-envelope"></i>
          </div>
        </div>
        <input type="text" class="form-control" placeholder="<?=$this->lang->line('email')?htmlspecialchars($this->lang->line('email')):'Email'?>" name="identity">
      </div>
    </div>
  </form>

<script>
  site_key = '<?php echo get_google_recaptcha_site_key(); ?>';
</script>

<?php $recaptcha_site_key = get_google_recaptcha_site_key(); if($recaptcha_site_key){ ?>
  <script src="https://www.google.com/recaptcha/api.js?render=<?=htmlspecialchars($recaptcha_site_key)?>"></script>
<?php } ?>

<?php $this->load->view('includes/js'); ?>

<?php if($google_client_id){ ?>
  
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
  function handleCredentialResponse(response) {

    const data = response.credential;
    var base64Url = data.split('.')[1];
    var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    var Payload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));

    const responsePayload = JSON.parse(Payload);

    if(responsePayload && responsePayload.email && responsePayload.given_name && responsePayload.family_name){
      if(site_key){
        grecaptcha.ready(function() {
          grecaptcha.execute(site_key, {action: 'register_form'}).then(function(token) {
            $.ajax({
                type: "POST",
                url: base_url+'auth/social_auth', 
                data: "email="+responsePayload.email+"&first_name="+responsePayload.given_name+"&last_name="+responsePayload.family_name+"&token="+token+"&action=register_form",
                dataType: "json",
                success: function(result) 
                {	
                  if(result['error'] == false){
                      location.reload();
                  }else{
                    iziToast.error({
                        title: result['message'],
                        message: "",
                        position: 'topRight'
                    });
                  }
                }        
            });
          });
        });
      }else{
        $.ajax({
            type: "POST",
            url: base_url+'auth/social_auth', 
            data: "email="+responsePayload.email+"&first_name="+responsePayload.given_name+"&last_name="+responsePayload.family_name,
            dataType: "json",
            success: function(result) 
            {	
              if(result['error'] == false){
                  location.reload();
              }else{
                iziToast.error({
                    title: result['message'],
                    message: "",
                    position: 'topRight'
                });
              }
            }        
        });
      }
    }else{
      iziToast.error({
          title: something_wrong_try_again,
          message: "",
          position: 'topRight'
      });
    }
  }
</script>
<?php } ?>

</body>
</html>