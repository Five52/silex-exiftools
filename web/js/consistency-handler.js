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

//form is already define in meta-form.js
form.onsubmit = function(event) {
    console.log(event);
    let consistency = consistencyCheck();

    if (!consistency) {
        let inconsistencyInfo = document.querySelector("#inconsistencyInfo");
        inconsistencyInfo.innerHTML = `
            <p>Il existe des incohérences dans les métadonnées renseignées:</p>
            <button id="applyConsistencyXmp">Préférer XMP</button>
            <button id="applyConsistencyIptc">Conserver les incohérences</button>
            <button id="applyInconsistency">Préférer IPTC</button>
        `;
        return false; //the form is not consistent, we stop the sending.
    } 
    return true; //the form is consistent so we send it.
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