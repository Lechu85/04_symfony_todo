
var $container = $('.js-vote-arrows');

$container.find('a').on('click', function(e) {
    e.preventDefault();
    var $link = $(e.currentTarget);

    //narazie id jako hardcode
    $.ajax({
        url: '/comments/10/vote/'+$link.data('direction'),
        method: 'POST'
    }).then(function(data) {

        $container.find('.js-vote-total').text(data.votes);
        //używamy ttuaj votes, bo naszjson zwraca klucz votes
    })

})