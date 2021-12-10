import $ from 'jquery';
import 'autocomplete.js/dist/autocomplete.jquery';
import '../styles/algolia-autocomplete.scss';
//TODO przerobić na wersje bez jquery.

//NOTE teraz funkcja jest reużywalną funkcją zamiast robić coś.
export default function ($elements, dataKey, displayKey) {

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
                        if (dataKey) {
                            data = data[dataKey]
                        }
                        cb(data)//.users
                    })
                },
                displayKey: displayKey, //'email',
                debounce: 500 //info jedno zapytanie nie szybciej niż raz na pól sekundy
            }
        ])

    });

}


/*
poprzednia wersja
export default function ($elements, dataKey, displayKey) {

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
                        if (dataKey) {
                            data = data[dataKey]
                        }
                        cb(data)//.users
                    })
                },
                displayKey: displayKey, //'email',
                debounce: 500 //info jedno zapytanie nie szybciej niż raz na pól sekundy
            }
        ])

    });

}
 */