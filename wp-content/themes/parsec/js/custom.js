( function( $ ) {
  function setHeight() {
    maxHeight = 0;
    $(".project h3 a").each(function(){
      $(this).css('height', '');
      if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
    });

    $(".project h3 a").height(maxHeight);
  }

  $(document).on('ready', function(){
    if( $('body').hasClass('post-type-archive-jetpack-portfolio') ) {
      setHeight();
    }
  });

  $(window).on('resize', function(){
    if( $('body').hasClass('post-type-archive-jetpack-portfolio') ) {
      setHeight();
    }
  });

} )( jQuery );
