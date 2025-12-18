$(document).ready(function () {

    $("#formRecherche").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "index.php?r=site/recherche",
            type: "POST",
            data: $(this).serialize(),
            success: function(html) {

                $("#resultats").html(html);

                $("#notif")
                    .text("Recherche terminée")
                    .fadeIn()
                    .delay(2000)
                    .fadeOut();
            }
        });
    });

});
