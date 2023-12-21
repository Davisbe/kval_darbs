$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// For converting MySQL timestamp to usable local time
function convertUTCDateToLocalDate(date) {
    var dateLocal = new Date(date.replace(' ', 'T') + 'Z');
    var newDate = new Date(dateLocal.getTime() - dateLocal.getTimezoneOffset());
    return newDate;
}

function DateToHHMM(date) {
    datetext = date.toTimeString();
    datetext = datetext.split(' ')[0];
    datetext = datetext.split(':');
    datetext = datetext[0] + ':' + datetext[1];
    return datetext;
}

function DateToDDMMYYYY(date) {
    datetext = date.toLocaleString('en-GB', {
        hour12: false,
      });
    datetext = datetext.split(',')[0];
    return datetext;
}

function DateToDDMM(date) {
    datetext = date.toLocaleString('en-GB', {
        hour12: false,
      });
    datetext = datetext.split(',')[0];
    datetext = datetext.split('/');
    datetext = datetext[0] + '/' + datetext[1];
    return datetext;
}

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
    var button_wrapper = document.getElementById('profile-button-row');
    var button = document.getElementById('friendRequestButton');
    var div = document.getElementById('friendRequestDiv');
    div.classList.add('clicked');
    div.textContent = MESSAGE_FRIEND_REQUEST_WAIT;

    // Send AJAX request
    $.ajax({
        url: SEND_FRIEND_REQUEST_URL,
        method: 'POST',
        success: function(response) {
            button.disabled = true;
            if (response.accepted_fiend) {
                div.textContent = MESSAGE_FRIEND_REQUEST_ACCEPTED;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                    button_wrapper.classList.add('closed');
                }, 4000);
            } else if (response.request_already_sent) {
                div.textContent = MESSAGE_FRIEND_REQUEST_ALREADY_SENT;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                    button_wrapper.classList.add('closed');
                }, 4000);
            } else if (response.success) {
                div.textContent = MESSAGE_FRIEND_REQUEST_SENT;
                // Wait for 3 seconds
                setTimeout(function() {
                    // Hide the button
                    div.classList.add('closed');
                    button_wrapper.classList.add('closed');
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
        success: function(response) {
            var confirm_window = document.getElementById('confirmation-window');
            confirm_window.classList.remove('opened');
            if (response.success) {
                var friend_div = document.querySelector(`div[friend-row-name="${name}"]`);
                friend_div.remove();
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
    All for preview - same done serverside

*/

function handleProfilePicture(file) {
    
    var pfp_overlay = document.getElementById('pfp-overlay');
    var pfp_overlay_text = document.getElementById('pfp-overlay-text');
    pfp_overlay_text.style.display = 'bolck';

    pfp_overlay_text.textContent = MESSAGE_PROFILE_PICTURE_EDIT_PROCESSING;

    var img = new Image();

    // if file selected, crop, resize and load it into img
    img.onload = function() {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');

        // Calculate crop offset from top left corner
        // to achieve 1:1 aspect ratio
        var cropSize = Math.min(img.width, img.height);
        var cropX = Math.floor((img.width - cropSize) / 2);
        var cropY = Math.floor((img.height - cropSize) / 2);

        // if picture smaller than 256px, dont upsize it
        var end_picture_size = 256;
        if (cropSize < 256) {
            end_picture_size = cropSize;
        }

        // Resize the image
        canvas.width = end_picture_size;
        canvas.height = end_picture_size;
        ctx.drawImage(img, cropX, cropY, cropSize, cropSize, 0, 0, end_picture_size, end_picture_size);

        // Convert the resized image to a data URL
        var dataURL = canvas.toDataURL('image/jpeg', 0.75);

        // Display the resized image in the #profile-image-holder element
        document.getElementById('profile-image-holder').src = dataURL;
    };

    // check if file was selected
    if (file) {
        img.src = URL.createObjectURL(file);
    }

    pfp_overlay_text.style.display = 'none';
    if (pfp_overlay) {
        pfp_overlay.remove();
    }
}

/*

    GAME INDEX PAGE
    For loading avaliable games (infinite scroll /w AJAX)

*/
var game_index_result_wrapper = document.getElementById('game-record-container');


// Loading the game records from the server
const loadMoreIndexGames = () => {
    if (game_index_page) {
        $.ajax({
            url: LOAD_MORE_AVAILABLE_GAMES_URL,
            data: {page: game_index_page},
            type: 'GET',
            dataType: "json",
            success: function(response){
                
                // games is recieved as lluminate\Pagination\LengthAwarePaginator
                // instance converted to JSON (with metadata)
                if(response[0].data.length > 0){
                    response[0].data.forEach(game => {
                        
                        game.start_time = convertUTCDateToLocalDate(game.start_time);
                        game.start_time.hhmm = DateToHHMM(game.start_time);
                        game.start_time.ddmmyyyy = DateToDDMM(game.start_time);
    
                        game.end_time = convertUTCDateToLocalDate(game.end_time);
                        game.end_time.hhmm = DateToHHMM(game.end_time);
                        game.end_time.ddmmyyyy = DateToDDMM(game.end_time);
                        
                        let current_time = new Date();
                        let gameRecordElementHTML = GAME_INDEX_AVAILABLE_GAMES_HTML;
    
                        if (current_time > game.start_time) {
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_GAME_ACTIVE_CLASS$', 'game-active');
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_START_SVG$', GAME_INDEX_FASTFORWARD_ICON);
                        } else {
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_GAME_ACTIVE_CLASS$', '');
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_START_SVG$', GAME_INDEX_PLAY_ICON);
                        }
    
                        if (game.joined == 1) {
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_GAME_JOINED_CLASS$', 'game-joined');
                        } else {
                            gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_GAME_JOINED_CLASS$', '');
                        }
    
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_IMAGE$', game.picture);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_TIME_END_DATE$', game.end_time.ddmmyyyy);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_TIME_END_TIME$', game.end_time.hhmm);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_TIME_START_DATE$', game.start_time.ddmmyyyy);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_TIME_START_TIME$', game.start_time.hhmm);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_NAME$', game.name);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_PLAYER_COUNT$', game.player_count);
                        gameRecordElementHTML = gameRecordElementHTML.replace('$RECORD_GAME_LINK$', game.link);
    
                        game_index_result_wrapper.innerHTML += gameRecordElementHTML;
    
                        
                    })

                    game_index_page++;

                    if (response[0].data.length < 10) {
                        // disable infinite scroll
                        window.removeEventListener("scroll", handleGameIndexInfScroll);

                        // add ending element
                        var endingElement = document.createElement('div');
                        endingElement.classList.add('col-hor-center');
                        endingElement.classList.add('game-records-end');
                        endingElement.textContent = MESSAGE_NO_MORE_GAMES;
                        game_index_result_wrapper.append(endingElement);        
                        
                    }
                }
                
    
            },
        });
    }

}

// throttle function for infinite scroll
var gameIndexThrottleTimer = false;
const gameIndexThrottle = (callback, time) => {
    if (gameIndexThrottleTimer) return;
    gameIndexThrottleTimer = true;
    setTimeout(() => {
        callback();
        gameIndexThrottleTimer = false;
    }, time);
};

// infinite scroll - called by window event listener
const handleGameIndexInfScroll = () => {
    gameIndexThrottle(() => {
        if ((window.innerHeight + window.scrollY) >= (0.8 * game_index_result_wrapper.offsetHeight)) {
            loadMoreIndexGames();
        }
    }, 1000);
}

// infinite scroll - initial call and event listener setup
if (game_index_result_wrapper) {
    var game_index_page = 1;
    loadMoreIndexGames();

    window.addEventListener('scroll', handleGameIndexInfScroll);
}


/*

    SHOW GAME PAGE
    Largely for converting timestamps to readable local time

*/

var game_show_start_time = document.getElementById('game-start-time');
var game_show_end_time = document.getElementById('game-end-time');

if (game_show_start_time) {
    let start_time_temp = convertUTCDateToLocalDate(GAME_SHOW_START_TIME);
    let start_time = DateToDDMMYYYY(start_time_temp);
    start_time += ' '+DateToHHMM(start_time_temp);

    game_show_start_time.textContent = start_time;
}

if (game_show_end_time) {
    let end_time_temp = convertUTCDateToLocalDate(GAME_SHOW_END_TIME);
    let end_time = DateToDDMMYYYY(end_time_temp);
    end_time += ' '+DateToHHMM(end_time_temp);

    game_show_end_time.textContent = end_time;
}

//test
// function getLocation() {
//     if (navigator.geolocation) {
//         navigator.geolocation.getCurrentPosition(sendLocation);
//     } else {
//         alert("Geolocation is not supported by this browser.");
//     }
// }
  
// function sendLocation(position) {
//     var latitude = position.coords.latitude;
//     var longitude = position.coords.longitude;
//     console.log(latitude+', '+longitude);

//     $.ajax({
//         url: "https://mauclocal.lv/games/test",
//         type: "POST",
//         data: {
//         latitude: latitude,
//         longitude: longitude,
//         },
//         success: function (response) {
//             console.log(response);
//         },
//         error: function (response) {
//             alert("Error: " + response.responseText);
//         },
//     });
// }