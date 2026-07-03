<form action="<?=base_url('settings/save-maintenance-mode-setting')?>" method="POST" id="language-form">

    <div class="card-header">
      <h4><?=$this->lang->line('maintenance_mode_title')?htmlspecialchars($this->lang->line('maintenance_mode_title')):'Maintenance Mode'?></h4>
    </div>
    <div class="card-body row">

    
      <div class="form-group col-md-12">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="<?=(isset($maintenance_mode) && !empty($maintenance_mode))?$maintenance_mode:0?>" <?=(isset($maintenance_mode) && !empty($maintenance_mode) && $maintenance_mode == 1)?'checked':''?>>
          <label class="d-block form-check-label" for="maintenance_mode"><?=$this->lang->line('active')?$this->lang->line('active'):'Active'?> <?=$this->lang->line('maintenance_mode_title')?htmlspecialchars($this->lang->line('maintenance_mode_title')):'Maintenance Mode'?>
          </label>
        </div>
      </div>


    </div>

    <div class="card-footer bg-whitesmoke text-md-right">
      <button class="btn btn-primary savebtn"><?=$this->lang->line('save_changes')?$this->lang->line('save_changes'):'Save Changes'?></button>
    </div>
    <div class="result"></div>
</form>