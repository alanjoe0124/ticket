
$(document).ready(function () {
    var form = '<form class="ticket_form" style="display:none">' +
                    '<p>ticket</p>' +
                        '<p>title</p>' +
                            '<input type="text" name="ticket_title">' +
                        '<br>desc<br>' +
                            '<textarea id="descprition" rows="10" cols="20"></textarea>' +
                        '<br><button type="submit" id="ticket_sub">submit</button>' +
                '</form>';
    $('.ticket').append(form);

    $('.create_ticket').click(function () {
        $('.ticket_form').css('display', 'inline');
    });

    $('.ticket_form').submit(function (e) {
        e.preventDefault();
        $.post('http://ticket.dev/Ticket/create',
                {title: $('input[name=ticket_title]').val(), description: $('#descprition').val(), email: $('#email').text(), domain: location.host},
                function (response) {
                    if(response === 'success'){
                        alert('send successfully');
                        $(".ticket_form").trigger('reset');  
                    }
                },
                "text");
    });

    $('.view_ticket').click(function () {
        location.href = 'http://ticket.dev/Index/index?email=' + $('#email').text();
    });
});