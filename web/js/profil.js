// Met a jour la variable CSS pour garder le contenu sous la navbar fixe.
function updateNavOffset() { // Calcule hauteur navbar.
  var $nav = $(".navbar"); // Selection navbar.
  if (!$nav.length) { // Si pas de navbar.
    return; // Stop.
  }
  var height = $nav.outerHeight(); // Hauteur reelle.
  if (!height) { // Si hauteur invalide.
    return; // Stop.
  }
  document.documentElement.style.setProperty("--nav-height", height + "px"); // Set CSS var.
}

// Comportements de la page profil (sections embarquees + formulaire proposer).
$(function () { // Lance quand DOM pret.
  updateNavOffset(); // Applique le decalage.
  $(window).on("resize", updateNavOffset); // Recalcule au resize.

  // Charge les sections du profil (reservations / voyages / proposer) sur place.
  $(document).on("click", ".js-profile-load", function (e) { // Click sur lien profil.
    e.preventDefault(); // Evite navigation.

    var url = $(this).attr("href"); // URL cible.
    var $container = $("#profile-content"); // Conteneur embed.

    // Si on ne peut pas charger en AJAX, on retombe en navigation classique.
    if (!url || !$container.length) { // Verifie preconditions.
      window.location.href = url; // Navigation normale.
      return; // Stop.
    }

    // Ajoute le flag embed pour demander un rendu partiel cote serveur.
    if (url.indexOf("embed=1") === -1) { // Si param absent.
      url += (url.indexOf("?") === -1 ? "?" : "&") + "embed=1"; // Ajoute param.
    }

    $container
      .addClass("profile-loading") // Ajoute classe chargement.
      .html('<div class="text-center py-4">Chargement...</div>'); // Placeholder.

    // Chargement AJAX de la section demandee.
    $.ajax({ // Requete AJAX.
      url: url, // URL partielle.
      type: "GET", // Methode GET.
      dataType: "html", // Reponse HTML.
      success: function (html) { // Callback succes.
        $container.removeClass("profile-loading").html(html); // Injecte contenu.
        $("html, body").animate({ scrollTop: $container.offset().top - 90 }, 250); // Scroll.
      },
      error: function () { // Callback erreur.
        $container.removeClass("profile-loading").empty(); // Nettoie le conteneur.
        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") { // Si helper dispo.
          window.CeriCar.showGlobalNotif("danger", "Erreur de chargement."); // Notif globale.
        }
      }
    });
  });

  // Soumet le formulaire proposer en AJAX et affiche la notification globale.
  $(document).on("submit", ".proposer-form", function (e) { // Submit proposer.
    e.preventDefault(); // Evite rechargement.

    var $form = $(this); // Formulaire.

    // Envoi des donnees du formulaire sans rechargement.
    $.ajax({ // Requete AJAX.
      url: $form.attr("action"), // URL action.
      type: "POST", // Methode POST.
      dataType: "json", // Reponse JSON.
      data: $form.serialize(), // Donnees serializees.
      success: function (res) { // Callback succes.
        // Succes : message + reset optionnel.
        if (res && res.status === "success") { // Si succes.
          if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") { // Helper dispo.
            window.CeriCar.showGlobalNotif("success", res.message || "Voyage propose."); // Notif succes.
          }
          if (res.reset && $form.length) { // Si reset demande.
            $form[0].reset(); // Reset HTML.
          }
          return; // Stop.
        }

        // Erreurs : message + liste detaillee.
        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") { // Helper dispo.
          window.CeriCar.showGlobalNotif(
            "danger", // Type.
            (res && res.message) ? res.message : "Erreur.", // Message principal.
            res && res.errors ? res.errors : [] // Erreurs liste.
          );
        }
      },
      error: function () { // Callback erreur.
        if (window.CeriCar && typeof window.CeriCar.showGlobalNotif === "function") { // Helper dispo.
          window.CeriCar.showGlobalNotif("danger", "Erreur AJAX. Voir console."); // Notif erreur.
        }
      }
    });
  });
});
