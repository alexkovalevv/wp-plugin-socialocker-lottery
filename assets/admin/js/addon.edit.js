(function($){
    $(document).ready(function(){
        
        window.onpsl_al_coupons_edit = {
            init: function(){
              this.events();
              this.selectDiscountRange();               
            },
            events: function(){
              var self = this;              
              $('#onpsl_al_discountRange').change(function(){           
                   self.selectDiscountRange();
              });
            },
            selectDiscountRange: function(){
                var $isActiveFieldSelectorDiscount = $('.factory-control-is-active[name="discount_is_active"]'); 
                var $isActiveFieldSelectorDiscountRange =  $('.factory-control-is-active[name="discountMin_is_active"],.factory-control-is-active[name="discountMax_is_active"]');
                
                if( $('#onpsl_al_discountRange').prop('checked') ) {
                    $('.onpsl-al-discount').fadeOut(300, function(){
                       $('.onpsl-al-discount-range').fadeIn(300); 
                    });        
                    $isActiveFieldSelectorDiscountRange.val(1); 
                    $isActiveFieldSelectorDiscount.val(0);
                } else { 
                    $('.onpsl-al-discount-range').fadeOut(300, function(){
                        $('.onpsl-al-discount').fadeIn(300);
                    });                
                    $isActiveFieldSelectorDiscountRange.val(0);
                    $isActiveFieldSelectorDiscount.val(1);
                }
          },
          /**
            * Binds the change event of the WP editor.
          */
          bindWpEditorChange: function( ed ) {
              var self = this;

              var changed = function() {
                  tinyMCE.activeEditor.save();                    
              };

              if ( tinymce.majorVersion <= 3 ) {
                  ed.onChange.add(function(){ changed(); });
              } else {
                  ed.on("change", function(){ changed(); });
              }
          }
        };
        
        window.onpsl_al_coupons_edit.init();        
    });
})(jQuery);