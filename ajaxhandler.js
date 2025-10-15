define(['jquery', 'core/notification'], function($, notification) {
    return {
        init: function() {
            $(document).ready(function() {
                $('#mailevent_register_form').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                     formData.append('sesskey', M.cfg.sesskey);
                    
                     var subjects = [];
                    $('#mailevent_register_form input[name^="subject_"]:checked').each(function() {
                        subjects.push($(this).attr('name').replace('subject_', ''));
                    });
                    formData.set('subjects', subjects.join(',')); 
                    var draftitemid = $('#id_image').val();
                    formData.append('image', draftitemid);

                    $.ajax({
                        url: M.cfg.wwwroot + '/local/mailevent/registerajax.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            notification.addNotification({
                                message: 'Form submitted successfully!',
                                type: 'success'
                            });
                            console.log(response);
                        },
                        error: function(err) {
                            notification.addNotification({
                                message: 'Error submitting form!',
                                type: 'error'
                            });
                            console.error(err);      
                        }
                    });
                });
            });
        }
    };
});
