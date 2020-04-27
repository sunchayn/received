class Dropdown {
    constructor(selector, options = {}) {
        this.selector = selector;

        // Default options
        this.options = {
            event: 'click',
        };

        this.options = Object.assign({}, this.options, options);

        this.bootstrap();
    }

    reload() {
        this.bootstrap();
    }

    bootstrap() {
        this.data = [];
        this.current = null;
        this.run();
    }

    run() {
        const DOMObjects = document.querySelectorAll(this.selector);

        this.init(DOMObjects);
        this.registerEvents();
    }

    init(elements) {
        Array.from(elements).forEach(dropdown => {
            this.data.push({
                node: dropdown,
                trigger: dropdown.querySelector('.js-dropdown-trigger'),
                menu: dropdown.querySelector('.js-dropdown-menu')
            });
        });
    }

    registerEvents() {
        this.data.forEach(data => {
            this._dropdownEvent(data);
        });

        window.addEventListener(this.options.event, e => {
            this.closeCurrent();
        });
    }

    closeCurrent() {
        if (this.current) {
            this._closeDropdown(this.current);
        }
    }

    addCurrent(data) {
        if (this.current && !this.current.menu.isSameNode(data.menu)) {
            this.closeCurrent();
        }

        this.current = data;
    }

    _dropdownEvent(data) {
        data.menu.addEventListener(this.options.event, e => {
            e.stopPropagation();
        });

        data.trigger.addEventListener(this.options.event, e => {
            e.stopPropagation();

            if (data.node.classList.contains('is-active')) {
                this._closeDropdown(data);
            } else {
                this._openDropdown(data);
                this.addCurrent(data);
            }

        });
    }

    _openDropdown(data) {
        data.trigger.classList.add('is-dropdown-open');
        data.node.classList.add('is-active');
    }

    _closeDropdown(data) {
        data.trigger.classList.remove('is-dropdown-open');
        data.node.classList.remove('is-active');
    }
}

export default Dropdown;
