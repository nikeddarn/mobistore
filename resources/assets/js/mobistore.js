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
    // $(storeNavbar).find('li.dropdown').hover(function () {
    //     $(this).addClass('open');
    //     // let megaMenu = $(this).find('.dropdown-menu');
    //     // let megaMenuTop = parseInt($(megaMenu).css('top'));
    //     // $(megaMenu).css({'top': (megaMenuTop - 20) + 'px', 'opacity': 0});
    //     // if ($('#product-path-categories').hasClass('hidden')) {
    //     //     $(megaMenu).stop(true, true).animate({opacity: 1, top: megaMenuTop + 'px'}, 200);
    //     // } else {
    //     //     let grid = $('.grid');
    //     //     grid.one('layoutComplete', function () {
    //     //         $(megaMenu).stop(true, true).animate({opacity: 1, top: megaMenuTop + 'px'}, 200);
    //     //     });
    //     //     grid.isotope();
    //     // }
    // }, function () {
    //     $(this).removeClass('open').find('.dropdown-menu').stop(true, true).fadeOut(200);
    // });


    // prevent to hide mega menu when there was clicked not on link
    $('.yamm-content').click(function (event) {
        if ($(event.target).parent()[0].nodeName !== 'A') {
            event.stopPropagation();
            event.preventDefault();
        }
    });

    // change retrieve product path in mega menu. show appropriate block
    let selectingPathLinks = $('#product-path-selection').find('.product-path-link');
    $(selectingPathLinks).each(function () {
        $(this)
            .click(function (event) {
                changeSelectingProductBlock(event, this);
            })
            .hover(function (event) {
                changeSelectingProductBlock(event, this);
            });
    });

    // change active product path link. display appropriate block.
    function changeSelectingProductBlock(event, clicked) {
        event.preventDefault();
        event.stopPropagation();

        // set 'product-path-active-link' class only to clicked link
        $(selectingPathLinks).each(function () {
            $(this).removeClass('product-path-active-link');
        });
        $(clicked).addClass('product-path-active-link');

        // change showing block
        let showingPathBlock = $('#' + $(clicked).data('target'));
        if ($(showingPathBlock).hasClass('hidden')) {
            $('.product-path-block:not(".hidden")').animate({opacity: 0}, 100, 'swing', function () {
                $(this).addClass('hidden');
                $(showingPathBlock).css('opacity', 0).removeClass('hidden').animate({opacity: 1}, 100, 'swing');
            });
        }
    }
});