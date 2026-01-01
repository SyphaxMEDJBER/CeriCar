$(function () {
  $(document).on("submit", "#login-form, #signup-form", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $notif = $form.find(".auth-notif");
    const $globalNotif = $("#notif");

    $.ajax({
      url: $form.attr("action"),
      type: "POST",
      dataType: "json",
      data: $form.serialize(),
      success: function (res) {
        if (res.status === "success") {
          if ($notif.length) {
            $notif
              .removeClass("d-none alert-danger")
              .addClass("alert-success")
              .text(res.message || "Succes.");
          }

          if (res.redirect) {
            window.location.href = res.redirect;
          }
          return;
        }

        const errors = [];
        if (res.errors) {
          Object.keys(res.errors).forEach(function (key) {
            res.errors[key].forEach(function (msg) {
              errors.push(msg);
            });
          });
        }

        const errorText = errors.length ? errors.join("<br>") : (res.message || "Erreur.");
        if ($notif.length) {
          $notif
            .removeClass("d-none alert-success")
            .addClass("alert-danger")
            .html(errorText);
        }
        if ($globalNotif.length) {
          $globalNotif
            .removeClass("d-none alert-success")
            .addClass("alert-danger")
            .html(errorText);
        }
      },
      error: function (xhr) {
        if ($notif.length) {
          $notif
            .removeClass("d-none alert-success")
            .addClass("alert-danger")
            .text("Erreur AJAX. Voir console.");
        }
        if ($globalNotif.length) {
          $globalNotif
            .removeClass("d-none alert-success")
            .addClass("alert-danger")
            .text("Erreur AJAX. Voir console.");
        }
        console.log("AUTH AJAX ERROR", xhr.status, xhr.responseText);
      }
    });
  });
});
