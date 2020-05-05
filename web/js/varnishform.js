$(function() {
    var varnishLinkCheckbox = $('.varnish-link');
    if (varnishLinkCheckbox.length) {
        varnishLinkCheckbox.on('click', function (e) {
            var postData = {
                'varnishId': $(this).attr('data-varnishid'),
                'websiteId': $(this).attr('data-websiteid'),
                'checked': $(this).is(':checked')
            };
            var ajaxCall = $.ajax({
                url: 'varnish/link',
                type: "POST",
                data: postData
            });
            ajaxCall.done(function (response) {
                response = JSON.parse(response);
                var msgElem = '.varnish-link-msg-' +postData. varnishId + '-' + postData.websiteId;
                $(msgElem).text(response.message);
                $(msgElem).show().delay(1000).fadeOut();
                console.log(response.message);
            });
        });
    }
});
