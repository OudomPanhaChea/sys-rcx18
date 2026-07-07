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
              <?=$this->lang->line('projects')?$this->lang->line('projects'):'Projects'?> 
              <?php if(my_plan_features('projects')){  if ($this->ion_auth->is_admin() || permissions('project_create')){ ?>
                <a href="#" id="modal-add-project" class="btn btn-sm btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> <?=$this->lang->line('create')?$this->lang->line('create'):'Create'?></a>
                <a href="#" id="modal-manage-categories" class="btn btn-sm btn-icon icon-left btn-primary"><i class="fas fa-tags"></i> <?=$this->lang->line('category')?$this->lang->line('category'):'Category'?></a>
              <?php } } ?>
              <div class="btn-group view-toggle" role="group" aria-label="<?=$this->lang->line('view')?htmlspecialchars($this->lang->line('view')):'View'?>">
                <a href="#" class="btn btn-sm btn-primary active" title="<?=$this->lang->line('list_view')?htmlspecialchars($this->lang->line('list_view')):'List View'?>" data-toggle="tooltip"><i class="fas fa-list-ul"></i></a>
                <a href="<?=base_url('projects')?>" class="btn btn-sm" title="<?=$this->lang->line('grid_view')?htmlspecialchars($this->lang->line('grid_view')):'Grid View'?>" data-toggle="tooltip"><i class="fas fa-th-large"></i></a>
              </div>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('projects')?$this->lang->line('projects'):'Projects'?></div>
            </div>
          </div>
          <div class="section-body">
            <?php if(!$this->ion_auth->in_group(4)){ ?>
            <?php
              $total_categorised = 0;
              if(!empty($project_category_counts)){ $total_categorised = array_sum($project_category_counts); }
            ?>
            <?php if(!empty($project_categories)){ ?>
            <div class="category-stats" id="category_stats">
              <button type="button" class="category-stat is-all active" data-cat="">
                <span class="category-stat__label"><?=$this->lang->line('all')?htmlspecialchars($this->lang->line('all')):'All'?></span>
                <span class="category-stat__count"><?=$total_categorised?></span>
              </button>
              <?php foreach($project_categories as $category){
                $cat_class = !empty($category['class']) ? $category['class'] : 'primary';
                $cat_count = isset($project_category_counts[(int)$category['id']]) ? $project_category_counts[(int)$category['id']] : 0; ?>
              <button type="button" class="category-stat cat-<?=htmlspecialchars($cat_class)?>" data-cat="<?=htmlspecialchars($category['id'])?>">
                <span class="category-stat__dot"></span>
                <span class="category-stat__label"><?=htmlspecialchars($category['title'])?></span>
                <span class="category-stat__count"><?=$cat_count?></span>
              </button>
              <?php } ?>
            </div>
            <?php } ?>

            <div id="tool" class="projects-filterbar">
              <div class="projects-filterbar__row">
                <div class="projects-filterbar__field">
                  <select class="form-control select2" id="project_filters_user">
                    <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
                    <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
                    <option value="<?=htmlspecialchars($system_user->id)?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
                    <?php } } ?>
                  </select>
                </div>
                <div class="projects-filterbar__field">
                  <select class="form-control select2" id="project_filters_client">
                    <option value=""><?=$this->lang->line('select_clients')?$this->lang->line('select_clients'):'Select Clients'?></option>
                    <?php foreach($system_clients as $system_client){ if($system_client->saas_id == $this->session->userdata('saas_id')){ ?>
                    <option value="<?=htmlspecialchars($system_client->id)?>"><?=htmlspecialchars($system_client->first_name)?> <?=htmlspecialchars($system_client->last_name)?></option>
                    <?php } } ?>
                  </select>
                </div>
                <div class="projects-filterbar__field">
                  <select class="form-control select2" id="project_filters_status">
                    <option value=""><?=$this->lang->line('select_status')?$this->lang->line('select_status'):'Select Status'?></option>
                    <?php foreach($project_status as $status){ ?>
                    <option value="<?=htmlspecialchars($status['id'])?>"><?=htmlspecialchars($status['title'])?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="projects-filterbar__field">
                  <select class="form-control select2" id="project_filters_issue">
                    <option value=""><?=$this->lang->line('select_issue')?$this->lang->line('select_issue'):'Select Issue'?></option>
                    <?php if(!empty($project_issues)){ foreach($project_issues as $issue){ ?>
                    <option value="<?=htmlspecialchars($issue['id'])?>"><?=htmlspecialchars($issue['title'])?></option>
                    <?php } } ?>
                  </select>
                </div>
              </div>
              <input type="hidden" id="project_filters_category" value="">
            </div>
            <?php } ?>


            <div class="row">





  
              <div class="col-md-12">
                <div class="card card-primary">
                  <div class="card-body"> 
                    <table class='table-striped' id='projects_list'
                      data-toggle="table"
                      data-url="<?=base_url('projects/get_projects_list')?>"
                      data-click-to-select="true"
                      data-side-pagination="server"
                      data-pagination="true"
                      data-page-list="[5, 10, 20, 50, 100, 200]"
                      data-search="true" data-show-columns="true"
                      data-show-refresh="false" data-trim-on-search="false"
                      data-sort-name="id" data-sort-order="DESC"
                      data-mobile-responsive="true"
                      data-toolbar="" data-show-export="false"
                      data-maintain-selected="true"
                      data-export-options='{
                        "fileName": "projects_list",
                      }'
                      data-query-params="queryParams">
                      <thead>
                        <tr>
                          
                          <th data-field="title" data-sortable="true"><?=$this->lang->line('title')?htmlspecialchars($this->lang->line('title')):'Title'?></th>

                          <th data-field="category" data-sortable="false"><?=$this->lang->line('category')?htmlspecialchars($this->lang->line('category')):'Category'?></th>

                          <th data-field="issue" data-sortable="false"><?=$this->lang->line('issue')?htmlspecialchars($this->lang->line('issue')):'Issue'?></th>

                          <th data-field="project_client" data-sortable="false"><?=$this->lang->line('project_client')?htmlspecialchars($this->lang->line('project_client')):'Project Client'?></th>

                          <th data-field="project_users" data-sortable="false"><?=$this->lang->line('team_member')?htmlspecialchars($this->lang->line('team_member')):'Team Member'?></th>

                          <th data-field="stats" data-sortable="false"><?=$this->lang->line('stats')?htmlspecialchars($this->lang->line('stats')):'Stats'?></th>

                          <th data-field="starting_date" data-sortable="true" data-visible="false"><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?></th>

                          <th data-field="ending_date" data-sortable="true" data-visible="false"><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?></th>

                          <th data-field="project_status" data-sortable="true"><?=$this->lang->line('status')?htmlspecialchars($this->lang->line('status')):'Status'?></th>

                          <th data-field="action" data-sortable="false"><?=$this->lang->line('action')?htmlspecialchars($this->lang->line('action')):'Action'?></th>

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

<form action="<?=base_url('projects/create-project')?>" method="POST" class="modal-part" id="modal-add-project-part" data-title="<?=$this->lang->line('create_new_project')?$this->lang->line('create_new_project'):'Create New Project'?>" data-btn="<?=$this->lang->line('create')?$this->lang->line('create'):'Create'?>">
  <div class="form-group">
    <label><?=$this->lang->line('project_title')?$this->lang->line('project_title'):'Project Title'?><span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" required="">
  </div>
  <div class="form-group">
    <label><?=$this->lang->line('description')?$this->lang->line('description'):'Description'?><span class="text-danger">*</span></label>
    <textarea type="text" name="description" class="form-control"></textarea>
  </div>
  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('starting_date')?$this->lang->line('starting_date'):'Starting Date'?><span class="text-danger">*</span></label>
      <input type="text" name="starting_date" class="form-control datepicker">
    </div>

    <div class="form-group col-md-6">
      <label><?=$this->lang->line('ending_date')?$this->lang->line('ending_date'):'Ending Date'?><span class="text-danger">*</span></label>
      <input type="text" name="ending_date" class="form-control datepicker">
    </div>
  </span>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('budget')?$this->lang->line('budget'):'Budget'?> - <?=get_currency('currency_code')?></label>
      <input type="number" pattern="[0-9]" name="budget" class="form-control">
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?><span class="text-danger">*</span></label>
      <select name="status" class="form-control select2">
        <?php foreach($project_status as $status){ ?>
        <option value="<?=htmlspecialchars($status['id'])?>"><?=htmlspecialchars($status['title'])?></option>
        <?php } ?>
      </select>
    </div>
  </span>
  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('category')?$this->lang->line('category'):'Category'?></label>
      <select name="category" class="form-control select2 project-category-select" data-issue-target="add">
        <option value=""><?=$this->lang->line('select_category')?$this->lang->line('select_category'):'Select Category'?></option>
        <?php if(!empty($project_categories)){ foreach($project_categories as $category){ ?>
        <option value="<?=htmlspecialchars($category['id'])?>"><?=htmlspecialchars($category['title'])?></option>
        <?php } } ?>
      </select>
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('issue')?$this->lang->line('issue'):'Issue'?></label>
      <select name="issue" class="form-control project-issue-select-add">
        <option value=""><?=$this->lang->line('select_issue')?$this->lang->line('select_issue'):'Select Issue'?></option>
      </select>
    </div>
  </span>
  <div class="form-group">
    <label><?=$this->lang->line('project_users')?$this->lang->line('project_users'):'Project Users'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project')?$this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project'):"Add users who will work on this project. Only this users are able to see this project."?>"></i></label>
    <select name="users[]" class="form-control select2" multiple="">
      <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
      <option value="<?=htmlspecialchars($system_user->id)?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
      <?php } } ?>
    </select>
  </div>
  <div class="form-group">
    <label><?=$this->lang->line('project_client')?$this->lang->line('project_client'):'Project Client'?></label>
    <select name="client" class="form-control select2">
      <option value=""><?=$this->lang->line('select_clients')?$this->lang->line('select_clients'):'Select Clients'?></option>
      <?php foreach($system_clients as $system_client){ if($system_client->saas_id == $this->session->userdata('saas_id')){ ?>
      <option value="<?=htmlspecialchars($system_client->id)?>"><?=htmlspecialchars($system_client->first_name)?> <?=htmlspecialchars($system_client->last_name)?></option>
      <?php } } ?>
    </select>
  </div>

  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" id="send_email_notification" name="send_email_notification">
    <label class="form-check-label text-danger" for="send_email_notification"><?=$this->lang->line('send_email_notification')?$this->lang->line('send_email_notification'):'Send email notification'?></label>
  </div>

</form>

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
      <label><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?><span class="text-danger">*</span></label>
      <select name="status" id="status" class="form-control select2">
        <?php foreach($project_status as $status){ ?>
        <option value="<?=htmlspecialchars($status['id'])?>"><?=htmlspecialchars($status['title'])?></option>
        <?php } ?>
      </select>
    </div>
  </span>

  <span class="row">
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('category')?$this->lang->line('category'):'Category'?></label>
      <select name="category" id="category_edit" class="form-control select2 project-category-select" data-issue-target="edit">
        <option value=""><?=$this->lang->line('select_category')?$this->lang->line('select_category'):'Select Category'?></option>
        <?php if(!empty($project_categories)){ foreach($project_categories as $category){ ?>
        <option value="<?=htmlspecialchars($category['id'])?>"><?=htmlspecialchars($category['title'])?></option>
        <?php } } ?>
      </select>
    </div>
    <div class="form-group col-md-6">
      <label><?=$this->lang->line('issue')?$this->lang->line('issue'):'Issue'?></label>
      <select name="issue" id="issue_edit" class="form-control project-issue-select-edit">
        <option value=""><?=$this->lang->line('select_issue')?$this->lang->line('select_issue'):'Select Issue'?></option>
      </select>
    </div>
  </span>

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

<?php if ($this->ion_auth->is_admin() || permissions('project_create')) { ?>
<div class="modal-part" id="manage-categories-part" data-title="<?=$this->lang->line('manage_categories')?$this->lang->line('manage_categories'):'Manage Categories'?>">
  <div class="card mb-3">
    <div class="card-body p-3">
      <label class="mb-1"><strong><?=$this->lang->line('add_category')?$this->lang->line('add_category'):'Add Category'?></strong></label>
      <div class="row">
        <div class="col-7 pr-1">
          <input type="text" id="new_category_title" class="form-control" placeholder="<?=$this->lang->line('category_title')?$this->lang->line('category_title'):'Category title (e.g. Facebook)'?>">
        </div>
        <div class="col-3 px-1">
          <select id="new_category_class" class="form-control">
            <option value="primary">Blue</option>
            <option value="info">Cyan</option>
            <option value="success">Green</option>
            <option value="warning">Yellow</option>
            <option value="danger">Red</option>
            <option value="secondary">Grey</option>
          </select>
        </div>
        <div class="col-2 pl-1">
          <button type="button" class="btn btn-primary btn-block add-category-btn"><i class="fas fa-plus"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div id="categories-list-container">
    <div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i></div>
  </div>
</div>
<a href="#" id="modal-manage-categories-trigger" style="display:none"></a>
<?php } ?>

<div id="modal-edit-project"></div>
<?php $this->load->view('includes/js'); ?>
<script>
  function queryParams(p){
    return {
      "status": $('#project_filters_status').val(),
      "user": $('#project_filters_user').val(),
      "client": $('#project_filters_client').val(),
      "category": $('#project_filters_category').val(),
      "issue": $('#project_filters_issue').val(),
      limit:p.limit,
      sort:p.sort,
      order:p.order,
      offset:p.offset,
      search:p.search
    };
  }
  
  $('#tool').on('change',function(e){
    $('#projects_list').bootstrapTable('refresh');
  });

  // Category stat chips → set hidden filter value + refresh the table
  $(document).on('click', '#category_stats .category-stat', function(){
    $('#category_stats .category-stat').removeClass('active');
    $(this).addClass('active');
    $('#project_filters_category').val($(this).data('cat'));
    $('#projects_list').bootstrapTable('refresh');
  });
</script>
</body>
</html>
