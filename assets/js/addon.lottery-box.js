(function($){
    $(document).ready(function(){
        $('#onp-al-send-email').on('click', function(){            
            var $email = $(this).parent().find('#onp-al-email').val();
            var $lockerId = $(this).closest('.onp-al-emailbox').data('locker-id');
            
            var requestData = {
                 email: $email,
                 lockerId: $lockerId
            };
            
            $('.onp-al-emailbox-loader').show();
            
            var request = $.ajax({
                    url: window.onpsl.al.options.ajaxUrl + '?action=onpsl_al_register_lottery_email',
                    type: 'POST', 
                    dataType: 'json',
                    data: requestData,
                    success: function(data) {    
                        console.log(data);
                        $('.onp-al-emailbox-loader').hide();

                        if( data && data.error ) {
                            $('.onp-al-emailbox').addClass('error');

                            if( !$('.onpsl-al-error-message').length )
                                $('.onp-al-emailbox-form').append(
                                   '<p class="onpsl-al-error-message">' + data.error + '</p>'
                                );
                            else
                                $('.onpsl-al-error-message').text(data.error);
                        }

                        if( data && data.success ) {
                            //Добавляем куку и если она существует скрываем форму.                           
                            $('.onp-al-emailbox').removeClass('error')
                                    .addClass('success')
                                    .html(data.success);                            
                        }                        
                    }
            });
            
            return false;
        });
    });
})(jQuery);