
import $ from 'jquery';
//import autocomplete from './components/algoria-autocomplete';


//document.addEventListener('DOMContentLoaded', function () { //NOTE po wybraniu pierwszej opcji, drugi select przeładowuje sie
$(document).ready(function() {

    const $autoComplete = $('.js-user-autocomplete');
    if (!$autoComplete.is(':disabled')) {

        //DOŁĄDOWUJEMY jak będzie użyte
        import('./components/algolia-autocomplete').then((autocomplete) => {
            autocomplete.default($autoComplete, 'users', 'email');
        });


        //autocomplete($autoComplete, 'users', 'email'); było tak
    }

    var $locationSelect = $('.js-article-form-location'); //pierwszy seelct
    var $specificLocationTarget = $('.js-specific-location-target'); //element znajdujący się wokół drugiego pola

    $locationSelect.on('change', function(e) {
        $.ajax({
            url: $locationSelect.data('specific-location-url'),
            data: {
                location: $locationSelect.val()
            },
            success: function (html) {
                if (!html) { //NOTE if response is empty
                    $specificLocationTarget.find('select').remove();
                    $specificLocationTarget.addClass('d-none');

                    return;
                }

                // Replace the current field and show
                $specificLocationTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});

// NOTE -w docelowym js nie ma tego kodu class(), webpack dostosowuje do starszych przeglądarek :)
//  webpack przepisuje skrypot, żeby był zgodny ze starszymi przeglądarkami.
//  Jest to bartdzo fajne, ponieważ nie chcemy czekac 10 lat, zanim przegladarki to wprowadzą.
// TODO - use Webpack Encore so ES6 syntax is transpiled to ES5
class ReferenceList
{
    constructor($element) {
        var stuff = new WeakSet([])

        this.$element = $element;
        this.sortable = Sortable.create(this.$element[0], {
            handle: '.drag-handle',
            animation: 150,
            onEnd: () => {
                $.ajax({
                    url: this.$element.data('url')+'/reorder',
                    method: 'POST',
                    data: JSON.stringify(this.sortable.toArray())
                });
            }
        });
        this.references = [];
        this.render();

        this.$element.on('click', '.js-reference-delete', (event) => {
            this.handleReferenceDelete(event);
        });

        this.$element.on('blur', '.js-edit-filename', (event) => {
            this.handleReferenceEditFilename(event);
        });

        $.ajax({
            url: this.$element.data('url')
        }).then(data => {
            this.references = data;
            this.render();
        })
    }

    addReference(reference) {
        this.references.push(reference);
        this.render();
    }

    handleReferenceDelete(event) {
        const $li = $(event.currentTarget).closest('.list-group-item');
        const id = $li.data('id');
        $li.addClass('disabled');

        $.ajax({
            url: '/admin/article/references/'+id,
            method: 'DELETE'
        }).then(() => {
            this.references = this.references.filter(reference => {
                return reference.id !== id;
            });
            this.render();
        });
    }

    handleReferenceEditFilename(event) {
        const $li = $(event.currentTarget).closest('.list-group-item');
        const id = $li.data('id');
        const reference = this.references.find(reference => {
            return reference.id === id;
        });
        reference.originalFilename = $(event.currentTarget).val();

        $.ajax({
            url: '/admin/article/references/'+id,
            method: 'PUT',
            data: JSON.stringify(reference)
        });
    }

    render() {
        const itemsHtml = this.references.map(reference => {
            return `
<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${reference.id}">
    <span class="drag-handle fa fa-reorder"></span>
    <input type="text" value="${reference.originalFilename}" class="form-control js-edit-filename" style="width: auto;">

    <span>
        <a href="/admin/article/references/${reference.id}/download" class="btn btn-link btn-sm"><span class="fa fa-download" style="vertical-align: middle"></span></a>
        <button class="js-reference-delete btn btn-link btn-sm"><span class="fa fa-trash"></span></button>
    </span>
</li>
`
        });

        this.$element.html(itemsHtml.join(''));
    }
}

/**
 * @param {ReferenceList} referenceList
 */
function initializeDropzone(referenceList) {
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }

    var dropzone = new Dropzone(formElement, {
        paramName: 'reference',
        init: function() {
            this.on('success', function(file, data) {
                referenceList.addReference(data);
            });

            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });
        }
    });
}