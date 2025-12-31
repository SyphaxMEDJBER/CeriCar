$(function () {
  $("#formRecherche").on("submit", function (e) {//envoyer le post , recharger  la page
    e.preventDefault();//interdit au navigateuer de recharger la page , js prends le controle

    $.ajax({    //le js envoie la requette au serveur
      url: $(this).attr("action"),//envoi la requette ajax a l'url definie dans lattribut action de formulaire
      type: "POST",//type post 
      dataType: "json", //format de donnees json
      data: $(this).serialize(),// toute les valeurs saisies
      success: function (res) { //sexecute quand http xhr.status=200 le serveur a repondu correctemenet, res la reponse de controleur json
        $("#resultats").html(res.html);//le html généré par renderpartial(...),on replace le contenu de <div id="resultats">
        $(".card-correspondance").removeClass("is-selected is-open");
        $(".corr-item").removeClass("corr-item-expanded");
        $(".correspondance-inline").addClass("d-none").empty();
        $(".result-toggle").text("Voir les details");

        if (res.notif) {
          $("#notif") //selection du bandeau global (dans le layout)
            .removeClass("d-none alert-success alert-warning alert-danger")// supprime d-none => rend le bandeau visible
            .addClass("alert-" + res.notif.type)//ajoute dynamiquement aloert-success-warning-danger selon ce que le serveur a decidé
            .text(res.notif.message);//affiche le massage serveur
        }
      }
    });
  });

  $(document).on("click", ".card-correspondance", function (e) {
    if ($(e.target).closest("button, a").length) {
      return;
    }

    var $card = $(this);
    var ids = $card.data("voyageIds") || $card.attr("data-voyage-ids");
    var nb = $card.data("nb");
    var $results = $("#resultats");
    var url = $results.attr("data-details-url");
    var $row = $card.closest("[class*='col-']");

    if (!ids || !url) {
      return;
    }

    if ($card.hasClass("is-selected")) {
      $card.removeClass("is-selected is-open");
      $card.closest(".corr-item").removeClass("corr-item-expanded");
      $card.find(".correspondance-inline").addClass("d-none").empty();
      $card.find(".result-toggle").text("Voir les details");
      return;
    }

    $.ajax({
      url: url,
      type: "GET",
      data: { ids: ids, nb: nb },
      success: function (html) {
        $(".card-correspondance").removeClass("is-selected is-open");
        $(".corr-item").removeClass("corr-item-expanded");
        $(".correspondance-inline").addClass("d-none").empty();
        $(".result-toggle").text("Voir les details");
        $card.addClass("is-selected is-open");
        $card.closest(".corr-item").addClass("corr-item-expanded");
        $card.find(".correspondance-inline").html(html).removeClass("d-none");
        $card.find(".result-toggle").text("Masquer les details");
        $("html, body").animate({ scrollTop: $card.offset().top - 90 }, 250);
      }
    });
  });
});
