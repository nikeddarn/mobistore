'use strict';

$(document).ready(function () {

    // -------------------------------------- Select Picker ---------------------------------------

    // activate selectpicker
    $('.selectpicker').selectpicker();

    // ---------------------------------------Search Panel -----------------------------------------

    // fixed search panel on scroll
    let storeNavbar = $('#store-navbar');
    let bottomHeaderElement = $(storeNavbar);
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

    // ------------------------------- Mega Menu ----------------------------------------------------

    // Animate mega-menu. Adding class .open on hover to highlighting dropdown toggler.
    let storeNavbarCollapse = $('#store-navbar-collapse');
    let megaMenu = $(storeNavbarCollapse).find('.dropdown-menu');
    $(storeNavbarCollapse).find('.dropdown-toggle').hover(
        function () {
            $(this).addClass('open');
            $(megaMenu).css({'top': '20px', 'opacity': 0}).stop(true, true).animate({opacity: 1, top: '40px'}, 200);
            //align categories with 'isotope' package if it's showing
            if (!$('#product-path-categories').hasClass('hidden')) {
                $('.grid').isotope();
            }
        },
        function () {
            $(this).removeClass('open');
        });

    // prevent to hide mega menu when there was clicked not on link
    $('.yamm-content').click(function (event) {
        if (!$(event.target).closest('a').length) {
            event.stopPropagation();
            event.preventDefault();
        }
    });

    // change retrieve product path in mega menu. show appropriate block
    let selectingPathLinks = $('#product-path-selection').find('a');

    $(selectingPathLinks).each(function () {
        $(this).click(function (event) {
            event.preventDefault();
            event.stopPropagation();

            // highlight selected link
            $(selectingPathLinks).removeClass('product-path-selected');
            $(this).addClass('product-path-selected');

            // show selected content
            let productPathContent = $('#product-path-content');
            $(productPathContent).children('div').addClass('hidden');
            $(productPathContent).find('#' + $(this).data('target')).removeClass('hidden');
        });
    });
});