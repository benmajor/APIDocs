$(function() {
  
    hljs.registerLanguage('curl', window.hljsDefineCurl);
    
    // Show the child items:
    $('.argument-list-item-child-list.collapsed .argument-list-item-child-list-content').hide();
    
    $('.argument-list-item-child-list.collapsed .argument-list-item-child-list-title').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('collapsed');
        
        if( $(this).parent().hasClass('collapsed') )
        {
            $(this).siblings().slideUp('fast');
        }
        else
        {
            $(this).siblings().slideDown('fast');
        }
    });
    
    // Initialise JQScroll:
    $('#sidebar-content').jqscroll();
    
    // Add line numbers:
    $('.code-snippet-code.hilite').each(function() {
        
        // Initialise the highlight.js plugin:
        $(this).addClass( $(this).find('pre code').prop('class') );
        hljs.highlightBlock(this);
        
        // Calculate the number of lines:
        var lines = $(this).html().trim().split(/\n/g),
            lineCount = lines.length;
            
        var html = '<table class="code-table">';
        html+= '<tbody>';
        
        for( var i = 1; i <= lineCount; i++ )
        {
            html+= '<tr>';
            html+= '<td class="line-number">'+i+'</td>';
            html+= '<td><pre>'+lines[(i - 1)]+'</pre></td>';
            html+= '</tr>';
        }
        
        html+= '</tbody>';
        html+= '</table>';
        
        $(this).empty().append(html);
        var $cont = $(this);
        
        $cont.jqscroll();
        
        $(this).on('scroll', function() { console.log('yep'); });
    });
    
    // Toggle sidebar groups:
    $('.sidebar-group-title').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('sidebar-group-in');
    });
    
    // Handle the buttons:
    $('.section-toggle .section-toggle-button .btn').on('click', function(e) {
        e.preventDefault();
        $(this).parents('.section-toggle').removeClass('section-toggle-hidden');
    });
    
    // Handle mobile header:
    $('#sidebar-mobile-nav-reveal').on('touchstart', function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('#sidebar').toggleClass('mobile-in');
    });
});