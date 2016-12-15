//IPTC to check const
const objectNameIptc = document.querySelector('[name="form\[IPTC:ObjectName\]"]');
const headlineIptc = document.querySelector('[name="form\[IPTC:Headline\]"]');
const byLineIptc = document.querySelector('[name="form\[IPTC:By-line\]"]');
const creditIptc = document.querySelector('[name="form\[IPTC:Credit\]"]');
const sourceIptc = document.querySelector('[name="form\[IPTC:Source\]"]');
const captionAbstractIptc = document.querySelector('[name="form\[IPTC:Caption-Abstract\]"]');
const dateCreatedIptc = document.querySelector('[name="form\[IPTC:DateCreated\]"]');
const provinceStateIptc = document.querySelector('[name="form\[IPTC:Province-State\]"]');
const copyrightNoticeIptc = document.querySelector('[name="form\[IPTC:CopyrightNotice\]"]');
const cityIptc = document.querySelector('[name="form\[IPTC:City\]"]');
const countryPrimaryIptc = document.querySelector('[name="form\[IPTC:Country-PrimaryLocationName\]"]');

//XMP to check const
const titleXmp = document.querySelector('[name="form\[XMP:Title\]"]');
const headlineXmp = document.querySelector('[name="form\[XMP:Headline\]"]');
const creatorXmp = document.querySelector('[name="form\[XMP:Creator\]"]');
const creditXmp = document.querySelector('[name="form\[XMP:Credit\]"]');
const sourceXmp = document.querySelector('[name="form\[XMP:Source\]"]');
const descriptionXmp = document.querySelector('[name="form\[XMP:Description\]"]');
const dateCreatedXmp = document.querySelector('[name="form\[XMP:DateCreated\]"]');
const stateXmp = document.querySelector('[name="form\[XMP:State\]"]');
const usageTermsXmp = document.querySelector('[name="form\[XMP:UsageTerms\]"]');
const cityXmp = document.querySelector('[name="form\[XMP:City\]"]');
const countryXmp = document.querySelector('[name="form\[XMP:Country\]"]');

let checkConsistency = true;

//form is already define in meta-form.js
if (checkConsistency) {
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

    if (type == "applyInconsistency") {
        //we indicate to don't check consistency again and send the form
        checkConsistency = false;
        form.submit();
        return null;
    }
            
    //we normalize the form with the choosen metadata type
    //we don't send automaticaly the form to let the user check.
    if (objectNameIptc !== null && titleXmp !== null) {
        if (type == "applyXmp") {
            objectNameIptc.value = titleXmp.value;
        } else if (type == "applyIptc") {
            titleXmp.value = objectNameIptc.value;
        }
    }
    if (headlineIptc !== null && headlineXmp !== null) {
        if (type == "applyXmp") {
            headlineIptc.value = headlineXmp.value;
        } else if (type == "applyIptc") {
            headlineXmp.value = headlineIptc.value;
        }
    }
    if (byLineIptc !== null && creatorXmp !== null) {
        if (type == "applyXmp") {
            byLineIptc.value = creatorXmp.value;
        } else if (type == "applyIptc") {
            creatorXmp.value = byLineIptc.value;
        }
    }
    if (creditIptc !== null && creditXmp !== null) {
        if (type == "applyXmp") {
            creditIptc.value = creditXmp.value;
        } else if (type == "applyIptc") {
            creditXmp.value = creditIptc.value;
        }
    }
    if (sourceIptc !== null && sourceXmp !== null) {
        if (type == "applyXmp") {
            sourceIptc.value = sourceXmp.value;
        } else if (type == "applyIptc") {
            sourceXmp.value = sourceIptc.value;
        }
    }
    if (captionAbstractIptc !== null && descriptionXmp !== null) {
        if (type == "applyXmp") {
            captionAbstractIptc.value = descriptionXmp.value;
        } else if (type == "applyIptc") {
            descriptionXmp.value = captionAbstractIptc.value;
        }
    }
    if (dateCreatedIptc !== null && dateCreatedXmp !== null) {
        if (type == "applyXmp") {
            dateCreatedIptc.value = dateCreatedXmp.value;
        } else if (type == "applyIptc") {
            dateCreatedXmp.value = dateCreatedIptc.value;
        }
    }
    if (provinceStateIptc !== null && stateXmp !== null) {
        if (type == "applyXmp") {
            provinceStateIptc.value = stateXmp.value;
        } else if (type == "applyIptc") {
            stateXmp.value = provinceStateIptc.value;
        }
    }
    if (copyrightNoticeIptc !== null && usageTermsXmp !== null) {
        if (type == "applyXmp") {
            copyrightNoticeIptc.value = usageTermsXmp.value;
        } else if (type == "applyIptc") {
            usageTermsXmp.value = copyrightNoticeIptc.value;
        }
    }
    if (cityIptc !== null && cityXmp !== null) {
        if (type == "applyXmp") {
            cityIptc.value = cityXmp.value;
        } else if (type == "applyIptc") {
            cityXmp.value = cityIptc.value;
        }
    }
    if (countryPrimaryIptc !== null && countryXmp !== null) {
        if (type == "applyXmp") {
            countryPrimaryIptc.value = countryXmp.value;
        } else if (type == "applyIptc") {
            countryXmp.value = countryPrimaryIptc.value;
        }
    }
}

function consistencyCheck() {
    if (objectNameIptc !== null && titleXmp !== null) {
        if (objectNameIptc.value != titleXmp.value) {
            return false;
        }
    }
    if (headlineIptc !== null && headlineXmp !== null) {
        if (headlineIptc.value != headlineXmp.value) {
            return false;
        }
    }
    if (byLineIptc !== null && creatorXmp !== null) {
        if (byLineIptc.value != creatorXmp.value) {
            return false;
        }
    }
    if (creditIptc !== null && creditXmp !== null) {
        if (creditIptc.value != creditXmp.value) {
            return false;
        }
    }
    if (sourceIptc !== null && sourceXmp !== null) {
        if (sourceIptc.value != sourceXmp.value) {
            return false;
        }
    }
    if (captionAbstractIptc !== null && descriptionXmp !== null) {
        if (captionAbstractIptc.value != descriptionXmp.value) {
            return false;
        }
    }
    if (dateCreatedIptc !== null && dateCreatedXmp !== null) {
        if (dateCreatedIptc.value != dateCreatedXmp.value) {
            return false;
        }
    }
    if (provinceStateIptc !== null && stateXmp !== null) {
        if (provinceStateIptc.value != stateXmp.value) {
            return false;
        }
    }
    if (copyrightNoticeIptc !== null && usageTermsXmp !== null) {
        if (copyrightNoticeIptc.value != usageTermsXmp.value) {
            return false;
        }
    }
    if (cityIptc !== null && cityXmp !== null) {
        if (cityIptc.value != cityXmp.value) {
            return false;
        }
    }
    if (countryPrimaryIptc !== null && countryXmp !== null) {
        if (countryPrimaryIptc.value != countryXmp.value) {
            return false;
        }
    }
    return true;
}