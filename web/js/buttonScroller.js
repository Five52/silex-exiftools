//first we gather information about client/page height
//then we show the navigation button if the page is 1.5 time bigger than the client size. 
let scrollTop = document.querySelector("#scroll-top"),
    scrollBottom = document.querySelector("#scroll-bottom"),
    body = document.body,
    html = document.documentElement;

let totalHeight = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight ),
    visibleHeight = html.clientHeight,
    displayNavigationButton = totalHeight > 1.5 * visibleHeight ? true : false;

console.log(displayNavigationButton);

if (displayNavigationButton) {
    scrollTop.style.display = "block";
    scrollBottom.style.display = "block";

    scrollTop.onclick = function(event) {
        event.preventDefault();
        window.pageYOffset = document.documentElement.scrollTop = document.body.scrollTop = 0;
    }

    scrollBottom.onclick = function(event) {
        event.preventDefault();
        window.pageYOffset = document.documentElement.scrollTop = document.body.scrollTop = totalHeight;
    }
}