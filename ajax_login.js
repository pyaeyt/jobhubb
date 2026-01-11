$(document).ready(function() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'ajax_login.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to the appropriate dashboard
                    window.location.href = response.redirect;
                } else {
                    // Display error message
                    $('#login-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#login-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
}); 