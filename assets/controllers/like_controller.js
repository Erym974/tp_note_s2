import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

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

    }

    async like() {

        const response = await axios.post(`/api/like/${this.element.dataset.id}`)

        if (response.data.success) {
            const like = this.element.querySelector('[data-like]')
            const icon = this.element.querySelector('[data-icon]')

            if (response.data.liked) {
                icon.classList.remove('bi-hand-thumbs-up')
                icon.classList.add('bi-hand-thumbs-up-fill')
            } else {
                icon.classList.remove('bi-hand-thumbs-up-fill')
                icon.classList.add('bi-hand-thumbs-up')
            }
            like.textContent = `${response.data.likes} ${response.data.likes > 1 ? 'likes' : 'like'}`
        }

    }

}
