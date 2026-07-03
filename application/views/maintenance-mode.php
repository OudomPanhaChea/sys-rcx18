<?php $this->load->view('includes/head'); ?>
</head>
<body>
<div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="page-error">
          <div class="page-inner">
            <h1><?=$this->lang->line('maintenance_mode_title')?htmlspecialchars($this->lang->line('maintenance_mode_title')):'Maintenance Mode'?></h1>
            <div class="page-description">
            <?=$this->lang->line('maintenance_mode_message')?htmlspecialchars($this->lang->line('maintenance_mode_message')):'Website is under maintenance.'?>
            </div>
          </div>
        </div>
        <div class="simple-footer mt-5">
        <?=htmlspecialchars(footer_text())?>
        </div>
      </div>
    </section>
  </div>
</body>
</html>