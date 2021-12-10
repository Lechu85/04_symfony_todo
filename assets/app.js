/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import './styles/app.scss';

import $ from 'jquery';
import 'bootstrap'; // adds functions to jQuery
// uncomment if you have legacy code that needs global variables
//global.$ = $;

import './bootstrap';


//import { Collapse } from 'bootstrap';


//NOTE symfony casts
// pierwszy sposób dołaczania wartości - jedna rzecz
//const getNiceMessage = require('./components/get_nice_message')

//NOTE symfony casts
// drugi sposób
import getNiceMessage from './components/get_nice_message'


console.log(getNiceMessage(6))

