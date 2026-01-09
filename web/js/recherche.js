// Comportements de la page recherche (submit AJAX + resultats dynamiques).
$(function () { // Lance quand le DOM est pret.
  // Lance une reservation automatique si l'utilisateur revient du login.
  tryAutoReserve(); // Tente la reservation en attente.
});

$(document).on("submit", "#formRecherche", function (e) { // Envoi formulaire recherche.
  e.preventDefault(); // Interdit le rechargement de page.

  // Envoie le formulaire sans rechargement de page.
  $.ajax({ // Requete AJAX.
    url: $(this).attr("action"), // recuperer lurl de l'action du formulaire
    type: "POST", // Methode POST.
    dataType: "json", // Reponse JSON attendue.
    data: $(this).serialize(), // Donnees serializees.
    success: function (res) { // Callback succes.
      // Remplace la zone resultats par le HTML rendu cote serveur.
      $("#resultats").html(res.html); // Injecte le HTML.
      $(".card-correspondance").removeClass("is-selected is-open"); // Reset etat cartes.
      $(".corr-item").removeClass("corr-item-expanded"); // Reset etat corr.
      $(".correspondance-inline").addClass("d-none").empty(); // Cache details.
      $(".result-toggle").text("Voir les details"); // Texte par defaut.

      if (res.notif) { // Si notif presente.
        // Affiche la notification globale (succes/alerte/erreur).
        $("#notif") // Bandeau global.
          .removeClass("d-none alert-success alert-warning alert-danger") // RAZ classes.
          .addClass("alert-" + res.notif.type) // Ajoute classe selon type.
          .text(res.notif.message); // Message serveur.
      }
    }
  });
});

// Ouvre/ferme les details de correspondance via un partiel AJAX.
$(document).on("click", ".card-correspondance", function (e) { // Click sur carte.
  if ($(e.target).closest("button, a").length) { // Ignore si clic sur bouton/lien.
    return; // Stop.
  }

  var $card = $(this); // Carte courante.
  var ids = $card.data("voyageIds") || $card.attr("data-voyage-ids"); // IDs voyages.
  var nb = $card.data("nb"); // Nb voyageurs.
  var $results = $("#resultats"); // Conteneur resultats.
  var url = $results.attr("data-details-url"); // URL details.
  var $row = $card.closest("[class*='col-']"); // Ligne courante (non utilise).

  if (!ids || !url) { // Verifie donnees.
    return; // Stop si manque.
  }

  if ($card.hasClass("is-selected")) { // Si deja ouvert.
    $card.removeClass("is-selected is-open"); // Ferme la carte.
    $card.closest(".corr-item").removeClass("corr-item-expanded"); // Ferme le bloc.
    $card.find(".correspondance-inline").addClass("d-none").empty(); // Cache details.
    $card.find(".result-toggle").text("Voir les details"); // Texte par defaut.
    return; // Stop.
  }

  // Recupere le detail des correspondances.
  $.ajax({ // Requete AJAX.
    url: url, // URL details.
    type: "GET", // Methode GET.
    data: { ids: ids, nb: nb }, // Parametres.
    success: function (html) { // Callback succes.
      $(".card-correspondance").removeClass("is-selected is-open"); // Ferme autres cartes.
      $(".corr-item").removeClass("corr-item-expanded"); // Reset.
      $(".correspondance-inline").addClass("d-none").empty(); // Cache details.
      $(".result-toggle").text("Voir les details"); // Texte par defaut.
      $card.addClass("is-selected is-open"); // Ouvre carte courante.
      $card.closest(".corr-item").addClass("corr-item-expanded"); // Etend bloc.
      $card.find(".correspondance-inline").html(html).removeClass("d-none"); // Injecte details.
      $card.find(".result-toggle").text("Masquer les details"); // Texte alternatif.
      $("html, body").animate({ scrollTop: $card.offset().top - 90 }, 250); // Scroll vers carte.
    }
  });
});

// Action de reservation geree en AJAX.
$(document).on("click", ".btn-reserver", function (e) { // Click reserver.
  e.preventDefault(); // Evite navigation.

  const ids = $(this).data("voyageIds") || $(this).attr("data-voyage-ids"); // IDs voyages.
  const nb = $("input[name='voyageurs']").val() || 1; // Nb voyageurs.

  // Reservation via AJAX (reponse JSON).
  console.log("CLICK RESERVER", ids, nb); // Log debug.

  $.ajax({ // Requete AJAX.
    url: $("#resultats").attr("data-reserver-url"), // URL reserver.
    type: "POST", // Methode POST.
    dataType: "json", // Reponse JSON.
    data: { // Donnees envoyees.
      voyage_ids: ids, // IDs voyages.
      nb: nb, // Nb voyageurs.
      _csrf: yii.getCsrfToken() // Token CSRF Yii.
    },
    success: function (res) { // Callback succes.
      console.log("RESERVER OK", res); // Log debug.

      if (res.status === "login" && res.redirect) { // Si non connecte.
        // Si non connecte, on sauvegarde la reservation et on redirige vers login.
        const payload = { // Payload a memoriser.
          voyage_ids: ids, // IDs voyages.
          nb: nb // Nb voyageurs.
        };
        sessionStorage.setItem("pendingReservation", JSON.stringify(payload)); // Stocke en session.
        const returnUrl = encodeURIComponent(window.location.href); // URL retour.
        const redirectUrl = res.redirect.indexOf("?") === -1
          ? res.redirect + "?returnUrl=" + returnUrl
          : res.redirect + "&returnUrl=" + returnUrl;
        window.location.href = redirectUrl; // Redirection login.
        return; // Stop.
      }

      $("#notif") // Bandeau global.
        .removeClass("d-none alert-success alert-warning alert-danger") // Reset classes.
        .addClass(res.status === "success" ? "alert-success" : "alert-danger") // Classe selon statut.
        .text(res.message || "Reponse recue."); // Message.

      if (res.status === "success" && res.places) { // Si succes + places.
        updatePlacesDispo(res.places, res.nb || nb); // Met a jour badges.
      }
    },
    error: function (xhr) { // Callback erreur.
      console.log("RESERVER ERROR", xhr.status, xhr.responseText); // Log debug.
      $("#notif") // Bandeau global.
        .removeClass("d-none") // Affiche.
        .addClass("alert-danger") // Classe erreur.
        .text("Erreur AJAX reservation. Voir console."); // Message.
    }
  });
});

function tryAutoReserve() { // Rejoue reservation apres login.
  const $results = $("#resultats"); // Conteneur resultats.
  if (!$results.length) { // Si pas de resultats.
    return; // Stop.
  }
  const url = $results.attr("data-reserver-url"); // URL reserver.
  if (!url) { // Si URL absente.
    return; // Stop.
  }

  // Si une reservation etait en attente apres login, on la rejoue.
  const pending = sessionStorage.getItem("pendingReservation"); // Recupere stockage.
  if (!pending) { // Si rien.
    return; // Stop.
  }

  sessionStorage.removeItem("pendingReservation"); // Nettoie le stockage.
  let payload = null; // Payload a parser.
  try {
    payload = JSON.parse(pending); // Parse JSON.
  } catch (e) {
    return; // Stop si JSON invalide.
  }

  if (!payload || !payload.voyage_ids) { // Verifie contenu.
    return; // Stop.
  }

  $.ajax({ // Requete AJAX.
    url: url, // URL reserver.
    type: "POST", // Methode POST.
    dataType: "json", // Reponse JSON.
    data: { // Donnees envoyees.
      voyage_ids: payload.voyage_ids, // IDs voyages.
      nb: payload.nb || 1, // Nb voyageurs.
      _csrf: yii.getCsrfToken() // Token CSRF Yii.
    },
    success: function (res) { // Callback succes.
      if (res.status === "success") { // Si succes.
        $("#notif") // Bandeau global.
          .removeClass("d-none alert-warning alert-danger") // Reset classes.
          .addClass("alert-success") // Classe succes.
          .text(res.message || "Reservation confirmee."); // Message.
        if (res.places) { // Si places retournees.
          updatePlacesDispo(res.places, res.nb || payload.nb || 1); // Met a jour badges.
        }
      }
    }
  });
}


// quand on reserve on met a jour le nembre de places dispo
function updatePlacesDispo(places, nb) { // Met a jour les places affichees.
  const nbNeeded = parseInt(nb, 10) || 1; // Nb requis.

  // Met a jour les badges de places sur les cartes.
  $(".btn-reserver").each(function () { // Boucle sur boutons.
    const $btn = $(this); // Bouton courant.
    const idsRaw = $btn.data("voyageIds") || $btn.attr("data-voyage-ids"); // IDs bruts.
    if (!idsRaw) { // Si pas d'IDs.
      return; // Stop.
    }

    const ids = String(idsRaw) // Convertit en string.
      .split(",") // Separe.
      .map(function (id) { // Map en int.
        return parseInt(id, 10);
      })
      .filter(function (id) { // Filtre NaN.
        return !Number.isNaN(id);
      });

    if (!ids.length) { // Si aucun ID valide.
      return; // Stop.
    }

    let minPlaces = null; // Min places sur les segments.
    ids.forEach(function (id) { // Boucle IDs.
      if (Object.prototype.hasOwnProperty.call(places, id)) { // Si places connues.
        const current = parseInt(places[id], 10); // Places courantes.
        if (!Number.isNaN(current)) { // Si valides.
          minPlaces = minPlaces === null ? current : Math.min(minPlaces, current); // Min.
        }
      }
    });

    if (minPlaces === null) { // Si aucune valeur.
      return; // Stop.
    }

    const $card = $btn.closest(".card"); // Carte parent.
    const $badge = $card.find(".result-badge").first(); // Badge dispo.
    if (!$badge.length) { // Si pas de badge.
      return; // Stop.
    }

    $badge // Met a jour classes + texte.
      .toggleClass("badge-complet", minPlaces < nbNeeded) // Etat complet.
      .toggleClass("badge-dispo", minPlaces >= nbNeeded) // Etat dispo.
      .text(minPlaces < nbNeeded ? "Complet" : "Disponible (" + minPlaces + ")"); // Texte.

    if (minPlaces < nbNeeded) { // Si pas assez de places.
      $btn.prop("disabled", true); // Desactive bouton.
    } else {
      $btn.prop("disabled", false); // Active bouton.
    }
  });
}
