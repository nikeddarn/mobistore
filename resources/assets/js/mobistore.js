$(document).ready(function () {

    // activate selectpicker
    $('.selectpicker').selectpicker();

    // fixed search panel on scroll
    let bottomHeaderElement = $('#store-navbar');
    let fixingScrollHeight = $(bottomHeaderElement).offset().top + $(bottomHeaderElement).height();
    let unfixingHeight = $('#top-navbar').height();
    $(window).scroll(function () {
        let fixingElement = $('#header-middle');
        let logoImage = $(fixingElement).find('img');
        if ($(window).scrollTop() > fixingScrollHeight) {
            $(fixingElement).addClass('fixed-header-middle');
            $(logoImage).addClass('hidden-xs');
            if (!$(fixingElement).find('#header_logo_placeholder').length) {
                $('<h4/>', {
                    'id': 'header_logo_placeholder',
                    'class': 'text-gray text-center visible-xs'
                }).text($(logoImage).data('text-logo')).insertBefore($(logoImage));
            }
            $(fixingElement).animate({top: 0});
        }
        if ($(window).scrollTop() <= unfixingHeight) {
            $(fixingElement).removeClass('fixed-header-middle');
            $(fixingElement).stop(true);
            $(fixingElement).removeAttr('style');
            $(logoImage).removeClass('hidden-xs');
            $(fixingElement).find('#header_logo_placeholder').remove();
        }
    });
});