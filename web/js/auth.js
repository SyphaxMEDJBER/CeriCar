// Gestion des formulaires d'authentification (login/signup) avec retour AJAX.
$(function () { // Lance quand le DOM est pret.
  $(document).on("submit", "#login-form, #signup-form", function (e) { // Intercepte submit login/signup.
    e.preventDefault(); // Evite le rechargement complet.

    // References aux elements du formulaire et au bandeau global.
    const $form = $(this); // Formulaire courant.
    const $notif = $form.find(".auth-notif"); // Zone notif du formulaire.
    const $globalNotif = $("#notif"); // Bandeau global.

    // Envoie les identifiants via AJAX et traite la reponse JSON.
    $.ajax({ // Requete AJAX.
      url: $form.attr("action"), // URL du formulaire.
      type: "POST", // Envoi POST.
      dataType: "json", // Reponse JSON attendue.
      data: $form.serialize(), // Donnees serializees.
      success: function (res) { // Callback succes.
        // Cas succes : message + redirection eventuelle.
        if (res.status === "success") { // Statut OK.
          if ($notif.length) { // Si notif locale existe.
            $notif // Met a jour les classes.
              .removeClass("d-none alert-danger") // Retire classes erreur.
              .addClass("alert-success") // Ajoute classe succes.
              .text(res.message || "Succes."); // Texte de succes.
          }

          if (res.redirect) { // Si redirection.
            window.location.href = res.redirect; // Change la page.
          }
          return; // Stop suite.
        }

        // Aplatis les erreurs de champs dans un seul message.
        const errors = []; // Liste d'erreurs.
        if (res.errors) { // Si erreurs de validation.
          // Transforme l'objet d'erreurs en liste de messages.
          Object.keys(res.errors).forEach(function (key) { // Parcourt les champs.
            res.errors[key].forEach(function (msg) { // Parcourt les messages.
              errors.push(msg); // Ajoute un message.
            });
          });
        }

        const errorText = errors.length ? errors.join("<br>") : (res.message || "Erreur."); // Texte final.
        if ($notif.length) { // Affiche dans notif locale.
          $notif
            .removeClass("d-none alert-success") // Retire classe succes.
            .addClass("alert-danger") // Ajoute classe erreur.
            .html(errorText); // HTML pour retours ligne.
        }
        if ($globalNotif.length) { // Affiche dans bandeau global.
          $globalNotif
            .removeClass("d-none alert-success") // Retire classe succes.
            .addClass("alert-danger") // Ajoute classe erreur.
            .html(errorText); // HTML pour retours ligne.
        }
      },
      error: function (xhr) { // Callback erreur HTTP.
        // Erreurs reseau/500 : message generique.
        if ($notif.length) { // Notif locale.
          $notif
            .removeClass("d-none alert-success") // Retire classe succes.
            .addClass("alert-danger") // Ajoute classe erreur.
            .text("Erreur AJAX. Voir console."); // Texte generique.
        }
        if ($globalNotif.length) { // Bandeau global.
          $globalNotif
            .removeClass("d-none alert-success") // Retire classe succes.
            .addClass("alert-danger") // Ajoute classe erreur.
            .text("Erreur AJAX. Voir console."); // Texte generique.
        }
        console.log("AUTH AJAX ERROR", xhr.status, xhr.responseText); // Log technique.
      }
    });
  });
});
