import {OptionSelectedEvent} from "./events.js";

export default class Option {
    /**
     * @type {Select}
     */
    select;

    /**
     * @type {HTMLElement}
     */
    #element;

    /**
     * @param {Select} select
     * @param {HTMLElement} option
     */
    constructor(select, option) {
        this.select = select;
        this.#element = option;

        option.addEventListener('click', () => {
            this.#element.dispatchEvent(new OptionSelectedEvent(this));
        });
    }

    getLabel() {
        return this.#element.querySelector('.ssrn-option-label').textContent;
    }

    getValue() {
        return this.#element.dataset.value;
    }

    /**
     * @param {boolean} selected
     */
    setSelected(selected = true) {
        this.#element.classList.toggle('selected', selected);
    }

    isSelected() {
        return this.#element.classList.contains('selected');
    }

    setInStock(inStock = true) {
        this.#element.classList.toggle('out-of-stock', !inStock);
    }

    setAvailable(available = true) {
        this.#element.classList.toggle('unavailable', !available);

        if(!available && this.isSelected()) {
            this.select.reset();
        }
    }
}
