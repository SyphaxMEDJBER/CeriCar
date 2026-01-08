function updateNavOffset() {
  var $nav = $(".navbar");
  if (!$nav.length) {
    return;
  }
  var height = $nav.outerHeight();
  if (!height) {
    return;
  }
  document.documentElement.style.setProperty("--nav-height", height + "px");
}

$(function () {
  updateNavOffset();
  $(window).on("resize", updateNavOffset);

  $(document).on("click", ".js-profile-load", function (e) {
    e.preventDefault();

    var url = $(this).attr("href");
    var $container = $("#profile-content");

    if (!url || !$container.length) {
      window.location.href = url;
      return;
    }

    if (url.indexOf("embed=1") === -1) {
      url += (url.indexOf("?") === -1 ? "?" : "&") + "embed=1";
    }

    $container
      .addClass("profile-loading")
      .html('<div class="text-center py-4">Chargement...</div>');

    $.ajax({
      url: url,
      type: "GET",
      dataType: "html",
      success: function (html) {
        $container.removeClass("profile-loading").html(html);
        $("html, body").animate({ scrollTop: $container.offset().top - 90 }, 250);
      },
      error: function () {
        $container.removeClass("profile-loading").empty();
        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") {
          window.CeriCar.showGlobalNotif("danger", "Erreur de chargement.");
        }
      }
    });
  });

  $(document).on("submit", ".proposer-form", function (e) {
    e.preventDefault();

    var $form = $(this);

    $.ajax({
      url: $form.attr("action"),
      type: "POST",
      dataType: "json",
      data: $form.serialize(),
      success: function (res) {
        if (res && res.status === "success") {
          if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") {
            window.CeriCar.showGlobalNotif("success", res.message || "Voyage propose.");
          }
          if (res.reset && $form.length) {
            $form[0].reset();
          }
          return;
        }

        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") {
          window.CeriCar.showGlobalNotif(
            "danger",
            (res && res.message) ? res.message : "Erreur.",
            res && res.errors ? res.errors : []
          );
        }
      },
      error: function () {
        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") {
          window.CeriCar.showGlobalNotif("danger", "Erreur AJAX. Voir console.");
        }
      }
    });
  });
});
