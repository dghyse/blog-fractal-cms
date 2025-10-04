import {ILogger, resolve} from 'aurelia';

export class PorteFolioApp {
    constructor(
        private readonly logger: ILogger = resolve(ILogger).scopeTo('PorteFolio'),
    ) {
    }

    public attaching()
    {
        this.logger.trace('Attaching');
    }
}