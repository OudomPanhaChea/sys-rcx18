<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <ul class="navbar-nav mr-auto">
      <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
  <ul class="navbar-nav navbar-right">

  <?php if(!$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4) && is_module_allowed('timesheet')){ ?>
    <li id="nav_timer" class="<?=(check_my_timer())?'':'d-none'?>"><a href="<?=base_url('projects/timesheet')?>" class="nav-link nav-link-lg beep" target="_blank"><i class="fas fa-stopwatch text-danger"></i></a></li>
  <?php } ?>

  <?php 
  if(is_module_allowed('notifications')){ 
    echo get_notifications_live(); 
  } ?>
  
  <?php if(is_module_allowed('languages')){ ?>
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
      <i class="fa fa-language"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <?php $languages = get_languages('', '', 1);
          if($languages){
          foreach($languages as $language){  ?>
            <a href="<?=base_url('languages/change/'.$language['language'])?>" class="dropdown-item <?=$language['language'] == $this->session->userdata('lang') || ($language['language'] == default_language() && !$this->session->userdata('lang'))?'active':''?>">
              <?=ucfirst($language['language'])?>
            </a>
        <?php } } ?>
      </div>
    </li>
  <?php } ?>

    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
      <?php if(isset($current_user->profile) && !empty($current_user->profile)){ 
          if(file_exists('assets/uploads/profiles/'.$current_user->profile)){
            $file_upload_path = 'assets/uploads/profiles/'.$current_user->profile;
          }else{
            $file_upload_path = 'assets/uploads/f'.$this->session->userdata('saas_id').'/profiles/'.$current_user->profile;
          }
        ?>
        <img alt="image" src="<?=base_url($file_upload_path)?>" class="rounded-circle mr-1">
      <?php }else{ ?>
          <figure class="avatar mr-2 avatar-sm bg-danger text-white" data-initial="<?=mb_substr(htmlspecialchars($current_user->first_name), 0, 1, "utf-8").''.mb_substr(htmlspecialchars($current_user->last_name), 0, 1, "utf-8")?>"></figure>
      <?php } ?>
      <div class="d-sm-none d-lg-inline-block"><?=htmlspecialchars($current_user->first_name)?> <?=htmlspecialchars($current_user->last_name)?></div></a>
      <div class="dropdown-menu dropdown-menu-right">
        <?php
          if($this->ion_auth->is_admin()){
            $my_plan = get_current_plan(); ?>
          <div class="dropdown-title">
            <h6 class="text-danger"><?=$my_plan['title']?></h6>
          </div>
        <?php  }
        ?>
        
        <a href="<?=base_url('users/profile')?>" class="dropdown-item has-icon <?=(current_url() == base_url('users/profile'))?'active':''?>">
          <i class="far fa-user"></i> <?=$this->lang->line('profile')?$this->lang->line('profile'):'Profile'?>
        </a>
        
        <?php if($this->ion_auth->in_group(4)){ ?>
          <a href="<?=base_url('users/company')?>" class="dropdown-item has-icon <?=(current_url() == base_url('users/company'))?'active':''?>">
            <i class="far fa-copyright"></i> <?=$this->lang->line('company')?$this->lang->line('company'):'Company'?>
          </a>
        <?php } ?>

        <div class="dropdown-divider"></div>
        <a href="<?=base_url('auth/logout')?>" class="dropdown-item has-icon text-danger">
          <i class="fas fa-sign-out-alt"></i> <?=$this->lang->line('logout')?$this->lang->line('logout'):'Logout'?>
        </a>
      </div>
    </li>
  </ul>
</nav>
<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?=base_url()?>"><img class="navbar-logos" alt="Logo" src="<?=base_url('assets/uploads/logos/'.full_logo())?>"></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?=base_url()?>"><img class="navbar-logos" alt="Logo Half" src="<?=base_url('assets/uploads/logos/'.half_logo())?>"></a>
    </div>
    <ul class="sidebar-menu">
      <li <?= (current_url() == base_url('/') || current_url() == base_url('home'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url()?>"><i class="fas fa-home text-primary"></i> <span><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></span></a></li>
      
      <?php if(($this->ion_auth->is_admin() || permissions('project_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('projects')){ ?> 
        <li <?=(current_url() == base_url('projects') || $this->uri->segment(2) == 'detail' || $this->uri->segment(2) == 'list')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('projects')?>"><i class="fas fa-layer-group text-danger"></i> <span><?=$this->lang->line('projects')?$this->lang->line('projects'):'Projects'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('task_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('tasks')){ ?>  
        <li <?=(current_url() == base_url('projects/tasks') || $this->uri->segment(2) == 'tasks' || $this->uri->segment(2) == 'tasks-list')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('projects/tasks')?>"><i class="fas fa-tasks text-success"></i> <span><?=$this->lang->line('tasks')?$this->lang->line('tasks'):'Tasks'?></span></a></li>
      <?php } ?>
      
      
      <?php if(($this->ion_auth->is_admin() || permissions('meetings_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('meetings')){ ?> 
        <li <?=(current_url() == base_url('meetings'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('meetings')?>"><i class="fas fa-video text-dark"></i> <span><?=$this->lang->line('video_meetings')?$this->lang->line('video_meetings'):'Video Meetings'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('client_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('clients')){ ?>  
        <li <?=(current_url() == base_url('users/client'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('users/client')?>"><i class="fas fa-handshake text-warning"></i> <span><?=$this->lang->line('clients')?$this->lang->line('clients'):'Clients'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('lead_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('leads')){ ?>  
        <li <?=(current_url() == base_url('leads'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('leads')?>"><i class="fas fa-phone text-danger"></i> <span><?=$this->lang->line('leads')?$this->lang->line('leads'):'Leads'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || $this->ion_auth->in_group(4)) && (is_module_allowed('invoices') || is_module_allowed('estimates') || is_module_allowed('taxes'))){ ?>           
        <li class="dropdown <?=((current_url() == base_url('invoices') || current_url() == base_url('estimates') || current_url() == base_url('products') || current_url() == base_url('settings/taxes') || $this->uri->segment(1) == 'invoices' || $this->uri->segment(1) == 'estimates') && ($this->uri->segment(2) != 'payments'))?'active':''; ?>">
        <a class="nav-link has-dropdown" href="#"><i class="fas fa-shopping-cart text-success"></i> 
        <span><?=$this->lang->line('sales')?$this->lang->line('sales'):'Sales'?></span></a>
          <ul class="dropdown-menu">
            <?php if(is_module_allowed('invoices')){ ?>
              <li <?=(current_url() == base_url('invoices') || $this->uri->segment(1) == 'invoices' && ($this->uri->segment(2) != 'payments'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('invoices')?>"><?=$this->lang->line('invoices')?$this->lang->line('invoices'):'Invoices'?></a></li> 
            <?php } ?>

            <?php if(is_module_allowed('estimates')){ ?>
              <li <?=(current_url() == base_url('estimates') || $this->uri->segment(1) == 'estimates')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('estimates')?>"><?=$this->lang->line('estimates')?$this->lang->line('estimates'):'Estimates'?></a></li> 
            <?php } ?>

            <?php if($this->ion_auth->is_admin() && is_module_allowed('estimates')){ ?>
              <li <?=(current_url() == base_url('products'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('products')?>"><?=$this->lang->line('products')?$this->lang->line('products'):'Products'?></a></li>
            <?php } ?>

            <?php if($this->ion_auth->is_admin() && is_module_allowed('taxes')){ ?>
              <li <?=(current_url() == base_url('settings/taxes'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/taxes')?>"><?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>
            <?php } ?>

          </ul>
        </li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || $this->ion_auth->in_group(4)) && (is_module_allowed('payments') || is_module_allowed('expenses'))){ ?>           
        <li class="dropdown <?=(current_url() == base_url('invoices/payments') || $this->uri->segment(2) == 'payments'|| current_url() == base_url('expenses'))?'active':''; ?>">
        <a class="nav-link has-dropdown" href="#"><i class="fas fa-credit-card text-info"></i> 
        <span><?=$this->lang->line('finance')?$this->lang->line('finance'):'Finance'?></span></a>
          <ul class="dropdown-menu">
            <?php if(is_module_allowed('payments')){ ?>
              <li <?=(current_url() == base_url('invoices/payments') || $this->uri->segment(2) == 'payments')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('invoices/payments')?>"><?=$this->lang->line('payments')?$this->lang->line('payments'):'Payments'?><?=$this->ion_auth->is_admin()?' / '.($this->lang->line('income')?htmlspecialchars($this->lang->line('income')):'Income'):''?></a></li>
            <?php } ?>

            <?php if($this->ion_auth->is_admin() && is_module_allowed('expenses')){ ?>
              <li <?=(current_url() == base_url('expenses'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('expenses')?>"><?=$this->lang->line('expenses')?$this->lang->line('expenses'):'Expenses'?></a></li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('user_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('team_members')){ ?> 
        <li <?=(current_url() == base_url('users'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('users')?>"><i class="fas fa-users text-dark"></i> <span><?=$this->lang->line('team_members')?$this->lang->line('team_members'):'Team Members'?></span></a></li>
      <?php } ?>

      <?php if (($this->ion_auth->is_admin() || permissions('chat_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('chat')){ ?>  
        <li <?= (current_url() == base_url('chat'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('chat')?>"><i class="fas fa-comment-alt text-primary"></i> <span><?=$this->lang->line('chat')?$this->lang->line('chat'):'Chat'?></span></a></li>
      <?php } ?>

      <?php if(!$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4) && is_module_allowed('timesheet')){ ?>  
        <li <?=(current_url() == base_url('projects/timesheet') || $this->uri->segment(2) == 'timesheet')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('projects/timesheet')?>"><i class="fas fa-clock text-info"></i> <span><?=$this->lang->line('timesheet')?$this->lang->line('timesheet'):'Timesheet'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('gantt_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('gantt')){ ?>  
        <li <?=(current_url() == base_url('projects/gantt') || $this->uri->segment(2) == 'gantt')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('projects/gantt')?>"><i class="fas fa-layer-group text-success"></i> <span><?=$this->lang->line('gantt')?$this->lang->line('gantt'):'Gantt'?></span></a></li>
      <?php } ?>

      <?php if(($this->ion_auth->is_admin() || permissions('calendar_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('calendar')){ ?>  
        <li <?=(current_url() == base_url('projects/calendar') || $this->uri->segment(2) == 'calendar')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('projects/calendar')?>"><i class="fas fa-calendar-alt text-danger"></i> <span><?=$this->lang->line('calendar')?$this->lang->line('calendar'):'Calendar'?></span></a></li>
      <?php } ?>
      
      <?php if (($this->ion_auth->is_admin() || permissions('todo_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('todo')){ ?>  
        <li <?= (current_url() == base_url('todo'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('todo')?>"><i class="fas fa-tasks text-warning"></i> <span><?=$this->lang->line('todo')?$this->lang->line('todo'):'ToDo'?></span></a></li>
      <?php } ?>

      <?php if (($this->ion_auth->is_admin() || permissions('notes_view')) && !$this->ion_auth->in_group(3) && is_module_allowed('notes')){ ?>  
        <li <?= (current_url() == base_url('notes'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('notes')?>"><i class="fas fa-sticky-note text-info"></i> <span><?=$this->lang->line('notes')?$this->lang->line('notes'):'Notes'?></span></a></li>
      <?php } ?>

      <?php if(!$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4) && is_module_allowed('leaves')){ ?>  
        <li <?=(current_url() == base_url('leaves'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('leaves')?>"><i class="fas fa-walking text-danger"></i> <span><?=$this->lang->line('leaves')?$this->lang->line('leaves'):'Leaves'?></span></a></li>
      <?php } ?>

      <?php if(!$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4) && is_module_allowed('attendance')){ ?>  
        <li <?=(current_url() == base_url('attendance'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('attendance')?>"><i class="fas fa-clipboard-check text-success"></i> <span><?=$this->lang->line('attendance')?$this->lang->line('attendance'):'Attendance'?></span></a></li>
      <?php } ?>

      <?php if ($this->ion_auth->in_group(3)){ ?> 
      
        <li class="dropdown <?=($this->uri->segment(1) == 'plans' || current_url() == base_url('users/saas') || current_url() == base_url('settings/taxes'))?'active':''; ?>">

        <a class="nav-link has-dropdown" href="#"><i class="fas fa fa-dollar-sign text-dark"></i> 
        <span><?=$this->lang->line('subscription')?htmlspecialchars($this->lang->line('subscription')):'Subscription'?></span></a>
        <ul class="dropdown-menu">

          <li <?=(current_url() == base_url('plans'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('plans')?>"><?=$this->lang->line('subscription_plans')?$this->lang->line('subscription_plans'):'Plans'?></a></li>
          
          <li <?=(current_url() == base_url('settings/taxes'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/taxes')?>"><?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>  

          <li <?=(current_url() == base_url('plans/orders'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('plans/orders')?>"><?=$this->lang->line('orders')?$this->lang->line('orders'):'Orders'?></a></li>

          <li <?=(current_url() == base_url('plans/offline-requests'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('plans/offline-requests')?>"><?=$this->lang->line('offline_requests')?$this->lang->line('offline_requests'):'Offline Requests'?></a></li>

          <li <?=(current_url() == base_url('users/saas'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('users/saas')?>"><?=$this->lang->line('subscribers')?htmlspecialchars($this->lang->line('subscribers')):'Subscribers'?></a></li>

        </ul>
      </li>


      <li class="dropdown <?=($this->uri->segment(1) == 'front')?'active':''; ?>">
        <a class="nav-link has-dropdown" href="#"><i class="fas fa-puzzle-piece text-primary"></i> 
        <span><?=$this->lang->line('frontend')?$this->lang->line('frontend'):'Frontend'?></span></a>
        <ul class="dropdown-menu">

          <li <?=(current_url() == base_url('front/landing'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('front/landing')?>"><?=$this->lang->line('general')?$this->lang->line('general'):'General'?></a></li>

          <li <?=(current_url() == base_url('front/features'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('front/features')?>"><?=$this->lang->line('features')?$this->lang->line('features'):'Features'?></a></li>

          <li <?=(current_url() == base_url('front/about'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('front/about')?>"><?=$this->lang->line('about')?$this->lang->line('about'):'About Us'?></a></li>

          <li <?=(current_url() == base_url('front/saas-privacy-policy'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('front/saas-privacy-policy')?>"><?=$this->lang->line('privacy_policy')?$this->lang->line('privacy_policy'):'Privacy Policy'?></a></li>

          <li <?=(current_url() == base_url('front/saas-terms-and-conditions'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('front/saas-terms-and-conditions')?>"><?=$this->lang->line('terms_and_conditions')?$this->lang->line('terms_and_conditions'):'Terms and Conditions'?></a></li>

        </ul>
      </li>

      <li <?= (current_url() == base_url('users'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('users')?>"><i class="fas fa-user-tie text-info"></i> <span><?=$this->lang->line('saas_admins')?$this->lang->line('saas_admins'):'SaaS Admins'?></span></a></li>
      
        <li <?= (current_url() == base_url('broadcast') || current_url() == base_url('broadcast/create-broadcast'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('broadcast')?>"><i class="fas fa-bullhorn text-danger"></i> <span><?=$this->lang->line('broadcast')?$this->lang->line('broadcast'):'Broadcast Mail'?></span></a></li>
      <?php } ?> 

      <?php if($this->ion_auth->is_admin() || $this->ion_auth->in_group(3)){ ?>   
        
        <?php if (is_module_allowed('support')){ ?> 
        <li <?=($this->uri->segment(1) == 'support')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('support')?>"><i class="fas fa-question-circle text-warning"></i> <span><?=$this->lang->line('support')?htmlspecialchars($this->lang->line('support')):'Support'?></a></li>
        <?php } ?>
        
      <?php if ($this->ion_auth->is_admin()){ ?>  
        <li class="dropdown <?=($this->uri->segment(1) == 'plans')?'active':''; ?>">
          <a class="nav-link has-dropdown" href="#"><i class="fas fa fa-dollar-sign text-dark"></i> 
          <span><?=$this->lang->line('subscription')?htmlspecialchars($this->lang->line('subscription')):'Subscription'?></span></a>
          <ul class="dropdown-menu">

            <li <?=(current_url() == base_url('plans')  || $this->uri->segment(2) == 'pay')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('plans')?>"><?=$this->lang->line('subscription_plans')?$this->lang->line('subscription_plans'):'Plans'?></a></li>
    
                
            <li <?=(current_url() == base_url('plans/orders'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('plans/orders')?>"><?=$this->lang->line('orders')?$this->lang->line('orders'):'Orders'?></a></li>
          
          </ul>
        </li>
        
      <?php } ?>

      
      <?php if($this->ion_auth->is_admin() && is_module_allowed('reports')){ ?>           
        <li class="dropdown <?=(current_url() == base_url('reports') || $this->uri->segment(1) == 'reports')?'active':''; ?>">
        <a class="nav-link has-dropdown" href="#"><i class="fas fa-chart-bar text-primary"></i> 
        <span><?=$this->lang->line('reports')?$this->lang->line('reports'):'Reports'?></span></a>
          <ul class="dropdown-menu">

              <li <?=(current_url() == base_url('reports/projects'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/projects')?>"><?=$this->lang->line('projects')?htmlspecialchars($this->lang->line('projects')):'Projects'?></a></li>

              <li <?=(current_url() == base_url('reports/tasks'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/tasks')?>"><?=$this->lang->line('tasks')?htmlspecialchars($this->lang->line('tasks')):'Tasks'?></a></li>
              
              <li <?=(current_url() == base_url('reports/clients'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/clients')?>"><?=$this->lang->line('clients')?htmlspecialchars($this->lang->line('clients')):'Clients'?></a></li>

              <li <?=(current_url() == base_url('reports/team'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/team')?>"><?=$this->lang->line('team_members')?htmlspecialchars($this->lang->line('team_members')):'Team Members'?></a></li>

              <li <?=(current_url() == base_url('reports/meetings'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/meetings')?>"><?=$this->lang->line('video_meetings')?htmlspecialchars($this->lang->line('video_meetings')):'Video Meetings'?></a></li>

              <li <?=(current_url() == base_url('reports/leads'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/leads')?>"><?=$this->lang->line('leads')?htmlspecialchars($this->lang->line('leads')):'Leads'?></a></li>
              
              <li <?=(current_url() == base_url('reports/timesheet'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/timesheet')?>"><?=$this->lang->line('timesheet')?htmlspecialchars($this->lang->line('timesheet')):'Timesheet'?></a></li>

              <li <?=(current_url() == base_url('reports/leaves'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/leaves')?>"><?=$this->lang->line('leaves')?htmlspecialchars($this->lang->line('leaves')):'Leaves'?></a></li>

              <li <?=(current_url() == base_url('reports/attendance'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/attendance')?>"><?=$this->lang->line('attendance')?htmlspecialchars($this->lang->line('attendance')):'Attendance'?></a></li>

              <li <?=(current_url() == base_url('reports/estimates'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/estimates')?>"><?=$this->lang->line('estimates')?htmlspecialchars($this->lang->line('estimates')):'Estimates'?></a></li>

              <li <?=(current_url() == base_url('reports/income'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/income')?>"><?=$this->lang->line('income')?$this->lang->line('income'):'Income'?></a></li>

              <li <?=(current_url() == base_url('reports/expenses'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports/expenses')?>"><?=$this->lang->line('expenses')?$this->lang->line('expenses'):'Expenses'?></a></li> 

              <li <?=(current_url() == base_url('reports'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('reports')?>"><?=$this->lang->line('income_vs_expenses')?$this->lang->line('income_vs_expenses'):'Income VS Expenses'?></a></li>
          </ul>
        </li>
      <?php } ?>


        <li class="dropdown <?=($this->uri->segment(1) == 'settings' || $this->uri->segment(1) == 'languages')?'active':''; ?>">
        <a class="nav-link has-dropdown" href="#"><i class="fas fa-cog text-dark"></i> 
        <span><?=$this->lang->line('settings')?$this->lang->line('settings'):'Settings'?></span></a>
          <ul class="dropdown-menu">

            <li <?=(current_url() == base_url('settings'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings')?>"><?=$this->lang->line('general')?$this->lang->line('general'):'General'?></a></li>

            <?php if (is_module_allowed('payment_gateway')){ ?> 
              <li <?=(current_url() == base_url('settings/payment'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/payment')?>"><?=$this->lang->line('payment_gateway')?$this->lang->line('payment_gateway'):'Payment Gateway'?></a></li>
            <?php } ?>

            <?php if ($this->ion_auth->in_group(3)){ ?> 

              <li <?=(current_url() == base_url('settings/seo'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/seo')?>"><?=$this->lang->line('seo')?$this->lang->line('seo'):'SEO'?></a></li>

              <li <?=(current_url() == base_url('settings/logins'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/logins')?>"><?=$this->lang->line('social_login')?htmlspecialchars($this->lang->line('social_login')):'Social Login'?></a></li>

              <li <?=(current_url() == base_url('settings/email'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/email')?>"><?=$this->lang->line('email')?$this->lang->line('email'):'Email'?></a></li>
              
              <li <?=(current_url() == base_url('settings/email-templates'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/email-templates')?>"><?=$this->lang->line('email_templates')?$this->lang->line('email_templates'):'Email Templates'?></a></li>

              <li <?=(current_url() == base_url('languages'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('languages')?>"><?=$this->lang->line('languages')?$this->lang->line('languages'):'Languages'?></a></li>

              <li <?=(current_url() == base_url('settings/taxes'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/taxes')?>"><?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>

              <li <?=(current_url() == base_url('settings/update'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/update')?>"><?=$this->lang->line('update')?$this->lang->line('update'):'Update'?></a></li>

              <li <?=(current_url() == base_url('settings/recaptcha'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/recaptcha')?>"><?=$this->lang->line('google_recaptcha')?$this->lang->line('google_recaptcha'):'Google reCAPTCHA'?></a></li>

              <li <?=(current_url() == base_url('settings/custom-code'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/custom-code')?>"><?=$this->lang->line('custom_code')?$this->lang->line('custom_code'):'Custom Code'?></a></li>

              <li <?=($this->uri->segment(2) == 'maintenance-mode')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/maintenance-mode')?>"><?=$this->lang->line('maintenance_mode_title')?htmlspecialchars($this->lang->line('maintenance_mode_title')):'Maintenance Mode'?></a></li>

              <li <?=($this->uri->segment(2) == 'livechat')?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/livechat')?>"><?=$this->lang->line('livechat')?htmlspecialchars($this->lang->line('livechat')):'LiveChat'?></a></li>

            <?php }else{ ?>

              <li <?=(current_url() == base_url('settings/company'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/company')?>"><?=$this->lang->line('company')?$this->lang->line('company'):'Company'?></a></li>

              <?php if (is_module_allowed('taxes')){ ?>
                <li <?=(current_url() == base_url('settings/taxes'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/taxes')?>"><?=$this->lang->line('taxes')?$this->lang->line('taxes'):'Taxes'?></a></li>
              <?php } ?> 

              <?php if (is_module_allowed('user_permissions')){ ?> 
                <li <?=(current_url() == base_url('settings/user-permissions'))?'class="active"':''; ?>><a class="nav-link" href="<?=base_url('settings/user-permissions')?>"><?=$this->lang->line('user_permissions')?$this->lang->line('user_permissions'):'User Permissions'?></a></li>
              <?php } ?>
              
            <?php } ?>


          </ul>
        </li>
      <?php } ?>
      
    </ul>
  </aside>
</div>