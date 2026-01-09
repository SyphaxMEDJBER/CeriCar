// Navigation AJAX globale (remplace le contenu sans rechargement complet).
(function ($) { // IIFE avec jQuery.
  "use strict"; // Mode strict.

  // Securite : intercepter uniquement les liens internes.
  function isSameOrigin(url) { // Verifie l'origine.
    try {
      var target = new URL(url, window.location.href); // Parse URL.
      return target.origin === window.location.origin; // Compare origines.
    } catch (e) {
      return false; // URL invalide.
    }
  }

  function isLoginUrl(url) { // Detecte la page login.
    try {
      var target = new URL(url, window.location.href); // Parse URL.
      if (target.search && target.search.indexOf("r=site/login") !== -1) { // Yii route.
        return true; // C'est login.
      }
      return target.pathname.indexOf("site/login") !== -1; // Path login.
    } catch (e) {
      return false; // URL invalide.
    }
  }

  function shouldSkipLink($link) { // Decide si on ignore le lien.
    var href = $link.attr("href"); // Href du lien.
    if (!href || href === "#") { // Href vide.
      return true; // Ignore.
    }
    // Permet de desactiver l'AJAX sur un lien precis.
    if ($link.data("ajax") === false || $link.attr("data-ajax") === "false") { // Flag disable.
      return true; // Ignore.
    }
    if ($link.attr("data-bs-toggle") || $link.attr("data-toggle")) { // Liens Bootstrap.
      return true; // Ignore.
    }
    // Ne pas intercepter les liens externes ou speciaux.
    if ($link.attr("target") || $link.attr("download")) { // Nouvel onglet ou download.
      return true; // Ignore.
    }
    if (href.indexOf("mailto:") === 0 || href.indexOf("tel:") === 0) { // Liens speciaux.
      return true; // Ignore.
    }
    if (href.indexOf("javascript:") === 0) { // Liens JS.
      return true; // Ignore.
    }
    return false; // Interception possible.
  }

  // Utilitaire du bandeau global pour succes/alerte/erreur.
  function showGlobalNotif(type, message, errors, html) { // Affiche bandeau.
    var $notif = $("#notif"); // Selection bandeau.
    if (!$notif.length) { // Si absent.
      return; // Stop.
    }

    $notif
      .removeClass("d-none alert-success alert-warning alert-danger alert-info") // Reset classes.
      .addClass("alert-" + type); // Ajoute classe type.

    if (errors && errors.length) { // Si erreurs liste.
      var list = "<strong>Erreurs:</strong><ul class=\"mb-0\">"; // Debut liste.
      errors.forEach(function (err) { // Parcourt erreurs.
        list += "<li>" + $("<div>").text(err).html() + "</li>"; // Echappe HTML.
      });
      list += "</ul>"; // Fin liste.
      $notif.html(list); // Injecte HTML.
      return; // Stop.
    }

    if (html) { // Si HTML fourni.
      $notif.html(html); // Injecte HTML.
      return; // Stop.
    }

    $notif.text(message || ""); // Texte simple.
  }

  // Nettoie le bandeau lors d'une navigation.
  function clearGlobalNotif() { // Cache bandeau.
    var $notif = $("#notif"); // Selection bandeau.
    if (!$notif.length) { // Si absent.
      return; // Stop.
    }
    $notif.addClass("d-none").removeClass("alert-success alert-warning alert-danger alert-info"); // Cache + reset.
    $notif.empty(); // Nettoie contenu.
  }

  // Si le serveur rend une alerte dans le contenu, la recopier dans le bandeau.
  function syncNotifFromContent($content) { // Synchronise alertes.
    var $alert = $content.find(".alert:not(.d-none):not(.auth-notif)").first(); // Premiere alerte visible.
    if (!$alert.length) { // Si aucune alerte.
      return; // Stop.
    }

    var rawText = $alert.text(); // Texte brut.
    if (!rawText || !rawText.trim()) { // Ignore alertes vides.
      return; // Stop.
    }

    var type = "success"; // Type par defaut.
    if ($alert.hasClass("alert-danger")) { // Erreur.
      type = "danger";
    } else if ($alert.hasClass("alert-warning")) { // Warning.
      type = "warning";
    } else if ($alert.hasClass("alert-info")) { // Info.
      type = "info";
    }

    showGlobalNotif(type, null, null, $alert.html()); // Rejoue dans bandeau.
    $alert.remove(); // Retire l'alerte locale.
  }

  // Remplace le contenu principal + la navbar apres navigation AJAX.
  function applyResponse(html, url, pushState) { // Applique la reponse.
    var parser = new DOMParser(); // Parseur HTML.
    var doc = parser.parseFromString(html, "text/html"); // Document temporaire.
    var $doc = $(doc); // Wrapper jQuery.
    var $newContent = $doc.find("#page-content").first(); // Contenu cible.
    if (!$newContent.length) { // Fallback.
      $newContent = $doc.find("main").first(); // Cherche main.
    }

    // Remplace le contenu principal par celui de la reponse.
    if ($newContent.length) { // Si contenu trouve.
      $("#page-content").html($newContent.html()); // Remplace HTML.
      syncNotifFromContent($("#page-content")); // Sync bandeau.
    }

    // Met a jour la navbar si le HTML en contient une.
    var $newNav = $doc.find(".navbar").first(); // Navbar nouvelle.
    if ($newNav.length) { // Si trouve.
      $(".navbar").replaceWith($newNav); // Remplace navbar.
    }

    // Met a jour le titre de la page.
    var newTitle = $doc.find("title").first().text(); // Nouveau titre.
    if (newTitle) { // Si non vide.
      document.title = newTitle; // Applique titre.
    }

    if (pushState) { // Si on veut changer l'URL.
      window.history.pushState({}, "", url); // Push history.
    }

    if (typeof updateNavOffset === "function") { // Si helper dispo.
      updateNavOffset(); // Recalcule offset nav.
    }

    if (typeof tryAutoReserve === "function") { // Si helper dispo.
      tryAutoReserve(); // Rejoue reservation en attente.
    }
  }

  // Recupere et applique une nouvelle page sans rechargement complet.
  function loadPage(url, pushState) { // Charge une page.
    clearGlobalNotif(); // Cache bandeau.
    $.ajax({ // Requete AJAX.
      url: url, // URL cible.
      type: "GET", // Methode GET.
      dataType: "html", // Reponse HTML.
      success: function (html, status, xhr) { // Callback succes.
        var finalUrl = (xhr && xhr.responseURL) ? xhr.responseURL : url; // URL finale.
        applyResponse(html, finalUrl, pushState); // Applique reponse.
      },
      error: function () { // Callback erreur.
        showGlobalNotif("danger", "Erreur de navigation."); // Notif erreur.
      }
    });
  }

  window.CeriCar = window.CeriCar || {}; // Namespace global.
  window.CeriCar.showGlobalNotif = showGlobalNotif; // Expose helper notif.

  $(document).on("click", "a", function (e) { // Intercepte tous les liens.
    var $link = $(this); // Lien courant.
    var href = $link.attr("href"); // Href.

    if (shouldSkipLink($link)) { // Si on ignore.
      return; // Stop.
    }
    if (!isSameOrigin(href)) { // Si lien externe.
      return; // Stop.
    }
    if (isLoginUrl(href)) { // Si lien login.
      return; // Stop (autorise rechargement).
    }

    e.preventDefault(); // Bloque navigation.
    // Navigation AJAX sans changer l'URL (souhait utilisateur).
    loadPage(href, false); // Charge contenu.
  });

  $(document).on("submit", "form", function (e) { // Intercepte formulaires.
    var $form = $(this); // Formulaire courant.
    var id = $form.attr("id"); // ID du formulaire.

    // Certains formulaires gerent deja leur propre AJAX.
    if (id === "login-form" || id === "signup-form" || id === "formRecherche") {
      return; // Laisse faire.
    }
    if ($form.hasClass("proposer-form")) { // Form proposer gere ailleurs.
      return; // Laisse faire.
    }
    if ($form.data("ajax") === false || $form.attr("data-ajax") === "false") { // Opt-out.
      return; // Laisse faire.
    }

    var method = ($form.attr("method") || "get").toLowerCase(); // Methode.
    var action = $form.attr("action") || window.location.href; // Action.

    if (!isSameOrigin(action)) { // Si action externe.
      return; // Laisse faire.
    }

    e.preventDefault(); // Bloque soumission.

    if (method === "get") { // Pour GET.
      // Pour GET, on transforme les champs en querystring.
      var query = $form.serialize(); // Serialise champs.
      var url = action; // Base URL.
      if (query) { // Si query non vide.
        url += (url.indexOf("?") === -1 ? "?" : "&") + query; // Ajoute query.
      }
      loadPage(url, false); // Charge contenu.
      return; // Stop.
    }

    // Pour POST, on envoie en AJAX et on remplace le contenu.
    $.ajax({ // Requete AJAX.
      url: action, // URL action.
      type: method.toUpperCase(), // Methode.
      data: $form.serialize(), // Donnees.
      dataType: "html", // Reponse HTML.
      success: function (html, status, xhr) { // Callback succes.
        var finalUrl = (xhr && xhr.responseURL) ? xhr.responseURL : action; // URL finale.
        applyResponse(html, finalUrl, false); // Applique reponse.
      },
      error: function () { // Callback erreur.
        showGlobalNotif("danger", "Erreur AJAX. Voir console."); // Notif erreur.
      }
    });
  });

  window.addEventListener("popstate", function () { // Navigation historique.
    loadPage(window.location.href, false); // Recharge contenu.
  });
})(jQuery);
