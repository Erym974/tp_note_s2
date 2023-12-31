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
export default class extends Controller {
    connect() {

        var myModal = new bootstrap.Modal(this.element)

        myModal.show()

    }

    close() {
        const query = this.element.dataset.query;

        if(query) {
            let url = new URL(window.location.href)
            url.searchParams.delete(query)
            window.location.href = url
        }

    }

}
