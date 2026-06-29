$(window).on('scroll', function () {
    const header = $('#header');
    if (!header.length) return;
    header.toggleClass('header-sticky-shadow', $(window).scrollTop() > 8);
});

