import {bindable, customAttribute, ILogger, INode, resolve, IPlatform, IEventAggregator} from "aurelia";

@customAttribute('blog-front-menu')
export class Menu {

    @bindable() id: number;

    private actionButtons:NodeList;
    private menuLink:NodeList;
    public constructor(
        private readonly logger: ILogger = resolve(ILogger).scopeTo('Menu'),
        private readonly element: HTMLElement = resolve(INode) as HTMLElement,
        private readonly ea: IEventAggregator = resolve(IEventAggregator),
        private readonly platform:IPlatform = resolve(IPlatform)
    ) {
        this.logger.trace('constructor');
    }

    public attached()
    {
        this.logger.trace('attached');
        this.addEvent();
    }
    public detached()
    {
        this.logger.trace('attached');
        this.removeEvent();
    }

    private addEvent()
    {
        this.logger.trace('addEvent');
        this.actionButtons = this.element.querySelectorAll('button');
       this.actionButtons.forEach((ele, index) => {
           ele.addEventListener('click', this.onAction);
       });
    }

    private removeEvent()
    {
        this.logger.trace('removeEvent');
        this.actionButtons.forEach((ele, index) => {
            ele.removeEventListener('click', this.onAction);
        });

    }
    private readonly onAction = (event:Event) => {
        this.logger.trace('onAction');
        let button:HTMLElement = <HTMLElement>event.currentTarget;
        if (button) {
            if (button.nodeName !== 'button') {
                button = button.closest('button');
            }
            if (button) {
                const targetId = button.getAttribute('aria-controls');
                const target:HTMLElement = this.element.querySelector('#'+targetId);
                if (target) {
                    this.menuLink = target.querySelectorAll('a');
                    if (target.classList.contains('hidden') === false) {
                        target.classList.add('hidden');
                        target.setAttribute('aria-expanded', 'false');
                        target.setAttribute('aria-hidden', 'true');
                        button.setAttribute('aria-expanded', 'false');
                    } else {
                        target.classList.remove('hidden');
                        target.setAttribute('aria-expanded', 'true');
                        target.setAttribute('aria-hidden', 'false');
                        button.setAttribute('aria-expanded', 'true');
                        if (this.menuLink.length > 0) {
                            const link:HTMLLinkElement = this.menuLink[0] as HTMLLinkElement;
                            if (link) {
                                link.focus();
                            }
                        }
                    }
                }
            }
        }
    }
}