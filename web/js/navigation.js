/* global tryAutoReserve, updateNavOffset */
(function ($) {
  "use strict";

  function isSameOrigin(url) {
    try {
      var target = new URL(url, window.location.href);
      return target.origin === window.location.origin;
    } catch (e) {
      return false;
    }
  }

  function isLoginUrl(url) {
    try {
      var target = new URL(url, window.location.href);
      if (target.search && target.search.indexOf("r=site/login") !== -1) {
        return true;
      }
      return target.pathname.indexOf("site/login") !== -1;
    } catch (e) {
      return false;
    }
  }

  function shouldSkipLink($link) {
    var href = $link.attr("href");
    if (!href || href === "#") {
      return true;
    }
    if ($link.data("ajax") === false || $link.attr("data-ajax") === "false") {
      return true;
    }
    if ($link.attr("data-bs-toggle") || $link.attr("data-toggle")) {
      return true;
    }
    if ($link.attr("target") || $link.attr("download")) {
      return true;
    }
    if (href.indexOf("mailto:") === 0 || href.indexOf("tel:") === 0) {
      return true;
    }
    if (href.indexOf("javascript:") === 0) {
      return true;
    }
    return false;
  }

  function showGlobalNotif(type, message, errors, html) {
    var $notif = $("#notif");
    if (!$notif.length) {
      return;
    }

    $notif
      .removeClass("d-none alert-success alert-warning alert-danger alert-info")
      .addClass("alert-" + type);

    if (errors && errors.length) {
      var list = "<strong>Erreurs:</strong><ul class=\"mb-0\">";
      errors.forEach(function (err) {
        list += "<li>" + $("<div>").text(err).html() + "</li>";
      });
      list += "</ul>";
      $notif.html(list);
      return;
    }

    if (html) {
      $notif.html(html);
      return;
    }

    $notif.text(message || "");
  }

  function clearGlobalNotif() {
    var $notif = $("#notif");
    if (!$notif.length) {
      return;
    }
    $notif.addClass("d-none").removeClass("alert-success alert-warning alert-danger alert-info");
    $notif.empty();
  }

  function syncNotifFromContent($content) {
    var $alert = $content.find(".alert").first();
    if (!$alert.length) {
      return;
    }

    var type = "success";
    if ($alert.hasClass("alert-danger")) {
      type = "danger";
    } else if ($alert.hasClass("alert-warning")) {
      type = "warning";
    } else if ($alert.hasClass("alert-info")) {
      type = "info";
    }

    showGlobalNotif(type, null, null, $alert.html());
    $alert.remove();
  }

  function applyResponse(html, url, pushState) {
    var parser = new DOMParser();
    var doc = parser.parseFromString(html, "text/html");
    var $doc = $(doc);
    var $newContent = $doc.find("#page-content").first();
    if (!$newContent.length) {
      $newContent = $doc.find("main").first();
    }

    if ($newContent.length) {
      $("#page-content").html($newContent.html());
      syncNotifFromContent($("#page-content"));
    }

    var $newNav = $doc.find(".navbar").first();
    if ($newNav.length) {
      $(".navbar").replaceWith($newNav);
    }

    var newTitle = $doc.find("title").first().text();
    if (newTitle) {
      document.title = newTitle;
    }

    if (pushState) {
      window.history.pushState({}, "", url);
    }

    if (typeof updateNavOffset === "function") {
      updateNavOffset();
    }

    if (typeof tryAutoReserve === "function") {
      tryAutoReserve();
    }
  }

  function loadPage(url, pushState) {
    clearGlobalNotif();
    $.ajax({
      url: url,
      type: "GET",
      dataType: "html",
      success: function (html, status, xhr) {
        var finalUrl = (xhr && xhr.responseURL) ? xhr.responseURL : url;
        applyResponse(html, finalUrl, pushState);
      },
      error: function () {
        showGlobalNotif("danger", "Erreur de navigation.");
      }
    });
  }

  window.CeriCar = window.CeriCar || {};
  window.CeriCar.showGlobalNotif = showGlobalNotif;

  $(document).on("click", "a", function (e) {
    var $link = $(this);
    var href = $link.attr("href");

    if (shouldSkipLink($link)) {
      return;
    }
    if (!isSameOrigin(href)) {
      return;
    }
    if (isLoginUrl(href)) {
      return;
    }

    e.preventDefault();
    loadPage(href, false);
  });

  $(document).on("submit", "form", function (e) {
    var $form = $(this);
    var id = $form.attr("id");

    if (id === "login-form" || id === "signup-form" || id === "formRecherche") {
      return;
    }
    if ($form.hasClass("proposer-form")) {
      return;
    }
    if ($form.data("ajax") === false || $form.attr("data-ajax") === "false") {
      return;
    }

    var method = ($form.attr("method") || "get").toLowerCase();
    var action = $form.attr("action") || window.location.href;

    if (!isSameOrigin(action)) {
      return;
    }

    e.preventDefault();

    if (method === "get") {
      var query = $form.serialize();
      var url = action;
      if (query) {
        url += (url.indexOf("?") === -1 ? "?" : "&") + query;
      }
      loadPage(url, false);
      return;
    }

    $.ajax({
      url: action,
      type: method.toUpperCase(),
      data: $form.serialize(),
      dataType: "html",
      success: function (html, status, xhr) {
        var finalUrl = (xhr && xhr.responseURL) ? xhr.responseURL : action;
        applyResponse(html, finalUrl, false);
      },
      error: function () {
        showGlobalNotif("danger", "Erreur AJAX. Voir console.");
      }
    });
  });

  window.addEventListener("popstate", function () {
    loadPage(window.location.href, false);
  });
})(jQuery);
