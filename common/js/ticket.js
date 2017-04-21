
function create_ticket() {
    $(".create_ticket").click(function () { 
        $('.ticket').show();
        $("#ticket_sub").click(function () {
            $('.ticket_form').submit(function () {
                alert('send successfully');
                var title = $('input[name=ticket_title]').val(), description = $('#descprition').val(), email = $('#email').text();
                $.post("http://ticket.dev/ticket_create.php", {title: title, description: description, email: email});
            });
        });
    });
}

function view_ticket() {
    var email = $('#email').text();
    $(".view_ticket").click(function () {
        url = "http://ticket.dev/index.php?email=" + email;
        $(location).attr("href", url);
    });
}

function form_generate(){
     var form = '<form class="ticket_form">' +
                        '<p>ticket</p>' +
                        '<p>title</p>'+
                        '<input type="text" name="ticket_title">' +
                        '<br>desc<br>'+
                        '<textarea id="descprition" rows="10" cols="20"></textarea>' +
                        '<br><button type="submit" id="ticket_sub">submit</button>'+
                '</form>';
        $(".ticket").append(form);
        $('.ticket').hide();
}
$(document).ready(function () {
    form_generate();
    create_ticket();
    view_ticket();
});

