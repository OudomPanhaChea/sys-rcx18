<?php $google_client_id = get_google_client_id(); ?>
<?php $this->load->view('includes/head'); ?>
</head>
<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="login-brand">
              <a href="<?=base_url()?>">
                <img src="<?=base_url('assets/uploads/logos/'.full_logo());?>" alt="logo" width="40%">
              </a>
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4><?=$this->lang->line('register')?$this->lang->line('register'):'Register'?></h4></div>

              <div class="card-body">
                <form id="register" method="POST" action="<?=base_url('auth/create_user')?>" class="needs-validation" novalidate="">
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="frist_name"><?=$this->lang->line('first_name')?$this->lang->line('first_name'):'First Name'?></label>
                      <input type="hidden" name="groups" value="1">
                      <input type="hidden" name="new_register" value="1">
                      <input type="hidden" id="plan_id" name="plan_id" value="<?=$plan_id?>">
                      <input id="frist_name" type="text" class="form-control" name="first_name" tabindex="1" required  autofocus>
                    </div>
                    <div class="form-group col-6">
                      <label for="last_name"><?=$this->lang->line('last_name')?$this->lang->line('last_name'):'Last Name'?></label>
                      <input id="last_name" type="text" class="form-control" name="last_name" tabindex="2" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="email"><?=$this->lang->line('email')?$this->lang->line('email'):'Email'?></label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="3" required>
                    
                  </div>
                  
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="password" class="d-block"><?=$this->lang->line('password')?$this->lang->line('password'):'Password'?></label>
                      <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" tabindex="4" required >
                      <div id="pwindicator" class="pwindicator">
                        <div class="bar"></div>
                        <div class="label"></div>
                      </div>
                    </div>
                    <div class="form-group col-6">
                      <label for="password2" class="d-block"><?=$this->lang->line('password_confirmation')?$this->lang->line('password_confirmation'):'Password Confirmation'?></label>
                      <input id="password2" type="password" class="form-control" name="password_confirm" tabindex="5" required >
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="agree" class="custom-control-input" id="agree_regi" checked>
                      <label class="custom-control-label" for="agree_regi"><?=$this->lang->line('i_agree_to_the_terms_and_conditions')?htmlspecialchars($this->lang->line('i_agree_to_the_terms_and_conditions')):'I agree to the terms and conditions'?></label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="savebtn btn btn-primary btn-lg btn-block" tabindex="6">
                    <?=$this->lang->line('register')?$this->lang->line('register'):'Register'?>
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

                  <div class="form-group">
                    <div class="result"><?=isset($message)?htmlspecialchars($message):'';?></div>
                  </div>

                  <div class="text-muted text-center">
                  <?=$this->lang->line('already_have_an_account')?$this->lang->line('already_have_an_account'):'Already have an account?'?> <a href="<?=base_url('auth');?>"><?=$this->lang->line('login_here')?$this->lang->line('login_here'):'Login Here'?></a>
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

    var plan_id = $("#plan_id").val();

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
                  data: "email="+responsePayload.email+"&first_name="+responsePayload.given_name+"&last_name="+responsePayload.family_name+"&token="+token+"&action=register_form&plan_id="+plan_id,
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
              data: "email="+responsePayload.email+"&first_name="+responsePayload.given_name+"&last_name="+responsePayload.family_name+"&plan_id="+plan_id,
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