var Domains = (function($, window, document) {
    var obj = {};
    var pdfDoneEl;
    var basePath;
    obj.init = function() {
        basePath = $('head base').attr('href');
        pdfDoneEl = $('.pdfDone');
        pdfDone();
    }
    var pdfDone = function() {
        pdfDoneEl.change(function() {
            var fileName = $(this).val();
            var checked = this.checked;
            $.ajax({
                type: "POST",
                url: basePath + '/ipmpdf/pdfdone',
                data: {
                    'fileName': fileName,
                    'checked': checked
                },

                success:function(data) {
                    console.log(data);
                }
            });
            console.log($(this).val());
        });
    }
    return obj;
})($, window, document);

$(function() {
    Domains.init();
});

