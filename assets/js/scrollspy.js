$(function() {
    
    var elems  = $('.section-row'),
        offset = 75;
        
    var title = $('title').text();
    
    $(window).on('scroll', function() {
        
        var currentTop = $(window).scrollTop() + offset;
        
        elems.each(function() {
            var elemTop 	= $(this).offset().top,
                elemBottom 	= elemTop + $(this).height();
            
            if( currentTop >= elemTop && currentTop <= elemBottom )
            {
                var $sidebarEle = $('#sidebar').find('a[href="#' + $(this).find('a[name]').prop('name')+ '"]');
                
                $('#sidebar').find('.sidebar-group-in,.sidebar-item-active').removeClass('sidebar-group-in sidebar-item-active');
                
                // Is it in a group?
                if( $sidebarEle.parents('.sidebar-group').length )
                {
                    $sidebarEle.parent().addClass('sidebar-item-active').parents('.sidebar-group').addClass('sidebar-group-in');
                }
                
                // It's a base element:
                else
                {
                    $sidebarEle.parent().addClass('sidebar-item-active');
                }
                
                // Set the meta title:
                $('title').text(function() { return ($sidebarEle.length) ? $sidebarEle.text() + ' | '+title : title; });
            }
        });
    });
    
    // Handle the click:
    $('#sidebar a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        // Get the element:
        var $ele = $( $(this).attr('href') );
        
        // If it's in a child:
        if( $(this).parents('.sidebar-group').length && $ele.parents('.section-toggle-hidden').length )
        {
            // Expand the parent:
            $ele.parents('.section-toggle-hidden').removeClass('section-toggle-hidden');
        }
        
        // Calculate the scroll position:
        var yPos = Math.max(0, ($ele.offset().top - offset + 10));
            
        window.location.hash = $(this).attr('href').replace('#', '');
            
        $(window).scrollTop(yPos).trigger('scroll');
    });
});