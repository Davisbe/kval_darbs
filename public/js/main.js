// Function for closing/opening the sidebar navigation menu
function toggleShelf() {
  const shelf = document.getElementById("shelf-wrapper")
  shelf.classList.toggle("shelf-wrapper-closed")
  shelf.classList.toggle("shelf-wrapper-opened")
}

/*

    SEARCH USERS/FRIENDS PAGE
    For searching users

*/
$('#searchbar-users').on('submit',function(e) {
  e.preventDefault();
  var query = $('#searchUser').val().trim(); 

  if(query) {
        var html = `
                <div class="search-result row-section">
                    <div class="search-result-image">
                    </div>
                    <div class="search-result-text">
                        Meklējam..
                    </div>
                </div>
            `;
        $('#searchResults').html(html);

        $.ajax({
            url: RETURN_USERS_URL,
            type:"GET",
            data:{'query':query},
            success:function (data) {
                var html = '';
                if(data.length > 0) {
                    data.forEach(user => {
                        html += `
                            <div class="search-result row-section">
                                <div class="search-result-image">
                                    <img src="${user.profile_picture}" alt="Profile Picture">
                                </div>
                                <div class="search-result-text">
                                    <a href="${user.profile_link}">${user.name}</a>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = `
                    <div class="search-result row-section">
                        <div class="search-result-image">
                        </div>
                        <div class="search-result-text">
                            Rezultāti nav atrasti
                        </div>
                    </div>
                `;
                }

                $('#searchResults').html(html);
            }
        })
    }
});

/*

    USER PROFILE PAGE
    For sending friend requests

*/
function sendFriendRequest() {
    // Change div's color and text
    var button = document.getElementById('friendRequestButton');
    var div = document.getElementById('friendRequestDiv');
    div.classList.add('clicked');
    div.textContent = MESSAGE_FRIEND_REQUEST_WAIT;

    // Send AJAX request
    $.ajax({
        url: SEND_FRIEND_REQUEST_URL,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            button.disabled = true;
            if (response.accepted_fiend) {
                div.textContent = MESSAGE_FRIEND_REQUEST_ACCEPTED;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                }, 4000);
            } else if (response.request_already_sent) {
                div.textContent = MESSAGE_FRIEND_REQUEST_ALREADY_SENT;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                }, 4000);
            } else if (response.success) {
                div.textContent = MESSAGE_FRIEND_REQUEST_SENT;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                }, 4000);
            }
            else {
                div.classList.add('error');
                div.textContent = MESSAGE_FRIEND_REQUEST_ERROR;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                }, 4000);
            }
            
        }
    });
}

/*

    FRIENDS LIST PAGE
    For removing friends and the confirmation window

*/
function removeFriendConfirmWindow(name) {
    var confirm_window = document.getElementById('confirmation-window');
    confirm_window.classList.toggle('opened');
    confirm_window.setAttribute('friend-name', name);

    var confirm_window_text = document.getElementById('confirmation-window-text');
    confirm_window_text.textContent = MESSAGE_FRIEND_REMOVE_CONFIRMATION.replace("$", name);
}


var confirm_window = document.getElementById('confirmation-window');
var confirm_window_content = document.getElementById('confirmation-window-content');
var confirm_window_cancel_button = document.getElementById('confirmation-button-cancel');
var confirm_window_confirm_button = document.getElementById('confirmation-button-confirm');
var confirmButtonListener = function() {
    var name = confirm_window.getAttribute('friend-name');
    
    $.ajax({
        // %24, because the URL was generated with Laravel route()
        // and I used $ in the route as the 'name' variable
        url: REMOVE_FRIEND_URL.replace("%24", name),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            var confirm_window = document.getElementById('confirmation-window');
            confirm_window.classList.remove('opened');
            console.log('1');
            if (response.success) {
                var friend_div = document.querySelector(`div[friend-row-name="${name}"]`);
                friend_div.remove();
                console.log('2');
            }
        }
    });

};

var confirm_window_content_propogation = function(e) {
    e.stopPropagation();
};

var confirm_window_close = function() {
    confirm_window.classList.remove('opened');
};

if (confirm_window) {
    confirm_window.addEventListener('click', confirm_window_close);
    confirm_window_cancel_button.addEventListener('click', confirm_window_close);
    confirm_window_content.addEventListener('click', confirm_window_content_propogation);
    confirm_window_confirm_button.addEventListener('click', confirmButtonListener);
}

/*

    NOTIFICATIONS PAGE

*/

function acceptFriendRequest(name) {
    // Change div's color and text
    var friend_reqest_div = document.querySelector(`div[user_friend_request_name="${name}"]`);

    // Send AJAX request
    $.ajax({
        url: ACCEPT_FRIEND_REQUEST_URL.replace("%24", name),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                friend_reqest_div.remove();
            }
        }
    });
}

function denyFriendRequest(name) {
    // Change div's color and text
    var friend_reqest_div = document.querySelector(`div[user_friend_request_name="${name}"]`);

    // Send AJAX request
    $.ajax({
        url: DENY_FRIEND_REQUEST_URL.replace("%24", name),
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                friend_reqest_div.remove();
            }
        }
    });
}

/*

    EDIT PROFILE PAGE
    For profile picture preview

*/

function handleProfilePicture(file) {

    var pfp_overlay = document.getElementById('pfp-overlay');
    var pfp_overlay_text = document.getElementById('pfp-overlay-text');

    pfp_overlay_text.textContent = MESSAGE_PROFILE_PICTURE_EDIT_PROCESSING;

    var img = new Image();
    img.onload = function() {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');

        // Calculate crop offset from top left corner
        // to achieve 1:1 aspect ratio
        var cropSize = Math.min(img.width, img.height);
        var cropX = Math.floor((img.width - cropSize) / 2);
        var cropY = Math.floor((img.height - cropSize) / 2);

        var end_picture_size = 256;
        if (cropSize < 256) {
            end_picture_size = cropSize;
        }

        // Resize the image
        canvas.width = end_picture_size;
        canvas.height = end_picture_size;
        ctx.drawImage(img, cropX, cropY, cropSize, cropSize, 0, 0, end_picture_size, end_picture_size);

        // Convert the resized image to a data URL
        var dataURL = canvas.toDataURL('image/jpeg', 1);

        // Display the resized image in the #profile-image-holder element
        document.getElementById('profile-image-holder').src = dataURL;
    };
    img.src = URL.createObjectURL(file);

    pfp_overlay_text.remove();
    pfp_overlay.remove();
}