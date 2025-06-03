import {OptionSelectedEvent, VariantResolvedEvent} from "./events.js";

/**
 * @typedef {import('./types.js').Product} Product
 */

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

        this.#element.addEventListener('click', () => {
            if(!this.isAvailable()) {
                return;
            }

            this.#element.dispatchEvent(new OptionSelectedEvent(this));
        });
    }

    getLabel() {
        return this.#element.querySelector('.ssrn-option-label').textContent;
    }

    /**
     * @param {string} label
     */
    setLabel(label) {
        this.#element.querySelector('.ssrn-option-label').textContent = label;
    }

    getValue() {
        return this.#element.dataset.value;
    }

    isSelected() {
        return this.#element.classList.contains('selected');
    }

    /**
     * @param {boolean} selected
     */
    setSelected(selected = true) {
        this.#element.classList.toggle('selected', selected);
    }

    isInStock() {
        return !this.#element.classList.contains('out-of-stock');
    }

    setInStock(inStock = true) {
        this.#element.classList.toggle('out-of-stock', !inStock);
    }

    isAvailable() {
        return !this.#element.classList.contains('unavailable');
    }

    setAvailable(available = true) {
        this.#element.classList.toggle('unavailable', !available);

        if(!available && this.isSelected()) {
            this.select.reset();
        }
    }

    getVariant() {
        if('variant' in this.#element.dataset) {
            return this.#element.dataset.variant;
        }

        return null;
    }

    /**
     * @param {Product} variant
     */
    setVariant(variant) {
        this.setInStock(variant.inStock)
        this.#element.dataset.variant = variant.code;

        this.#element.dispatchEvent(new VariantResolvedEvent(this));
    }
}
