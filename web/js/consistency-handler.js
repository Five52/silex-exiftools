//We first need to check if commune metadata are both present
const presentMetadata = [];

if (document.querySelector('[name="form\[XMP:Title\]"]') !== null && document.querySelector('[name="form\[IPTC:ObjectName\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Title\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:ObjectName\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Headline\]"]') !== null && document.querySelector('[name="form\[IPTC:Headline\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Headline\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Headline\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Creator\]"]') !== null && document.querySelector('[name="form\[IPTC:By-line\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Creator\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:By-line\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Credit\]"]') !== null && document.querySelector('[name="form\[IPTC:Credit\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Credit\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Credit\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Source\]"]') !== null && document.querySelector('[name="form\[IPTC:Source\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Source\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Source\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Description\]"]') !== null && document.querySelector('[name="form\[IPTC:Caption-Abstract\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Description\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Caption-Abstract\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:DateCreated\]"]') !== null && document.querySelector('[name="form\[IPTC:DateCreated\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:DateCreated\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:DateCreated\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:State\]"]') !== null && document.querySelector('[name="form\[IPTC:Province-State\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:State\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Province-State\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:UsageTerms\]"]') !== null && document.querySelector('[name="form\[IPTC:CopyrightNotice\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:UsageTerms\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:CopyrightNotice\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:City\]"]') !== null && document.querySelector('[name="form\[IPTC:City\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:City\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:City\]"]')
    });
}
if (document.querySelector('[name="form\[XMP:Country\]"]') !== null && document.querySelector('[name="form\[IPTC:Country-PrimaryLocationName\]"]') !== null) {
    presentMetadata.push({
        xmp : document.querySelector('[name="form\[XMP:Country\]"]'), 
        iptc : document.querySelector('[name="form\[IPTC:Country-PrimaryLocationName\]"]')
    });
}

//used to don't check the form if user apply edit by button.
let checkConsistency = true;

if (checkConsistency) {
    //let form is already define in meta-form.js
    form.onsubmit = function(event) {
        event.preventDefault();
        let consistency = consistencyCheck();

        if (!consistency) {
            if (document.querySelector("#inconsistencyInfo") == null) {
                let updateForm = document.querySelector("#updateForm");
                let inconsistencyInfo = document.createElement("div");
                inconsistencyInfo.setAttribute("id", "inconsistencyInfo");
                inconsistencyInfo.innerHTML = `
                    <p>Il existe des incohérences dans les métadonnées renseignées:</p>
                    <button id="applyConsistencyXmp">Préférer XMP</button>
                    <button id="applyInconsistency">Conserver les incohérences</button>
                    <button id="applyConsistencyIptc">Préférer IPTC</button>
                `;

                updateForm.insertBefore(inconsistencyInfo, updateForm.firstChild);
                inconsistencyButtonListener();
            }
            //we take back the user where the error is display
            //These = = = are needed to have a full bowsers compatibility
            window.pageYOffset = document.documentElement.scrollTop = document.body.scrollTop = document.querySelector("#utilLinks").offsetTop;

            return false; //the form is not consistent, we stop the sending.
        } 
        form.submit(); //the form is consistent so we send it.
    }
}

function inconsistencyButtonListener() {
    //set up the listener needed
    let applyXmp = document.querySelector("#applyConsistencyXmp");
    let applyIptc = document.querySelector("#applyConsistencyIptc");
    let applyInconsistency = document.querySelector("#applyInconsistency");

    applyXmp.onclick = function(event) {
        applyConsistencyMesure("applyXmp");
    }

    applyIptc.onclick = function(event) {
        applyConsistencyMesure("applyIptc");
    }

    applyInconsistency.onclick = function(event) {
        applyConsistencyMesure("applyInconsistency");
    } 
}
    
function applyConsistencyMesure(type) {
    if (type != "applyInconsistency") {     
        //we normalize the form with the choosen metadata type
        for (let i = 0; i < presentMetadata.length ; i++) {
            if (type == "applyXmp") {
                presentMetadata[i].iptc.value = presentMetadata[i].xmp.value;
            } else if (type == "applyIptc") {
                presentMetadata[i].xmp.value = presentMetadata[i].iptc.value;
            } 
        }
    }
    //we indicate to don't check consistency again and send the form
    checkConsistency = false;
    form.submit();
}

function consistencyCheck() {
    for (let i = 0; i < presentMetadata.length ; i++) {
        if (presentMetadata[i].iptc.value != presentMetadata[i].xmp.value) {
            return false;
        }
    }
    return true;
}
