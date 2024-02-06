let quill;
// Define a function named fetchRecord that takes a URL as input
function fetchRecord(url) {
    // Get the value of an element with the id 'page' and store it in the 'page' variable
    const page = $("#page").val();
    var requestUrl = url;

    // Check if the 'page' variable is not equal to 0
    if (page != 0) requestUrl += '?page=' + page;

    // Make an AJAX request
    $.ajax({
        url : requestUrl,
        type : 'GET',
        success: function (response) {
            // Update the content of an element with the id 'data' with the data received in the response
            $('#data').html(response.data);

            // Check if the 'feather' library is available and replace SVG icons with updated ones
            if (feather) {
                feather.replace({width: 14, height: 14});
            }
        },
        error: function (response) {
            console.log(response);
        }
    });
}

function quillEditor()
{
    var toolbarOptions = [
        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [
            'bold', 'italic',
            'underline',
            'strike',
            { 'color': [] },
            { 'background': [] },
            'blockquote',
            'code-block',
            { 'header': 1 },
            { 'header': 2 },
            { 'list': 'ordered'},
            { 'list': 'bullet' },
            { 'align': [] }
        ],
    ];

    quill = new Quill('.editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow'
    });
}

// Attach a click event handler to elements with the class 'page-link' within the document
$(document).on('click', '.page-link', function(event) {
    // Prevent the default action of clicking a link
    event.preventDefault();

    // Get the URL of the clicked link
    let pageUrl = new URL($(this).attr('href'));

    // Extract the value of the 'page' parameter from the URL
    page = pageUrl.searchParams.get("page");

    // Set the value of an element with the id 'page' to the extracted page number
    $("#page").val(page);

    // Remove the query parameters from the URL, leaving only the origin and pathname
    pageUrl = pageUrl.origin + pageUrl.pathname;

    // Fetch records using the modified URL
    fetchRecord(pageUrl);
});

$('body').on('click', '[data-act=ajax-modal]', function () {
    const _self = $(this);

    const content = $("#ajax_model_content");
    const spinner = $("#ajax_model_spinner");
    // Check if the _self element has the 'data-quill' attribute
    var quillAttr = _self.attr('data-quill');

    content.hide();
    spinner.show();

    var metaData = {};
    $(this).each(function () {
        $.each(this.attributes, function () {
            if (this.specified && this.name.match("^data-post-")) {
                var dataName = this.name.replace("data-post-", "");
                metaData[dataName] = this.value;
            }
        });
    });

    $.ajax({
        url : _self.attr('data-action-url'),
        type : _self.attr('data-method'),
        data : metaData,
        success: function (response) {
            spinner.hide();

            if (response.status === 200) {
                // Update the content of the modal body with the data received in the response
                $('.modal-body').html(response.data);

                // Show the modal dialog
                $('.modal').modal('show');

                // Initialize select2 plugin for any elements with the 'select2' class within the modal body
                $('.select2').select2();


                // If data-quill attribute is present, set quillAttr to true
                if (quillAttr) {
                    quillEditor();
                }

            } else {
                var toastrData = {type: 'error', message: 'Something went wrong.'};
                showToastr(toastrData);
            }
        },
        error: function (response) {
            var toastrData = {type: 'error', message: response.responseJSON.error};
            showToastr(toastrData);
        }
    });
});

$('body').on('submit', '[data-form=ajax-form]', function (event) {
    event.preventDefault();

    const form = $(this);
    const confirm = $(form).data('confirm');

    if (confirm == 'yes') {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to submit this form?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, do it!"
        }).then((result) => {
            if (result.value) sendAjaxForm(form);
        });
    } else {
        sendAjaxForm(form);
    }
});

function sendAjaxForm(form) {
    const _self = $(form);
    const modal = _self.data('modal');
    const dt = _self.data('data-table');
    const reload = _self.data('reload');
    const redirect = _self.data('redirect');
    const formReset = _self.data('form-reset');
    const callback = _self.data('callback');
    const editFormCallback = _self.data('edit-form');
    const onPageBtn = _self.find('.on-page-disable');

    const btn = _self.find('[type="submit"]'); // Get the submit button
    let originalText = btn.text(); // Store the original button text

    // Disable the submit button and show spinner
    disableSubmitButton(btn);

    // Check if Quill instance is defined before populating description field
    if (typeof quill !== 'undefined') {
        // Populate hidden form field with Quill editor content
        let desc = document.querySelector('input[name=description]');
        desc.value = quill.root.innerHTML;
    }

    const formData = new FormData(_self[0]);

    $.ajax({
        url : _self.attr('action'),
        type : _self.attr('method'),
        data : formData,
        processData: false, // Prevent jQuery from processing data
        contentType: false, // Prevent jQuery from setting content type
        success: function (response) {
            if (response.status === 200) {
                $('.modal').modal('hide');

                if (response.url != '') fetchRecord(response.url);

                var toastrData = {type: 'success', message: response.success};
                showToastr(toastrData);

                if (formReset == true) _self.trigger('reset');

                if (reload == true) window.location.reload();

                if (redirect) window.location.href = redirect;

                if(callback) window[callback]();

                if(editFormCallback) window[editFormCallback](response.data.model.uuid, response.data.model.id);
            } else {
                var toastrData = {type: 'error', message: 'Something went wrong.'};
                showToastr(toastrData);
            }
        },
        error: function (response) {
            if (response.responseJSON.error) {
                var toastrData = {type: 'error', message: response.responseJSON.error};
                showToastr(toastrData);
            } else {
                // Display errors for dynamic fields, if any
                $.each(response.responseJSON.errors, function(fieldName, fieldErrors) {
                    displayFieldErrors(fieldName, fieldErrors);
                });
            }
        },
        complete: function () {
            // Re-enable the submit button after the request completes
            enableSubmitButton(btn, originalText);
        }
    });

    // Clear error message and remove text-danger class on form submission
    _self.on('submit', function() {
        // Clear error messages and remove text-danger class for all relevant fields
        $(this).find('.error-message').text('').removeClass('text-danger');
    });
}

// Function to display errors for dynamic fields
function displayFieldErrors(fieldName, fieldErrors) {
    // Check if the field name contains square brackets
    if (fieldName.endsWith('[]')) {
        // Remove the brackets to match the field name in the DOM
        fieldName = fieldName.slice(0, -2);
    }

    // Find the corresponding DOM element by name
    var $field = $('[name="' + fieldName + '[]"]');

    // If the field is not found with square brackets, try without them
    if ($field.length === 0) {
        $field = $('[name="' + fieldName + '"]');
    }

    // Clear previous error messages for the dynamic field
    $field.siblings('.text-danger').remove();

    // Find the existing error message span
    var $errorMessageSpan = $field.siblings('.error-message');

    // If the error message span doesn't exist, create it
    if ($errorMessageSpan.length === 0) {
        $errorMessageSpan = $('<small class="error-message"></small>');
        $field.after($errorMessageSpan);
    }

    // Append error message below the corresponding dynamic field
    $errorMessageSpan.text(fieldErrors[0]).addClass('text-danger');
}

$('body').on('submit', '[data-form-search=ajax-form]', function (event) {
    event.preventDefault();

    _self = $(this);
    formData = new FormData(_self[0]);
    $.ajax({
        url : _self.attr('action'),
        type : _self.attr('method'),
        data : formData,
        processData: false, // Prevent jQuery from processing data
        contentType: false, // Prevent jQuery from setting content type
        success: function (response) {
            // Update the content of an element with the id 'data' with the data received in the response
            $('#data').html(response.data);

            // Check if the 'feather' library is available and replace SVG icons with updated ones
            if (feather) {
                feather.replace({width: 14, height: 14});
            }
        },
        error: function (response) {
            if (response.responseJSON.error) {
                var toastrData = {type: 'error', message: response.responseJSON.error};
                showToastr(toastrData);
            } else {
                var errors = response.responseJSON.errors;
                var error;

                for (const key in errors) {
                    error = `${errors[key]}`
                }

                var toastrData = {type: 'error', message: error};
                showToastr(toastrData);
            }
        }
    });
});

// Function to add spinner and disable the button
function disableSubmitButton(button) {
    return button.attr('disabled', 'disabled').html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Processing...`);
}

// Function to remove spinner and enable the button
function enableSubmitButton(button, originalText) {
    return button.removeAttr('disabled').html(`${originalText}`);
}

$(document).on('click', '.delete', function () {
    let url = $(this).data('url');
    let reload = $(this).data('reload');
    let tableId = '#' + $(this).data('table');
    let redirect = $(this).data('redirect');
    let callback = $(this).data('callback');
    let triggered = $(this).data('triggered');

    var metaData = {};
    $(this).each(function () {
        $.each(this.attributes, function () {
            if (this.specified && this.name.match("^data-post-")) {
                var dataName = this.name.replace("data-post-", "");
                metaData[dataName] = this.value;
            }
        });
    });

    deleteConfirmation(url, tableId, reload, redirect, callback, triggered, metaData);
});

function deleteConfirmation(url, tableId, reload = false, redirect = false, callback = false, triggered=false, metaData = {}) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this record',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true
            });
            $.ajax({
                url: url,
                type: 'DELETE', // Specify the HTTP method as DELETE
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: metaData,
                success: function (response) {
                    Swal.close();
                    if (reload)
                        location.reload();
                    else if (redirect)
                        window.location.href = redirect;
                    else if (callback && typeof window[callback] === 'function')
                        window[callback]();

                    var toastrData = {type: 'success', message: response.success};
                    showToastr(toastrData);
                    if (response.url != '') fetchRecord(response.url);
                },
                error: function (response) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete the record.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// Define a function named showToastr that takes a parameter named 'data'
function showToastr(data)
{
    // Set toastr options
    toastr.options = {
        closeButton: true,          // Show a close button on the toastr
        preventDuplicates: true,    // Prevent duplicate toastr messages from appearing
        progressBar: true,          // Show a progress bar indicating the time until the toastr disappears
    };

    // Display a toastr message of the type specified in 'data.type' with the message specified in 'data.message'
    toastr[data.type](data.message);
}
