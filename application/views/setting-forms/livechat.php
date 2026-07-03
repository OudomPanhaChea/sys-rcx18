<form action="<?=base_url('settings/save-livechat-setting')?>" method="POST" id="language-form">
    <div class="card-body row">

      <div class="form-group col-md-12">
        <label><?=$this->lang->line('livechat')?htmlspecialchars($this->lang->line('livechat')):'LiveChat'?> <a href="https://www.tawk.to/" target="_blank">Tawk.to</a></label>
        <textarea rows="4" cols="50" type="text" name="livechat" class="form-control" placeholder="<script>
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement('script'),s0=document.getElementsByTagName('script')[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6747fb2424fdsfdsf4f5a4fdd2/dfdsfsdf';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>"><?=(isset($livechat) && !empty($livechat))?htmlspecialchars($livechat):''?></textarea>
      </div> 
      
      <div class="form-group col-md-6">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="enable_on_landing_page_frontend" name="enable_on_landing_page_frontend" value="<?=(isset($enable_on_landing_page_frontend) && !empty($enable_on_landing_page_frontend))?$enable_on_landing_page_frontend:0?>" <?=(isset($enable_on_landing_page_frontend) && !empty($enable_on_landing_page_frontend) && $enable_on_landing_page_frontend == 1)?'checked':''?>>
          <label class="d-block form-check-label" for="enable_on_landing_page_frontend"><?=$this->lang->line('enable_on_landing_page_frontend')?$this->lang->line('enable_on_landing_page_frontend'):'Enable on Landing Page - Frontend'?>
          </label>
        </div>
      </div>
      <div class="form-group col-md-6">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="enable_on_dashboard_backend" name="enable_on_dashboard_backend" value="<?=(isset($enable_on_dashboard_backend) && !empty($enable_on_dashboard_backend))?$enable_on_dashboard_backend:0?>" <?=(isset($enable_on_dashboard_backend) && !empty($enable_on_dashboard_backend) && $enable_on_dashboard_backend == 1)?'checked':''?>>
          <label class="d-block form-check-label" for="enable_on_dashboard_backend"><?=$this->lang->line('enable_on_dashboard_backend')?$this->lang->line('enable_on_dashboard_backend'):'Enable on Dashboard - Backend'?>
          </label>
        </div>
      </div>

    </div>
    <?php if($this->ion_auth->in_group(3)){ ?>
      <div class="card-footer bg-whitesmoke text-md-right">
        <button class="btn btn-primary savebtn"><?=$this->lang->line('save_changes')?$this->lang->line('save_changes'):'Save Changes'?></button>
      </div>
    <?php } ?>
    <div class="result"></div>
  </form>