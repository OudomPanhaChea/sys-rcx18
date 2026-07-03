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
            <?=$this->lang->line('attendance')?htmlspecialchars($this->lang->line('attendance')):'Attendance'?> 
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('attendance')?htmlspecialchars($this->lang->line('attendance')):'Attendance'?></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row">
                <div class="form-group col-md-4">
                  <select class="form-control select2" id="attendance_filter_user">
                    <option value=""><?=$this->lang->line('select_users')?$this->lang->line('select_users'):'Select Users'?></option>
                    <?php foreach($system_users as $system_user){ if($system_user->saas_id == $this->session->userdata('saas_id')){ ?>
                    <option value="<?=$system_user->id?>"><?=htmlspecialchars($system_user->first_name)?> <?=htmlspecialchars($system_user->last_name)?></option>
                    <?php } } ?>
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
                        <table class='table-striped' id='attendance_list'
                          data-toggle="table"
                          data-url="<?=base_url('attendance/get_attendance')?>"
                          data-click-to-select="true"
                          data-side-pagination="server"
                          data-pagination="true"
                          data-page-list="[5, 10, 20, 50, 100, 200]"
                          data-search="true" data-show-columns="true"
                          data-show-refresh="false" data-trim-on-search="false"
                          data-sort-name="id" data-sort-order="desc"
                          data-mobile-responsive="true"
                          data-toolbar="" data-show-export="true"
                          data-maintain-selected="true"
                          data-query-params="queryParams">
                          <thead>
                            <tr>
                              <th data-field="user" data-sortable="false"><?=$this->lang->line('team_members')?$this->lang->line('team_members'):'Team Members'?></th>
                           
                              <th data-field="check_in" data-sortable="true"><?=$this->lang->line('check_in')?htmlspecialchars($this->lang->line('check_in')):'Check In'?></th>
                              <th data-field="check_out" data-sortable="true"><?=$this->lang->line('check_out')?htmlspecialchars($this->lang->line('check_out')):'Check Out'?></th>
                              <th data-field="total_time" data-sortable="false"><?=$this->lang->line('total_time')?$this->lang->line('total_time'):'Total Time'?></th>
                              <th data-field="note" data-sortable="false" data-visible="false"><?=$this->lang->line('note')?$this->lang->line('note'):'Note'?></th>
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
        "user_id": $('#attendance_filter_user').val(),
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
  $('#attendance_list').bootstrapTable('refresh');
});

$(document).ready(function(){
  $('#from').daterangepicker({
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
