function community_post(){
    $.ajax({
        method: 'POST',
        url: '/crud',
        data: {show_feed: 'show feed'},
        success: function(response){
            $("#feed_container").html(response);
            additionals();
        },
    });
}

function additionals(){
    const url = new URL(window.location.href);
    const post_id = url.searchParams.get('post');
    
    $.ajax({
        method: 'POST',
        url:'/crud',
        data:{additionals_post_viewId: post_id},
        success: function(response){
            var jsonResponse = JSON.parse(response);
            $("#up__vote").html(jsonResponse.like_result);
            $("#comment_count").html(jsonResponse.comment_count);
        },
    });
}

function comments(){
    const url = new URL(window.location.href);
    const post_id = url.searchParams.get('post');


    $.ajax({
        method: 'POST',
        url: '/crud',
        data: {pv_comment: post_id},
        success: function(response){
            $('#comments__container').html(response);
            additionals();
        },
    });
}