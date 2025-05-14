export default class Trigger {
    /**
     * @type {HTMLElement}
     */
    #element;

    /**
     * @param {HTMLElement} trigger
     */
    constructor(trigger) {
        this.#element = trigger;
        this.#element.dataset.initialValue = this.#element.querySelector('span').textContent;
    }

    getElement() {
        return this.#element;
    }

    setValue(value) {
        this.#element.querySelector('span').textContent = value;
    }

    reset() {
        this.setValue(this.#element.dataset.initialValue);
    }
}
