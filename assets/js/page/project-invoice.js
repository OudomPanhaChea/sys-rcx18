"use strict";

/* =====================================================================
 *  Project Invoice modal
 *  - Fetches the invoice payload from projects/get_invoice. The first
 *    time it is synced from the project; once saved it comes from the
 *    project_invoices snapshot and project edits no longer affect it.
 *  - Preview: one or more A4 sheets (794 x 1123 px @96dpi), flowing the
 *    content blocks onto extra pages when they overflow, 2-per-row
 *    (1 on small screens) scaled to fit. Exports JPG / multi-page PDF.
 *  - Edit mode: a single unscaled sheet whose fields are inputs styled
 *    into the paper; items can be added/removed, discount and booking
 *    are editable, totals recompute live. Save posts to
 *    projects/save_invoice.
 * ===================================================================== */

(function ($) {
  var A4_W = 794;
  var A4_H = 1123;
  var currentInvoice = null;
  var currentProjectId = null;
  var isEditing = false;

  function esc(s) {
    // Quote entities too — esc() output is also used inside value="..."
    // attributes on the edit sheet.
    return $("<div>")
      .text(s == null ? "" : String(s))
      .html()
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function money(inv, n) {
    var v = parseFloat(n);
    if (isNaN(v)) v = 0;
    return (
      inv.currency_symbol +
      v.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }

  function num(n) {
    var v = parseFloat(n);
    return isNaN(v) ? 0 : v;
  }

  function totals(inv) {
    var subtotal = 0;
    $.each(inv.items || [], function (i, item) {
      subtotal += num(item.amount);
    });
    var discount =
      inv.discount_type === "percent"
        ? (subtotal * num(inv.discount_value)) / 100
        : num(inv.discount_value);
    if (discount > subtotal) discount = subtotal;
    var booking = num(inv.booking);
    return {
      subtotal: subtotal,
      discount: discount,
      booking: booking,
      balance: Math.max(subtotal - discount - booking, 0),
    };
  }

  /* ---- Small inline SVG icons (stroke follows .inv-billed__row svg) ---- */
  var ICONS = {
    building:
      '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
      '<rect x="4" y="3" width="16" height="18" rx="1.5"></rect><path d="M9 8h1.5M13.5 8H15M9 12h1.5M13.5 12H15M9.5 21v-4h5v4"></path></svg>',
    mail:
      '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
      '<rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3.5 7 8.5 6 8.5-6"></path></svg>',
    phone:
      '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
      '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L8.1 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.6 1.9z"></path></svg>',
  };

  /* Sheet footer, pinned to the bottom of the paper via margin-top:auto:
     static bank QR (ABA KHQR) on the left — '' when disabled or not
     uploaded, shown at natural ratio and preloaded by renderInvoice so
     pagination measures the real height — and a signature line with the
     admin's name on the right. */
  function footerBlock(inv) {
    var qr = inv.payment_qr
      ? '<div class="inv-pay">' +
          '<img class="inv-pay__qr" src="' + esc(inv.payment_qr) + '" alt="' + esc(invoice_i18n.scan_to_pay) + '">' +
        "</div>"
      : "";
    return (
      '<div class="inv-footer">' +
        qr +
        '<div class="inv-sign">' +
          '<div class="inv-sign__line"></div>' +
          '<div class="inv-sign__name">' + esc(inv.signed_by || "") + "</div>" +
        "</div>" +
      "</div>"
    );
  }

  function headerBlock(inv) {
    var addressBits = [inv.company_address, inv.company_city, inv.company_country].filter(function (x) {
      return x && String(x).trim() !== "";
    });
    return (
      '<div class="inv-header">' +
        "<div>" +
          '<div class="inv-header__company">' + esc(inv.company_name) + "</div>" +
          (addressBits.length ? '<div class="inv-header__address">' + esc(addressBits.join(", ")) + "</div>" : "") +
        "</div>" +
        '<div class="inv-header__title">' +
          '<div class="inv-header__word">' + esc(invoice_i18n.invoice) + "</div>" +
          '<div class="inv-header__meta"><strong>' + esc(inv.invoice_no) + "</strong><br>" +
            esc(invoice_i18n.date_issued) + ": " + esc(inv.issued_date) + "</div>" +
        "</div>" +
      "</div>"
    );
  }

  /* ---- Preview blocks (in flow order). Each block is one indivisible
   *      chunk; pagination moves whole blocks to the next sheet. ---- */
  function buildBlocks(inv) {
    var blocks = [];
    blocks.push(headerBlock(inv));

    var billed = '<div class="inv-billed">' +
      '<div class="inv-label">' + esc(invoice_i18n.billed_to) + "</div>" +
      '<div class="inv-billed__name">' + esc(inv.client_name || "—") + "</div>";
    if (inv.client_company) billed += '<div class="inv-billed__company">' + esc(inv.client_company) + "</div>";
    if (inv.client_email) billed += '<div class="inv-billed__row">' + ICONS.mail + "<span>" + esc(inv.client_email) + "</span></div>";
    if (inv.client_phone) billed += '<div class="inv-billed__row">' + ICONS.phone + "<span>" + esc(inv.client_phone) + "</span></div>";
    billed += "</div>";
    blocks.push(billed);

    var rows = "";
    $.each(inv.items || [], function (i, item) {
      rows +=
        "<tr><td>" + esc(item.description) +
          (item.details ? '<div class="sub">' + esc(item.details) + "</div>" : "") +
        "</td>" +
        '<td class="num">' + money(inv, item.amount) + "</td></tr>";
    });
    blocks.push(
      '<table class="inv-table">' +
        "<thead><tr>" +
          "<th>" + esc(invoice_i18n.description) + "</th>" +
          '<th class="num">' + esc(invoice_i18n.amount) + "</th>" +
        "</tr></thead>" +
        "<tbody>" + rows + "</tbody>" +
      "</table>"
    );

    var t = totals(inv);
    var totalsHtml =
      '<div class="inv-totals">' +
        '<div class="row-line"><span>' + esc(invoice_i18n.subtotal) + "</span><span>" + money(inv, t.subtotal) + "</span></div>";
    if (t.discount > 0) {
      var discountLabel = invoice_i18n.discount +
        (inv.discount_type === "percent" ? " (" + num(inv.discount_value) + "%)" : "");
      totalsHtml += '<div class="row-line"><span>' + esc(discountLabel) + "</span><span>&minus; " + money(inv, t.discount) + "</span></div>";
    }
    if (t.booking > 0) {
      totalsHtml += '<div class="row-line"><span>' + esc(invoice_i18n.booking_paid) + "</span><span>&minus; " + money(inv, t.booking) + "</span></div>";
    }
    totalsHtml += '<div class="row-line total"><span>' + esc(invoice_i18n.balance_due) + "</span><span>" + money(inv, t.balance) + "</span></div></div>";
    blocks.push(totalsHtml);

    // Notes flow paragraph-by-paragraph so long text can paginate.
    var notes = (inv.notes || "").trim();
    if (notes !== "") {
      blocks.push('<div class="inv-label">' + esc(invoice_i18n.notes) + "</div>");
      $.each(notes.split(/\n+/), function (i, p) {
        if (p.trim() !== "") {
          blocks.push('<div class="inv-notes"><p>' + esc(p.trim()) + "</p></div>");
        }
      });
    }

    // Last block: footer pins itself to the sheet's bottom edge; pagination
    // bumps it to a fresh page when the last page is already full.
    blocks.push(footerBlock(inv));

    return blocks;
  }

  function newSheet($pages, inv, pageNo) {
    var $holder = $('<div class="invoice-sheet-holder"></div>');
    var $sheet = $('<div class="invoice-sheet"></div>');
    var $content = $('<div class="invoice-sheet__content"></div>');

    if (pageNo > 1) {
      // Small continuation header so every exported page stands alone.
      $content.append(
        '<div class="inv-header" style="padding-bottom:14px;">' +
          '<div class="inv-header__company" style="font-size:16px;">' + esc(inv.company_name) + "</div>" +
          '<div class="inv-header__meta"><strong>' + esc(inv.invoice_no) + "</strong></div>" +
        "</div>" +
        '<hr class="inv-rule">'
      );
    }

    $sheet.append($content);
    $holder.append($sheet);
    $pages.append($holder);
    return $content;
  }

  var qrReadyUrl = "";

  function renderInvoice(inv) {
    // Pagination measures block heights, so the natural-ratio QR image must
    // be loaded (cached) before the sheets are laid out.
    if (inv.payment_qr && qrReadyUrl !== inv.payment_qr) {
      var img = new Image();
      img.onload = img.onerror = function () {
        qrReadyUrl = inv.payment_qr;
        renderInvoice(inv);
      };
      img.src = inv.payment_qr;
      return;
    }

    setEditingChrome(false);
    var $pages = $("#invoice-pages").empty();
    var blocks = buildBlocks(inv);
    var pageNo = 1;
    var $content = newSheet($pages, inv, pageNo);

    $.each(blocks, function (i, html) {
      var $block = $(html);
      $content.append($block);
      if ($content[0].scrollHeight > $content[0].clientHeight + 1 && $content.children().length > 1) {
        $block.detach();
        pageNo++;
        $content = newSheet($pages, inv, pageNo);
        $content.append($block);
      }
    });

    $pages.toggleClass("is-single", pageNo === 1);
    $("#invoice-modal-no").text(inv.invoice_no);
    fitSheets();
  }

  /* ---- Scale each fixed-size sheet down to its responsive holder ---- */
  function fitSheets() {
    $("#invoice-pages .invoice-sheet-holder").not(".is-edit-holder").each(function () {
      var scale = this.clientWidth / A4_W;
      if (scale > 0) {
        $(this).find(".invoice-sheet").css("transform", "scale(" + scale + ")");
      }
    });
  }

  $(window).on("resize", fitSheets);

  /* =====================================================================
   *  Edit mode
   * ===================================================================== */

  function setEditingChrome(editing) {
    isEditing = editing;
    $("#invoice-view-actions").toggle(!editing);
    $("#invoice-edit-actions").toggle(editing);
    $("#invoice-pages").toggleClass("is-editing", editing);
    $("#project-invoice-modal .modal-body").toggleClass("is-editing-body", editing);
  }

  function textInput(cls, name, value, placeholder) {
    return (
      '<input type="text" class="inv-input ' + (cls || "") + '" data-field="' + name + '" value="' +
      esc(value == null ? "" : value) + '" placeholder="' + esc(placeholder || "") + '" aria-label="' + esc(placeholder || name) + '">'
    );
  }

  function amountInput(name, value) {
    return (
      '<input type="number" step="0.01" min="0" inputmode="decimal" class="inv-input inv-input--num" data-field="' +
      name + '" value="' + num(value) + '" aria-label="' + esc(invoice_i18n.amount) + '">'
    );
  }

  function itemRow(inv, item) {
    return (
      '<tr class="inv-item-row">' +
        "<td>" +
          textInput("", "item-description", item.description, invoice_i18n.description) +
          '<div class="sub">' + textInput("", "item-details", item.details, invoice_i18n.details) + "</div>" +
        "</td>" +
        '<td class="num">' + amountInput("item-amount", item.amount) +
          '<button type="button" class="inv-item-remove" title="' + esc(invoice_i18n.remove) + '" aria-label="' + esc(invoice_i18n.remove) + '">&times;</button>' +
        "</td>" +
      "</tr>"
    );
  }

  function renderEditor(inv) {
    setEditingChrome(true);
    var $pages = $("#invoice-pages").empty().removeClass("is-single");
    var $holder = $('<div class="invoice-sheet-holder is-edit-holder"></div>');
    var $sheet = $('<div class="invoice-sheet invoice-sheet--edit"></div>');
    var $content = $('<div class="invoice-sheet__content"></div>');

    var addressBits = [inv.company_address, inv.company_city, inv.company_country].filter(function (x) {
      return x && String(x).trim() !== "";
    });

    var html =
      '<div class="inv-header">' +
        "<div>" +
          '<div class="inv-header__company">' + esc(inv.company_name) + "</div>" +
          (addressBits.length ? '<div class="inv-header__address">' + esc(addressBits.join(", ")) + "</div>" : "") +
        "</div>" +
        '<div class="inv-header__title">' +
          '<div class="inv-header__word">' + esc(invoice_i18n.invoice) + "</div>" +
          '<div class="inv-header__meta">' +
            '<input type="text" class="inv-input inv-input--inline" data-field="invoice_no" value="' + esc(inv.invoice_no) +
              '" style="width:150px;text-align:right;font-weight:600;color:#0f172a;" aria-label="' + esc(invoice_i18n.invoice) + ' #"><br>' +
            esc(invoice_i18n.date_issued) + ": " +
            '<input type="date" class="inv-input inv-input--inline" data-field="issued_date" value="' + esc(inv.issued_date_raw) +
              '" style="width:150px;" aria-label="' + esc(invoice_i18n.date_issued) + '">' +
          "</div>" +
        "</div>" +
      "</div>" +

      '<div class="inv-billed">' +
        '<div class="inv-label">' + esc(invoice_i18n.billed_to) + "</div>" +
        '<div class="inv-billed__name">' + textInput("", "client_name", inv.client_name, invoice_i18n.client_name) + "</div>" +
        '<div class="inv-billed__company">' + textInput("", "client_company", inv.client_company, invoice_i18n.company) + "</div>" +
        '<div class="inv-billed__row">' + ICONS.mail + textInput("", "client_email", inv.client_email, invoice_i18n.email) + "</div>" +
        '<div class="inv-billed__row">' + ICONS.phone + textInput("", "client_phone", inv.client_phone, invoice_i18n.phone) + "</div>" +
      "</div>" +

      '<table class="inv-table">' +
        "<thead><tr>" +
          "<th>" + esc(invoice_i18n.description) + "</th>" +
          '<th class="num">' + esc(invoice_i18n.amount) + "</th>" +
        "</tr></thead>" +
        '<tbody id="inv-edit-items"></tbody>' +
      "</table>" +
      '<button type="button" class="inv-add-item" id="inv-add-item">+ ' + esc(invoice_i18n.add_item) + "</button>" +

      '<div class="inv-totals">' +
        '<div class="row-line"><span>' + esc(invoice_i18n.subtotal) + '</span><span id="inv-edit-subtotal"></span></div>' +
        '<div class="row-line"><span>' + esc(invoice_i18n.discount) + "</span>" +
          '<span class="inv-discount-ctl">' +
            '<select class="inv-input" data-field="discount_type" aria-label="' + esc(invoice_i18n.discount) + '">' +
              '<option value="amount"' + (inv.discount_type !== "percent" ? " selected" : "") + ">" + esc(inv.currency_symbol) + "</option>" +
              '<option value="percent"' + (inv.discount_type === "percent" ? " selected" : "") + ">%</option>" +
            "</select>" +
            '<input type="number" step="0.01" min="0" inputmode="decimal" class="inv-input inv-input--num" data-field="discount_value" value="' +
              num(inv.discount_value) + '" aria-label="' + esc(invoice_i18n.discount) + '">' +
          "</span>" +
        "</div>" +
        '<div class="row-line"><span>' + esc(invoice_i18n.booking_paid) + "</span>" +
          '<span class="inv-discount-ctl">' + amountInput("booking", inv.booking) + "</span>" +
        "</div>" +
        '<div class="row-line total"><span>' + esc(invoice_i18n.balance_due) + '</span><span id="inv-edit-balance"></span></div>' +
      "</div>" +

      '<div class="inv-label">' + esc(invoice_i18n.notes) + "</div>" +
      '<div class="inv-notes"><textarea class="inv-input" data-field="notes" rows="3" placeholder="' +
        esc(invoice_i18n.notes_placeholder) + '" aria-label="' + esc(invoice_i18n.notes) + '">' + esc(inv.notes || "") + "</textarea></div>" +

      footerBlock(inv);

    $content.html(html);
    $sheet.append($content);
    $holder.append($sheet);
    $pages.append($holder);

    var $tbody = $("#inv-edit-items");
    var items = (inv.items && inv.items.length) ? inv.items : [{ description: "", details: "", amount: 0 }];
    $.each(items, function (i, item) {
      $tbody.append(itemRow(inv, item));
    });

    refreshEditorTotals();
  }

  /* ---- Read the edited sheet back into a plain object ---- */
  function collectEditor() {
    var $sheet = $("#invoice-pages .invoice-sheet--edit");
    if (!$sheet.length) return null;

    function val(field) {
      return $.trim($sheet.find('[data-field="' + field + '"]').val() || "");
    }

    var items = [];
    $sheet.find(".inv-item-row").each(function () {
      var $row = $(this);
      items.push({
        description: $.trim($row.find('[data-field="item-description"]').val() || ""),
        details: $.trim($row.find('[data-field="item-details"]').val() || ""),
        amount: num($row.find('[data-field="item-amount"]').val()),
      });
    });

    return {
      invoice_no: val("invoice_no"),
      issued_date: val("issued_date"),
      client_name: val("client_name"),
      client_company: val("client_company"),
      client_email: val("client_email"),
      client_phone: val("client_phone"),
      booking: num(val("booking")),
      discount_type: val("discount_type") === "percent" ? "percent" : "amount",
      discount_value: num(val("discount_value")),
      notes: val("notes"),
      items: items,
    };
  }

  function refreshEditorTotals() {
    var edited = collectEditor();
    if (!edited || !currentInvoice) return;
    var t = totals(edited);
    $("#inv-edit-subtotal").text(money(currentInvoice, t.subtotal));
    $("#inv-edit-balance").text(money(currentInvoice, t.balance));
  }

  $(document).on("input change", "#project-invoice-modal .inv-input", refreshEditorTotals);

  $(document).on("click", "#inv-add-item", function () {
    if (!currentInvoice) return;
    var $row = $(itemRow(currentInvoice, { description: "", details: "", amount: 0 }));
    $("#inv-edit-items").append($row);
    $row.find('[data-field="item-description"]').trigger("focus");
  });

  $(document).on("click", ".inv-item-remove", function () {
    var $rows = $("#inv-edit-items .inv-item-row");
    if ($rows.length <= 1) {
      // Keep at least one row on the sheet; just clear it instead.
      $rows.find("input").val("").first().trigger("focus");
    } else {
      $(this).closest("tr").remove();
    }
    refreshEditorTotals();
  });

  $(document).on("click", "#invoice-edit-btn", function () {
    if (!currentInvoice) return;
    renderEditor(currentInvoice);
  });

  $(document).on("click", "#invoice-cancel-btn", function () {
    if (!currentInvoice) return;
    renderInvoice(currentInvoice);
  });

  $(document).on("click", "#invoice-save-btn", function () {
    if (!currentInvoice || !currentProjectId) return;
    var edited = collectEditor();
    if (!edited) return;

    var hasItem = false;
    $.each(edited.items, function (i, item) {
      if (item.description !== "" || item.details !== "" || item.amount !== 0) hasItem = true;
    });
    if (!hasItem || edited.invoice_no === "") {
      iziToast.error({ title: something_wrong_try_again, message: "", position: "topRight" });
      return;
    }

    var $btn = $(this);
    $btn.addClass("btn-progress");

    $.ajax({
      type: "POST",
      url: base_url + "projects/save_invoice",
      data: {
        project_id: currentProjectId,
        invoice_no: edited.invoice_no,
        issued_date: edited.issued_date,
        client_name: edited.client_name,
        client_company: edited.client_company,
        client_email: edited.client_email,
        client_phone: edited.client_phone,
        booking: edited.booking,
        discount_type: edited.discount_type,
        discount_value: edited.discount_value,
        notes: edited.notes,
        items: JSON.stringify(edited.items),
      },
      dataType: "json",
      success: function (result) {
        $btn.removeClass("btn-progress");
        if (result["error"] == false) {
          iziToast.success({ title: result["message"], message: "", position: "topRight" });
          // Re-fetch so dates/numbers come back in canonical server format.
          loadInvoice(currentProjectId, null);
        } else {
          iziToast.error({ title: result["message"] || something_wrong_try_again, message: "", position: "topRight" });
        }
      },
      error: function () {
        $btn.removeClass("btn-progress");
        iziToast.error({ title: something_wrong_try_again, message: "", position: "topRight" });
      },
    });
  });

  /* =====================================================================
   *  Open / load
   * ===================================================================== */

  // Pagination needs real element heights, so the sheets are built only
  // once the modal is visible.
  $(document).on("shown.bs.modal", "#project-invoice-modal", function () {
    if (currentInvoice) {
      renderInvoice(currentInvoice);
    }
  });

  $(document).on("hidden.bs.modal", "#project-invoice-modal", function () {
    setEditingChrome(false);
  });

  function loadInvoice(projectId, $btn) {
    if ($btn) $btn.addClass("btn-progress");

    $.ajax({
      type: "POST",
      url: base_url + "projects/get_invoice",
      data: { project_id: projectId },
      dataType: "json",
      success: function (result) {
        if ($btn) $btn.removeClass("btn-progress");
        if (result["error"] == false && result["data"]) {
          currentInvoice = result["data"];
          currentProjectId = projectId;
          var $modal = $("#project-invoice-modal");
          $("#invoice-pages").empty();
          $("#invoice-modal-no").text(currentInvoice.invoice_no);
          if ($modal.hasClass("show")) {
            renderInvoice(currentInvoice);
          } else {
            $modal.modal("show");
          }
        } else {
          iziToast.error({ title: result["message"] || something_wrong_try_again, message: "", position: "topRight" });
        }
      },
      error: function () {
        if ($btn) $btn.removeClass("btn-progress");
        iziToast.error({ title: something_wrong_try_again, message: "", position: "topRight" });
      },
    });
  }

  $(document).on("click", ".project-invoice-btn", function (e) {
    e.preventDefault();
    loadInvoice($(this).data("id"), $(this));
  });

  /* ---- Export helpers ---- */
  function captureSheets() {
    // Clone each sheet into an off-screen stage at scale 1 so html2canvas
    // captures the full-resolution page, not the shrunken preview.
    var $stage = $("#invoice-capture-stage").empty();
    var clones = [];
    $("#invoice-pages .invoice-sheet").each(function () {
      var $clone = $(this).clone().css("transform", "none").css("position", "relative");
      $stage.append($clone);
      clones.push($clone[0]);
    });

    var chain = Promise.resolve([]);
    $.each(clones, function (i, el) {
      chain = chain.then(function (canvases) {
        return html2canvas(el, {
          scale: 2,
          backgroundColor: "#ffffff",
          width: A4_W,
          height: A4_H,
          logging: false,
        }).then(function (canvas) {
          canvases.push(canvas);
          return canvases;
        });
      });
    });

    return chain.then(
      function (canvases) {
        $stage.empty();
        return canvases;
      },
      function (err) {
        $stage.empty();
        throw err;
      }
    );
  }

  function withButtonProgress($btn, work) {
    var original = $btn.html();
    $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> ' + wait);
    return work().then(
      function () {
        $btn.prop("disabled", false).html(original);
      },
      function () {
        $btn.prop("disabled", false).html(original);
        iziToast.error({ title: something_wrong_try_again, message: "", position: "topRight" });
      }
    );
  }

  function triggerDownload(href, filename) {
    var a = document.createElement("a");
    a.href = href;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  $(document).on("click", "#invoice-download-jpg", function () {
    if (!currentInvoice || isEditing) return;
    var inv = currentInvoice;
    withButtonProgress($(this), function () {
      return captureSheets().then(function (canvases) {
        $.each(canvases, function (i, canvas) {
          var name = canvases.length === 1 ? inv.invoice_no + ".jpg" : inv.invoice_no + "-page-" + (i + 1) + ".jpg";
          // Small stagger keeps browsers from swallowing multi-file downloads.
          setTimeout(function () {
            triggerDownload(canvas.toDataURL("image/jpeg", 0.92), name);
          }, i * 400);
        });
      });
    });
  });

  $(document).on("click", "#invoice-download-pdf", function () {
    if (!currentInvoice || isEditing) return;
    var inv = currentInvoice;
    withButtonProgress($(this), function () {
      return captureSheets().then(function (canvases) {
        var pdf = new window.jspdf.jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });
        $.each(canvases, function (i, canvas) {
          if (i > 0) pdf.addPage();
          pdf.addImage(canvas.toDataURL("image/jpeg", 0.92), "JPEG", 0, 0, 210, 297);
        });
        pdf.save(inv.invoice_no + ".pdf");
      });
    });
  });
})(jQuery);
