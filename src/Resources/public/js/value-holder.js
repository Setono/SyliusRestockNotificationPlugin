import Select from './select.js';
import { OptionSelectedEvent } from './events.js';

export default class ValueHolder {
    /**
     * @type {HTMLInputElement|HTMLSelectElement}
     */
    #element;

    constructor(valueHolder) {
        this.#element = valueHolder;
    }

    /**
     * @param {String} value
     */
    setValue(value) {
        this.#element.value = value;
        this.#element.dispatchEvent(new Event('change', { bubbles: true }));
    }

    reset() {
        this.setValue('');
    }
}
