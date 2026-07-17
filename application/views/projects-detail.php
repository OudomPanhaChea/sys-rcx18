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
            <?=$this->lang->line('projects_detail')?$this->lang->line('projects_detail'):'Projects Detail'?>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item active"><a href="<?=base_url('projects')?>"><?=$this->lang->line('projects')?$this->lang->line('projects'):'Projects'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('detail')?$this->lang->line('detail'):'Detail'?></div>
            </div>
          </div>
          <div class="section-body">
            <?php 
              if(isset($project[0]) && !empty($project[0])){
                $project = $project[0];
            ?>

            
            <?php if (($this->ion_auth->is_admin() || permissions('task_view')) && is_module_allowed('tasks')){ ?>
              <a href="<?=base_url("projects/tasks/".htmlspecialchars($project['id']))?>" class="btn btn-icon icon-left btn-primary"><i class="fas fa-tasks"></i> <?=$this->lang->line('tasks')?$this->lang->line('tasks'):'Tasks'?></a>
            <?php } ?>

            <a href="#" class="btn btn-icon icon-left btn-primary project-invoice-btn" data-id="<?=htmlspecialchars($project['id'])?>"><i class="fas fa-file-invoice-dollar"></i> <?=$this->lang->line('invoice')?$this->lang->line('invoice'):'Invoice'?></a>
            
            
            <?php if (($this->ion_auth->is_admin() || permissions('calendar_view')) && is_module_allowed('calendar')){ ?>
              <a href="<?=base_url("projects/calendar/".htmlspecialchars($project['id']))?>" class="btn btn-icon icon-left btn-primary"><i class="fas fa-calendar-alt"></i> <?=$this->lang->line('calendar')?$this->lang->line('calendar'):'Calendar'?></a>
            <?php } ?>

            <?php if (($this->ion_auth->is_admin() || permissions('gantt_view')) && is_module_allowed('gantt')){ ?>
              <a href="<?=base_url("projects/gantt/".htmlspecialchars($project['id']))?>" class="btn btn-icon icon-left btn-primary"><i class="fas fa-layer-group"></i> <?=$this->lang->line('gantt')?$this->lang->line('gantt'):'Gantt'?></a>
            <?php } ?>
            
            <?php if(!$this->ion_auth->in_group(4) && is_module_allowed('timesheet')){ ?>  
              <a href="<?=base_url("projects/timesheet/".htmlspecialchars($project['id']))?>" class="btn btn-icon icon-left btn-primary"><i class="fas fa-stopwatch"></i> <?=$this->lang->line('timesheet')?$this->lang->line('timesheet'):'Timesheet'?></a>
            <?php } ?>

            <?php if ($this->ion_auth->is_admin() || permissions('project_edit')){ ?>
              <a href="#" data-edit="<?=htmlspecialchars($project['id'])?>" class="btn btn-icon icon-left btn-info modal-edit-project"><i class="fas fa-edit"></i> <?=$this->lang->line('edit')?$this->lang->line('edit'):'Edit'?></a>
            <?php } ?>
            
            <?php if ($this->ion_auth->is_admin() || permissions('project_delete')){ ?>
              <a href="#" class="btn btn-icon icon-left btn-danger delete_project" data-id="<?=htmlspecialchars($project['id'])?>"><i class="fas fa-times"></i> <?=$this->lang->line('delete')?$this->lang->line('delete'):'Delete'?></a>
            <?php } ?>

              <div class="row mt-3">
              <div class="col-md-7">
                <div class="card author-box card-<?=htmlspecialchars($project['project_class'])?>">
                  <div class="card-body">
                    <div class="author-box-name">
                      <?php if($this->ion_auth->is_admin() || permissions('project_budget')){ ?>
                        <div class="float-right">
                          <?=get_currency('currency_symbol')?><?=htmlspecialchars($project['budget'])?>
                        </div>
                      <?php } ?>
                      <a><?=htmlspecialchars($project['title'])?></a>
                    </div>
                    <div class="author-box-job text-<?=htmlspecialchars($project['project_class'])?>"><?=htmlspecialchars($project['project_status'])?></div>
                    <div class="author-box-description">
                      <p class="description-wrapper"><?=htmlspecialchars($project['description'])?></p>
                    </div>
                  </div>
                </div>
              </div>
              
              <?php if(!empty($project['project_client'])){ ?>
              
              <div class="col-md-5">
                <div class="card card-<?=htmlspecialchars($project['project_class'])?>">
                  <div class="card-header">
                    <h4><?=$this->lang->line('client_detail')?$this->lang->line('client_detail'):'Client Detail'?></h4>
                  </div>
                  <div class="card-body pb-0">
                    <div class="profile-widget mt-0">
                    <div class="profile-widget-header">
                      <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('name')?$this->lang->line('name'):'Name'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['project_client']->first_name)?> <?=htmlspecialchars($project['project_client']->last_name)?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('company_name')?$this->lang->line('company_name'):'Company Name'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['project_client']->company)?></div>
                        </div>
                      </div>
                    </div>
                    <div class="profile-widget-header">
                      <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('email')?$this->lang->line('email'):'Email'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['project_client']->email)?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('mobile')?$this->lang->line('mobile'):'Mobile'?></div>
                          <div class="profile-widget-item-value"><?=$project['project_client']->phone?htmlspecialchars($project['project_client']->phone):'No Number'?></div>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>


              <div class="col-md-<?=!empty($project['project_client'])?12:5?>">
                <div class="card <?=!empty($project['project_client'])?'':'card-'.htmlspecialchars($project['project_class'])?>">
                  <div class="card-header">
                    <h4><?=$this->lang->line('task_overview')?$this->lang->line('task_overview'):'Task Overview'?></h4>
                  </div>
                  <div class="card-body pb-0">
                    <div class="profile-widget mt-0">
                    <div class="profile-widget-header">
                      <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('days')?$this->lang->line('days'):'Days'?> <?=htmlspecialchars($project['days_status'])?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['days_count'])?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['starting_date'])?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['ending_date'])?></div>
                        </div>
                      </div>
                    </div>
                    <div class="profile-widget-header">
                      <div class="profile-widget-items">
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('total_tasks')?$this->lang->line('total_tasks'):'Total Tasks'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['total_tasks'])?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('completed_tasks')?$this->lang->line('completed_tasks'):'Completed Tasks'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['completed_tasks'])?></div>
                        </div>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('pending_tasks')?$this->lang->line('pending_tasks'):'Pending Tasks'?></div>
                          <div class="profile-widget-item-value"><?=htmlspecialchars($project['total_tasks'])-htmlspecialchars($project['completed_tasks'])?></div>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h4><?=$this->lang->line('project_users')?$this->lang->line('project_users'):'Project Users'?></h4>
                  </div>
                  <div class="card-body"> 
                    <table class='table-striped' id='users_list'
                      data-toggle="table"
                      data-url="<?=base_url('projects/get_project_users/'.$project['id'])?>"
                      data-click-to-select="true"
                      data-side-pagination="server"
                      data-pagination="false"
                      data-page-list="[5, 10, 20, 50, 100, 200]"
                      data-search="false" data-show-columns="false"
                      data-show-refresh="false" data-trim-on-search="false"
                      data-sort-name="first_name" data-sort-order="asc"
                      data-mobile-responsive="true"
                      data-toolbar="" data-show-export="false"
                      data-maintain-selected="true"
                      data-export-types='["txt","excel"]'
                      data-export-options='{
                        "fileName": "users-list",
                        "ignoreColumn": ["state"] 
                      }'
                      data-query-params="queryParams">
                      <thead>
                        <tr>
                          <th data-field="full_name" data-sortable="true"><?=$this->lang->line('name')?$this->lang->line('name'):'Name'?></th>
                          <th data-field="email" data-sortable="true"><?=$this->lang->line('email')?$this->lang->line('email'):'Email'?></th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
								<div class="col-md-12">
                  <div class="card card-primary" id="project-comment-card">
                    <div class="card-header">
                      <h4><?=$this->lang->line('comments')?$this->lang->line('comments'):'Comments'?></h4>
                    </div>
                    <div class="card-body"> 
                      <?php
                        if($project_comments){ 
                          
                        foreach($project_comments as $project_comment){ 
                          $profile = '';
                          $file_upload_path = '';
                          if($project_comment['profile']){

                            if(file_exists('assets/uploads/profiles/'.$project_comment['profile'])){
                              $file_upload_path = base_url('assets/uploads/profiles/'.$project_comment['profile']);
                            }else{
                              $file_upload_path = base_url('assets/uploads/f'.($this->session->userdata('saas_id')).'/profiles/'.$project_comment['profile']);
                            }

                            $profile = '<figure class="avatar avatar-md mr-3">
                            <img src="'.$file_upload_path.'" alt="'.$project_comment['first_name'].' '.$project_comment['last_name'].'">
                            </figure>';
                          }else{
                            $profile = '<figure class="avatar avatar-md bg-primary text-white mr-3" data-initial="'.$project_comment['short_name'].'"></figure>';
                          }
                          $can_delete = '';
                          if($project_comment['can_delete']){
                            $can_delete = '<div class="float-right text-primary"><a href="#" class="btn btn-icon btn-sm btn-danger delete_comment" data-id="'.$project_comment['id'].'" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a></div>';
                          }

                          ?>

                          <ul class="list-unstyled list-unstyled-border mt-3">
                            <li class="media"><?=$profile?>
                              <div class="media-body">
                              <div class="float-right text-primary"><?=$project_comment['created']?></div>
                              <div class="media-title"><?=$project_comment['first_name']?> <?=$project_comment['last_name']?></div><?=$can_delete?>
                              <span class="text-muted"><?=$project_comment['message']?></span>
                              </div>
                            </li>
                          </ul>

                      <?php } } ?>
                      

                      <form action="<?=base_url('projects/create-project-comment')?>" method="POST" id="project-comment-form">
                        <div class="p-0 d-flex">
                          <input type="hidden" name="comment_project_id" value="<?=$project['id']?>">
                          <input type="text" name="message" id="message" class="form-control" placeholder="<?=$this->lang->line('type_your_message')?$this->lang->line('type_your_message'):'Type your message'?>">
                          <button type="submit" class="btn btn-primary savebtn">
                            <i class="far fa-paper-plane"></i>
                          </button>
                        </div>
                        <div class="result mt-2"></div>
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

<form action="<?=base_url('projects/edit-project')?>" method="POST"  class="modal-part" id="modal-edit-project-part" data-title="<?=$this->lang->line('edit_project')?$this->lang->line('edit_project'):'Edit Project'?>" data-btn="<?=$this->lang->line('update')?$this->lang->line('update'):'Update'?>">
  <input type="hidden" name="update_id" id="update_id">
  <div class="form-group">
    <label><?=$this->lang->line('project_title')?$this->lang->line('project_title'):'Project Title'?><span class="text-danger">*</span></label>
    <input type="text" name="title" id="title" class="form-control" required="">
  </div>
  <div class="form-group">
    <label><?=$this->lang->line('description')?$this->lang->line('description'):'Description'?><span class="text-danger">*</span></label>
    <textarea type="text" name="description" id="description" class="form-control"></textarea>
  </div>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?><span class="text-danger">*</span></label>
      <input type="text" name="starting_date" id="starting_date" class="form-control datepicker">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?><span class="text-danger">*</span></label>
      <input type="text" name="ending_date" id="ending_date" class="form-control datepicker">
    </div>
  </span>
  
  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('budget')?$this->lang->line('budget'):'Budget'?> - <?=get_currency('currency_code')?></label>
      <input type="number" pattern="[0-9]" name="budget" id="budget" class="form-control">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('booking')?$this->lang->line('booking'):'Booking'?> (<?=get_currency('currency_symbol')?>)</label>
      <input type="number" step="any" min="0" name="booking" id="booking" class="form-control">
    </div>
  </span>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('account_url')?$this->lang->line('account_url'):'Account URL'?></label>
      <input type="url" name="account_url" id="account_url" class="form-control" placeholder="https://">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('username_or_id')?$this->lang->line('username_or_id'):'Username or ID'?></label>
      <input type="text" name="account_username" id="account_username" class="form-control">
    </div>
  </span>

  <div class="form-group">
    <label><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?><span class="text-danger">*</span></label>
    <select name="status" id="status" class="form-control select2">
      <?php foreach($project_status as $status){ ?>
      <option value="<?=htmlspecialchars($status['id'])?>"><?=htmlspecialchars($status['title'])?></option>
      <?php } ?>
    </select>
  </div>

  <div class="form-group">
    <label><?=$this->lang->line('project_users')?$this->lang->line('project_users'):'Project Users'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project')?$this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project'):"Add users who will work on this project. Only this users are able to see this project."?>"></i></label>
    <select name="users[]" id="users" class="form-control select2" multiple="">
      <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
      <option value="<?=htmlspecialchars($system_user->id)?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
      <?php } } ?>
    </select>
  </div>

  <div class="form-group">
    <label><?=$this->lang->line('project_client')?$this->lang->line('project_client'):'Project Client'?></label>
    <select name="client" id="client" class="form-control select2">
      <option value=""><?=$this->lang->line('select_clients')?$this->lang->line('select_clients'):'Select Clients'?></option>
      <?php foreach($system_clients as $system_client){ if($system_client->saas_id == $this->session->userdata('saas_id')){ ?>
      <option value="<?=htmlspecialchars($system_client->id)?>"><?=htmlspecialchars($system_client->first_name)?> <?=htmlspecialchars($system_client->last_name)?></option>
      <?php } } ?>
    </select>
  </div>
</form>

<div id="modal-edit-project"></div>

<?php $this->load->view('includes/js'); ?>

<script>
  project_id = "<?=htmlspecialchars($project['id'])?>";
</script>
<?php $this->load->view('includes/invoice-modal'); ?>

</body>
</html>
