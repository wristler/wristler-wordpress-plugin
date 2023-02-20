const Wrislter = {

    watches: [],

    selectedWatch: null,

    init: function () {
        this.fetchInitialSelection();
        this.handleConditionalFields();
        this.handleAutocompletionInput();
        this.handleWatchSelection();
    },

    fetchInitialSelection: function () {
        let curObj = this;
        const selectedWatchInput = document.querySelector('input#_wristler_selected_id');

        if (!selectedWatchInput || !selectedWatchInput.value || selectedWatchInput.value.length === 0) {
            return;
        }

        this.getWatchById(selectedWatchInput.value)
            .then(resp => {
                this.watches.push(curObj.formatResource(resp));

                this.selectWatch(selectedWatchInput.value);
            });
    },

    handleConditionalFields: () => {
        const syncProductToWristler = document.querySelector('input#_wristler_sync')
        const conditionalFields = document.querySelector('div.wristler_fields')

        if (!syncProductToWristler) {
            return;
        }

        conditionalFields.style.display = syncProductToWristler.checked ? 'block' : 'none'

        syncProductToWristler.addEventListener('change', function () {
            conditionalFields.style.display = this.checked ? 'block' : 'none'
        })
    },

    handleAutocompletionInput: function () {
        let curObj = this;
        const referenceInput = document.querySelector('input#_wristler_reference');

        if (!referenceInput) {
            return;
        }

        referenceInput.addEventListener('input', Wrislter.debounce(event => {
            curObj.showLoader();
            curObj.selectedWatch = null;
            curObj.watches = [];

            this.search(event.target.value).then(resp => {
                curObj.watches = resp.data.map(watch => curObj.formatResource(watch));
                curObj.hideLoader();
                curObj.formatResults()
            })
        }, 250))
    },

    formatResults: function () {
        const container = document.querySelector('#wristler-autocomplete-results');

        if (!container) {
            return;
        }

        container.innerHTML = this.watches.map(watch => {
            return `
                <li>
                    <a data-id="${watch.id}" href="#">
                        <h4>${watch.name} <small>${watch.reference}</small></h4>
                        <p>${watch.tagLine}</p>
                    </a>
                </li>
            `;
        }).join('');

        this.handleWatchSelection();
    },

    handleWatchSelection: function () {
        let curObj = this;

        const watchButtons = document.querySelectorAll('#wristler-autocomplete-results li a');

        if (!watchButtons) {
            return;
        }

        watchButtons.forEach(element => {
            element.addEventListener('click', event => {
                event.preventDefault();

                watchButtons.forEach(element => element.classList.remove('is-active'))

                event.target.closest('a').classList.add('is-active');

                curObj.selectWatch(event.target.closest('a').dataset.id)
            })
        })
    },

    selectWatch: function (watch) {
        const selectedWatchInput = document.querySelector('input#_wristler_selected_id');
        const selectedWatchContainer = document.querySelector('.wristler-selected-watch-container');

        this.selectedWatch = this.watches.filter(w => w.id === watch)[0];

        if (selectedWatchInput) {
            selectedWatchInput.value = this.selectedWatch.id;
        }

        if (selectedWatchContainer) {
            selectedWatchContainer.style.display = 'block';

            document.querySelector('.wristler-selected-watch').innerHTML = `
                <div>
                    <h3>Selected watch</h3>
                    <h4>${this.selectedWatch.name} <small>${this.selectedWatch.reference}</small></h4>
                    <p>${this.selectedWatch.tagLine}</p>
                </div>
            `;
        }
    },

    debounce: (callback, wait) => {
        let timeoutId = null;
        return (...args) => {
            window.clearTimeout(timeoutId);

            timeoutId = window.setTimeout(() => {
                callback.apply(null, args);
            }, wait);
        };
    },

    showLoader: function() {
        document.querySelector('.wristler-autocomplete-loader').style.display = 'block';
    },

    hideLoader: function() {
        document.querySelector('.wristler-autocomplete-loader').style.display = 'none';
    },

    formatResource: function (watch) {
        let tags = ['category', 'gender', 'movement', 'caseSize', 'dial'].map(tag => {
            return watch[tag] && watch[tag].length > 0 ? watch[tag] : null;
        }).filter(tag => tag !== null);

        return {
            id: watch.uuid,
            reference: watch.reference,
            name: `${watch.brand} ${watch.model}`,
            tagLine: tags.join(' / ')
        }
    },

    search: function (query) {
        return this.request(`watches?filter[search]=${query}`)
    },

    getWatchById: function (id) {
        return this.request(`watches/${id}`)
    },

    request: function (path) {
        let token = '8720cf29-8b66-4d25-9d87-cecb4e1b0555';

        return fetch(`https://data.wristler.eu/api/v1/${path}`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
        }).then(response => response.json())
    }
}

document.addEventListener("DOMContentLoaded", () => Wrislter.init());