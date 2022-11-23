/******************************************************************************
 * filename mod_hgd-image-compare.js
 *
 * Javascript-Funktionalität für HGD Image Compare
 *
 * @author  Holger Genth -Dienstleistungen- (https://holger-genth.de)
 * @copyright Copyright (c) 2014 - 2017. All rights reserved.
 * @license GNU General Public License version 2 or later: http://www.gnu.org/copyleft/gpl.html
 */

/*****************************************************************************
 * Drag plugin
 */
(function ($) {
  $.fn.hgdSlider = function (options) {

    const settings = $.extend({
      // drag vertically
      vert: false,
      // drag horizontally
      horz: true,
      handler: '.handler',
      ondrag: $.noop,
      ondragstart: $.noop,
      ondragend: $.noop
    }, options);

    let touch = true;

    const element = this;
    const handler = $(settings.handler, this);

    let elPosition = element.position();
    let elOffset = element.offset();
    const parent = element.offsetParent();

    let parentBBox = {};

    let pos = {};

    const calcParentBBox = function () {
      parentBBox = parent.offset();
      parentBBox.right = parentBBox.left + parent.outerWidth();
      parentBBox.bottom = parentBBox.top + parent.outerHeight();
    };

    const end = function (e) {
      e.preventDefault();
      const orig = e.originalEvent;

      settings.ondragend({
        top: orig.changedTouches[0].pageY,
        left: orig.changedTouches[0].pageX
      });
    };
    const up = function (e) {
      $(document).off("mouseup", up);
      parent.off("mousemove", mmove);
      settings.ondragend(pos);
    };
    const mmove = function (e) {
      if (e.pageX < parentBBox.left) e.pageX = parentBBox.left;
      if (e.pageX > parentBBox.right) e.pageX = parentBBox.right;
      if (e.pageY < parentBBox.top) e.pageY = parentBBox.top;
      if (e.pageY > parentBBox.bottom) e.pageY = parentBBox.bottom;

      setTimeout(function () {
        pos = {
          left: e.pageX - elOffset.left + elPosition.left,
          top: e.pageY - elOffset.top + elPosition.top
        };

        if (settings.horz)
          element.css('left', pos.left);
        if (settings.vert)
          element.css('top', pos.top);

        settings.ondrag(pos)
      }, 0);
    }



    if (!("ontouchstart" in document.documentElement)) {
      touch = false;
    }

    if (touch) {
      handler.bind("touchstart", function (e) {
        const orig = e.originalEvent;
        pos = {
          left: orig.changedTouches[0].pageX - elOffset.left,
          top: orig.changedTouches[0].pageY - elOffset.top
        };
        settings.ondragstart(pos);
      });
      handler.bind("touchmove", function (e) {
        e.preventDefault();
        const orig = e.originalEvent;
        if (orig.changedTouches[0].pageX < parentBBox.left) orig.changedTouches[0].pageX = parentBBox.left;
        if (orig.changedTouches[0].pageX > parentBBox.right) orig.changedTouches[0].pageX = parentBBox.right;
        if (orig.changedTouches[0].pageY < parentBBox.top) orig.changedTouches[0].pageY = parentBBox.top;
        if (orig.changedTouches[0].pageY > parentBBox.bottom) orig.changedTouches[0].pageY = parentBBox.bottom;
        setTimeout(function () {
          pos = {
            left: orig.changedTouches[0].pageX - elOffset.left + elPosition.left,
            top: orig.changedTouches[0].pageY - elOffset.top + elPosition.top
          };

          // do now allow two touch points to drag the same element
          if (orig.targetTouches.length > 1)
            return;
          if (settings.horz)
            element.css('left', pos.left);
          if (settings.vert)
            element.css('top', pos.top);
          settings.ondrag(pos);
        }, 0);
      });
      handler.on("touchend", end);
      handler.on("touchcancel", end);
    } else {
      $(document).on('selectstart', function () {
        return false;
      });
      handler.on('dragstart', function () {
        return false;
      });
      handler.on("mousedown", function (e) {
        calcParentBBox();
        element.trigger('dragstart');
        elPosition = element.position();
        elOffset = element.offset();

        pos = {
          left: e.pageX - elOffset.left,
          top: e.pageY - elOffset.top
        };
        parent.on("mousemove", mmove);
        $(document).on("mouseup", up);
        settings.ondragstart(pos);

      });
    }

    return this;
  };

})(jQuery);
/*****************************************************************************
 * Image compare Plugin
 */
(function ($) {

  $.fn.hgdImageCompare = function (options) {
    // window size
    let ww = 0;
    let wh = 0;
    // container size
    let cw = 0;
    let ch = 0;
    // image sizes
    let iw = 0;
    let ih = 0;
    // Breite des Maken-Element
    let mw = 0;
    // get elements
    const $container = this;
    const $separator = $('.separator', $container);

    const $img1 = $container.find('.image-1');
    const $img2 = $container.find('.image-2');

    let separator_offs = 0;
    /*-------------------------------------------------------------------------
     * calcAndSetSize
     *
     * berechnet und setzt die Größe aller Elemente
     *
     *------------------------------------------------------------------------*/
    function calcAndSetSize() {
      // Größe des größten Bildes herausfinden
      const width = (options.image1.width > options.image2.width) ? options.image1.width : options.image2.width;
      const height = (options.image1.height > options.image2.height) ? options.image1.height : options.image2.height;
      let sizeFactor = 1;

      // Größe des Browser-Fensters
      ww = $(window).innerWidth();
      wh = $(window).innerHeight();
      // Größe des Containers
      cw = $container.outerWidth();
      ch = $container.outerHeight();

      mw = $container.find('.mask').width();

      // die Container-Größe soll automatisch angepasst werden
      if (options.autoHeight) {
        // Seitenverhältnis des Bildes
        const aspect = width / height;
        // containerhöhe anpassen
        ch = cw / aspect;
        $container.css('height', ch);
      }
      // Faktor bestimmen, im die Bilder auf die Container-Größe anzupassen
      sizeFactor = cw / width;
      // Offset zum Verschieben des Trenners
      separator_offs = $separator.width() / 2;
      // Größe der Bilder setzen
      $container.find('.mask img').css({
        width: width * sizeFactor,
        height: height * sizeFactor
      });
      $container.css('height', height * sizeFactor);

    }
    // Resize-Ereignis abfangen
    $(window).resize(calcAndSetSize);

    calcAndSetSize();

    // den Separator initialisieren
    $separator.hgdSlider({
      horz: true,
      vert: false,
      handler: ".handler",
      ondrag: function (pos) {
        var left = pos.left + separator_offs;
        $img1.css('width', left);
        $img2.css('width', mw - left);
      }
    });

    return this;
  }

})(jQuery);