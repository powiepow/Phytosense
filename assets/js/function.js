
//sign up 
function signup(){
    $('#signupForm').on('submit', function(event) {
        event.preventDefault(); 

        $.ajax({
            url: '/crud', 
            type: 'POST',
            data: $('#signupForm').serialize()+'&signup=1', 
            success: function(response) {
                $('#responseMessage').html(response); 
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html('An error occurred ' + error);
            }
        });
    });
}

//Sign in
function signin(){
    $('#signinForm').on('submit', function(event) {
        event.preventDefault(); 
        const nsk = "d21b4135ca1e7ecd64623ad6d4501832d35e4f4f2d820f6fd7fd351324899f47";
        
        var username = $('#username').val();
        var password = $('#password').val();

        // Encrypt the form data
        var encryptedUsername = encryptData(username, nsk);
        var encryptedPassword = encryptData(password, nsk);

        // Create data object with encrypted values
        var data = {
            username: encryptedUsername,
            password: encryptedPassword,
            signin: 1
        };
        $.ajax({
            url: '/crud', 
            type: 'POST',
            data: data, 
            success: function(response) {
                $('#responseMessage').html(response); 
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html('An error occurred: ' + error);
            }
        });
    });
}
function encryptData(plaintext, secretKey) {
    // Convert the secret key to a CryptoJS format
    var key = CryptoJS.enc.Hex.parse(secretKey);
    var iv = CryptoJS.lib.WordArray.random(16);

    var encrypted = CryptoJS.AES.encrypt(plaintext, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });


    var encryptedData = iv.toString(CryptoJS.enc.Base64) + ":" + encrypted.ciphertext.toString(CryptoJS.enc.Base64);
    return encryptedData;
}

let lastClickTime = 0; 
const clickCooldown = 2000; 
//Create post
function post_query() {
    $('#post-form').on('submit', function(event) {
        event.preventDefault();
        const descArea = document.getElementById('desc-area').value.trim(); 
        if (descArea === "") {
            swal("Please add a description", {
                title: "Note",
                icon: "info",
            });
            return false;
        } else {
            var formElement = $('#post-form')[0];
            var formData = new FormData(formElement);
            formData.append('add_post', '1'); 

            $.ajax({
                url: 'crud',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var res = response.trim();
                    if(res == "disabled"){
                        swal("Posted successfully", {
                            title: "Unsuccessful",
                            text: "User's posting is disabled.",
                            icon: "error",
                        })
                    }else{
                        swal("Posted successfully", {
                            title: "Success",
                            icon: "success",
                        }).then(function() {
                            $('#responseMessage').html(response);
                        });
                    }
                    
                },
                error: function(xhr, status, error) {
                    swal("Something occurred. Please try again later", {
                        title: "Failed",
                        icon: "error",
                    });
                }
            });
        }
    });
}

/*function post_query(){
    $('#post-form').on('submit', function(event){
        event.preventDefault();
        const descArea = document.getElementById('desc-area').value.trim(); // Check the value of the textarea
        if(descArea === ""){
            swal("Please add a description", {
                title: "Note",
                icon: "info",
            });
            return false;
        }else{
            var formElement = $('#post-form')[0];
            var formData = new FormData(formElement);
            formData.append('add_post', '1');
            $.ajax({
                url: 'crud',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    swal("Posted successfully", {
                        title: "Success",
                        icon: "success",
                    }).then(function(){
                        $('#responseMessage').html(response); 
                    });
                },
                error: function(xhr, status, error){
                    swal("Something occured. Please try again later", {
                        title: "Failed",
                        icon: "error",
                    });
                }
            });
        }
    });
}*/
//===========edit post


function edit_post(){
    $('#post-form').on('submit', function(event){
        event.preventDefault();
        const descArea = document.getElementById('desc-area').value.trim(); // Check the value of the textarea
        if(descArea === ""){
            swal("Please add a description", {
                title: "Note",
                icon: "info",
            });
            return false;
        }else{
            swal({
                title: "Save Changes",
                icon: "warning",
                dangerMode: true,
                buttons: true
            }).then((edit_post)=>{
                if(edit_post){
                    const url = new URL(window.location.href);
                    const post_id = url.searchParams.get('pe');
                    var formElement = $('#post-form')[0];
                    var formData = new FormData(formElement);
                    formData.append('edit_post', '1');
                    formData.append('postId', post_id);
                    $.ajax({
                        url: '/crud',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response){
                            if(response == "image file type error"){
                                swal("Only accepts images", {
                                    title: "Error",
                                    icon: "error",
                                }).then(function(){
                                    console.log(response);
                                });
                            }else if(response == "file exceeds maximum size"){
                                swal("File exceeds 5mb", {
                                    title: "Error",
                                    icon: "error",
                                }).then(function(){
                                    console.log(response);
                                });
                            }else{
                                swal("Updated Successfully", {
                                    title: "Success",
                                    icon: "success",
                                }).then(function(){
                                    console.log(response);
                                });
                            }
                        },
                        error: function(xhr, status, error){
                            swal("Something occured. Please try again later", {
                                title: "Failed",
                                icon: "error",
                            });
                        }
                    });
                }
            });
            
        }
    });
}

//like post

function like_dislike(action, postId) {
    const currentTime = Date.now(); // Get the current timestamp

    if (currentTime - lastClickTime < clickCooldown) {
        console.log('Please wait before clicking again.');
        return; 
    }

    lastClickTime = currentTime; 

    $.ajax({
        url: '/crud', 
        type: 'POST',
        dataType: 'json',
        data: { act_type: action, likePost: postId },
        
        success: function(response) {
            console.log('Response:', response);
            $('#dislikeCount_' + postId).html(response.dislikeCount + ' <i class="fa-regular fa-thumbs-down"></i>');
            $('#likeCount_' + postId).html(response.likeCount + ' <i class="fa-regular fa-thumbs-up"></i>'); 
            community_post();
           
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
}

//Report Post

function report_post(pk){
    swal({
        title:"Report Post",
        text: "Post will be reported, and reviewed by our team.",
        icon: "warning",
        buttons: true,
        dangerMode:true,

    }).then((report)=>{
        if(report){
            $.ajax({
                method: "POST",
                url: "/crud",
                data:{report_post: pk},
                success: function(response){
                    if(response == 0){
                        swal("Post Reported",{
                            title: "Success",
                            icon: "success",
                        });
                        community_post();
                    }else{
                        swal("Post Already Reported",{
                            title: "Note",
                            icon: "info",
                        });
                    }
                    
                    console.log(response);
                },
                error: function(xhr, status, error){
                    swal("AJAX error: ", error, status,{
                        title: "Error",
                        icon: "error",
                    });
                    console.log("Ajax error: ", error, status );
                },
            });
        }else{
            console.log("cancelled");
        }
    });
}

//delete post
function delete_post(dp){
    swal({
        title: "Delete Post",
        icon: "warning",
        text: "Are you sure you want to delete this post?",
        dangerMode: true,
        buttons: true,
    }).then((delete_post)=>{
        if(delete_post){
            $.ajax({
                method: "POST",
                url: "/crud",
                data:{delete_post: dp},
                success: function(response){
                    if(response == "Invalid"){
                        swal("Request Denied",{
                            title: "Error",
                            icon: "error",
                        });
                        community_post();
                    }else if (response == "Deleted") {
                        swal("Deleted Successfully",{
                            title: "Deleted",
                            icon: "success",
                        }).then(()=>{
                            window.location.href="/community";
                        });
                    } 
                    console.log(response);
                },
                error: function(xhr, status, error){
                    swal("AJAX error: ", error, status,{
                        title: "Error",
                        icon: "error",
                    });
                    console.log("Ajax error: ", error, status );
                },
            });
            console.log(dp);
        }else{
            console.log("cancelled");
        }
    });
}

//Add comment

function add_comment(){
    const txtCount = document.getElementById('cmnt__textCount');
    const commentTxt = document.getElementById('comment__area');
    let max_char = 250;
    commentTxt.addEventListener('input', function(){
        if(this.value.length > max_char){
            this.value.substring(0, max_char);
        }
        txtCount.innerHTML=this.value.length;
    });

    $("#add__comment").on("submit", function(event){
        event.preventDefault();
        const commentArea = document.getElementById('comment__area').value.trim();
        if(commentArea === ""){
            swal("Please add a comment", {
                title: "Note",
                icon: "info",
            });
            return false;
        }
        const url = new URL(window.location.href);
        const post_id = url.searchParams.get('post');
        $.ajax({
            method: 'POST',
            url: '/crud',
            data: {cm_postViewId: post_id, comment:commentArea },
            success: function(response){
                commentTxt.value = "";
                console.log(response);
                comments();
            },
        });
    });
}
// delete comment

function delete_comment(dc){
    swal({
        title: "Delete Comment",
        icon: "warning",
        dangerMode: true,
        buttons: true,
    }).then((delete_comment)=>{
        if(delete_comment){
            console.log(dc);
            $.ajax({
                method:"POST",
                url: "/crud",
                data:{delete_comment: dc},
                success: function(response){
                    console.log(response);
                    if(response == "Invalid"){
                        swal({
                            title: "Request Denied",
                            icon: "error",
                            
                        });
                    }else{
                        swal({
                            title: "Comment Deleted",
                            icon: "success",
                            
                        });
                    }
                    comments();
                },
                error: function(status, error, xhr){
                    console.log("Ajax error: ", error);
                },
            });
        }else{
            console.log("cancelled");
        }
    });
}

// /profile history tab


function show_history(){
    const hisFilter = document.getElementById("history-filter");
    const hisSearch = document.getElementById("history-search");
    const pFilter = document.getElementById("post-filter");
    $(document).ready(function() {
        console.log("hello world");
        sendDisplayRequest("", 1);
        postDisplayRequest("");
    });
    
    hisFilter.addEventListener("change", function() {
        sendDisplayRequest(hisFilter.value, 2);
    });
    hisSearch.addEventListener("keyup", function() {
        sendDisplayRequest(hisSearch.value, 1);
    });
    pFilter.addEventListener("change", function(){
        postDisplayRequest(pFilter.value);
    });
}



function sendDisplayRequest(data, type) {
    $.ajax({
        method: "POST",
        url: "/crud",
        data: { keyup: type, datas: data },
        success: function(response) {
            $("#history_container").html(response);
        },
        error: function(status, xhr, error) {
            console.log("failed", status, error);
        },
    });
}

function postDisplayRequest(data){
    $.ajax({
        method: "POST",
        url: "/crud",
        data: { pdp:data},
        success: function(response) {
            $("#p_post_container").html(response);
        },
        error: function(status, xhr, error) {
            console.log("failed", status, error);
        },
    });
}

//========Update profile

function update_profile(){
    $("#update_profile").on("submit", function(event){
        event.preventDefault();
        swal({
            title: "Update",
            icon: "warning",
            text: "Are you sure you want to update your profile?",
            dangerMode: true,
            buttons: true,
        }).then((update) =>{
            
            if(update){
                var formElement = $("#update_profile")[0];
                var frm = new FormData(formElement);
                frm.append('profile_update_btn', '1');
                $.ajax({
                    method: "POST",
                    url: "/crud",
                    data:frm,
                    processData: false,
                    contentType: false,
                    success: function(response){
                        console.log(response);
                        switch(response){
                            case "Please fill out all fields.":
                                swal({
                                    icon: "info",
                                    text: "Please fill out all fields.",
                                });
                                break;
                            case "Username is already in use.":
                                swal({
                                    icon: "info",
                                    text: "Username is already in use.",
                                });
                                break;
                            case "!image":
                                swal({
                                    icon: "error",
                                    text: "File type error",
                                });
                                break;
                            case "maxSize":
                                swal({
                                    icon: "error",
                                    text: "File size exceeds 5mb.",
                                });
                                break;
                            case "success":
                                swal({
                                    icon: "success",
                                    title: "Profile Updated",
                                });
                                break;
                            default:
                                swal({
                                    icon: "error",
                                    text: response,
                                });
                                break;
                        }
                    },error: function(xhr, status, error){
                        swal({
                            icon: "error",
                            title: "Error",
                            text: status, error,
                        });
                    }
                });
            }else{
            }
        });
    });
}

