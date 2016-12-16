//the let categories we are going to use here come from update.html.twig <- this value have to be given by twig.
if(categories) {
    for (let i = 0; i < categories.length; i++) {
        let tmp = document.querySelector("#scroll-" + categories[i]);
        tmp.onclick = function(event) {
            event.preventDefault();
            let fieldset = document.querySelector("#" + categories[i]);
            window.pageYOffset = document.documentElement.scrollTop = document.body.scrollTop = fieldset.offsetTop - 65;
        };
    }    
}
