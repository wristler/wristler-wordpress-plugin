document.addEventListener("DOMContentLoaded", () => {
    const wristlerFieldsTrigger = document.querySelector('input#_wristler_sync')
    const wristlerFields = document.querySelector('div.wristler_fields')

    const debounce = (callback, wait) => {
        let timeoutId = null;
        return (...args) => {
            window.clearTimeout(timeoutId);

            timeoutId = window.setTimeout(() => {
                callback.apply(null, args);
            }, wait);
        };
    }

    if (wristlerFieldsTrigger) {
        wristlerFields.style.display = wristlerFieldsTrigger.checked ? 'block' : 'none'

        wristlerFieldsTrigger.addEventListener('change', function () {
            wristlerFields.style.display = this.checked ? 'block' : 'none'
        })
    }

    const wristlerReferenceInput = document.querySelector('input#_wristler_reference');
    const wristlerAutocompleteResult = document.querySelector('#wristler-autocomplete-results');

    if (wristlerReferenceInput) {
        let watches = [];

        wristlerReferenceInput.addEventListener('input', debounce(event => {
            console.log('Fetch data from API');

            if (watches.length === 0) {
                watches.push({
                    id: 'appels',
                    name: 'test',
                    reference: '123456',
                    tagLine: 'Supermooi testhorloge'
                });
            }

            if (watches.length > 0) {
                wristlerAutocompleteResult.innerHTML = watches.map(watch => '<li><button data-id="' + watch.id + '">' + watch.name + '</button></li>');
            }

            let wristlerAutocompleteTriggers = document.querySelectorAll('#wristler-autocomplete-results li button');

            console.log(wristlerAutocompleteTriggers)

            wristlerAutocompleteTriggers.forEach(element => {
                element.addEventListener('click', event => {
                    event.preventDefault();

                    document.querySelector('input#_wristler_selected_id').value = event.target.dataset.id;
                });
            })
        }, 250));
    }
})
