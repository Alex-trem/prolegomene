var td = document.querySelectorAll('td');

td.forEach(element => {
    var smalls = element.getElementsByTagName('small');
    if(smalls.length >= 3 && smalls.length < 6){
        element.classList.add('bg-warning');
    } 
    if(smalls.length >= 6){
        element.classList.add('bg-danger');
        var small = element.getElementsByTagName('small');
        for (let i = 0; i < small.length; i++) {
            small[i].classList.add('d-none');
        }
    }
});


// (function ($) {
//     $('.navCalendar').on('click', function (e){
//         e.preventDefault();
//         var $a = $(this);
//         var url = window.location.href;
//         $.post(url, {
//                 month: jQuery(this).data('month'),
//                 year: jQuery(this).data('year')
//             }, 'text')
//             .done(function(data, text, jqxhr){
//                 $('.calendar').replaceWith(jqxhr.responseText)
//             })
//             .fail(function(jqxhr){
//                 console.log(jqxhr);
//             });
//     });
// })(jQuery);


window.onload = () => {
    const form = document.querySelector('#navCalendar')
    
    document.querySelectorAll('#navCalendar input').forEach(input => {
        input.addEventListener('click', function (e){
            const month = input.attributes.month.value
            const year = input.attributes.year.value
            const Params = new URLSearchParams();
            Params.append('month', month);
            Params.append('year', year);

            const url = new URL(window.location.href);
            fetch(url.pathname + '?ajax=1&' + Params.toString(), {
                headers: {
                    "HTTP_X_REQUESTED_WITH": "XMLHttpRequest"
                }
            }).then(response => 
                response.json()
            ).then(data => {
                const content = document.querySelector('#theCalendar');
                content.innerHTML = data.content;
                history.pushState({}, null, url.pathname + '?' + Params.toString());
            });
        });
    });
}

