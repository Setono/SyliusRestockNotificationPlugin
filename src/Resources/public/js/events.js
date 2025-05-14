export class OptionSelectedEvent extends Event {
    static name = 'ssrn:option-selected';

    /**
     * @param {Option} option
     */
    constructor(option) {
        super(OptionSelectedEvent.name, {
            bubbles: true,
            cancelable: true
        });

        this.option = option;
    }
}
