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
            <?=$this->lang->line('leaves')?$this->lang->line('leaves'):'Leaves'?> 
              <?php if (!$this->ion_auth->in_group(4)){ ?>
                <a href="#" id="modal-add-leaves" class="btn btn-sm btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> <?=$this->lang->line('create')?$this->lang->line('create'):'Create'?></a>
              <?php } ?>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('leaves')?$this->lang->line('leaves'):'Leaves'?></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row">
              <?php if($this->ion_auth->is_admin()){ ?>
                <div class="form-group col-md-12">
                  <select class="form-control select2 leaves_filter" id="leaves_filter_user">
                    <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
                    <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
                    <option value="<?=$system_user->id?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
                    <?php } } ?>
                  </select>
                </div>
              <?php } ?>
            </div>
            <div class="row">
                  <div class="col-md-12">
                    <div class="card card-primary">
                      <div class="card-body"> 
                        <table class='table-striped' id='leaves_list'
                          data-toggle="table"
                          data-url="<?=base_url('leaves/get_leaves')?>"
                          data-click-to-select="true"
                          data-side-pagination="server"
                          data-pagination="true"
                          data-page-list="[5, 10, 20, 50, 100, 200]"
                          data-search="true" data-show-columns="true"
                          data-show-refresh="false" data-trim-on-search="false"
                          data-sort-name="id" data-sort-order="desc"
                          data-mobile-responsive="true"
                          data-toolbar="" data-show-export="false"
                          data-maintain-selected="true"
                          data-export-types='["txt","excel"]'
                          data-export-options='{
                            "fileName": "leaves-list",
                            "ignoreColumn": ["state"] 
                          }'
                          data-query-params="queryParams">
                          <thead>
                            <tr>
                              <?php if($this->ion_auth->is_admin()){ ?>
                                <th data-field="user" data-sortable="true"><?=$this->lang->line('team_members')?$this->lang->line('team_members'):'Team Members'?></th>
                              <?php } ?>
                              <th data-field="leave_days" data-sortable="true"><?=$this->lang->line('leave_days')?$this->lang->line('leave_days'):'Leave Days'?></th>
                              <th data-field="starting_date" data-sortable="true"><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?></th>
                              <th data-field="ending_date" data-sortable="true"><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?></th>
                              <th data-field="leave_reason" data-sortable="true" data-visible="false"><?=$this->lang->line('leave_reason')?$this->lang->line('leave_reason'):'Leave Reason'?></th>
                              <th data-field="status" data-sortable="true"><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?></th>
                              <th data-field="action" data-sortable="false"><?=$this->lang->line('action')?$this->lang->line('action'):'Action'?></th>
                            </tr>
                          </thead>
                        </table>
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


<form action="<?=base_url('leaves/create')?>" method="POST" class="modal-part" id="modal-add-leaves-part" data-title="<?=$this->lang->line('create')?$this->lang->line('create'):'Create'?>" data-btn="<?=$this->lang->line('create')?$this->lang->line('create'):'Create'?>">

  <?php if($this->ion_auth->is_admin()){ ?>
    <div class="form-group">
      <label><?=$this->lang->line('team_members')?$this->lang->line('team_members'):'users'?></label>
      <select name="user_id" class="form-control select2">
        <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
        <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
        <option value="<?=$system_user->id?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
        <?php } } ?>
      </select>
    </div>
  <?php } ?>
  
  <div class="form-group">
    <label><?=$this->lang->line('leave_days')?$this->lang->line('leave_days'):'Leave Days'?><span class="text-danger">*</span></label>
    <input type="number" pattern="[0-9]" name="leave_days" class="form-control" required="">
  </div>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?><span class="text-danger">*</span></label>
      <input type="text" name="starting_date" class="form-control datepicker" required="">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?><span class="text-danger">*</span></label>
      <input type="text" name="ending_date" class="form-control datepicker" required="">
    </div>
  </span>

  <div class="form-group">
    <label><?=$this->lang->line('leave_reason')?$this->lang->line('leave_reason'):'Leave Reason'?><span class="text-danger">*</span></label>
    <textarea type="text" name="leave_reason" class="form-control" required=""></textarea>
  </div>

</form>

<form action="<?=base_url('leaves/edit')?>" method="POST" class="modal-part" id="modal-edit-leaves-part" data-title="<?=$this->lang->line('edit')?$this->lang->line('edit'):'Edit'?>" data-btn="<?=$this->lang->line('update')?$this->lang->line('update'):'Update'?>">
  <input type="hidden" name="update_id" id="update_id" value="">

  <?php if($this->ion_auth->is_admin()){ ?>
    <div class="form-group">
      <label><?=$this->lang->line('team_members')?$this->lang->line('team_members'):'users'?></label>
      <select name="user_id" id="user_id" class="form-control select2">
        <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
        <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
        <option value="<?=$system_user->id?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
        <?php } } ?>
      </select>
    </div>
  <?php } ?>
  
  <div class="form-group">
    <label><?=$this->lang->line('leave_days')?$this->lang->line('leave_days'):'Leave Days'?><span class="text-danger">*</span></label>
    <input type="number" pattern="[0-9]" name="leave_days" id="leave_days" class="form-control" required="">
  </div>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?><span class="text-danger">*</span></label>
      <input type="text" name="starting_date" id="starting_date" class="form-control datepicker" required="">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?><span class="text-danger">*</span></label>
      <input type="text" name="ending_date" id="ending_date" class="form-control datepicker" required="">
    </div>
  </span>

  <div class="form-group">
    <label><?=$this->lang->line('leave_reason')?$this->lang->line('leave_reason'):'Leave Reason'?><span class="text-danger">*</span></label>
    <textarea type="text" name="leave_reason" id="leave_reason" class="form-control" required=""></textarea>
  </div>
  
  <?php if($this->ion_auth->is_admin()){ ?>
    <div class="form-group">
      <label><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?></label>
      <select name="status" id="status" class="form-control select2">
        <option value=""><?=$this->lang->line('select_status')?$this->lang->line('select_status'):'Select Status'?></option>
        <option value="0"><?=$this->lang->line('pending')?htmlspecialchars($this->lang->line('pending')):'Pending'?></option>
        <option value="1"><?=$this->lang->line('approved')?htmlspecialchars($this->lang->line('approved')):'Approved'?></option>
        <option value="2"><?=$this->lang->line('rejected')?htmlspecialchars($this->lang->line('rejected')):'Rejected'?></option>
      </select>
    </div>
  <?php } ?>
  
</form>

<div id="modal-edit-leaves"></div>

<?php $this->load->view('includes/js'); ?>
<script>
  function queryParams(p){
      return {
        "user_id": $('#leaves_filter_user').val(),
        limit:p.limit,
        sort:p.sort,
        order:p.order,
        offset:p.offset,
        search:p.search
      };
  }

  $(document).on('change','.leaves_filter',function(){
    $('#leaves_list').bootstrapTable('refresh');
  });
  
</script>
</body>
</html>