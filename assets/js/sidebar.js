$(function() {
    
    $('#content > section').each(function() {
        let title = $(this).find('.section-title').first().text().trim();

        console.log(title);
    });
});