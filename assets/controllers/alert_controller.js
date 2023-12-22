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
        const alerts = this.element.querySelectorAll('.alert');

        alerts.forEach(async (alert, index) => {
            alert.addEventListener('click', () => {
                hideAlert(alert)
            });

            await wait(index * 250);

            setTimeout(() => {
                hideAlert(alert)
            }, 5000);
        });

        function hideAlert(alert) {
            alert.style.marginLeft = '5000px';
            setTimeout(() => {
                alert.remove()
            }, 2500);
        }

        async function wait(ms) {
            return new Promise(resolve => {
                setTimeout(resolve, ms);
            });
        }
    }


}
