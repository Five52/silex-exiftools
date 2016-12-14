let labels = document.querySelectorAll('label');

let fieldsets = {};

labels.forEach((label) => {
    const splitted = label.textContent.split(':');
    if (splitted.length === 2) {
        const name = splitted[0];
        if (!fieldsets.hasOwnProperty(name)) {
            let fieldset = document.createElement('fieldset');
            fieldset.id = name;
            fieldset.innerHTML = '<legend>' + name + '</legend>';
            fieldsets[name] = fieldset;
        }
        label.textContent = splitted[1].trim();
        fieldsets[name].appendChild(label.parentNode);
    }
});

let form = document.querySelector('form');
let submit = document.querySelector('button[type="submit"]');
for (let key in fieldsets) {
    if (fieldsets.hasOwnProperty(key)) {
        form.firstChild.insertBefore(fieldsets[key], submit.parentNode);
        if (
            fieldsets[key].id !== 'XMP'
            && fieldsets[key].id !== 'IPTC'
            && fieldsets[key].id !== 'EXIF'
        ) {
            fieldsets[key].querySelectorAll('div').forEach((div) => {
                let input = div.querySelector('input[type="text"]');
                input.setAttribute("readonly", "readonly");
                let elt = document.createElement('span');
                elt.className = 'lock';
                div.appendChild(elt);
            })
        }
    }
}
