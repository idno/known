  // Modal Extension
  // ===============================

	$('.modal-dialog').attr( {'role' : 'document'})
    var modalhide =   $.fn.modal.Constructor.prototype.hide
    $.fn.modal.Constructor.prototype.hide = function(){
       var modalOpener = this.$element.parent().find('[data-target="#' + this.$element.attr('id') + '"]')
       modalhide.apply(this, arguments)
       modalOpener.focus()
       $(document).off('keydown.bs.modal')
    }

    var modalfocus =   $.fn.modal.Constructor.prototype.enforceFocus
    $.fn.modal.Constructor.prototype.enforceFocus = function(){
      var focEls = this.$element.find(":tabbable")
        , lastEl = focEls[focEls.length-1]
      $(document).on('keydown.bs.modal', $.proxy(function (ev) {
        if(!this.$element.has(ev.target).length && ev.shiftKey && ev.keyCode === 9) {  
          lastEl.focus()
          ev.preventDefault();
        }
      }, this))

      modalfocus.apply(this, arguments)
    }    