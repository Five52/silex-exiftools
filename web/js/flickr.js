var flickr = {
    api_key: '53ca1be909c561305e0860acc2fe631b',
    request: 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=@api_key@&tags=@tags@&sort=relevance&page=@page@&per_page=10&format=json&nojsoncallback=1',
    img_url: 'https://farm@farm_id@.staticflickr.com/@server_id@/@id@_@secret@_m.jpg',
    getRequest: function(page) {
        return this.request
            .replace('@api_key@', this.api_key)
            .replace('@tags@', tags)
            .replace('@page', page)
        ;
    },
    getImgUrl: function(data) {
        return this.img_url
            .replace('@farm_id@', data.farm)
            .replace('@server_id@', data.server)
            .replace('@id@', data.id)
            .replace('@secret@', data.secret)
        ;
    }
};

var main = document.querySelector('main');
var container = document.createElement('div');
container.id = 'home';
main.appendChild(container);

var more = document.createElement('button');
more.textContent = "plus d'images";
more.id = 'more';
more.onclick = function() {
    counter++;
    loadImages();
}
main.appendChild(more);

var counter = 1;

loadImages();

function loadImages() {
    doRequest(flickr.getRequest(counter), function(status, json) {
        if (status == 200) {
            json = JSON.parse(json);
            for (var i = 0; i < 10; i++) {
                var photo = json.photos.photo[i];
                var imgSrc = flickr.getImgUrl(photo);
                var imgName = photo.title;
                var imgUrl = 'https://www.flickr.com/photos/' + photo.owner + '/' + photo.id;
                container.appendChild(createImage(imgName, imgSrc, imgUrl));
            }
        }
    });
}

function createImage(name, src, url) {
    var figure = document.createElement('figure');

    var a = document.createElement('a');
    a.href = url;
    a.target = '_blank';
    a.title = name;
    figure.appendChild(a);

    var image = document.createElement('img');
    image.src = src;
    image.alt = name;
    a.appendChild(image);

    var figcaption = document.createElement('figcaption');
    figcaption.textContent = name;
    figure.appendChild(figcaption);

    return figure;
}

function doRequest(url, callback) {
    var xhr = new XMLHttpRequest();

    xhr.addEventListener('load', function() {
        callback(this.status, xhr.responseText);
    });
    xhr.addEventListener('error', function() {
        callback(this.status);
    });

    xhr.open('GET', url, true);
    xhr.send();
    return xhr;
}

console.log(flickr);
console.log(tags);


