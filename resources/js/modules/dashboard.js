$('.dashboard-slider').each(function () {
    const slider = $(this);
    const track = slider.find('.dashboard-slider-track');
    const cards = track.children();
    let index = 0;

    if (!cards.length) return;

    const move = () => {
        const width = slider.width();
        track.css('transform', `translateX(-${index * width}px)`);
    };

    slider.find('[data-slide="next"]').on('click', function () {
        index = (index + 1) % cards.length;
        move();
    });

    slider.find('[data-slide="prev"]').on('click', function () {
        index = (index - 1 + cards.length) % cards.length;
        move();
    });

    $(window).on('resize', move);
    move();
});

