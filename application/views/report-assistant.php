<?php $this->load->view('includes/head'); ?>
<style>
  /* ============ Report Assistant — themed, no gradients ============ */
  .ra, .ra-modal {
    --pri: var(--brand-primary, #304A59);
    --pri-hover: var(--brand-primary-hover, #273d49);
    --pri-soft: var(--brand-primary-soft, #f1f2f3);
    --pri-light: var(--brand-primary-light, #e6e9eb);
    --pri-ring: var(--brand-primary-ring, rgba(48,74,89,.28));
    --ink: #1f2a36;
    --muted: #6c7480;
    --line: #e9ecef;
    --card: #ffffff;
    --ok: #2e9e6b;
    --mono: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
    --radius: 12px;
    --shadow: 0 1px 2px rgba(24,32,42,.04), 0 6px 20px -12px rgba(24,32,42,.16);
  }

  .ra .ra-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); }

  /* Hero */
  .ra-hero { padding: 16px 18px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--line); }
  .ra-hero__mark { width: 42px; height: 42px; border-radius: 10px; display: grid; place-items: center; background: var(--pri); color: #fff; font-size: 18px; flex: none; }
  .ra-hero h2 { font-weight: 700; font-size: 18px; margin: 0; color: var(--ink); letter-spacing: -.01em; }
  .ra-hero p { margin: 2px 0 0; font-size: 12.5px; color: var(--muted); }

  /* Shared control (filters + AI button share this look) */
  .ra-control { position: relative; display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 12px; border: 1px solid var(--line); border-radius: 9px; background: #fff; font-size: 13px; color: var(--ink); cursor: pointer; transition: border-color .15s, box-shadow .15s, background-color .15s; }
  .ra-control:hover { border-color: var(--pri); background: var(--pri-soft); }
  .ra-control:focus-within { outline: none; border-color: var(--pri); box-shadow: 0 0 0 3px var(--pri-ring); background: #fff; }
  .ra-control__ic { color: var(--muted); font-size: 12px; flex: none; pointer-events: none; transition: color .15s; }
  .ra-control:hover .ra-control__ic { color: var(--pri); }
  .ra-control__select { flex: 1 1 auto; min-width: 118px; border: 0; background: transparent; font-size: 13px; font-weight: 500; color: var(--ink); cursor: pointer; padding: 0; margin: 0; height: 100%;
    appearance: none; -webkit-appearance: none; }
  .ra-control__select:focus { outline: none; }
  .ra-control__select option { font-weight: 500; color: var(--ink); }
  .ra-control__caret { color: var(--muted); font-size: 10px; flex: none; pointer-events: none; transition: transform .18s ease, color .15s; }
  .ra-control:hover .ra-control__caret { color: var(--pri); }
  .ra-dot { width: 8px; height: 8px; border-radius: 50%; flex: none; }

  /* Custom dropdown (themed option panel) */
  .ra-dd { position: relative; }
  .ra-dd.is-open { border-color: var(--pri); box-shadow: 0 0 0 3px var(--pri-ring); background: #fff; }
  .ra-dd.is-open .ra-control__caret { transform: rotate(180deg); color: var(--pri); }
  .ra-dd__native { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: 0; overflow: hidden; clip: rect(0 0 0 0); opacity: 0; pointer-events: none; }
  .ra-dd__label { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; color: var(--ink); }
  .ra-dd__panel { position: absolute; z-index: 50; top: calc(100% + 6px); left: 0; min-width: 100%; width: max-content; max-width: 340px; background: #fff; border: 1px solid var(--line); border-radius: 11px; box-shadow: 0 16px 40px -14px rgba(24,32,42,.32); padding: 6px; max-height: 300px; overflow-y: auto; opacity: 0; transform: translateY(-5px); pointer-events: none; transition: opacity .14s ease, transform .14s ease; }
  .ra-dd.is-open .ra-dd__panel { opacity: 1; transform: translateY(0); pointer-events: auto; }
  .ra-dd__opt { display: flex; align-items: center; gap: 10px; padding: 9px 11px; border-radius: 8px; font-size: 13px; font-weight: 500; color: var(--ink); cursor: pointer; white-space: nowrap; transition: background-color .12s, color .12s; }
  .ra-dd__opt:hover, .ra-dd__opt.is-active { background: var(--pri-soft); color: var(--pri); }
  .ra-dd__opt.is-selected { color: var(--pri); }
  .ra-dd__check { margin-left: auto; padding-left: 12px; color: var(--pri); font-size: 11px; opacity: 0; }
  .ra-dd__opt.is-selected .ra-dd__check { opacity: 1; }
  .ra-control--on .ra-dot { background: var(--ok); }
  .ra-control--off .ra-dot { background: #c0392b; }
  .ra-control code { font-family: var(--mono); font-size: 11.5px; color: var(--muted); }

  /* Toolbar */
  .ra-toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; justify-content: space-between; padding: 12px 18px; border-bottom: 1px solid var(--line); }
  .ra-toolbar__group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .ra-runstat { font-size: 12.5px; color: var(--muted); font-variant-numeric: tabular-nums; min-width: 54px; text-align: right; }

  /* Buttons */
  .ra-btn { border: 0; cursor: pointer; border-radius: 9px; font-weight: 600; font-size: 13px; padding: 9px 15px; min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; text-decoration: none; line-height: 1.2; transition: background-color .15s, border-color .15s, color .15s, box-shadow .15s; }
  .ra-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--pri-ring); }
  .ra-btn:disabled { opacity: .5; cursor: not-allowed; }
  .ra-btn--primary { color: #fff; background: var(--pri); }
  .ra-btn--primary:hover:not(:disabled) { background: var(--pri-hover); color: #fff; }
  .ra-btn--ghost { background: #fff; border: 1px solid var(--line); color: var(--ink); }
  .ra-btn--ghost:hover:not(:disabled) { border-color: var(--pri); color: var(--pri); }
  .ra-btn--sm { padding: 6px 11px; min-height: 0; font-size: 12.5px; border-radius: 8px; }

  /* Icon button (message tools) */
  .ra-iconbtn { border: 0; background: transparent; color: var(--muted); cursor: pointer; width: 28px; height: 28px; border-radius: 7px; display: inline-grid; place-items: center; font-size: 12.5px; transition: background-color .15s, color .15s; }
  .ra-iconbtn:hover:not(:disabled) { background: var(--pri-light); color: var(--pri); }
  .ra-iconbtn:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--pri-ring); }
  .ra-iconbtn:disabled { opacity: .4; cursor: not-allowed; }

  /* Progress line */
  .ra-progress { height: 3px; background: transparent; overflow: hidden; }
  .ra-progress__fill { height: 100%; width: 0; background: var(--pri); transition: width .3s ease; }

  /* Compose */
  .ra-compose { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; padding: 14px 18px; background: var(--pri-soft); border-bottom: 1px solid var(--line); }
  .ra-field { display: flex; flex-direction: column; gap: 5px; }
  .ra-field label { font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
  .ra-input { border: 1px solid var(--line); border-radius: 9px; padding: 9px 12px; font-size: 13.5px; color: var(--ink); background: #fff; transition: border-color .15s, box-shadow .15s; }
  .ra-input:focus { outline: none; border-color: var(--pri); box-shadow: 0 0 0 3px var(--pri-ring); }
  .ra-input::placeholder { color: #aab0b8; }
  .ra-input--mono { font-family: var(--mono); }
  .ra-atwrap { display: flex; align-items: stretch; }
  .ra-atwrap span { display: grid; place-items: center; padding: 0 11px; border: 1px solid var(--line); border-right: 0; border-radius: 9px 0 0 9px; background: #fff; color: var(--muted); font-size: 13px; }
  .ra-atwrap .ra-input { border-radius: 0 9px 9px 0; }

  /* Table */
  .ra-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .ra-table thead th { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); padding: 11px 14px; border-bottom: 1px solid var(--line); text-align: left; background: #fbfcfc; }
  .ra-table tbody td { padding: 12px 14px; border-bottom: 1px solid var(--line); vertical-align: top; }
  .ra-table tbody tr:last-child td { border-bottom: 0; }
  .ra-table tbody tr:hover { background: var(--pri-soft); }
  .ra-idx { color: #aeb4bc; font-family: var(--mono); font-size: 12px; }

  .ra-proj { font-weight: 600; color: var(--ink); font-size: 14px; text-decoration: none; }
  .ra-proj:hover { color: var(--pri); text-decoration: underline; }
  .ra-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
  .ra-badge { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 999px; background: var(--pri-light); color: var(--pri); }
  .ra-linked { font-size: 11.5px; color: var(--muted); display: inline-flex; align-items: center; gap: 5px; }

  /* Copy chip (username / email) */
  .ra-chip { display: inline-flex; align-items: center; gap: 8px; max-width: 100%; border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 7px 10px; cursor: pointer; transition: border-color .12s, background-color .12s, color .12s; font-family: var(--mono); font-size: 12.5px; color: var(--ink); }
  .ra-chip:hover { border-color: var(--pri); background: var(--pri-soft); }
  .ra-chip:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--pri-ring); }
  .ra-chip__val { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ra-chip__ic { color: #aeb4bc; font-size: 12px; transition: color .12s; }
  .ra-chip:hover .ra-chip__ic { color: var(--pri); }
  .ra-chip.is-empty { opacity: .6; cursor: default; border-style: dashed; font-family: inherit; }
  .ra-chip.is-empty:hover { border-color: var(--line); background: #fff; }
  .ra-chip.is-copied { border-color: var(--ok); background: #eef8f2; color: #22794f; }
  .ra-chip.is-copied .ra-chip__ic { color: var(--ok); }
  .ra-nouser { font-size: 12px; color: #aeb4bc; font-style: italic; }

  /* Message cell */
  .ra-msgcol { min-width: 320px; }
  .ra-msg { margin-top: 2px; border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
  .ra-msg__head { display: flex; align-items: center; gap: 4px; padding: 4px 5px 4px 11px; border-bottom: 1px solid var(--line); background: #fff; }
  .ra-msg__age { font-size: 11px; color: #aeb4bc; }
  .ra-msg__age.is-flag { color: var(--ok); font-weight: 600; }
  .ra-msg__spacer { flex: 1; }
  .ra-msg__body { position: relative; padding: 11px 34px 11px 13px; background: var(--pri-soft); cursor: pointer; transition: background-color .12s; }
  .ra-msg__body:hover { background: var(--pri-light); }
  .ra-msg__body.is-copied { background: #eef8f2; }
  .ra-msg__body.is-editing { cursor: text; background: #fff; box-shadow: inset 0 0 0 2px var(--pri-ring); }
  .field-message { display: block; font-size: 13px; line-height: 1.6; color: #2a343f; white-space: pre-wrap; word-break: break-word; outline: none; }
  .ra-msg__copyic { position: absolute; top: 10px; right: 11px; font-size: 11.5px; color: var(--muted); opacity: 0; transition: opacity .12s, color .12s; pointer-events: none; }
  .ra-msg__body:hover .ra-msg__copyic { opacity: .75; }
  .ra-msg__body.is-copied .ra-msg__copyic { opacity: 1; color: var(--ok); }
  .ra-msg__body.is-editing .ra-msg__copyic { display: none; }

  /* Empty state */
  .ra-empty { text-align: center; padding: 60px 20px; color: var(--muted); }
  .ra-empty i { font-size: 28px; color: #d3d8de; margin-bottom: 10px; }

  /* Modal */
  .ra-modal .modal-content { border: 0; border-radius: 14px; box-shadow: 0 24px 70px -20px rgba(24,32,42,.4); }
  .ra-modal .modal-header { border: 0; padding: 18px 20px 4px; }
  .ra-modal .modal-title { font-weight: 700; color: var(--ink); }
  .ra-modal .modal-body { padding: 8px 20px 6px; }
  .ra-modal .modal-footer { border: 0; padding: 12px 20px 18px; }
  .ra-modal .ra-control__select { max-width: none; width: 100%; }
  .ra-modal .ra-control { width: 100%; }
  .ra-lbl { font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 5px; }
  .ra-hint { font-size: 12px; color: var(--muted); margin-top: 6px; }
  .ra-help-link { color: var(--pri); font-weight: 600; }

  @media (max-width: 767px) { .ra-msgcol { min-width: 0; } .ra-toolbar { flex-direction: column; align-items: stretch; } }
  @media (prefers-reduced-motion: reduce) { .ra *, .ra-modal * { transition: none !important; } }
</style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <?php $this->load->view('includes/navbar'); ?>
      <div class="main-content">
        <section class="section ra">
          <div class="section-header">
            <div class="section-header-back">
              <a href="javascript:history.go(-1)" class="btn btn-icon" aria-label="Back"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1><?= $this->lang->line('report_assistant') ? $this->lang->line('report_assistant') : 'Report Assistant' ?></h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?= base_url() ?>"><?= $this->lang->line('dashboard') ? $this->lang->line('dashboard') : 'Dashboard' ?></a></div>
              <div class="breadcrumb-item"><a href="<?= base_url('projects') ?>"><?= $this->lang->line('projects') ? $this->lang->line('projects') : 'Projects' ?></a></div>
              <div class="breadcrumb-item"><?= $this->lang->line('report_assistant') ? $this->lang->line('report_assistant') : 'Report Assistant' ?></div>
            </div>
          </div>

          <div class="section-body">
            <div class="ra-card">

              <!-- Hero -->
              <div class="ra-hero">
                <div class="ra-hero__mark"><i class="fas fa-robot" aria-hidden="true"></i></div>
                <div>
                  <h2><?= $this->lang->line('report_assistant') ? $this->lang->line('report_assistant') : 'Report Assistant' ?></h2>
                  <p><?= $this->lang->line('report_assistant_tag') ? $this->lang->line('report_assistant_tag') : 'Copy username, contact email &amp; message straight into the TikTok report form.' ?></p>
                </div>
              </div>

              <!-- Toolbar: filters (left) + AI setting & actions (right) -->
              <div class="ra-toolbar">
                <div class="ra-toolbar__group">
                  <form method="GET" action="<?= base_url('projects/report-assistant') ?>" id="ra-filter" style="display:flex; gap:10px; flex-wrap:wrap; margin:0;">
                    <span class="ra-control">
                      <i class="fas fa-layer-group ra-control__ic" aria-hidden="true"></i>
                      <select name="category" class="ra-control__select" aria-label="Filter by category" onchange="this.form.submit()">
                        <option value=""><?= $this->lang->line('all_categories') ? $this->lang->line('all_categories') : 'All categories' ?></option>
                        <?php if (!empty($project_categories)) { foreach ($project_categories as $c) { ?>
                          <option value="<?= (int)$c['id'] ?>" <?= ($category_filter == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['title']) ?></option>
                        <?php } } ?>
                      </select>
                      <i class="fas fa-chevron-down ra-control__caret" aria-hidden="true"></i>
                    </span>
                    <span class="ra-control">
                      <i class="fas fa-exclamation-triangle ra-control__ic" aria-hidden="true"></i>
                      <select name="issue" class="ra-control__select" aria-label="Filter by issue" onchange="this.form.submit()">
                        <option value=""><?= $this->lang->line('all_issues') ? $this->lang->line('all_issues') : 'All issues' ?></option>
                        <?php if (!empty($project_issues)) { foreach ($project_issues as $i) { ?>
                          <option value="<?= (int)$i['id'] ?>" <?= ($issue_filter == $i['id']) ? 'selected' : '' ?>><?= htmlspecialchars($i['title']) ?></option>
                        <?php } } ?>
                      </select>
                      <i class="fas fa-chevron-down ra-control__caret" aria-hidden="true"></i>
                    </span>
                  </form>
                </div>

                <div class="ra-toolbar__group">
                  <button type="button" class="ra-control <?= !empty($ai_key_present) ? 'ra-control--on' : 'ra-control--off' ?>" id="ai_status_pill" data-toggle="modal" data-target="#aiKeyModal" aria-label="AI key settings">
                    <span class="ra-dot" aria-hidden="true"></span>
                    <span id="ai_status_text"><?= !empty($ai_key_present) ? 'AI ready' : ($this->lang->line('set_up_key') ? $this->lang->line('set_up_key') : 'Set up AI key') ?></span>
                    <?php if (!empty($ai_key_present)) { ?><code id="ai_status_model"><?= htmlspecialchars($ai_model) ?></code><?php } ?>
                    <i class="fas fa-cog ra-control__ic" aria-hidden="true"></i>
                  </button>
                  <span class="ra-runstat" id="genall_status"></span>
                  <button type="button" class="ra-btn ra-btn--ghost" id="clear_all_btn"><i class="fas fa-trash-alt" aria-hidden="true"></i> <?= $this->lang->line('clear_all') ? $this->lang->line('clear_all') : 'Clear all' ?></button>
                  <button type="button" class="ra-btn ra-btn--primary" id="generate_all_btn" <?= empty($ai_key_present) ? 'disabled' : '' ?>>
                    <i class="fas fa-bolt" aria-hidden="true"></i> <span id="genall_label"><?= $this->lang->line('generate_all') ? $this->lang->line('generate_all') : 'Generate all' ?></span>
                  </button>
                </div>
              </div>

              <!-- Progress line -->
              <div class="ra-progress" role="progressbar" aria-label="Generation progress"><div class="ra-progress__fill" id="genall_bar"></div></div>

              <!-- Compose: contact email builder -->
              <div class="ra-compose">
                <div class="ra-field">
                  <label for="email_base"><?= $this->lang->line('contact_email') ? $this->lang->line('contact_email') : 'Contact email' ?></label>
                  <input type="text" id="email_base" class="ra-input ra-input--mono" placeholder="yourname" style="width:150px;">
                </div>
                <div class="ra-field">
                  <label for="email_start"><?= $this->lang->line('start_index') ? $this->lang->line('start_index') : 'Start #' ?></label>
                  <input type="number" id="email_start" class="ra-input ra-input--mono" value="1" min="0" style="width:82px;">
                </div>
                <div class="ra-field">
                  <label for="email_domain"><?= $this->lang->line('domain') ? $this->lang->line('domain') : 'Domain' ?></label>
                  <div class="ra-atwrap">
                    <span aria-hidden="true">@</span>
                    <input type="text" id="email_domain" class="ra-input ra-input--mono" placeholder="gmail.com" style="width:150px;">
                  </div>
                </div>
              </div>

              <!-- Table -->
              <?php if (empty($projects)) { ?>
                <div class="ra-empty">
                  <i class="fas fa-inbox d-block" aria-hidden="true"></i>
                  <?= $this->lang->line('no_projects_found') ? $this->lang->line('no_projects_found') : 'No projects match this filter.' ?>
                </div>
              <?php } else { ?>
                <div class="table-responsive">
                  <table class="ra-table" id="report-table">
                    <thead>
                      <tr>
                        <th style="width:40px;">#</th>
                        <th><?= $this->lang->line('project') ? $this->lang->line('project') : 'Project' ?></th>
                        <th style="width:168px;"><?= $this->lang->line('username') ? $this->lang->line('username') : 'Username' ?></th>
                        <th style="width:210px;"><?= $this->lang->line('contact_email') ? $this->lang->line('contact_email') : 'Contact email' ?></th>
                        <th class="ra-msgcol"><?= $this->lang->line('additional_message') ? $this->lang->line('additional_message') : 'Additional message' ?></th>
                        <th style="width:96px;"><?= $this->lang->line('tasks') ? $this->lang->line('tasks') : 'Tasks' ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($projects as $idx => $p) {
                        $uname = $p['tiktok_username'];
                        $linked = $p['linked_account']; ?>
                        <tr data-project-id="<?= (int)$p['id'] ?>" data-row-index="<?= (int)$idx ?>">
                          <td><span class="ra-idx"><?= (int)$idx + 1 ?></span></td>
                          <td>
                            <a href="<?= base_url('projects/detail/' . $p['id']) ?>" class="ra-proj"><?= htmlspecialchars($p['title']) ?></a>
                            <div class="ra-meta">
                              <?php if (!empty($p['issue_title'])) { ?><span class="ra-badge"><?= htmlspecialchars($p['issue_title']) ?></span><?php } ?>
                              <?php if ($linked['display'] !== '') { ?><span class="ra-linked"><i class="fas fa-link" aria-hidden="true"></i> <?= htmlspecialchars($linked['display']) ?></span><?php } ?>
                            </div>
                          </td>
                          <td>
                            <?php if ($uname !== '') { ?>
                              <button type="button" class="ra-chip ra-copy" aria-label="Copy username" title="Click to copy">
                                <span class="ra-chip__val field-username"><?= htmlspecialchars($uname) ?></span>
                                <i class="fas fa-copy ra-chip__ic" aria-hidden="true"></i>
                              </button>
                            <?php } else { ?>
                              <span class="ra-nouser"><?= $this->lang->line('no_username') ? $this->lang->line('no_username') : 'no @username' ?></span>
                            <?php } ?>
                          </td>
                          <td>
                            <button type="button" class="ra-chip ra-copy is-empty" aria-label="Copy contact email" title="Click to copy">
                              <span class="ra-chip__val field-email">&mdash;</span>
                              <i class="fas fa-copy ra-chip__ic" aria-hidden="true"></i>
                            </button>
                          </td>
                          <td class="ra-msgcol">
                            <button type="button" class="ra-btn ra-btn--ghost ra-btn--sm btn-generate" <?= empty($ai_key_present) ? 'disabled' : '' ?>>
                              <i class="fas fa-magic" aria-hidden="true"></i> <?= $this->lang->line('generate') ? $this->lang->line('generate') : 'Generate' ?>
                            </button>
                            <div class="ra-msg report-output" style="display:none;">
                              <div class="ra-msg__head">
                                <span class="ra-msg__age msg-age"></span>
                                <span class="ra-msg__spacer"></span>
                                <button type="button" class="ra-iconbtn btn-edit" aria-label="Edit message" title="Edit"><i class="fas fa-pen" aria-hidden="true"></i></button>
                                <button type="button" class="ra-iconbtn btn-regenerate" aria-label="Regenerate message" title="Regenerate"><i class="fas fa-redo" aria-hidden="true"></i></button>
                              </div>
                              <div class="ra-msg__body" title="Click to copy">
                                <span class="field-message"></span>
                                <i class="fas fa-copy ra-msg__copyic" aria-hidden="true"></i>
                              </div>
                            </div>
                          </td>
                          <td>
                            <a href="<?= base_url('projects/tasks/' . $p['id']) ?>" target="_blank" rel="noopener" class="ra-btn ra-btn--ghost ra-btn--sm" title="Open tasks">
                              <i class="fas fa-tasks" aria-hidden="true"></i> <?= $this->lang->line('tasks') ? $this->lang->line('tasks') : 'Tasks' ?>
                            </a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php } ?>

            </div>
          </div>
        </section>
      </div>
      <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

  <!-- AI key modal -->
  <div class="modal fade ra-modal" id="aiKeyModal" tabindex="-1" role="dialog" aria-labelledby="aiKeyTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="aiKeyTitle"><i class="fas fa-key mr-1" aria-hidden="true"></i> <?= $this->lang->line('your_ai_key') ? $this->lang->line('your_ai_key') : 'Your AI Key' ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="ai_api_key" class="ra-lbl"><?= $this->lang->line('gemini_api_key') ? $this->lang->line('gemini_api_key') : 'Gemini API key' ?></label>
            <input type="text" id="ai_api_key" class="ra-input ra-input--mono" style="width:100%;" placeholder="AIza&hellip;" value="<?= !empty($ai_api_key) ? htmlspecialchars($ai_api_key) : '' ?>">
            <div class="ra-hint"><a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="ra-help-link"><?= $this->lang->line('get_free_key') ? $this->lang->line('get_free_key') : 'Get a free key' ?> <i class="fas fa-external-link-alt" style="font-size:10px;" aria-hidden="true"></i></a></div>
          </div>
          <div class="mb-2">
            <label for="ai_model" class="ra-lbl"><?= $this->lang->line('model') ? $this->lang->line('model') : 'Model' ?></label>
            <span class="ra-control">
              <i class="fas fa-microchip ra-control__ic" aria-hidden="true"></i>
              <select id="ai_model" class="ra-control__select" aria-label="AI model">
                <?php
                  $free_models = array(
                    'gemini-2.5-flash'      => 'Gemini 2.5 Flash - balanced (recommended)',
                    'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite - fastest',
                    'gemini-2.0-flash'      => 'Gemini 2.0 Flash - stable',
                    'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite - light',
                    'gemini-1.5-flash'      => 'Gemini 1.5 Flash - legacy',
                  );
                  $sel_model = !empty($ai_model) ? $ai_model : 'gemini-2.5-flash';
                  foreach ($free_models as $mid => $label) {
                    echo '<option value="'.htmlspecialchars($mid).'"'.($sel_model == $mid ? ' selected' : '').'>'.htmlspecialchars($label).'</option>';
                  }
                ?>
              </select>
              <i class="fas fa-chevron-down ra-control__caret" aria-hidden="true"></i>
            </span>
            <div class="ra-hint"><?= $this->lang->line('models_same_key') ? $this->lang->line('models_same_key') : 'All models are free and use the same key.' ?></div>
          </div>
          <div id="ai_key_result"></div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
          <button type="button" class="ra-btn ra-btn--ghost ra-btn--sm" id="ai_test_btn"><i class="fas fa-plug" aria-hidden="true"></i> <?= $this->lang->line('test') ? $this->lang->line('test') : 'Test' ?></button>
          <button type="button" class="ra-btn ra-btn--primary ra-btn--sm" id="ai_save_btn"><i class="fas fa-save" aria-hidden="true"></i> <?= $this->lang->line('save_key') ? $this->lang->line('save_key') : 'Save key' ?></button>
        </div>
      </div>
    </div>
  </div>

  <?php $this->load->view('includes/js'); ?>

  <script>
    (function () {
      var genUrl  = '<?= base_url('projects/generate-report-message') ?>';
      var saveUrl = '<?= base_url('projects/save-ai-key') ?>';
      var testUrl = '<?= base_url('projects/test-ai-key') ?>';
      var raUserId = '<?= (int)$this->session->userdata('user_id') ?>';

      var MSG_TTL = 12 * 60 * 60 * 1000;
      var MSG_STORE = 'ra_msg_v1';
      var EMAIL_STORE = 'ra_email_v1:' + raUserId;

      function loadStore() { try { return JSON.parse(localStorage.getItem(MSG_STORE)) || {}; } catch (e) { return {}; } }
      function saveStore(o) { try { localStorage.setItem(MSG_STORE, JSON.stringify(o)); } catch (e) {} }
      function pruneStore() { var s = loadStore(), now = Date.now(), ch = false; for (var k in s) { if (s.hasOwnProperty(k) && (!s[k] || (now - s[k].ts) > MSG_TTL)) { delete s[k]; ch = true; } } if (ch) saveStore(s); return s; }
      function mKey(pid) { return raUserId + ':' + pid; }
      function getCached(pid) { var r = loadStore()[mKey(pid)]; return (r && (Date.now() - r.ts) <= MSG_TTL) ? r : null; }
      function setCached(pid, t) { var s = loadStore(); s[mKey(pid)] = { text: t, ts: Date.now() }; saveStore(s); }
      function dropCached(pid) { var s = loadStore(); delete s[mKey(pid)]; saveStore(s); }
      function ageLabel(ts) { var m = Math.round((Date.now() - ts) / 60000); if (m < 1) return 'just now'; if (m < 60) return m + 'm ago'; return Math.round(m / 60) + 'h ago'; }

      function doCopy(text, cb) {
        if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(text).then(cb, function () { legacy(text); cb(); }); }
        else { legacy(text); cb(); }
      }
      function legacy(text) { var t = $('<textarea>').val(text).css({ position: 'fixed', top: '-1000px' }).appendTo('body'); t.get(0).select(); document.execCommand('copy'); t.remove(); }
      function swalError(msg) { if (typeof swal === 'function') swal('Error', msg, 'error'); else alert(msg); }

      /* ----- contact email ----- */
      function buildEmails() {
        var base = ($('#email_base').val() || '').trim();
        var start = parseInt($('#email_start').val(), 10); if (isNaN(start)) start = 0;
        var domain = ($('#email_domain').val() || '').trim().replace(/^@+/, '');
        $('#report-table tbody tr').each(function () {
          var row = $(this), idx = parseInt(row.data('row-index'), 10) || 0,
              val = row.find('.field-email'), chip = val.closest('.ra-chip');
          if (base && domain) { val.text(base + '+' + (start + idx) + '@' + domain); chip.removeClass('is-empty'); }
          else { val.text('—'); chip.addClass('is-empty'); }
        });
        try { localStorage.setItem(EMAIL_STORE, JSON.stringify({ base: base, start: $('#email_start').val(), domain: domain })); } catch (e) {}
      }
      function restoreEmailCfg() {
        try { var c = JSON.parse(localStorage.getItem(EMAIL_STORE)); if (c) { if (c.base) $('#email_base').val(c.base); if (c.start !== undefined && c.start !== '') $('#email_start').val(c.start); if (c.domain) $('#email_domain').val(c.domain); } } catch (e) {}
        buildEmails();
      }
      $(document).on('input', '#email_base, #email_start, #email_domain', buildEmails);

      /* ----- message render ----- */
      function showMessage(row, text, ts) {
        row.find('.field-message').text(text);
        row.find('.btn-generate').hide();
        row.find('.report-output').show();
        row.find('.msg-age').removeClass('is-flag').text(ts ? ageLabel(ts) : '');
      }
      function resetRow(row) {
        row.find('.field-message').text('');
        row.find('.report-output').hide();
        row.find('.btn-generate').show();
      }
      function hydrate() { pruneStore(); $('#report-table tbody tr').each(function () { var row = $(this), r = getCached(row.data('project-id')); if (r) showMessage(row, r.text, r.ts); }); }
      function rowHasMsg(row) { return $.trim(row.find('.field-message').text()) !== ''; }

      /* ----- generate ----- */
      function generateRow(row) {
        var gen = row.find('.btn-generate'), regen = row.find('.btn-regenerate'), edit = row.find('.btn-edit');
        var hasCard = row.find('.report-output').is(':visible');
        gen.attr('disabled', true); regen.attr('disabled', true); edit.attr('disabled', true);
        var genOld = gen.html(), regenOld = regen.html();
        if (hasCard) regen.html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>'); else gen.html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>');
        return $.ajax({ type: 'POST', url: genUrl, data: { project_id: row.data('project-id') }, dataType: 'json' })
          .done(function (res) { if (res && res.error === false) { setCached(row.data('project-id'), res.text); showMessage(row, res.text, Date.now()); } })
          .always(function () { gen.attr('disabled', false).html(genOld); regen.attr('disabled', false).html(regenOld); edit.attr('disabled', false); });
      }
      $(document).on('click', '.btn-generate, .btn-regenerate', function () {
        generateRow($(this).closest('tr')).done(function (res) { if (!res || res.error !== false) swalError(res ? res.message : 'Request failed.'); })
          .fail(function () { swalError('Request failed. Please try again.'); });
      });

      /* ----- generate all ----- */
      var running = false, cancel = false;
      function setBar(done, total) { $('#genall_bar').css('width', total ? (Math.round(done / total * 100) + '%') : '0'); }
      $('#generate_all_btn').on('click', function () {
        var btn = $(this), label = $('#genall_label');
        if (running) { cancel = true; label.text('Stopping…'); return; }
        var pending = $('#report-table tbody tr').filter(function () { return !rowHasMsg($(this)); }).toArray();
        if (!pending.length) { $('#genall_status').text('All done'); setBar(1, 1); return; }
        running = true; cancel = false;
        var total = pending.length, done = 0;
        label.text('Stop'); btn.find('i').attr('class', 'fas fa-stop');
        setBar(0, total);
        function finish(msg) { running = false; btn.find('i').attr('class', 'fas fa-bolt'); label.text('Generate all'); $('#genall_status').text(msg || ''); }
        function next() {
          if (cancel) { finish('Stopped ' + done + '/' + total); return; }
          if (!pending.length) { finish('Done ' + done + '/' + total); return; }
          var row = $(pending.shift());
          $('#genall_status').text((done + 1) + '/' + total + '…');
          generateRow(row).done(function (res) {
            if (res && res.error === false) { done++; setBar(done, total); setTimeout(next, 900); }
            else { finish(res ? res.message : 'Failed'); swalError(res ? res.message : 'Request failed.'); }
          }).fail(function () { finish('Request failed'); swalError('Request failed. Please try again.'); });
        }
        next();
      });

      /* ----- clear all ----- */
      $('#clear_all_btn').on('click', function () {
        var rows = $('#report-table tbody tr').filter(function () { return rowHasMsg($(this)); });
        if (!rows.length) { $('#genall_status').text('Nothing to clear'); return; }
        var doClear = function () {
          rows.each(function () { var row = $(this); dropCached(row.data('project-id')); resetRow(row); });
          setBar(0, 0); $('#genall_status').text('');
        };
        if (typeof swal === 'function') {
          swal({ title: 'Clear all messages?', text: 'This removes the ' + rows.length + ' generated messages saved on this device.', icon: 'warning', buttons: true, dangerMode: true })
            .then(function (ok) { if (ok) doClear(); });
        } else if (confirm('Clear all generated messages?')) { doClear(); }
      });

      /* ----- copy: username / email chips ----- */
      $(document).on('click', '.ra-chip.ra-copy', function () {
        var chip = $(this), val = chip.find('.ra-chip__val').text().trim();
        if (!val || val === '—') return;
        doCopy(val, function () { chip.addClass('is-copied'); setTimeout(function () { chip.removeClass('is-copied'); }, 1100); });
      });

      /* ----- copy: message body (click text) ----- */
      $(document).on('click', '.ra-msg__body', function () {
        var body = $(this); if (body.hasClass('is-editing')) return;
        var val = body.find('.field-message').text(); if (!val) return;
        var age = body.closest('.ra-msg').find('.msg-age'), prev = age.text();
        doCopy(val, function () {
          body.addClass('is-copied'); age.addClass('is-flag').text('Copied');
          setTimeout(function () { body.removeClass('is-copied'); age.removeClass('is-flag').text(prev); }, 1100);
        });
      });

      /* ----- edit message (auto-saves on click-away) ----- */
      function startEdit(msg) {
        var body = msg.find('.ra-msg__body'), span = msg.find('.field-message'),
            btn = msg.find('.btn-edit'), regen = msg.find('.btn-regenerate');
        span.data('orig', span.text()).attr('contenteditable', 'true');
        body.addClass('is-editing');
        btn.find('i').attr('class', 'fas fa-check'); btn.attr('title', 'Save');
        regen.attr('disabled', true);
        span.focus();
        if (window.getSelection && document.createRange) { var r = document.createRange(); r.selectNodeContents(span[0]); r.collapse(false); var s = window.getSelection(); s.removeAllRanges(); s.addRange(r); }
      }
      function endEdit(msg, save) {
        var body = msg.find('.ra-msg__body'), span = msg.find('.field-message'),
            btn = msg.find('.btn-edit'), regen = msg.find('.btn-regenerate');
        if (!body.hasClass('is-editing')) return;
        if (save) {
          var text = $.trim(span.text());
          if (text === '') { text = span.data('orig') || ''; span.text(text); }
          setCached(msg.closest('tr').data('project-id'), text);
          msg.find('.msg-age').removeClass('is-flag').text('edited');
        } else {
          span.text(span.data('orig') || '');
        }
        span.attr('contenteditable', 'false'); body.removeClass('is-editing');
        btn.find('i').attr('class', 'fas fa-pen'); btn.attr('title', 'Edit');
        regen.attr('disabled', false);
      }
      // Keep the caret when the Save icon is clicked so focusout doesn't fire first.
      $(document).on('mousedown', '.btn-edit', function (e) {
        if ($(this).closest('.ra-msg').find('.ra-msg__body').hasClass('is-editing')) e.preventDefault();
      });
      $(document).on('click', '.btn-edit', function () {
        var msg = $(this).closest('.ra-msg');
        if (msg.find('.ra-msg__body').hasClass('is-editing')) endEdit(msg, true); else startEdit(msg);
      });
      // Auto-save when focus leaves the message (click-away, Tab, etc.).
      $(document).on('focusout', '.field-message', function () {
        endEdit($(this).closest('.ra-msg'), true);
      });
      $(document).on('keydown', '.field-message', function (e) {
        if (!$(this).closest('.ra-msg__body').hasClass('is-editing')) return;
        if (e.key === 'Escape') { e.preventDefault(); endEdit($(this).closest('.ra-msg'), false); }
      });

      /* ----- AI key modal ----- */
      function setStatus(ok, model) {
        var pill = $('#ai_status_pill');
        pill.removeClass('ra-control--on ra-control--off').addClass(ok ? 'ra-control--on' : 'ra-control--off');
        $('#ai_status_text').text(ok ? 'AI ready' : 'Set up AI key');
        if (ok) { if ($('#ai_status_model').length) $('#ai_status_model').text(model); else $('#ai_status_text').after('<code id="ai_status_model">' + model + '</code>'); }
      }
      $('#ai_save_btn').on('click', function () {
        var btn = $(this), res = $('#ai_key_result'), old = btn.html();
        btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>'); res.html('');
        $.ajax({ type: 'POST', url: saveUrl, dataType: 'json', data: { api_key: $('#ai_api_key').val(), model: $('#ai_model').val() } })
          .done(function (r) {
            var cls = (r && r.error === false) ? 'alert-success' : 'alert-danger';
            res.html('<div class="alert ' + cls + ' py-2 mb-0">' + (r ? r.message : '') + '</div>');
            if (r && r.error === false) {
              $('.btn-generate, #generate_all_btn').attr('disabled', false);
              setStatus($('#ai_api_key').val().trim() !== '', $('#ai_model').val());
              setTimeout(function () { $('#aiKeyModal').modal('hide'); res.html(''); }, 900);
            }
          })
          .fail(function () { res.html('<div class="alert alert-danger py-2 mb-0">Something went wrong.</div>'); })
          .always(function () { btn.attr('disabled', false).html(old); });
      });
      $('#ai_test_btn').on('click', function () {
        var btn = $(this), res = $('#ai_key_result'), old = btn.html();
        btn.attr('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>'); res.html('');
        $.ajax({ type: 'POST', url: testUrl, dataType: 'json', data: { api_key: $('#ai_api_key').val(), model: $('#ai_model').val() } })
          .done(function (r) { var cls = (r && r.error === false) ? 'alert-success' : 'alert-danger'; res.html('<div class="alert ' + cls + ' py-2 mb-0">' + (r ? r.message : '') + '</div>'); })
          .fail(function () { res.html('<div class="alert alert-danger py-2 mb-0">Could not connect.</div>'); })
          .always(function () { btn.attr('disabled', false).html(old); });
      });

      /* ----- custom themed dropdowns (all native selects on the page) ----- */
      function initDropdowns() {
        $('.ra-control').each(function () {
          var $ctrl = $(this), $select = $ctrl.children('.ra-control__select');
          if (!$select.length || $ctrl.hasClass('ra-dd')) return;

          $select.addClass('ra-dd__native').attr('tabindex', '-1').attr('aria-hidden', 'true');
          var $label = $('<span class="ra-dd__label"></span>');
          $select.before($label);

          var $panel = $('<div class="ra-dd__panel" role="listbox"></div>');
          $select.find('option').each(function () {
            var $o = $(this);
            $('<div class="ra-dd__opt" role="option"></div>')
              .attr('data-value', $o.val())
              .text($o.text())
              .append('<i class="fas fa-check ra-dd__check" aria-hidden="true"></i>')
              .appendTo($panel);
          });
          $ctrl.addClass('ra-dd').append($panel)
            .attr({ role: 'button', 'aria-haspopup': 'listbox', 'aria-expanded': 'false' });
          if (!$ctrl.is('button')) $ctrl.attr('tabindex', '0');
          syncLabel($ctrl);
        });
      }
      function syncLabel($ctrl) {
        var $select = $ctrl.children('.ra-control__select'), val = String($select.val());
        $ctrl.find('.ra-dd__label').text($select.find('option:selected').text());
        $ctrl.find('.ra-dd__opt').each(function () {
          var $o = $(this);
          $o.removeClass('is-active');
          $o.toggleClass('is-selected', $o.attr('data-value') === val);
        });
      }
      function openDD($ctrl) {
        $('.ra-dd.is-open').not($ctrl).each(function () { closeDD($(this)); });
        $ctrl.addClass('is-open').attr('aria-expanded', 'true');
        var $sel = $ctrl.find('.ra-dd__opt.is-selected');
        $ctrl.find('.ra-dd__opt').removeClass('is-active');
        ($sel.length ? $sel : $ctrl.find('.ra-dd__opt').first()).addClass('is-active');
      }
      function closeDD($ctrl) { $ctrl.removeClass('is-open').attr('aria-expanded', 'false'); }
      function chooseOpt($ctrl, $opt) {
        var $select = $ctrl.children('.ra-control__select'), val = $opt.attr('data-value');
        if (String($select.val()) !== String(val)) {
          $select.val(val);
          syncLabel($ctrl);
          $select[0].dispatchEvent(new Event('change', { bubbles: true })); // runs inline onchange (filter submit)
        }
        closeDD($ctrl);
      }

      $(document).on('click', '.ra-dd', function (e) {
        if ($(e.target).closest('.ra-dd__opt').length) return;
        var $ctrl = $(this);
        $ctrl.hasClass('is-open') ? closeDD($ctrl) : openDD($ctrl);
      });
      $(document).on('click', '.ra-dd__opt', function (e) {
        e.stopPropagation();
        chooseOpt($(this).closest('.ra-dd'), $(this));
      });
      $(document).on('mouseenter', '.ra-dd__opt', function () {
        $(this).addClass('is-active').siblings().removeClass('is-active');
      });
      $(document).on('keydown', '.ra-dd', function (e) {
        var $ctrl = $(this), open = $ctrl.hasClass('is-open');
        if (!open && (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown' || e.key === 'ArrowUp')) { e.preventDefault(); openDD($ctrl); return; }
        if (!open) return;
        var $opts = $ctrl.find('.ra-dd__opt'), $act = $opts.filter('.is-active'), i = $opts.index($act);
        if (e.key === 'ArrowDown') { e.preventDefault(); $opts.removeClass('is-active').eq(Math.min(i + 1, $opts.length - 1)).addClass('is-active'); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); $opts.removeClass('is-active').eq(Math.max(i - 1, 0)).addClass('is-active'); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); if ($act.length) chooseOpt($ctrl, $act); }
        else if (e.key === 'Escape') { e.preventDefault(); closeDD($ctrl); }
      });
      $(document).on('click', function (e) { if (!$(e.target).closest('.ra-dd').length) $('.ra-dd.is-open').each(function () { closeDD($(this)); }); });

      restoreEmailCfg();
      hydrate();
      initDropdowns();
    })();
  </script>

</body>
</html>
