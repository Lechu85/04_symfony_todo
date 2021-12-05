//zaimprtowałem ten moduł do webpoacka, ale neiwiem ja kużyć :)

//ready infgo aby mieć pewośc, że dom jest w pełni załadowany
document.addEventListener('DOMContentLoaded', function () {

    //robimy each, żeby dla każdego było, jest to jakby pętla.
    $(".js-user-autocomplete").each(function () {
        var autocompleteUrl = $(this).data('autocomplete-url');

        $(this).autocomplete({hint: false}, [
            {
                source: function (query, cb) {//info query to treśc pola z formularza

                    $.ajax({
                        url: autocompleteUrl+'?query='+query
                    }).then(function (data) {
                        //info callback funtion
                        cb(data.users)
                    })
                },
                displayKey: 'email',
                debounce: 500 //info jedno zapytanie nie szybciej niż raz na pól sekundy
            }
        ])

    });

});
