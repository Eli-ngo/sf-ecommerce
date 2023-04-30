/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

function changeQuantity(url, quantity, content) {
    let urlFetch = url.replace('change_quantity/1', 'change_quantity/' + quantity);
    fetch(urlFetch, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
    }).then((r) => r.json()).then((data) => {
        console.log(data);
        if(data.success) {
            document.querySelector('[data-content="' + content + '"] [data-total]').innerHTML = data.total / 100;
            document.querySelector('[data-totalcart]').innerHTML = data.totalCart;
            document.querySelector('[data-content="' + content + '"] .message').innerHTML = data.message;
        } else {
            document.querySelector('[data-content="' + content + '"] input').value = data.quantity;
            document.querySelector('[data-content="' + content + '"] [data-total]').innerHTML = data.total / 100;
            document.querySelector('[data-content="' + content + '"] .message').innerHTML = data.message;
        }
    });
}

function eventChangeQuantity() {
    const input = document.querySelectorAll('.quantity');
    input.forEach (inp => {
        console.log(inp);
        const content = inp.parentNode.dataset.content;
        let url = inp.dataset.url;
        inp.addEventListener('change', () => {
            changeQuantity(url, inp.value, content);
        });
    });
}

window.addEventListener('load', () => {
    eventChangeQuantity();
});