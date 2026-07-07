<form action="<?=base_url('settings/save-telegram-setting')?>" method="POST" id="telegram-form">

    <div class="card-header">
      <h4><i class="fab fa-telegram-plane"></i> <?=$this->lang->line('telegram')?htmlspecialchars($this->lang->line('telegram')):'Telegram'?></h4>
    </div>
    <div class="card-body row">

      <div class="form-group col-md-12">
        <div class="alert alert-info">
          <?=$this->lang->line('telegram_help')?$this->lang->line('telegram_help'):'Every notification an admin receives is mirrored to your Telegram group in real-time.'?>
          <ol class="mb-0 mt-2 pl-3">
            <li><?=$this->lang->line('telegram_step_bot')?$this->lang->line('telegram_step_bot'):'Create a bot with <b>@BotFather</b> and copy its <b>token</b>.'?></li>
            <li><?=$this->lang->line('telegram_step_group')?$this->lang->line('telegram_step_group'):'Add the bot to your group, then get the group <b>chat ID</b> (e.g. via <b>@getidsbot</b>). Group IDs usually start with <b>-100</b>.'?></li>
          </ol>
        </div>
      </div>

      <div class="form-group col-md-12">
        <label class="d-block"><?=$this->lang->line('enable_telegram_notifications')?htmlspecialchars($this->lang->line('enable_telegram_notifications')):'Enable Telegram Notifications'?></label>
        <label class="custom-switch mt-2 pl-0">
          <input type="checkbox" name="enabled" value="1" class="custom-switch-input" <?=(isset($telegram_enabled) && $telegram_enabled == '1')?'checked':''?>>
          <span class="custom-switch-indicator"></span>
          <span class="custom-switch-description"><?=$this->lang->line('send_admin_notifications_to_telegram')?htmlspecialchars($this->lang->line('send_admin_notifications_to_telegram')):'Send admin notifications to the Telegram group'?></span>
        </label>
      </div>

      <div class="form-group col-md-12">
        <label><?=$this->lang->line('bot_token')?htmlspecialchars($this->lang->line('bot_token')):'Bot Token'?></label>
        <input type="text" name="bot_token" id="telegram_bot_token" value="<?=(isset($telegram_bot_token) && !empty($telegram_bot_token))?htmlspecialchars($telegram_bot_token):''?>" class="form-control" placeholder="123456789:AAExxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
      </div>

      <div class="form-group col-md-12">
        <label><?=$this->lang->line('group_chat_id')?htmlspecialchars($this->lang->line('group_chat_id')):'Group Chat ID'?></label>
        <input type="text" name="chat_id" id="telegram_chat_id" value="<?=(isset($telegram_chat_id) && !empty($telegram_chat_id))?htmlspecialchars($telegram_chat_id):''?>" class="form-control" placeholder="-1001234567890">
      </div>

      <div class="form-group col-md-12">
        <label><?=$this->lang->line('topic_id')?htmlspecialchars($this->lang->line('topic_id')):'Topic ID'?> <small class="text-muted">(<?=$this->lang->line('optional')?htmlspecialchars($this->lang->line('optional')):'optional'?>)</small></label>
        <input type="text" name="thread_id" id="telegram_thread_id" value="<?=(isset($telegram_thread_id) && $telegram_thread_id !== '')?htmlspecialchars($telegram_thread_id):''?>" class="form-control" placeholder="e.g. 42">
        <small class="form-text text-muted"><?=$this->lang->line('topic_id_help')?$this->lang->line('topic_id_help'):'For supergroups with <b>Topics</b> enabled, send into a specific topic. Open the topic in Telegram Web/Desktop &mdash; the number after the last <b>/</b> in the URL is the Topic ID. Leave blank to post to the group\'s general chat.'?></small>
      </div>

      <div class="form-group col-md-12">
        <button type="button" class="btn btn-outline-primary" id="telegram_test_btn">
          <i class="fas fa-paper-plane"></i> <?=$this->lang->line('send_test_message')?htmlspecialchars($this->lang->line('send_test_message')):'Send Test Message'?>
        </button>
        <span id="telegram_test_result" class="ml-2"></span>
      </div>

    </div>

    <div class="card-footer bg-whitesmoke text-md-right">
      <button class="btn btn-primary savebtn"><?=$this->lang->line('save_changes')?$this->lang->line('save_changes'):'Save Changes'?></button>
    </div>
    <div class="result"></div>
</form>
