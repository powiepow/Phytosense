
function ad_signin(){
    $('#signinForm').on('submit', function(event) {
        event.preventDefault(); 
        const nsk = "d21b4135ca1e7ecd64623ad6d4501832d35e4f4f2d820f6fd7fd351324899f47";
        
        var username = $('#username').val();
        var password = $('#password').val();

   
        var encryptedUsername = encryptData(username, nsk);
        var encryptedPassword = encryptData(password, nsk);

        var data = {
            username: encryptedUsername,
            password: encryptedPassword,
            ad_signin: 1
        };
        $.ajax({
            url: '/a.crud', 
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

function ad_b_signin(){
    $('#signinForm').on('submit', function(event) {
        event.preventDefault(); 
        const nsk = "d21b4135ca1e7ecd64623ad6d4501832d35e4f4f2d820f6fd7fd351324899f47";
        
        var username = $('#username').val();
        var password = $('#password').val();

        var encryptedUsername = encryptData(username, nsk);
        var encryptedPassword = encryptData(password, nsk);

        var data = {
            username: encryptedUsername,
            password: encryptedPassword,
            ad_b_signin: 1
        };
        $.ajax({
            url: '/a.crud', 
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






function showUsers(){
    
    $("#search_btn").on('click', function() {
        var data = document.getElementById("search_user").value;
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {u_search: data},
            success: function(response){
                $("#user_card").html(response);
            },
            error: function(status,xhmr,error){
                console.log("failed", status, error);
            },
        });
    });
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {u_search: ""},
            success: function(response){
                $("#user_card").html(response);
            },
            error: function(status,xhmr,error){
                console.log("failed", status, error);
            },
        });
    })
}
function sendDisplayRequest(data, type) {
    $.ajax({
        method: "POST",
        url: "/a.crud",
        data: { keyup: type, datas: data },
        success: function(response) {
            $("#history_container").html(response);
        },
        error: function(status, xhr, error) {
            console.log("failed", status, error);
        },
    });
}


function m_user(type, event){
    event.preventDefault();

    switch(type){
        case "update":
            var i = "info";
            var tle = "Update";
            break;
        case "delete":
            var i = "warning";
            var tle = "Delete";
            break;
        case "flag":
            var i = "warning";
            var tle = "Flag";
            break;
        default:
            swal({
                icon: "error",
                title: "Invalid",
            });
            return;
    }
    swal({
        icon: i,
        title: tle,
        dangerMode: true,
        buttons: true,
        text: "Are you sure you want to " + tle,

    }).then((manage)=>{
        if(manage){
            var form_array = $("#manage__user__form")[0];
            var u_form = new FormData(form_array);
            u_form.append("u_manage_type", type);
            $.ajax({
                method: "POST",
                url: "/a.crud",
                data: u_form,
                processData: false,
                contentType:false,
                success: function(response){
                    var manageUsers = response.trim();
                    switch(manageUsers){
                        case "Deleted Successfully":
                            swal({
                                icon:"success",
                                title: manageUsers,
                            });
                            break;
                        case "Successfully Updated":
                            swal({
                                icon:"success",
                                title: manageUsers,
                            });
                            break;
                        case "Username is already in use.":
                            swal({
                                icon:"error",
                                title: manageUsers,
                            });
                            break;
                        case "Email is already in use.":
                            swal({
                                icon:"error",
                                title: manageUsers,
                            });
                            break;
                        case "Flag Status Updated":
                            swal({
                                icon:"success",
                                title: manageUsers,
                            });
                            break;
                        default:
                            swal({
                                icon:"info",
                                title: "Default",
                                text: manageUsers,
                            });
                            break;
                    }
                }
            });
        }
    });
}





function show_user_post(userId){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {manage_user_post: "", u_id: userId},
            success: function(response){
                $("#up_card_parent").html(response);
            },
        });
    });
    $("#user__post_filter").on('change', function(){
        var data = $(this).val();
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {manage_user_post: data, u_id: userId},
            success: function(response){
                $("#up_card_parent").html(response);
            },
        });
    });
}


function delete_post(postId){
    swal({
        icon: "warning",
        title: "Delete Post",
        text: "Are you sure you want to delete this post",
        dangerMode: true,
        buttons: true,
    }).then((deletes)=>{
        if(deletes){
            $.ajax({
                method: "POST",
                url: "/a.crud",
                data: {delete_posts: postId},
                success: function(response){
                    var deletePosts = response.trim();
                    if(deletePosts == "Success"){
                        swal({
                            icon: "success",
                            title: "Successfully Deleted",
                        }).then((confirms)=>{
                            location.reload();
                        });
                        
                    }else{
                        swal({
                            icon: "error",
                            title: "Failed",
                        });
                    }
                    
                },
            });
        }
    });
}



function show_user_histories(userId){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_histories: "", u_id: userId},
            success: function(response){
                $("#uh_card_parent").html(response);
            },
        });
    });
    $("#user__history_filter").on('change', function(){
        var data = $(this).val();
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_histories: data, u_id: userId},
            success: function(response){
                $("#uh_card_parent").html(response);
            },
        });
    });
}


function delete_history(historyId){
    swal({
        icon: "warning",
        title: "Delete History",
        text: "Are you sure you want to delete this history",
        dangerMode: true,
        buttons: true,
    }).then((deletes)=>{
        if(deletes){
            $.ajax({
                method: "POST",
                url: "/a.crud",
                data: {delete_histories: historyId},
                success: function(response){
                    var historyResponse = response.trim();
                    if(historyResponse === "History Deleted"){
                        swal({
                            icon: "success",
                            title: "Successfully Deleted",
                        }).then((confirms)=>{
                            location.reload();
                        });
                        console.log(response);
                        
                    }else{
                        swal({
                            icon: "error",
                            title: "Failure",
                        });
                        console.log(response);
                    }
                    
                },
            });
        }
    });
}



function show_user_flagged(userId){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_flagged: "", u_id: userId},
            success: function(response){
                $("#uf_card_parent").html(response);
            },
        });
    });
    $("#user__flagged_filter").on('change', function(){
        var data = $(this).val();
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_flagged: data, u_id: userId},
            success: function(response){
                $("#uf_card_parent").html(response);
            },
        });
    });
}
function delete_flagged(reportId){
    swal({
        icon: "warning",
        title: "Delete Report",
        text: "Are you sure you want to delete this report",
        dangerMode: true,
        buttons: true,
    }).then((deletes)=>{
        if(deletes){
            $.ajax({
                method: "POST",
                url: "/a.crud",
                data: {delete_report: reportId},
                success: function(response){
                    var flaggedResponse = response.trim();
                    if(flaggedResponse == "Success"){
                        swal({
                            icon: "success",
                            title: "Successfully Deleted",
                        }).then((confirms)=>{
                            location.reload();
                        });
                        
                    }else{
                        swal({
                            icon: "error",
                            title: "Failed",
                        });
                    }
                    
                },
            });
        }
    });
}



function displayUserPosts(){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_posting: ""},
            success: function(response){
                $("#post_parent_container").html(response);
            },
        });
    });
    $("#post__filter").on('change', function(){
        var data = $(this).val();
        $.ajax({
            method: "POST",
            url: "/a.crud",
            data: {show_user_posting: data},
            success: function(response){
                $("#post_parent_container").html(response);
            },
        });
    })
}
displayUserPosts();

function deleteUserPost(postId){
    var pid = postId;
    swal({
        icon: "warning",
        title: "Delete Post",
        text: "Are you sure you want to delete this post",
        buttons: true,
        dangerMode: true,
    }).then((deletes)=>{
        if(deletes){
            $.ajax({
                method: "POST",
                url: "/a.crud",
                data: {delete_user_post: pid},
                success: function(response){
                    var postDelete = response.trim();
                    swal({
                        icon: "success",
                        title: "Deleted Successfully",
                    }).then((deleted)=>{

                        if(postDelete == "Success"){
                        location.reload();
                        }else{
                            swal({
                                icon: "error",
                                title: "Try Again",
                            });
                        }
                    });
                },
            });
        }
    });
}


function viewOthers(){
    $.ajax({
        method: "POST",
        url: "/a.crud",
        data: {view_others:""},
        success: function(response){
            $("#otherContainer").html(response);
        },
    });
}
viewOthers();


function pdManage(type){
    $("#pd-form").on('submit', function(event){
        event.preventDefault();
        switch(type){
            case "Add":
                var i = "info";
                var tle = "Add";
                break;
            case "Update":
                var i = "info";
                var tle = "Update";
                break;
            case "Delete":
                var i = "warning";
                var tle = "Delete";
                break;
            default:
                swal({
                    icon: "error",
                    title: "Invalid",
                });
                return;
        }
        swal({
            icon: i,
            dangerMode: true,
            buttons:true,
            title: tle,
            text: "Are you sure you want to " + type + "?",
        }).then((manage) =>{
            if(manage){
                var formElement = $("#pd-form")[0];
                var frm = new FormData(formElement);
                frm.append("pd_manage_type", type);
                $.ajax({
                    method: "POST",
                    url: "/a.crud",
                    data: frm,
                    processData: false,
                    contentType:false,
                    success: function(response){
                        var plantDisease = response.trim();
                        switch(plantDisease){
                            case "Disease Added":
                                swal({
                                    icon: "success",
                                    title: response,
                                })
                                break;
                            case "Disease Exists":
                                swal({
                                    icon: "info",
                                    title: response,
                                })
                                break;
                            case "Deleted Successfully":
                                swal({
                                    icon: "success",
                                    title: response,
                                })
                                break;
                            case "Disease Updated":
                                swal({
                                    icon: "success",
                                    title: response,

                                })
                                break;
                            default:

                                swal({
                                    icon: "error",
                                    title: "Unknown Error Occured, Please try again",
                                })
                                break;
                        }
                    },
                });
            }
        });
    });
    
}




function analyticBarChart(){
    var ctx = document.getElementById('myBarChart').getContext('2d');

        var myBarChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: [], 
                datasets: [{
                    label: 'Predicted Times',
                    data: [], 
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,  // Y-axis starts from 0
                        title: {
                            display: true,
                            text: 'Prediction Times' // Y-axis label
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top', // Display legend at the top
                    }
                }
            }
        });

        function updateChart(crops, predictions) {
            myBarChart.data.labels = crops; 
            myBarChart.data.datasets[0].data = predictions; 
            myBarChart.update(); 
        }

    $.ajax({
        method: "POST",
        url: "/a.crud",
        data: {barcharts: ""},
        success: function(response){
            var datas = JSON.parse(response);
            var diseaseNames = datas.cropDisease;
            var diseaseCount = datas.predictCount;

            updateChart(diseaseNames, diseaseCount);
        },
    });
}



function showUserType(){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {showUserTypes: ""},
            success: function(response){
                $('#table_userTF').html(response);
            },
        });
    });
    $('#userTypeFilter').on('change', function(){
        var userTF = $(this).val();
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {showUserTypes: userTF},
            success: function(response){
                $('#table_userTF').html(response);
            },
        });
    });
}


function reportedPost(){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {reportedPosts: ""},
            success: function(response){
                $('#table_report').html(response);
            },
        });
    });
    $('#reportedFilter').on('change', function(){
        var userTF = $(this).val();
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {reportedPosts: userTF},
            success: function(response){
                $('#table_report').html(response);
            },
        });
    });
}



function searchFlaggedUsers(){

    $('#searchFlagged').on('change', function(){
        var userTF = $(this).val();
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {searchFlagged: userTF},
            success: function(response){
                $('#table_flagged').html(response);
            },
        });
    });
}

function flaggedUsers(){
    $(document).ready(function(){
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {flaggedUsers: ""},
            success: function(response){
                $('#table_flagged').html(response);
            },
        });
    });
    $('#flaggedFilter').on('change', function(){
        var userTF = $(this).val();
        $.ajax({
            method: "POST",
            url: '/a.crud',
            data: {flaggedUsers: userTF},
            success: function(response){
                $('#table_flagged').html(response);
            },
        });
    });
}



function flag_user(userId){
    swal({
        icon: "warning",
        title: "Flag User",
        text: "This will disable user to post.",
        dangerMode: true,
        button: true,
    }).then((conf)=>{
        if(conf){
            $.ajax({
                method: "POST",
                url: '/a.crud',
                data: {flaggedID: userId},
                success: function(response){
                    var res = response.trim();
                    if(res == "success"){
                        swal({
                            icon: "success",
                            title: "Successfully Updated",
                        })
                    }else if(res == "flagged"){
                        swal({
                            icon: "info",
                            title: "User is already flagged",
                        })
                    }else{
                        swal({
                            icon: "error",
                            title: "Error Occured, Please try again",
                        })
                    }
                    
                },
            });
        }
    });

}




function clearFlag(userId){
    swal({
        icon: "warning",
        title: "Unflag User",
        text: "This will enable user to post.",
        dangerMode: true,
        button: true,
    }).then((conf)=>{
        if(conf){
            $.ajax({
                method: "POST",
                url: '/a.crud',
                data: {unflagUser: userId},
                success: function(response){
                    var res = response.trim();
                    if(res == "success"){
                        swal({
                            icon: "success",
                            title: "Successfully Updated",
                        }).then((done)=>{
                            window.location.reload();
                        });
                        
                    }else if(res == "unflag"){
                        swal({
                            icon: "info",
                            title: "User is already unflagged",
                        })
                    }else{
                        swal({
                            icon: "error",
                            title: "Error Occured, Please try again",
                        })
                    }
                    
                },
            });
        }
    });
}