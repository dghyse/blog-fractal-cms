import {Aurelia, ConsoleSink, LoggerConfiguration, LogLevel } from 'aurelia';
import './scss/main.scss';

import {PorteFolioApp} from "./app/app";
import {ValidationHtmlConfiguration, ValidationTrigger} from "@aurelia/validation-html";
import * as globalAttributes from './app/attributes/index';

declare let webpackBaseUrl: string;
declare let __webpack_public_path__: string;
if ((window as any).webpackBaseUrl) {
    __webpack_public_path__ = webpackBaseUrl;
}
const page = document.querySelector('body') as HTMLElement;
declare const PRODUCTION:boolean;
const aurelia = Aurelia
    .register(globalAttributes);
if (PRODUCTION == false) {
    aurelia.register(LoggerConfiguration.create({
        level: LogLevel.trace,
        colorOptions: 'colors',
        sinks: [ConsoleSink]
    }));
}

aurelia.register(ValidationHtmlConfiguration.customize((options) => {
    // customization callback
    options.DefaultTrigger = ValidationTrigger.blur;
}));
aurelia
    .enhance({
        host: page,
        component: PorteFolioApp
    });

