import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */


import SwaggerUIBundle from 'swagger-ui-dist/swagger-ui-bundle';
import 'swagger-ui-dist/swagger-ui.css';    

export default class extends Controller {
    connect() {
        // const spec = fetch('/swagger').then(response => response.json());
        // SwaggerUIBundle({ spec , dom_id: '#swagger-ui' });
        fetch('/swagger').then(response => response.json()).then(data => {
            SwaggerUIBundle({ spec: data, dom_id: '#swagger-ui' });
        });
    }
}
