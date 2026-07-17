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
              <?= $this->lang->line('projects') ? $this->lang->line('projects') : 'Projects' ?>
              <?php if (my_plan_features('projects')) {
                if ($this->ion_auth->is_admin() || permissions('project_create')) { ?>
                  <a href="#" id="modal-add-project" class="btn btn-sm btn-icon icon-left btn-primary"><i
                      class="fas fa-plus"></i>
                    <?= $this->lang->line('create') ? $this->lang->line('create') : 'Create' ?></a>
                  <a href="#" id="modal-manage-categories" class="btn btn-sm btn-icon icon-left btn-primary"><i
                      class="fas fa-tags"></i>
                    <?= $this->lang->line('category') ? $this->lang->line('category') : 'Category' ?></a>
              <?php }
              } ?>
              <div class="btn-group view-toggle" role="group"
                aria-label="<?= $this->lang->line('view') ? htmlspecialchars($this->lang->line('view')) : 'View' ?>">
                <a href="<?= base_url('projects/list') ?>" class="btn btn-sm"
                  title="<?= $this->lang->line('list_view') ? htmlspecialchars($this->lang->line('list_view')) : 'List View' ?>"
                  data-toggle="tooltip"><i class="fas fa-list-ul"></i></a>
                <a href="#" class="btn btn-sm btn-primary active"
                  title="<?= $this->lang->line('grid_view') ? htmlspecialchars($this->lang->line('grid_view')) : 'Grid View' ?>"
                  data-toggle="tooltip"><i class="fas fa-th-large"></i></a>
              </div>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a
                  href="<?= base_url() ?>"><?= $this->lang->line('dashboard') ? $this->lang->line('dashboard') : 'Dashboard' ?></a>
              </div>
              <div class="breadcrumb-item">
                <?= $this->lang->line('projects') ? $this->lang->line('projects') : 'Projects' ?>
              </div>
            </div>
          </div>
          <div class="section-body">
            <?php
            // Status view tabs: the grid shows only active work (Not Started +
            // On Going) by default; Done / Failed are separate views reached
            // via ?status=N. Keep the other filters when switching tabs.
            $status_query = $_GET;
            unset($status_query['status']);
            $status_base = base_url('projects') . (empty($status_query) ? '' : '?' . http_build_query($status_query));
            $status_prefix = base_url('projects') . '?' . http_build_query($status_query);
            $status_prefix .= empty($status_query) ? 'status=' : '&status=';
            $active_status = (isset($_GET['status']) && is_numeric($_GET['status'])) ? (int) $_GET['status'] : null;
            ?>
            <nav class="status-tabs"
              aria-label="<?= $this->lang->line('status') ? htmlspecialchars($this->lang->line('status')) : 'Status' ?>">
              <a href="<?= htmlspecialchars($status_base) ?>"
                class="status-tab <?= $active_status === null ? 'active' : '' ?>"
                <?= $active_status === null ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-bolt" aria-hidden="true"></i>
                <span><?= $this->lang->line('active') ? htmlspecialchars($this->lang->line('active')) : 'Active' ?></span>
                <span class="status-tab__count"><?= isset($status_counts['active']) ? $status_counts['active'] : 0 ?></span>
              </a>
              <?php foreach ($project_status as $status) {
                if ((int) $status['id'] <= 2) continue;
                $status_icon = ((int) $status['id'] === 4) ? 'fa-times-circle' : 'fa-check-circle'; ?>
                <a href="<?= htmlspecialchars($status_prefix . $status['id']) ?>"
                  class="status-tab tab-<?= htmlspecialchars($status['class']) ?> <?= $active_status === (int) $status['id'] ? 'active' : '' ?>"
                  <?= $active_status === (int) $status['id'] ? 'aria-current="page"' : '' ?>>
                  <i class="fas <?= $status_icon ?>" aria-hidden="true"></i>
                  <span><?= htmlspecialchars($status['title']) ?></span>
                  <span
                    class="status-tab__count"><?= isset($status_counts[(int) $status['id']]) ? $status_counts[(int) $status['id']] : 0 ?></span>
                </a>
              <?php } ?>
            </nav>

            <?php
            // Preserve current filters when switching category via the stat chips.
            // Drop the previously selected issue too, since it may belong to a
            // different category than the one being switched to.
            $cat_query = $_GET;
            unset($cat_query['category']);
            unset($cat_query['issue']);
            $cat_base = base_url('projects') . (empty($cat_query) ? '' : '?' . http_build_query($cat_query));
            $cat_prefix = base_url('projects') . '?' . http_build_query($cat_query);
            $cat_prefix .= empty($cat_query) ? 'category=' : '&category=';
            $active_cat = (isset($_GET['category']) && is_numeric($_GET['category'])) ? (int) $_GET['category'] : null;
            $total_categorised = !empty($project_category_counts) ? array_sum($project_category_counts) : 0;
            ?>
            <?php if (!empty($project_categories)) { ?>
              <div class="category-stats">
                <a href="<?= htmlspecialchars($cat_base) ?>"
                  class="category-stat is-all <?= $active_cat === null ? 'active' : '' ?>">
                  <span
                    class="category-stat__label"><?= $this->lang->line('all') ? htmlspecialchars($this->lang->line('all')) : 'All' ?></span>
                  <span class="category-stat__count"><?= $total_categorised ?></span>
                </a>
                <?php foreach ($project_categories as $category) {
                  $cat_class = !empty($category['class']) ? $category['class'] : 'primary';
                  $cat_count = isset($project_category_counts[(int) $category['id']]) ? $project_category_counts[(int) $category['id']] : 0; ?>
                  <a href="<?= htmlspecialchars($cat_prefix . $category['id']) ?>"
                    class="category-stat cat-<?= htmlspecialchars($cat_class) ?> <?= $active_cat === (int) $category['id'] ? 'active' : '' ?>">
                    <!-- <span class="category-stat__dot"></span> -->
                    <span class="category-stat__label"><?= htmlspecialchars($category['title']) ?></span>
                    <span class="category-stat__count"><?= $cat_count ?></span>
                  </a>
                <?php } ?>
              </div>
            <?php } ?>

              <div class="projects-filterbar">
                <div class="projects-filterbar__row">
                  <div class="projects-filterbar__field projects-filterbar__field--grow">
                    <select class="form-control select2 project_filter">
                      <option value="<?= base_url("projects") ?>">
                        <?= $this->lang->line('select_project') ? $this->lang->line('select_project') : 'Select Project' ?>
                      </option>
                      <?php foreach ($projects_all as $project_all) { ?>
                        <option value="<?= base_url("projects/detail/" . htmlspecialchars($project_all['id'])) ?>">
                          <?= htmlspecialchars($project_all['title']) ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                  <?php if (!$this->ion_auth->in_group(4)) { ?>
                    <div class="projects-filterbar__field">
                      <select class="form-control select2 project_filter">
                        <option value="<?= base_url("projects") ?>">
                          <?= $this->lang->line('select_users') ? $this->lang->line('select_users') : 'Select Users' ?>
                        </option>
                        <?php foreach ($system_users as $system_user) {
                          if ($system_user->saas_id == $this->session->userdata('saas_id')) { ?>
                            <option value="<?= base_url("projects?user=" . htmlspecialchars($system_user->id)) ?>"
                              <?= (isset($_GET['user']) && !empty($_GET['user']) && is_numeric($_GET['user']) && $_GET['user'] == $system_user->id) ? "selected" : "" ?>>
                              <?= htmlspecialchars($system_user->first_name) ?>
                              <?= htmlspecialchars($system_user->last_name) ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                    </div>

                    <div class="projects-filterbar__field">
                      <select class="form-control select2 project_filter">
                        <option value="<?= base_url("projects") ?>">
                          <?= $this->lang->line('select_clients') ? $this->lang->line('select_clients') : 'Select Clients' ?>
                        </option>
                        <?php foreach ($system_clients as $system_client) {
                          if ($system_client->saas_id == $this->session->userdata('saas_id')) { ?>
                            <option value="<?= base_url("projects?client=" . htmlspecialchars($system_client->id)) ?>"
                              <?= (isset($_GET['client']) && !empty($_GET['client']) && is_numeric($_GET['client']) && $_GET['client'] == $system_client->id) ? "selected" : "" ?>>
                              <?= htmlspecialchars($system_client->first_name) ?>
                              <?= htmlspecialchars($system_client->last_name) ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  <?php } ?>

                  <div class="projects-filterbar__field">
                    <select class="form-control select2 project_filter_param" data-param="issue">
                      <option value="">
                        <?= $this->lang->line('select_issue') ? $this->lang->line('select_issue') : 'Select Issue' ?>
                      </option>
                      <?php if (!empty($project_issues)) {
                        foreach ($project_issues as $issue) { ?>
                          <option value="<?= htmlspecialchars($issue['id']) ?>" <?= (isset($_GET['issue']) && is_numeric($_GET['issue']) && $_GET['issue'] == $issue['id']) ? "selected" : "" ?>>
                            <?= htmlspecialchars($issue['title']) ?>
                          </option>
                      <?php }
                      } ?>
                    </select>
                  </div>

                  <div class="projects-filterbar__field">
                    <select class="form-control select2 project_filter">
                      <option value="<?= base_url("projects") ?>">
                        <?= $this->lang->line('sort_by') ? $this->lang->line('sort_by') : 'Sort By' ?>
                      </option>
                      <option value="<?= base_url("projects?sortby=latest") ?>" <?= (isset($_GET['sortby']) && !empty($_GET['sortby']) && $_GET['sortby'] == 'latest') ? "selected" : "" ?>>
                        <?= $this->lang->line('latest') ? $this->lang->line('latest') : 'Latest' ?>
                      </option>
                      <option value="<?= base_url("projects?sortby=old") ?>" <?= (isset($_GET['sortby']) && !empty($_GET['sortby']) && $_GET['sortby'] == 'old') ? "selected" : "" ?>>
                        <?= $this->lang->line('old') ? $this->lang->line('old') : 'Old' ?>
                      </option>
                      <option value="<?= base_url("projects?sortby=name") ?>" <?= (isset($_GET['sortby']) && !empty($_GET['sortby']) && $_GET['sortby'] == 'name') ? "selected" : "" ?>>
                        <?= $this->lang->line('name') ? $this->lang->line('name') : 'Name' ?>
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row projects-grid">

                <?php
                if (isset($projects) && !empty($projects)) {
                  foreach ($projects as $project) {
                ?>
                    <?php
                    /* ---- Team (overlapping avatar stack, capped at 5 + overflow) ---- */
                    $team = !empty($project['project_users']) ? $project['project_users'] : array();
                    $team_total = count($team);
                    $team_show = array_slice($team, 0, 5);
                    $team_more = $team_total - count($team_show);

                    /* ---- Description (full text, clamped in CSS) ---- */
                    $desc = trim(strip_tags($project['description']));

                    /* ---- Deadline urgency (auto colour by how near the ending date is) ---- */
                    $dr = isset($project['days_remaining']) ? (int) $project['days_remaining'] : (int) $project['days_count'];
                    if ($dr < 0) {
                      $deadline_level = 'overdue';
                      $deadline_text = ($this->lang->line('overdue') ? $this->lang->line('overdue') : 'Overdue') . ' ' . abs($dr) . ' ' . ($this->lang->line('days') ? $this->lang->line('days') : 'Days');
                    } elseif ($dr === 0) {
                      $deadline_level = 'today';
                      $deadline_text = $this->lang->line('due_today') ? $this->lang->line('due_today') : 'Due today';
                    } elseif ($dr <= 3) {
                      $deadline_level = 'critical';
                      $deadline_text = $dr . ' ' . ($this->lang->line('days') ? $this->lang->line('days') : 'Days') . ' ' . ($this->lang->line('left') ? $this->lang->line('left') : 'Left');
                    } elseif ($dr <= 7) {
                      $deadline_level = 'soon';
                      $deadline_text = $dr . ' ' . ($this->lang->line('days') ? $this->lang->line('days') : 'Days') . ' ' . ($this->lang->line('left') ? $this->lang->line('left') : 'Left');
                    } elseif ($dr <= 14) {
                      $deadline_level = 'approaching';
                      $deadline_text = $dr . ' ' . ($this->lang->line('days') ? $this->lang->line('days') : 'Days') . ' ' . ($this->lang->line('left') ? $this->lang->line('left') : 'Left');
                    } else {
                      $deadline_level = 'safe';
                      $deadline_text = $dr . ' ' . ($this->lang->line('days') ? $this->lang->line('days') : 'Days') . ' ' . ($this->lang->line('left') ? $this->lang->line('left') : 'Left');
                    }
                    /* proximity meter: 0% far away → 100% at/after deadline, over a 30-day window */
                    $deadline_fill = $dr < 0 ? 100 : max(0, min(100, round((30 - $dr) / 30 * 100)));
                    ?>
                    <div class="col-md-6 col-xl-4">
                      <div
                        class="card card-<?= htmlspecialchars($project['project_class']) ?> project-card project-card--<?= $deadline_level ?>">
                        <div class="card-body">

                          <div class="project-card__head">
                            <?php if (!empty($project['category_title']) || !empty($project['issue_title'])) { ?>
                              <div class="project-card__tags">
                                <?php if (!empty($project['category_title'])) { ?>
                                  <span class="badge badge-primary">
                                    <?= htmlspecialchars($project['category_title']) ?></span>
                                <?php } ?>
                                <?php if (!empty($project['issue_title'])) { ?>
                                  <span class="badge badge-light"><?= htmlspecialchars($project['issue_title']) ?></span>
                                <?php } ?>
                              </div>
                            <?php } ?>
                            <div class="dropdown project-card__menu">
                              <?php if (!empty($project['account_url'])) { ?>
                                <a href="<?= htmlspecialchars($project['account_url']) ?>" target="_blank"
                                  rel="noopener noreferrer" class="project-card__cog project-card__account-link"
                                  data-toggle="tooltip" data-placement="top"
                                  title="<?= $this->lang->line('view_account') ? htmlspecialchars($this->lang->line('view_account')) : 'View Account' ?>"
                                  aria-label="<?= $this->lang->line('view_account') ? htmlspecialchars($this->lang->line('view_account')) : 'View Account' ?>"><i
                                    class="fas fa-external-link-alt"></i></a>
                              <?php } ?>
                              <a href="#" class="project-card__cog" data-toggle="dropdown" aria-label="Project options"><i
                                  class="fas fa-ellipsis-h"></i></a>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item"
                                  href="<?= base_url("projects/detail/" . htmlspecialchars($project['id'])) ?>"><?= $this->lang->line('details') ? $this->lang->line('details') : 'Details' ?></a>
                                <?php if ($this->ion_auth->is_admin() || permissions('project_edit')) { ?>
                                  <a href="#" data-edit="<?= htmlspecialchars($project['id']) ?>"
                                    class="modal-edit-project dropdown-item"><?= $this->lang->line('edit') ? $this->lang->line('edit') : 'Edit' ?></a>
                                <?php } ?>
                                <?php if ($this->ion_auth->is_admin() || permissions('task_view')) { ?>
                                  <a class="dropdown-item"
                                    href="<?= base_url("projects/tasks/" . htmlspecialchars($project['id'])) ?>"><?= $this->lang->line('tasks') ? $this->lang->line('tasks') : 'Tasks' ?></a>
                                <?php } ?>
                                <a href="#" class="dropdown-item project-invoice-btn"
                                  data-id="<?= htmlspecialchars($project['id']) ?>"><?= $this->lang->line('invoice') ? $this->lang->line('invoice') : 'Invoice' ?></a>
                                <?php if ($this->ion_auth->is_admin() || permissions('project_delete')) { ?>
                                  <div class="dropdown-divider"></div>
                                  <a href="#" class="text-danger delete_project dropdown-item"
                                    data-id="<?= htmlspecialchars($project['id']) ?>"><?= $this->lang->line('trash') ? $this->lang->line('trash') : 'Trash' ?></a>
                                <?php } ?>
                              </div>
                            </div>
                          </div>

                          <h4 class="project-card__title">
                            <a
                              href="<?= base_url('projects/detail/' . htmlspecialchars($project['id'])) ?>"><?= htmlspecialchars($project['title']) ?></a>
                          </h4>

                          <?php if (!empty($project['project_client'])) { ?>
                            <div class="project-card__client">
                              <i class="fas fa-user-circle"></i> <?= htmlspecialchars($project['project_client']->first_name) ?>
                              <?= htmlspecialchars($project['project_client']->last_name) ?>
                            </div>
                          <?php } ?>

                          <p class="project-card__desc<?= $desc === '' ? ' is-empty' : '' ?>">
                            <?= $desc !== '' ? htmlspecialchars($desc) : ($this->lang->line('no_description') ? htmlspecialchars($this->lang->line('no_description')) : 'No description added yet.') ?>
                          </p>

                          <div class="project-card__footer">
                            <?php if ($team_total) { ?>
                              <div class="project-card__team">
                                <div class="project-card__avatars">
                                  <?php foreach ($team_show as $project_user) {
                                    if (!empty($project_user['profile'])) {
                                      if (file_exists('assets/uploads/profiles/' . $project_user['profile'])) {
                                        $file_upload_path = 'assets/uploads/profiles/' . $project_user['profile'];
                                      } else {
                                        $file_upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/profiles/' . $project_user['profile'];
                                      }
                                  ?>
                                      <figure class="avatar avatar-sm project-card__avatar">
                                        <img src="<?= base_url($file_upload_path) ?>"
                                          alt="<?= htmlspecialchars($project_user['first_name']) ?> <?= htmlspecialchars($project_user['last_name']) ?>"
                                          data-toggle="tooltip" data-placement="top"
                                          title="<?= htmlspecialchars($project_user['first_name']) ?> <?= htmlspecialchars($project_user['last_name']) ?>">
                                      </figure>
                                    <?php } else { ?>
                                      <figure class="avatar avatar-sm project-card__avatar"
                                        data-initial="<?= ucfirst(mb_substr(htmlspecialchars($project_user['first_name']), 0, 1, 'utf-8')) . '' . ucfirst(mb_substr(htmlspecialchars($project_user['last_name']), 0, 1, 'utf-8')) ?>"
                                        data-toggle="tooltip" data-placement="top"
                                        title="<?= htmlspecialchars($project_user['first_name']) ?> <?= htmlspecialchars($project_user['last_name']) ?>">
                                      </figure>
                                  <?php }
                                  } ?>
                                  <?php if ($team_more > 0) { ?>
                                    <figure class="avatar avatar-sm project-card__avatar project-card__avatar--more"
                                      data-initial="+<?= $team_more ?>" data-toggle="tooltip" data-placement="top"
                                      title="<?= $team_more ?> <?= $this->lang->line('more') ? htmlspecialchars($this->lang->line('more')) : 'more' ?>">
                                    </figure>
                                  <?php } ?>
                                </div>
                              </div>
                            <?php } ?>

                            <div class="project-deadline is-<?= $deadline_level ?>">
                              <div class="project-deadline-row">
                                <span class="project-deadline-icon"><i class="fas fa-hourglass-half"></i></span>
                                <span class="project-deadline-meta">
                                  <span
                                    class="project-deadline-label"><?= $this->lang->line('ending_date') ? htmlspecialchars($this->lang->line('ending_date')) : 'Ending Date' ?></span>
                                  <span class="project-deadline-date"><?= htmlspecialchars($project['ending_date']) ?></span>
                                </span>
                                <span class="project-deadline-pill"><?= htmlspecialchars($deadline_text) ?></span>
                              </div>
                              <span class="project-deadline-track"><span class="project-deadline-bar"
                                  style="width: <?= $deadline_fill ?>%"></span></span>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                <?php }
                } ?>

              </div>
              <div class="row mt-4">
                <div class="col-md-12">
                  <!-- Pagination links with HTML -->
                  <?php echo $links; ?>

                </div>
              </div>
          </>
        </section>
      </div>

      <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

  <form action="<?= base_url('projects/create-project') ?>" method="POST" class="modal-part" id="modal-add-project-part"
    data-title="<?= $this->lang->line('create_new_project') ? $this->lang->line('create_new_project') : 'Create New Project' ?>"
    data-btn="<?= $this->lang->line('create') ? $this->lang->line('create') : 'Create' ?>">
    <div class="form-group">
      <label><?= $this->lang->line('project_title') ? $this->lang->line('project_title') : 'Project Title' ?><span
          class="text-danger">*</span></label>
      <input type="text" name="title" class="form-control" required="">
    </div>
    <div class="form-group">
      <label><?= $this->lang->line('description') ? $this->lang->line('description') : 'Description' ?><span
          class="text-danger">*</span></label>
      <textarea type="text" name="description" class="form-control"></textarea>
    </div>
    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('starting_date') ? $this->lang->line('starting_date') : 'Starting Date' ?><span
            class="text-danger">*</span></label>
        <input type="text" name="starting_date" class="form-control datepicker">
      </div>

      <div class="form-group col-md-6">
        <label><?= $this->lang->line('ending_date') ? $this->lang->line('ending_date') : 'Ending Date' ?><span
            class="text-danger">*</span></label>
        <input type="text" name="ending_date" class="form-control datepicker">
      </div>
    </span>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('budget') ? $this->lang->line('budget') : 'Budget' ?> -
          <?= get_currency('currency_code') ?></label>
        <input type="number" pattern="[0-9]" name="budget" class="form-control">
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('booking') ? $this->lang->line('booking') : 'Booking' ?>
          (<?= get_currency('currency_symbol') ?>)</label>
        <input type="number" step="any" min="0" name="booking" class="form-control">
      </div>
    </span>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('account_url') ? $this->lang->line('account_url') : 'Account URL' ?></label>
        <input type="url" name="account_url" class="form-control" placeholder="https://">
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('username_or_id') ? $this->lang->line('username_or_id') : 'Username or ID' ?></label>
        <input type="text" name="account_username" class="form-control">
      </div>
    </span>

    <div class="form-group">
      <label><?= $this->lang->line('status') ? $this->lang->line('status') : 'Status' ?><span
          class="text-danger">*</span></label>
      <select name="status" class="form-control select2">
        <?php foreach ($project_status as $status) { ?>
          <option value="<?= htmlspecialchars($status['id']) ?>"><?= htmlspecialchars($status['title']) ?></option>
        <?php } ?>
      </select>
    </div>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('category') ? $this->lang->line('category') : 'Category' ?></label>
        <select name="category" class="form-control select2 project-category-select" data-issue-target="add">
          <option value="">
            <?= $this->lang->line('select_category') ? $this->lang->line('select_category') : 'Select Category' ?>
          </option>
          <?php if (!empty($project_categories)) {
            foreach ($project_categories as $category) { ?>
              <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['title']) ?></option>
          <?php }
          } ?>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('issue') ? $this->lang->line('issue') : 'Issue' ?></label>
        <select name="issue" class="form-control select2 project-issue-select-add">
          <option value=""><?= $this->lang->line('select_issue') ? $this->lang->line('select_issue') : 'Select Issue' ?>
          </option>
        </select>
      </div>
    </span>

    <div class="form-group">
      <label><?= $this->lang->line('project_users') ? $this->lang->line('project_users') : 'Project Users' ?> <i
          class="fas fa-question-circle" data-toggle="tooltip" data-placement="right"
          title="<?= $this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project') ? $this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project') : "Add users who will work on this project. Only this users are able to see this project." ?>"></i></label>
      <select name="users[]" class="form-control select2" multiple="">
        <?php foreach ($system_users as $system_user) {
          if ($system_user->saas_id == $this->session->userdata('saas_id')) { ?>
            <option value="<?= htmlspecialchars($system_user->id) ?>"><?= htmlspecialchars($system_user->first_name) ?>
              <?= htmlspecialchars($system_user->last_name) ?>
            </option>
        <?php }
        } ?>
      </select>
    </div>
    <div class="form-group">
      <label><?= $this->lang->line('project_client') ? $this->lang->line('project_client') : 'Project Client' ?></label>
      <select name="client" class="form-control select2">
        <option value="">
          <?= $this->lang->line('select_clients') ? $this->lang->line('select_clients') : 'Select Clients' ?>
        </option>
        <?php foreach ($system_clients as $system_client) {
          if ($system_client->saas_id == $this->session->userdata('saas_id')) { ?>
            <option value="<?= htmlspecialchars($system_client->id) ?>"><?= htmlspecialchars($system_client->first_name) ?>
              <?= htmlspecialchars($system_client->last_name) ?>
            </option>
        <?php }
        } ?>
      </select>
    </div>

    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" id="send_email_notification" name="send_email_notification">
      <label class="form-check-label text-danger"
        for="send_email_notification"><?= $this->lang->line('send_email_notification') ? $this->lang->line('send_email_notification') : 'Send email notification' ?></label>
    </div>

  </form>

  <form action="<?= base_url('projects/edit-project') ?>" method="POST" class="modal-part" id="modal-edit-project-part"
    data-title="<?= $this->lang->line('edit_project') ? $this->lang->line('edit_project') : 'Edit Project' ?>"
    data-btn="<?= $this->lang->line('update') ? $this->lang->line('update') : 'Update' ?>">
    <input type="hidden" name="update_id" id="update_id">
    <div class="form-group">
      <label><?= $this->lang->line('project_title') ? $this->lang->line('project_title') : 'Project Title' ?><span
          class="text-danger">*</span></label>
      <input type="text" name="title" id="title" class="form-control" required="">
    </div>
    <div class="form-group">
      <label><?= $this->lang->line('description') ? $this->lang->line('description') : 'Description' ?><span
          class="text-danger">*</span></label>
      <textarea type="text" name="description" id="description" class="form-control"></textarea>
    </div>
    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('starting_date') ? $this->lang->line('starting_date') : 'Starting Date' ?><span
            class="text-danger">*</span></label>
        <input type="text" name="starting_date" id="starting_date" class="form-control datepicker">
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('ending_date') ? $this->lang->line('ending_date') : 'Ending Date' ?><span
            class="text-danger">*</span></label>
        <input type="text" name="ending_date" id="ending_date" class="form-control datepicker">
      </div>
    </span>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('budget') ? $this->lang->line('budget') : 'Budget' ?> -
          <?= get_currency('currency_code') ?></label>
        <input type="number" pattern="[0-9]" name="budget" id="budget" class="form-control">
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('booking') ? $this->lang->line('booking') : 'Booking' ?>
          (<?= get_currency('currency_symbol') ?>)</label>
        <input type="number" step="any" min="0" name="booking" id="booking" class="form-control">
      </div>
    </span>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('account_url') ? $this->lang->line('account_url') : 'Account URL' ?></label>
        <input type="url" name="account_url" id="account_url" class="form-control" placeholder="https://">
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('username_or_id') ? $this->lang->line('username_or_id') : 'Username or ID' ?></label>
        <input type="text" name="account_username" id="account_username" class="form-control">
      </div>
    </span>

    <div class="form-group">
      <label><?= $this->lang->line('status') ? $this->lang->line('status') : 'Status' ?><span
          class="text-danger">*</span></label>
      <select name="status" id="status" class="form-control select2">
        <?php foreach ($project_status as $status) { ?>
          <option value="<?= htmlspecialchars($status['id']) ?>"><?= htmlspecialchars($status['title']) ?></option>
        <?php } ?>
      </select>
    </div>

    <span class="row">
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('category') ? $this->lang->line('category') : 'Category' ?></label>
        <select name="category" id="category_edit" class="form-control select2 project-category-select"
          data-issue-target="edit">
          <option value="">
            <?= $this->lang->line('select_category') ? $this->lang->line('select_category') : 'Select Category' ?>
          </option>
          <?php if (!empty($project_categories)) {
            foreach ($project_categories as $category) { ?>
              <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['title']) ?></option>
          <?php }
          } ?>
        </select>
      </div>
      <div class="form-group col-md-6">
        <label><?= $this->lang->line('issue') ? $this->lang->line('issue') : 'Issue' ?></label>
        <select name="issue" id="issue_edit" class="form-control select2 project-issue-select-edit">
          <option value=""><?= $this->lang->line('select_issue') ? $this->lang->line('select_issue') : 'Select Issue' ?>
          </option>
        </select>
      </div>
    </span>

    <div class="form-group">
      <label><?= $this->lang->line('project_users') ? $this->lang->line('project_users') : 'Project Users' ?> <i
          class="fas fa-question-circle" data-toggle="tooltip" data-placement="right"
          title="<?= $this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project') ? $this->lang->line('add_users_who_will_work_on_this_project_only_this_users_are_able_to_see_this_project') : "Add users who will work on this project. Only this users are able to see this project." ?>"></i></label>
      <select name="users[]" id="users" class="form-control select2" multiple="">
        <?php foreach ($system_users as $system_user) {
          if ($system_user->saas_id == $this->session->userdata('saas_id')) { ?>
            <option value="<?= htmlspecialchars($system_user->id) ?>"><?= htmlspecialchars($system_user->first_name) ?>
              <?= htmlspecialchars($system_user->last_name) ?>
            </option>
        <?php }
        } ?>
      </select>
    </div>
    <div class="form-group">
      <label><?= $this->lang->line('project_client') ? $this->lang->line('project_client') : 'Project Client' ?></label>
      <select name="client" id="client" class="form-control select2">
        <option value="">
          <?= $this->lang->line('select_clients') ? $this->lang->line('select_clients') : 'Select Clients' ?>
        </option>
        <?php foreach ($system_clients as $system_client) {
          if ($system_client->saas_id == $this->session->userdata('saas_id')) { ?>
            <option value="<?= htmlspecialchars($system_client->id) ?>"><?= htmlspecialchars($system_client->first_name) ?>
              <?= htmlspecialchars($system_client->last_name) ?>
            </option>
        <?php }
        } ?>
      </select>
    </div>
  </form>

  <?php if ($this->ion_auth->is_admin() || permissions('project_create')) { ?>
    <div class="modal-part" id="manage-categories-part"
      data-title="<?= $this->lang->line('manage_categories') ? $this->lang->line('manage_categories') : 'Manage Categories' ?>">

      <div class="card mb-3">
        <div class="card-body p-3">
          <label
            class="mb-1"><strong><?= $this->lang->line('add_category') ? $this->lang->line('add_category') : 'Add Category' ?></strong></label>
          <div class="row">
            <div class="col-7 pr-1">
              <input type="text" id="new_category_title" class="form-control"
                placeholder="<?= $this->lang->line('category_title') ? $this->lang->line('category_title') : 'Category title (e.g. Facebook)' ?>">
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
              <button type="button" class="btn btn-primary btn-block add-category-btn"><i
                  class="fas fa-plus"></i></button>
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
  <?php $this->load->view('includes/invoice-modal'); ?>
</body>

</html>