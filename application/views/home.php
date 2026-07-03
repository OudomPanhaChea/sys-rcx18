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
            <h1><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></h1>
          </div>

          <?php if($this->ion_auth->is_admin()){ 
            $my_plan = get_current_plan();
            if($my_plan && !is_null($my_plan['end_date']) && ($my_plan['expired'] == 0 || $my_plan['end_date'] <= date('Y-m-d',date(strtotime("+".alert_days()." day", strtotime(date('Y-m-d'))))))){ 
          ?>
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="hero text-white bg-danger">
                <div class="hero-inner">
                  <h2><?=$this->lang->line('alert')?$this->lang->line('alert'):'Alert...'?></h2>
                  <?php 
                    if($my_plan['expired'] == 0){ 
                  ?>
                    <p class="lead"><?=$this->lang->line('your_subscription_plan_has_been_expired_on_date')?$this->lang->line('your_subscription_plan_has_been_expired_on_date'):'Your subscription plan has been expired on date'?> <?=htmlspecialchars(format_date($my_plan["end_date"],system_date_format()))?>. <?=$this->lang->line('renew_it_now')?$this->lang->line('renew_it_now'):'Renew it now.'?></p>
                  <?php }elseif($my_plan['end_date'] <= date('Y-m-d',date(strtotime("+".alert_days()." day", strtotime(date('Y-m-d')))))){ ?>
                    <p class="lead"><?=$this->lang->line('your_current_subscription_plan_is_expiring_on_date')?$this->lang->line('your_current_subscription_plan_is_expiring_on_date'):'Your current subscription plan is expiring on date'?> <?=htmlspecialchars(format_date($my_plan["end_date"],system_date_format()))?>.</p>
                  <?php } ?>
                  <div class="mt-4">
                    <a href="<?=base_url('plans')?>" class="btn btn-outline-white btn-lg btn-icon icon-left"><i class="fas fa-arrow-right"></i> <?=$this->lang->line('renew_plan')?$this->lang->line('renew_plan'):'Renew Plan.'?></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } } ?>

          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
              <div class="card card-primary card-statistic-2">
                <div class="card-stats">
                  <div class="card-stats-title"><?=$this->lang->line('project_statistics')?$this->lang->line('project_statistics'):'Project Statistics'?> - 
                    <div class="dropdown d-inline">
                      <a href="<?=base_url('projects')?>"><?=$this->lang->line('view')?$this->lang->line('view'):'View'?></a>
                    </div>
                  </div>
                  <div class="card-stats-items mb-3">
                    <div class="card-stats-item text-danger">
                      <div class="card-stats-item-count">
                      <?php
                        if($this->ion_auth->is_admin()){
                          $pendingP = get_count('id','projects','(status=1 OR status=2) AND saas_id='.htmlspecialchars($this->session->userdata('saas_id')));
                        }elseif($this->ion_auth->in_group(4)){
                          $pendingP =  get_count('id','projects','(status=1 OR status=2) AND client_id='.htmlspecialchars($this->session->userdata('user_id')));
                        }else{
                          $pendingP = get_count('p.id','projects p LEFT JOIN project_users pu ON p.id=pu.project_id','(status=1 OR status=2) AND pu.user_id='.htmlspecialchars($this->session->userdata('user_id')));
                        }
                        echo htmlspecialchars($pendingP);
                      ?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('pending')?$this->lang->line('pending'):'Pending'?></div>
                    </div>
                    <div class="card-stats-item text-success">
                      <div class="card-stats-item-count">
                      <?php
                        if($this->ion_auth->is_admin()){
                          $completedP = get_count('id','projects','status=3 AND saas_id='.htmlspecialchars($this->session->userdata('saas_id')));
                        }elseif($this->ion_auth->in_group(4)){
                          $completedP =  get_count('id','projects','status=3 AND client_id='.htmlspecialchars($this->session->userdata('user_id')));
                        }else{
                          $completedP = get_count('p.id','projects p LEFT JOIN project_users pu ON p.id=pu.project_id','status=3 AND pu.user_id='.htmlspecialchars($this->session->userdata('user_id')));
                        }
                        echo htmlspecialchars($completedP);
                      ?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('completed')?$this->lang->line('completed'):'Completed'?></div>
                    </div>
                    <div class="card-stats-item">
                      <div class="card-stats-item-count text-primary">
                      <?=htmlspecialchars($pendingP)+htmlspecialchars($completedP)?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('total')?$this->lang->line('total'):'Total'?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-lg-4 col-md-4 col-sm-12">
              <div class="card card-primary card-statistic-2">
                <div class="card-stats">
                  <div class="card-stats-title"><?=$this->lang->line('tasks_statistics')?$this->lang->line('tasks_statistics'):'Tasks Statistics'?> - 
                    <div class="dropdown d-inline">
                      <a href="<?=base_url('projects/tasks')?>"><?=$this->lang->line('view')?$this->lang->line('view'):'View'?></a>
                      
                    </div>
                  </div>
                  <div class="card-stats-items mb-3">
                    <div class="card-stats-item text-danger">
                      <div class="card-stats-item-count">
                      <?php
                          if($this->ion_auth->is_admin()){
                            $pendingT =  get_count('id','tasks','(status=1 OR status=2 OR status=3) AND saas_id='.htmlspecialchars($this->session->userdata('saas_id')));
                          }elseif($this->ion_auth->in_group(4)){
                            $pendingT = get_count('t.id','tasks t LEFT JOIN projects p on t.project_id = p.id','(t.status=1 OR t.status=2 OR t.status=3) AND p.client_id = '.htmlspecialchars($this->session->userdata('user_id')));
                          }else{
                            $pendingT =  get_count('t.id','tasks t LEFT JOIN task_users tu ON t.id=tu.task_id','(status=1 OR status=2 OR status=3) AND tu.user_id='.htmlspecialchars($this->session->userdata('user_id')));
                          }
                          echo htmlspecialchars($pendingT);
                      ?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('pending')?$this->lang->line('pending'):'Pending'?></div>
                    </div>
                    <div class="card-stats-item text-success">
                      <div class="card-stats-item-count">
                      <?php
                          if($this->ion_auth->is_admin()){
                            $completedT =  get_count('id','tasks','status=4 AND saas_id='.$this->session->userdata('saas_id'));
                          }elseif($this->ion_auth->in_group(4)){
                            $completedT = get_count('t.id','tasks t LEFT JOIN projects p on t.project_id = p.id','t.status=4 AND p.client_id = '.htmlspecialchars($this->session->userdata('user_id')));
                          }else{
                            $completedT =  get_count('t.id','tasks t LEFT JOIN task_users tu ON t.id=tu.task_id','status=4 AND tu.user_id='.$this->session->userdata('user_id'));
                          }
                          echo htmlspecialchars($completedT);
                      ?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('completed')?$this->lang->line('completed'):'Completed'?></div>
                    </div>
                    <div class="card-stats-item text-primary">
                      <div class="card-stats-item-count">
                      <?=htmlspecialchars($pendingT)+htmlspecialchars($completedT)?>
                      </div>
                      <div class="card-stats-item-label"><?=$this->lang->line('total')?$this->lang->line('total'):'Total'?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <?php if($this->ion_auth->is_admin() || $this->ion_auth->in_group(4)){ 
              $get_my_invoices_details = get_my_invoices_details();
            ?>

              <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-primary card-statistic-2">
                  <div class="card-stats">
                    <div class="card-stats-title"><?=$this->lang->line('invoices')?$this->lang->line('invoices'):'Invoices'?> (<?=get_currency('currency_symbol')?>)</div>
                    <div class="card-stats-items mb-3">
                      <div class="card-stats-item text-danger">
                        <div class="card-stats-item-count"><?=htmlspecialchars($get_my_invoices_details['due'])?></div>
                        <div class="card-stats-item-label"><?=$this->lang->line('due')?$this->lang->line('due'):'Due'?></div>
                      </div>
                      <div class="card-stats-item text-success">
                        <div class="card-stats-item-count"><?=htmlspecialchars($get_my_invoices_details['paid'])?></div>
                        <div class="card-stats-item-label"><?=$this->lang->line('paid')?$this->lang->line('paid'):'Paid'?></div>
                      </div>
                      <div class="card-stats-item text-primary">
                        <div class="card-stats-item-count"><?=htmlspecialchars($get_my_invoices_details['total'])?></div>
                        <div class="card-stats-item-label"><?=$this->lang->line('total')?$this->lang->line('total'):'Total'?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php }else{ if(is_module_allowed('attendance')){ ?>
              <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-primary card-statistic-1">
                  <div class="card-icon shadow-primary bg-primary">
                    <i class="fas fa-sign-out-alt"></i>
                  </div>
                  <div class="card-wrap">
                    <?php if($my_att_running){ ?>
                      <div class="card-header">
                        <h4><?=$this->lang->line('check_in_at')?htmlspecialchars($this->lang->line('check_in_at')):'You checked in at'?> <?=format_date($my_att_running[0]['check_in'],system_date_format()." ".system_time_format());?></h4>
                      </div>
                      <div class="card-body">
                        <a class="btn btn-sm btn-outline-danger" href="<?=base_url('attendance/in-out')?>">
                          <?=$this->lang->line('check_out')?htmlspecialchars($this->lang->line('check_out')):'Check Out'?>
                        </a>
                      </div>
                    <?php }else{ ?>
                      <div class="card-header">
                        <h4><?=$this->lang->line('you_are_currently_checked_out')?htmlspecialchars($this->lang->line('you_are_currently_checked_out')):'You are currently checked out'?></h4>
                      </div>
                      <div class="card-body">
                        <a class="btn btn-sm btn-outline-danger" href="<?=base_url('attendance/in-out')?>">
                          <?=$this->lang->line('check_in')?htmlspecialchars($this->lang->line('check_in')):'Check In'?>
                        </a>
                      </div>
                    <?php } ?>

                  </div>
                </div>
              </div>
            <?php } } ?>
  
          </div>
          
          <div class="row">
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h4><?=$this->lang->line('project_statistics')?htmlspecialchars($this->lang->line('project_statistics')):'Project Statistics'?></h4>
                </div>
                <div class="card-body">
                  <canvas id="project_chart" height="auto"></canvas>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h4><?=$this->lang->line('tasks_statistics')?$this->lang->line('tasks_statistics'):'Tasks Statistics'?></h4>
                </div>
                <div class="card-body">
                  <canvas id="task_chart" height="auto"></canvas>
                </div>
              </div>
            </div>
          </div>
          
        </section>
      </div>
    
    <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

<form action="<?=base_url('meetings/edit')?>" method="POST" class="modal-part" id="modal-edit-meetings-part" data-title="<?=$this->lang->line('edit')?$this->lang->line('edit'):'Edit'?>" data-btn="<?=$this->lang->line('update')?$this->lang->line('update'):'Update'?>">

<input type="hidden" name="update_id" id="update_id">

<div class="form-group">
  <label><?=$this->lang->line('title')?$this->lang->line('title'):'Title'?><span class="text-danger">*</span></label>
  <input type="text" name="title" id="title" class="form-control" required="">
</div>

<div class="form-group">
  <label><?=$this->lang->line('starting_time')?$this->lang->line('starting_time'):'Starting Time'?><span class="text-danger">*</span></label>
  <input type="text" name="starting_date_and_time" id="starting_date_and_time" class="form-control datetimepicker">
</div>
<div class="form-group">
  <label><?=$this->lang->line('duration')?$this->lang->line('duration'):'Duration (Minutes)'?><span class="text-danger">*</span></label>
  <input type="number" pattern="[0-9]" name="duration" id="duration" class="form-control">
</div>

<div class="form-group">
  <label><?=$this->lang->line('users')?$this->lang->line('users'):'Users'?><span class="text-danger">*</span></label>
  <select name="users[]" id="users" class="form-control select2" multiple="">
    <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
    <option value="<?=htmlspecialchars($system_user->id)?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
    <?php } } ?>
  </select>
</div>

<div class="form-group">
  <label><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?><span class="text-danger">*</span></label>
  <select name="status" id="status" class="form-control select2">
    <option value="0"><?=$this->lang->line('scheduled')?$this->lang->line('scheduled'):'Scheduled'?></option>
    <option value="1"><?=$this->lang->line('running')?$this->lang->line('running'):'Running'?></option>
    <option value="2"><?=$this->lang->line('completed')?$this->lang->line('completed'):'Completed'?></option>
  </select>
</div>

</form>

<div id="modal-edit-meetings"></div>

<?php
  foreach($project_status as $project_title){
    $tmpP[] = $project_title['title'];
    if($this->ion_auth->is_admin()){
      $tmpPV[] =  get_count('id','projects','status='.$project_title['id'].' AND saas_id='.htmlspecialchars($this->session->userdata('saas_id')));
    }elseif($this->ion_auth->in_group(4)){
      $tmpPV[] =  get_count('id','projects','client_id='.htmlspecialchars($this->session->userdata('user_id')).' AND status='.htmlspecialchars($project_title['id']));
    }else{
      $tmpPV[] =  get_count('p.id','projects p LEFT JOIN project_users pu ON p.id=pu.project_id','status='.$project_title['id'].' AND pu.user_id='.htmlspecialchars($this->session->userdata('user_id')));
    }
  }

  foreach($task_status as $task_title){
    $tmpT[] = $task_title['title'];
    if($this->ion_auth->is_admin()){
      $tmpTV[] =  get_count('id','tasks','status='.htmlspecialchars($task_title['id']).' AND saas_id='.htmlspecialchars($this->session->userdata('saas_id')));
    }elseif($this->ion_auth->in_group(4)){
      $tmpTV[] =  get_count('t.id','tasks t LEFT JOIN projects p on t.project_id = p.id','p.client_id = '.htmlspecialchars($this->session->userdata('user_id')).' AND t.status = '.htmlspecialchars($task_title['id']));
    }else{
      $tmpTV[] =  get_count('t.id','tasks t LEFT JOIN task_users tu ON t.id=tu.task_id','status='.htmlspecialchars($task_title['id']).' AND tu.user_id='.htmlspecialchars($this->session->userdata('user_id')));
    }
  }

?>

<script>
  project_status = '<?=json_encode($tmpP)?>';
  project_status_values = '<?=json_encode($tmpPV)?>';
  task_status = '<?=json_encode($tmpT)?>';
  task_status_values = '<?=json_encode($tmpTV)?>';
</script>

<?php $this->load->view('includes/js'); ?>
<script src="<?=base_url('assets/js/page/home.js')?>"></script>

<script>
  function queryParams(p){
    return {
      "to_id": $('#client').val(),
      "status": $('#status_filter').val(),
      limit:p.limit,
      sort:p.sort,
      order:p.order,
      offset:p.offset,
      search:p.search
    };
  }
  $('#tool').on('change',function(e){
    $('#video_meetings_list').bootstrapTable('refresh');
  });
</script>

</body>
</html>
