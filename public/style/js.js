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
    const showTab = function (a) {
        var div = a.parentNode.parentNode.parentNode
        var h4 = a.parentNode

        if (h4.classList.contains('active')){
            return false
        }
        div.querySelector('.nav-item .active').classList.remove('active')
        h4.classList.add('active')

        div.querySelector('.content-tab.active-content').classList.remove('active-content')
        div.querySelector(a.getAttribute('href')).classList.add('active-content')
    }

    var tabs = document.querySelectorAll('.nav-tabs .nav-item a')

    for (var i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener('click', function (e) {
            $("html, body").animate( { scrollTop: window.scrollY }, 150);
            showTab(this)
        })
    }

    var hash = window.location.hash
    var a = document.querySelector('a[href="' + hash + '"]')
    if (a != null && !a.classList.contains('active')){
        showTab(a)
    }
})();