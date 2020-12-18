'uses strict';
AOS.init({
    duration: 800,
    easing: 'slide',
    once: true
});

$(function(){
    var counter = function() {
        
        $('.count-numbers').waypoint( function( direction ) {

            if( direction === 'down' && !$(this.element).hasClass('ut-animated') ) {

                var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
                $('.counter > .number').each(function(){
                    var $this = $(this),
                    num = $this.data('number');
                    $this.animateNumber(
                    {
                        number: num,
                        numberStep: comma_separator_number_step
                    }, 2000
                    );
                });
                
            }

        } , { offset: '95%' } );

    }
    counter();
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};