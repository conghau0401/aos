/**
 *  find address callback function
 *
 * @param string address01
 * @param string address02
 * @param string code
 */
function jusoCallBack(address01, address02, code){
    if (document.getElementById('address01') != null || document.getElementById('address01SameDay') != null) {
        let hasSameDay = document.getElementById('has-sameday').value
        if (hasSameDay.toString() == "false") {
            const input1 = $("#address01");
            input1.val(address01);
            const event1 = new Event('input', { bubbles: true });
            input1.get(0).dispatchEvent(event1);

            const input2 = $("#address02");
            input2.val(address02);
            const event2 = new Event('input', { bubbles: true });
            input2.get(0).dispatchEvent(event2);

            const input3 = $("#code");
            input3.val(code);
            const event3 = new Event('input', { bubbles: true });
            input3.get(0).dispatchEvent(event3);
        } else {
            const input1 = $("#address01SameDay");
            input1.val(address01);
            const event1 = new Event('input', { bubbles: true });
            input1.get(0).dispatchEvent(event1);

            const input2 = $("#address02SameDay");
            input2.val(address02);
            const event2 = new Event('input', { bubbles: true });
            input2.get(0).dispatchEvent(event2);

            const input3 = $("#codeSameDay");
            input3.val(code);
            const event3 = new Event('input', { bubbles: true });
            input3.get(0).dispatchEvent(event3);
        }
    }
}

// Payment Nicepay
//[PC Only]When pc payment window is closed, nicepay-3.0.js call back nicepaySubmit() function <<'nicepaySubmit()' DO NOT CHANGE>>
function nicepaySubmit(){
    document.payForm.submit();
}

//[PC Only]payment window close function <<'nicepayClose()' DO NOT CHANGE>>
function nicepayClose(){
    console.log("payment window is closed");
}

//pc, mobile chack script (sample code)
function checkPlatform(ua) {
    if(ua === undefined) {
        ua = window.navigator.userAgent;
    }

    ua = ua.toLowerCase();
    var platform = {};
    var matched = {};
    var userPlatform = "pc";
    var platform_match = /(ipad)/.exec(ua) || /(ipod)/.exec(ua)
        || /(windows phone)/.exec(ua) || /(iphone)/.exec(ua)
        || /(kindle)/.exec(ua) || /(silk)/.exec(ua) || /(android)/.exec(ua)
        || /(win)/.exec(ua) || /(mac)/.exec(ua) || /(linux)/.exec(ua)
        || /(cros)/.exec(ua) || /(playbook)/.exec(ua)
        || /(bb)/.exec(ua) || /(blackberry)/.exec(ua)
        || [];

    matched.platform = platform_match[0] || "";

    if(matched.platform) {
        platform[matched.platform] = true;
    }

    if(platform.android || platform.bb || platform.blackberry
            || platform.ipad || platform.iphone
            || platform.ipod || platform.kindle
            || platform.playbook || platform.silk
            || platform["windows phone"]) {
        userPlatform = "mobile";
    }

    if(platform.cros || platform.mac || platform.linux || platform.win) {
        userPlatform = "pc";
    }

    return userPlatform;
}

function outError() {
    window.location.href = "/login"
}
