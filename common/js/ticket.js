
function create_ticket() {
    $(".create_ticket").click(function () {
        var form = '<form class = "ticket_form"><p>ticket</p>' +
                '<p>title</p><input type="text" name="title">' +
                '<br>desc<br><textarea rows="10" cols="20"></textarea>' +
                '<br><button>submit</button></form>';
        $(".ticket").append(form);
    });
    $('button').on('click', function () {
        $('.ticket_form').on('submit', function () {
            var title = $('input[name=title]').val(), description = $('textarea').val(), email = $('#email').html();
            $.post("http://ticket.dev/ticket_create.php",
                    {title: title, description: description, email: email}
            ).done(function (data) {
                // alert( "Data Loaded: " + title + description +email);
            });
        });
    });
}

function view_ticket() {
    var email = $('#email').html();
    $(".view_ticket").click(function () {
        url = "http://ticket.dev/index.php?email="+email;
        $( location ).attr("href", url);
    });
}

$(document).ready(function () {
    create_ticket();
    view_ticket();
});

