$('document').ready(function(){
    
    $(".entitled_do").change(function(){
        var $radio = $(this);
        var entitled_val = $radio.val();
        var teacher_id = $radio.attr("data-user-id");
        var cat_val = $radio.attr("data-cat-id");
        
       $.ajax({
            
            url: 'inc/ajax_callbacks/set_entitled.php',
            method: 'POST',
            data: {
                user_id: teacher_id,
                is_entitled: entitled_val,
                cat_id: cat_val, 
            },
            cache: false,
             success:  function(data)
             {
                
                $radio.val('1');
                
             },
        
       });
    });
    
});