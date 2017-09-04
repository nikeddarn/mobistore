'use strict';

$(document).ready(function () {

    // activate selectpicker
    $('.selectpicker').selectpicker();

    let storeNavbar = $('#store-navbar');

    // fixed search panel on scroll
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

    // Animate mega-menu. Adding class .open on hover to highlighting dropdown toggler.

    $(storeNavbar).find('li.dropdown').hover(function () {
        let megaMenu = $(this).find('.dropdown-menu');
        $(this).addClass('open');
        let megaMenuTop = parseInt($(megaMenu).css('top'));
        $(megaMenu).css({'top': (megaMenuTop-20)+'px', 'opacity': 0});
        $(megaMenu).stop(true,true).animate({opacity: 1, top: megaMenuTop+'px'}, 200);
    }, function () {
        $(this).removeClass('open').find('.dropdown-menu').stop(true, true).fadeOut(200);
    });

    // $(storeNavbar).find('li.dropdown').click(function(event) {
    //     event.preventDefault();
    //     event.stopPropagation();
    //     if($(this).hasClass('open')){
    //         $(this).removeClass('open');
    //         $(this).find('.dropdown-menu').stop(true, true).css('display', 'none');
    //     }else{
    //         $(this).addClass('open');
    //         $(this).find('.dropdown-menu').stop(true, true).css({'top': '100%', 'display': 'block', 'opacity': 1});
    //     }
    // });
});