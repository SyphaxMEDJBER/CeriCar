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

        if (res.notif) {
          $("#notif") //selection du bandeau global (dans le layout)
            .removeClass("d-none alert-success alert-warning alert-danger")// supprime d-none => rend le bandeau visible
            .addClass("alert-" + res.notif.type)//ajoute dynamiquement aloert-success-warning-danger selon ce que le serveur a decidé
            .text(res.notif.message);//affiche le massage serveur
        }
      }
    });
  });
});


