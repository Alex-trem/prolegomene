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


