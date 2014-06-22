if (phantom.args.length === 0) {
    console.log('Usage: sahi.js <Sahi Playback Start URL>');
    phantom.exit();
} else {
    var address = phantom.args[0];
    console.log('Loading ' + address);
    var page = new WebPage();
    page.viewportSize = {width: 1024, height: 768};
    page.open(address, function(status) {
        if (status === 'success') {
            var title = page.evaluate(function() {
                return document.title;
            });
            console.log('Page title is ' + title);
        } else {
            console.log('FAIL to load the address');
        }
    });

    // add callback listener to catch window.callPhantom() in the page
    page.onCallback = function(filename) {
        page.render(filename + '.png');
        require('fs').write(filename + '.html', page.content, 'w');
    };
}
