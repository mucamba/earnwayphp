<script src="{{ asset('general/js/prism.min.js') }}"></script>
<script>
    "use strict";
    // Scroll-spy for sidebar
    $(window).on('scroll', function() {
        const scrollPos = $(document).scrollTop();
        $('nav.sidebar a').each(function() {
            const currLink = $(this);
            const ref = $(currLink.attr('href'));
            if (ref.position().top - 140 <= scrollPos && ref.position().top + ref.height() > scrollPos) {
                $('nav.sidebar a').removeClass('active');
                currLink.addClass('active');
            }
        });
    });
</script>