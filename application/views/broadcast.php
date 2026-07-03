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
              <?=$this->lang->line('broadcast')?htmlspecialchars($this->lang->line('broadcast')):'Broadcast'?> 
              <a href="<?=base_url('broadcast/create-broadcast')?>" class="btn btn-sm btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> <?=$this->lang->line('create')?$this->lang->line('create'):'Create'?> <?=$this->lang->line('broadcast')?htmlspecialchars($this->lang->line('broadcast')):'Broadcast'?></a>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('broadcast')?htmlspecialchars($this->lang->line('broadcast')):'Broadcast'?></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row">
              <div class="form-group col-md-4">
                <select class="form-control select2" id="broadcast_filter_user">
                  <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
                  
                  <option value="all"><?=$this->lang->line('all')?htmlspecialchars($this->lang->line('all')):'All'?></option>

                  <option value="saas_admins"><?=$this->lang->line('saas_admins')?htmlspecialchars($this->lang->line('saas_admins')):'SaaS Admins'?></option>

                  <option value="subscribers"><?=$this->lang->line('subscribers')?htmlspecialchars($this->lang->line('subscribers')):'Subscribers'?></option>

                  <option value="users"><?=$this->lang->line('users')?htmlspecialchars($this->lang->line('users')):'Users'?></option>

                  <?php foreach($all_users as $system_user){ ?>
                  <option value="<?=$system_user->email?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?>  - <?=$system_user->email?></option>
                  <?php } ?>

                </select>
              </div>
              <div class="form-group col-md-3">
                <input type="text" name="from" id="from" class="form-control">
              </div>
              <div class="form-group col-md-3">
                <input type="text" name="too" id="too" class="form-control">
              </div>
              <div class="form-group col-md-2">
                <button type="button" class="btn btn-primary btn-lg btn-block" id="filter">
                  <?=$this->lang->line('filter')?$this->lang->line('filter'):'Filter'?>
                </button>
              </div>
            
            </div>
            <div class="row">
                  <div class="col-md-12">
                    <div class="card card-primary">
                      <div class="card-body"> 
                        <table class='table-striped' id='broadcast_list'
                          data-toggle="table"
                          data-url="<?=base_url('broadcast/get_broadcast')?>"
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
                          data-query-params="queryParams">
                          <thead>
                            <tr>

                              <th data-field="from_user" data-sortable="false" data-visible="false"><?=$this->lang->line('from')?htmlspecialchars($this->lang->line('from')):'From'?></th>

                              <th data-field="to_whom" data-sortable="false"><?=$this->lang->line('to_user')?htmlspecialchars($this->lang->line('to_user')):'To User'?></th>

                              <th data-field="subject" data-sortable="true"><?=$this->lang->line('subject')?htmlspecialchars($this->lang->line('subject')):'Subject'?></th>

                              
                              <th data-field="msg" data-sortable="true" data-visible="false"><?=$this->lang->line('email_message')?htmlspecialchars($this->lang->line('email_message')):'Email Message'?></th>

                              <th data-field="created" data-sortable="true" data-visible="false"><?=$this->lang->line('created')?htmlspecialchars($this->lang->line('created')):'Created'?></th>

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

<?php $this->load->view('includes/js'); ?>
<script>
  function queryParams(p){
      return {
        "to_whom": $('#broadcast_filter_user').val(),
        "from": $('#from').val(),
        "too": $('#too').val(),
        limit:p.limit,
        sort:p.sort,
        order:p.order,
        offset:p.offset,
        search:p.search
      };
  }

</script>

<script>
$('#filter').on('click',function(e){
  $('#broadcast_list').bootstrapTable('refresh');
});

$(document).ready(function(){
  var start = moment().subtract(7, 'days');
  $('#from').daterangepicker({
    startDate: start,
    locale: {format: date_format_js},
    singleDatePicker: true,
  });

  $('#too').daterangepicker({
    locale: {format: date_format_js},
    singleDatePicker: true,
  });
});
</script>

</body>
</html>