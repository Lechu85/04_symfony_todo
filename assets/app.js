/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';



// start the Stimulus application
import './bootstrap';

// activates collapse functionality
import { Collapse } from 'bootstrap';


//NOTE symfony casts
// pierwszy sposób dołaczania wartości - jedna rzecz
//const getNiceMessage = require('./get_nice_message')

//NOTE symfony casts
// drugi sposób
import getNiceMessage from './get_nice_message'

import './styles/autoload.css';

console.log(getNiceMessage(6))




//import { autocomplete } from '@algolia/autocomplete-js';

//ready infgo aby mieć pewośc, że dom jest w pełni załadowany

//zamienic na div trzeba tenelement.


/*
import $ from 'jquery';


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
*/