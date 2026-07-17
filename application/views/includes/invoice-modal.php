<?php /* Project invoice modal — A4 sheet preview with JPG/PDF export and an
        in-place edit mode. Edits are saved to project_invoices(+items) and
        never write back to the project row.
        Loaded on projects.php and projects-detail.php AFTER includes/js. */ ?>

<style>
  /* ---- Modal shell ---- */
  .invoice-modal-dialog {
    max-width: 1100px;
    margin: 1.25rem auto;
  }
  #project-invoice-modal .modal-body {
    background: #eef1f4;
    padding: 1.25rem;
    max-height: calc(100vh - 220px);
    overflow-y: auto;
  }
  [data-theme="dark"] #project-invoice-modal .modal-body,
  body.dark #project-invoice-modal .modal-body {
    background: #1a2027;
  }

  /* ---- Page grid: 2 sheets per row, 1 on small screens ---- */
  .invoice-pages {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.25rem;
    align-items: start;
  }
  .invoice-pages.is-single {
    grid-template-columns: minmax(0, 680px);
    justify-content: center;
  }
  @media (max-width: 767.98px) {
    .invoice-pages,
    .invoice-pages.is-single {
      grid-template-columns: minmax(0, 1fr);
    }
  }

  /* ---- A4 holder keeps the paper ratio; the sheet inside is fixed-size and scaled ---- */
  .invoice-sheet-holder {
    position: relative;
    width: 100%;
    aspect-ratio: 210 / 297;
    overflow: hidden;
    border-radius: 4px;
    box-shadow: 0 10px 28px -12px rgba(15, 23, 42, 0.35);
    background: #ffffff;
  }

  /* ---- The A4 sheet (794 x 1123 px @96dpi). Always light, it is paper. ---- */
  .invoice-sheet {
    position: absolute;
    top: 0;
    left: 0;
    width: 794px;
    height: 1123px;
    padding: 56px 60px 70px;
    background: #ffffff;
    color: #0f172a;
    font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Arial, sans-serif;
    font-size: 14px;
    line-height: 1.55;
    transform-origin: 0 0;
    display: flex;
    flex-direction: column;
  }
  /* Flex column so the .inv-footer block can pin itself to the sheet's
     bottom edge with margin-top:auto. */
  .invoice-sheet__content { flex: 1 1 auto; overflow: hidden; display: flex; flex-direction: column; align-items: stretch; }

  /* ---- Invoice blocks ---- */
  .inv-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 40px; }
  .inv-header__company { font-size: 20px; font-weight: 700; letter-spacing: 0.01em; }
  .inv-header__address { color: #64748b; font-size: 12.5px; margin-top: 4px; }
  .inv-header__title { text-align: right; }
  .inv-header__word { font-size: 30px; font-weight: 300; letter-spacing: 0.28em; color: #0f172a; text-transform: uppercase; }
  .inv-header__meta { color: #64748b; font-size: 12.5px; margin-top: 6px; }
  .inv-header__meta strong { color: #0f172a; font-weight: 600; }
  /* Light hairline — only used on continuation-page headers now */
  .inv-rule { border: 0; border-top: 1px solid #e2e8f0; margin: 0 0 26px; }

  .inv-label { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; }

  /* ---- Billed To (plain typographic block — no box) ---- */
  .inv-billed {
    max-width: 400px;
    margin-bottom: 36px;
  }
  .inv-billed .inv-label { margin-bottom: 10px; }
  .inv-billed__name { font-size: 17px; font-weight: 700; line-height: 1.3; }
  .inv-billed__company { color: #334155; font-size: 13.5px; font-weight: 600; margin-top: 2px; }
  .inv-billed__row { display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px; margin-top: 7px; overflow-wrap: anywhere; }
  .inv-billed__row svg { flex: 0 0 auto; width: 14px; height: 14px; stroke: #94a3b8; }

  table.inv-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
  table.inv-table th { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #94a3b8; text-align: left; padding: 0 0 10px; border-bottom: 2px solid #0f172a; }
  table.inv-table th.num, table.inv-table td.num { text-align: right; white-space: nowrap; }
  table.inv-table td { padding: 14px 0; border-bottom: 1px solid #eef2f6; vertical-align: top; }
  table.inv-table td .sub { color: #64748b; font-size: 12.5px; margin-top: 2px; }
  .inv-totals { width: 320px; margin-left: auto; margin-bottom: 26px; }
  .inv-totals .row-line { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; color: #334155; }
  .inv-totals .row-line.total { border-top: 2px solid #0f172a; margin-top: 6px; padding-top: 12px; font-size: 17px; font-weight: 700; color: #0f172a; }

  /* ---- Sheet footer, pinned to the very bottom of the paper:
          payment QR (bank KHQR, natural ratio) left, signature right.
          No fixed QR height — html2canvas ignores object-fit and would
          squash the portrait KHQR image. ---- */
  .inv-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; padding-top: 34px; }
  .inv-pay { flex: 0 0 auto; }
  .inv-pay__qr { width: 190px; height: auto; display: block; }
  .inv-sign { width: 240px; margin-left: auto; text-align: center; padding-bottom: 4px; }
  .inv-sign__line { height: 44px; border-bottom: 1.5px solid #0f172a; }
  .inv-sign__name { margin-top: 9px; font-size: 13.5px; font-weight: 600; color: #0f172a; }

  .inv-notes p { color: #475569; font-size: 13px; margin: 0 0 10px; white-space: pre-line; }

  /* ---- Edit mode ----
     The edit sheet is a single unscaled page that grows with its content;
     inputs are styled to blend into the paper until hovered/focused. */
  .invoice-pages.is-editing { grid-template-columns: minmax(0, 794px); justify-content: center; }
  .invoice-sheet-holder.is-edit-holder { aspect-ratio: auto; overflow: visible; }
  .invoice-sheet--edit { position: relative; height: auto; min-height: 1123px; }
  .invoice-sheet--edit .invoice-sheet__content { overflow: visible; }
  @media (max-width: 860px) {
    #project-invoice-modal .modal-body.is-editing-body { overflow-x: auto; }
    .invoice-pages.is-editing { grid-template-columns: 794px; justify-content: start; }
  }

  .inv-input {
    font: inherit;
    color: inherit;
    background: transparent;
    border: 1px dashed transparent;
    border-radius: 4px;
    padding: 2px 6px;
    margin: -3px -7px;
    width: 100%;
    transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inv-input:hover { border-color: #cbd5e1; cursor: text; }
  .inv-input:focus { outline: none; border: 1px solid #2563eb; background: #f0f6ff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
  .inv-input::placeholder { color: #b6c2d2; font-weight: 400; }
  .inv-input--num { text-align: right; width: 130px; }
  .inv-input--inline { width: auto; }
  textarea.inv-input { resize: vertical; min-height: 74px; display: block; width: 100%; line-height: 1.55; }

  .inv-item-remove {
    position: absolute;
    right: -46px;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 1px solid #fecaca;
    background: #fff5f5;
    color: #dc2626;
    font-size: 15px;
    line-height: 1;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s ease, background-color 0.15s ease;
  }
  .inv-item-remove:focus { opacity: 1; outline: 2px solid #dc2626; outline-offset: 1px; }
  tr:hover .inv-item-remove { opacity: 1; }
  .inv-item-remove:hover { background: #fee2e2; }
  .invoice-sheet--edit table.inv-table td { position: relative; }
  .invoice-sheet--edit table.inv-table td.num { width: 150px; }

  .inv-add-item {
    display: block;
    width: 100%;
    margin: 10px 0 4px;
    padding: 9px 0;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    background: transparent;
    color: #2563eb;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: border-color 0.15s ease, background-color 0.15s ease;
  }
  .inv-add-item:hover { border-color: #2563eb; background: #f0f6ff; }
  .inv-add-item:focus { outline: 2px solid #2563eb; outline-offset: 2px; }

  .inv-discount-ctl { display: flex; align-items: center; gap: 6px; }
  .inv-discount-ctl select.inv-input {
    width: auto;
    margin: -3px 0;
    padding: 2px 4px;
    border: 1px dashed #cbd5e1;
    cursor: pointer;
  }
  .inv-discount-ctl select.inv-input:focus { border-style: solid; }
  .inv-discount-ctl .inv-input--num { width: 90px; }

  /* Footer action groups (spans) — restore the spacing modal-footer
     normally puts between direct button children */
  #invoice-view-actions .btn + .btn,
  #invoice-edit-actions .btn + .btn { margin-left: 0.35rem; }

  /* Off-screen stage for unscaled capture */
  #invoice-capture-stage { position: absolute; left: -12000px; top: 0; }
</style>

<div class="modal fade" id="project-invoice-modal" tabindex="-1" role="dialog" aria-labelledby="project-invoice-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered invoice-modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="project-invoice-title"><i class="fas fa-file-invoice-dollar mr-2"></i><?=$this->lang->line('invoice')?$this->lang->line('invoice'):'Invoice'?> <span id="invoice-modal-no" class="text-muted"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="invoice-pages" class="invoice-pages"></div>
      </div>
      <div class="modal-footer bg-whitesmoke">
        <span id="invoice-view-actions">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$this->lang->line('close')?$this->lang->line('close'):'Close'?></button>
          <?php if($this->ion_auth->is_admin() || permissions('project_edit')){ ?>
          <button type="button" class="btn btn-outline-secondary" id="invoice-edit-btn"><i class="fas fa-pen"></i> <?=$this->lang->line('edit')?$this->lang->line('edit'):'Edit'?></button>
          <?php } ?>
          <button type="button" class="btn btn-outline-primary" id="invoice-download-jpg"><i class="fas fa-image"></i> <?=$this->lang->line('download_jpg')?$this->lang->line('download_jpg'):'Download JPG'?></button>
          <button type="button" class="btn btn-primary" id="invoice-download-pdf"><i class="fas fa-file-pdf"></i> <?=$this->lang->line('download_pdf')?$this->lang->line('download_pdf'):'Download PDF'?></button>
        </span>
        <span id="invoice-edit-actions" style="display:none;">
          <button type="button" class="btn btn-secondary" id="invoice-cancel-btn"><?=$this->lang->line('cancel')?$this->lang->line('cancel'):'Cancel'?></button>
          <button type="button" class="btn btn-primary" id="invoice-save-btn"><i class="fas fa-check"></i> <?=$this->lang->line('save')?$this->lang->line('save'):'Save'?></button>
        </span>
      </div>
    </div>
  </div>
</div>

<div id="invoice-capture-stage" aria-hidden="true"></div>

<script>
  var invoice_i18n = {
    invoice: "<?=$this->lang->line('invoice')?htmlspecialchars($this->lang->line('invoice')):'Invoice'?>",
    billed_to: "<?=$this->lang->line('billed_to')?htmlspecialchars($this->lang->line('billed_to')):'Billed To'?>",
    date_issued: "<?=$this->lang->line('date_issued')?htmlspecialchars($this->lang->line('date_issued')):'Date Issued'?>",
    description: "<?=$this->lang->line('description')?htmlspecialchars($this->lang->line('description')):'Description'?>",
    amount: "<?=$this->lang->line('amount')?htmlspecialchars($this->lang->line('amount')):'Amount'?>",
    booking_paid: "<?=$this->lang->line('booking_paid')?htmlspecialchars($this->lang->line('booking_paid')):'Booking (paid)'?>",
    subtotal: "<?=$this->lang->line('subtotal')?htmlspecialchars($this->lang->line('subtotal')):'Subtotal'?>",
    discount: "<?=$this->lang->line('discount')?htmlspecialchars($this->lang->line('discount')):'Discount'?>",
    balance_due: "<?=$this->lang->line('balance_due')?htmlspecialchars($this->lang->line('balance_due')):'Balance Due'?>",
    notes: "<?=$this->lang->line('notes')?htmlspecialchars($this->lang->line('notes')):'Notes'?>",
    add_item: "<?=$this->lang->line('add_item')?htmlspecialchars($this->lang->line('add_item')):'Add Item'?>",
    remove: "<?=$this->lang->line('remove')?htmlspecialchars($this->lang->line('remove')):'Remove'?>",
    client_name: "<?=$this->lang->line('client_name')?htmlspecialchars($this->lang->line('client_name')):'Client Name'?>",
    company: "<?=$this->lang->line('company')?htmlspecialchars($this->lang->line('company')):'Company'?>",
    email: "<?=$this->lang->line('email')?htmlspecialchars($this->lang->line('email')):'Email'?>",
    phone: "<?=$this->lang->line('phone')?htmlspecialchars($this->lang->line('phone')):'Phone'?>",
    details: "<?=$this->lang->line('details')?htmlspecialchars($this->lang->line('details')):'Details'?>",
    notes_placeholder: "<?=$this->lang->line('invoice_notes_placeholder')?htmlspecialchars($this->lang->line('invoice_notes_placeholder')):'Optional notes shown at the bottom of the invoice…'?>",
    scan_to_pay: "<?=$this->lang->line('scan_to_pay')?htmlspecialchars($this->lang->line('scan_to_pay')):'Scan to Pay'?>"
  };
</script>
<script src="<?=base_url('assets/modules/html2canvas.min.js')?>"></script>
<script src="<?=base_url('assets/modules/jspdf.umd.min.js')?>"></script>
<script src="<?=base_url('assets/js/page/project-invoice.js')?>"></script>
