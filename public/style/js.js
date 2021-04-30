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


(function () {
    const form = document.querySelector('#navCalendar')
    
    document.querySelectorAll('#navCalendar input').forEach(input => {
        input.addEventListener('click', function (e){
            const month = input.attributes.month.value
            const year = input.attributes.year.value
            const inputId = input.attributes.id
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
                    const dataNav = data.content.substr(-220);
                    const numberPattern = /\d+/g;
                    document.querySelector('#prevMonth').setAttribute('month', dataNav.substr(148, 4).match(numberPattern).join(''));
                    document.querySelector('#nextMonth').setAttribute('month', dataNav.substr(193, 4).match(numberPattern).join(''));
                    const content = document.querySelector('#theCalendar');
                    content.innerHTML = data.content;
                    history.pushState({}, null, url.pathname + '?' + Params.toString());
                }).catch(e => console.log(e));
            });
        });
})();

