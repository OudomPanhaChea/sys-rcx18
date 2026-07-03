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
                <a href="<?=base_url('broadcast')?>" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
              </div>
              <h1><?=$this->lang->line('create')?$this->lang->line('create'):'Create'?> <?=$this->lang->line('broadcast')?htmlspecialchars($this->lang->line('broadcast')):'Broadcast'?></h1>
              <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
                <div class="breadcrumb-item active"><a href="<?=base_url('broadcast')?>"><?=$this->lang->line('broadcast')?$this->lang->line('broadcast'):'Broadcast'?></a></div>
                <div class="breadcrumb-item"><?=$this->lang->line('create')?$this->lang->line('create'):'Create'?> <?=$this->lang->line('broadcast')?htmlspecialchars($this->lang->line('broadcast')):'Broadcast'?></div>
              </div>
            </div>

            <div class="section-body">
              <div class="row">

                <div class="col-md-12" id="home-card">
                  <div class="card card-primary">
                    <div class="card-body">
                      <form action="<?=base_url('broadcast/create')?>" method="POST" id="broadcast-form">


                      
                        <div class="form-group">
                          <label><?=$this->lang->line('select_users')?htmlspecialchars($this->lang->line('select_users')):'Select Users'?></label>
                          <select class="form-control select2" name="to_user[]" multiple="">

                            <option value="all"><?=$this->lang->line('all')?htmlspecialchars($this->lang->line('all')):'All'?></option>

                            <option value="saas_admins"><?=$this->lang->line('saas_admins')?htmlspecialchars($this->lang->line('saas_admins')):'SaaS Admins'?></option>

                            <option value="subscribers"><?=$this->lang->line('subscribers')?htmlspecialchars($this->lang->line('subscribers')):'Subscribers'?></option>

                            <option value="users"><?=$this->lang->line('users')?htmlspecialchars($this->lang->line('users')):'Users'?></option>

                            <?php foreach($all_users as $all_user){ ?>
                              
                              <option value="<?=$all_user->email?>"><?=$all_user->first_name?> <?=$all_user->last_name?> - <?=$all_user->email?></option>

                            <?php } ?>
                          
                          </select>
                        </div>

                        <div class="form-group">
                          <label><?=$this->lang->line('subject')?htmlspecialchars($this->lang->line('subject')):'Subject'?></label>
                          <input type="text" name="subject" class="form-control">
                        </div>

                        <div class="form-group">
                          <label><?=$this->lang->line('email_message')?htmlspecialchars($this->lang->line('email_message')):'Email Message'?></label>
                          <textarea name="message" class="form-control">
                            
                          </textarea>
                        </div>
                        
                        <div class="card-footer bg-whitesmoke text-md-right">
                            <button class="btn btn-primary savebtn"><?=$this->lang->line('send_message')?htmlspecialchars($this->lang->line('send_message')):'Send Message'?></button>
                        </div>
                        <div class="result"></div>
                      </form>
                    </div>
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
<script src="<?=base_url('assets/modules/tinymce/js/tinymce/tinymce.min.js')?>"></script>
<script>

tinymce.init({
  selector: 'textarea',
  height: 240,
  plugins: 'print preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount textpattern noneditable help charmap  emoticons code',
  menubar: 'edit view insert format tools table tc help',
  toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor permanentpen removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment code',
  setup: function (editor) {
    editor.on("change keyup", function (e) {
        tinyMCE.triggerSave(); 
    });
  },
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});

</script>
</body>
</html>