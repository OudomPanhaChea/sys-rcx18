<form action="<?=base_url('settings/save-payment-qr-setting')?>" method="POST" id="payment-qr-form" enctype="multipart/form-data">

    <div class="card-header">
      <h4><i class="fas fa-qrcode"></i> <?=$this->lang->line('payment_qr')?htmlspecialchars($this->lang->line('payment_qr')):'Payment QR'?></h4>
    </div>
    <div class="card-body row">

      <div class="form-group col-md-12">
        <div class="alert alert-info">
          <?=$this->lang->line('payment_qr_help')?$this->lang->line('payment_qr_help'):'Upload your bank payment QR (e.g. <b>ABA KHQR</b> from ABA Mobile &gt; My QR). It is printed on every project invoice so clients can scan it with their banking app to pay.'?>
        </div>
      </div>

      <div class="form-group col-md-12">
        <label class="d-block"><?=$this->lang->line('show_payment_qr_on_invoices')?htmlspecialchars($this->lang->line('show_payment_qr_on_invoices')):'Show payment QR on invoices'?></label>
        <label class="custom-switch mt-2 pl-0">
          <input type="checkbox" name="enabled" value="1" class="custom-switch-input" <?=(isset($payment_qr_enabled) && $payment_qr_enabled == '1')?'checked':''?>>
          <span class="custom-switch-indicator"></span>
          <span class="custom-switch-description"><?=$this->lang->line('payment_qr_toggle_desc')?htmlspecialchars($this->lang->line('payment_qr_toggle_desc')):'Print the QR code on the invoice sheet'?></span>
        </label>
      </div>

      <div class="form-group col-md-12">
        <label><?=$this->lang->line('qr_code_image')?htmlspecialchars($this->lang->line('qr_code_image')):'QR Code Image'?> <small class="text-muted">(JPG/PNG, max 4MB)</small></label>
        <input type="file" name="qr_image" id="payment_qr_file" class="form-control" accept="image/png,image/jpeg">
        <input type="hidden" name="qr_image_old" value="<?=(isset($payment_qr_image) && !empty($payment_qr_image))?htmlspecialchars($payment_qr_image):''?>">
      </div>

      <div class="form-group col-md-12">
        <img id="payment_qr_preview"
             src="<?=(isset($payment_qr_image) && !empty($payment_qr_image))?base_url('assets/uploads/payment-qr/'.htmlspecialchars($payment_qr_image)):''?>"
             alt="<?=$this->lang->line('qr_code_image')?htmlspecialchars($this->lang->line('qr_code_image')):'QR Code Image'?>"
             style="max-width:220px;max-height:280px;<?=(isset($payment_qr_image) && !empty($payment_qr_image))?'':'display:none;'?>">
      </div>

    </div>

    <div class="card-footer bg-whitesmoke text-md-right">
      <button class="btn btn-primary savebtn"><?=$this->lang->line('save_changes')?$this->lang->line('save_changes'):'Save Changes'?></button>
    </div>
    <div class="result"></div>
</form>
