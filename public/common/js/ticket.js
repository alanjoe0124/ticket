$(document).ready(function () {
    var form = '<form id="ticket_form" style="display:none;position:fixed;top:80;">' +
                    '<p>ticket</p>' +
                    'title<br>' +
                    '<input type="text" id="ticket_title"><br>' +
                    'desc<br>' +
                    '<textarea id="ticket_description" rows="10" cols="20"></textarea><br>' +
                    '<button type="submit">submit</button>' +
               '</form>';
    $('body').append(form);

    var customerEmail;

    $('#ticket_form').submit(function (e) {
        e.preventDefault();
        
        var postData = {
            title: $('#ticket_title').val(),
            description: $('#ticket_description').val(),
            email: customerEmail,
            domain: location.host
        };
        $.post('http://ticket.dev/ticket/create', postData, function (response) {
            if (response === 'success') {
                alert('ticket created');
            }
        }, 'text');
    });

    window.createTicket = function (email) {
        $('#ticket_form').show();
        customerEmail = email;
    };
    window.viewMyTickets = function (email) {
        location.href = 'http://ticket.dev/index/index?email=' + encodeURIComponent(email);
    }
});