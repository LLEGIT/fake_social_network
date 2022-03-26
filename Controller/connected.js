export function getIdConnected() {
    $.ajax({
        method: "GET",
        url: "./../Model/isConnected.php",
        dataType: "json"
    }).done(function(data) {
        return data["connected"];
    })
}