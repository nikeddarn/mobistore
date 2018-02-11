/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 46);
/******/ })
/************************************************************************/
/******/ ({

/***/ 46:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(47);


/***/ }),

/***/ 47:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


$(document).ready(function () {

    // -------------------------------------- Select Picker ---------------------------------------

    // activate selectpicker
    $('.selectpicker').selectpicker();

    // ---------------------------------------Search Panel -----------------------------------------

    // fixed search panel on scroll
    var storeNavbar = $('#store-navbar');
    var bottomHeaderElement = $(storeNavbar);
    var fixingScrollHeight = $(bottomHeaderElement).offset().top + $(bottomHeaderElement).height();
    var unfixingHeight = $('#top-navbar').height();
    $(window).scroll(function () {
        var fixingElement = $('#header-middle');
        var logoImage = $(fixingElement).find('img');
        if ($(window).scrollTop() > fixingScrollHeight) {
            $(fixingElement).addClass('fixed-header-middle');
            $(logoImage).addClass('hidden-xs');
            if (!$(fixingElement).find('#header_logo_placeholder').length) {
                $('<h4/>', {
                    'id': 'header_logo_placeholder',
                    'class': 'text-gray text-center visible-xs'
                }).text($(logoImage).data('text-logo')).insertBefore($(logoImage));
            }
            $(fixingElement).animate({ top: 0 });
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
    var selectingPathLinks = $('#product-path-selection').find('.product-path-link');
    $(selectingPathLinks).each(function () {
        $(this).click(function (event) {
            changeSelectingProductBlock(event, this);
        }).hover(function (event) {
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
        var showingPathBlock = $('#' + $(clicked).data('target'));
        if ($(showingPathBlock).hasClass('hidden')) {
            $('.product-path-block:not(".hidden")').animate({ opacity: 0 }, 100, 'swing', function () {
                $(this).addClass('hidden');
                $(showingPathBlock).css('opacity', 0).removeClass('hidden').animate({ opacity: 1 }, 100, 'swing');
            });
        }
    }
});

/***/ })

/******/ });