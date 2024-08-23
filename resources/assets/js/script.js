import toastr from "toastr";
import Quill from "quill";
import flatpickr from "flatpickr";

document.addEventListener("livewire:initialized", () => {
    /**
     * show offcanvas modal
     */
    Livewire.on("select-container", (data) => {
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                dropdownAutoWidth: true,
                dropdownParent: $this.parent(),
                width: '100%',
                containerCssClass: 'select-md'
            });
        });
    });

    Livewire.on("open-offcanvas", (data) => {
        $("#modal-offcanvas").show();
        $("#modal-offcanvas").addClass("show");
    });

    /**
     * hide offcanvas modal
     */
    Livewire.on("close-offcanvas", (data) => {
        $("#modal-offcanvas").hide();
        $("#modal-offcanvas").removeClass("show");
    });

    Livewire.on("open-main-modal", (data) => {
        $("#main-modal").show();
        $("body").addClass("modal-open");
        $("body").append('<div class="modal-backdrop fade show"></div>');
        $("#main-modal").addClass("show");
    });

    /**
     * hide offcanvas modal
     */
    Livewire.on("close-main-modal", (data) => {
        $("#main-modal").hide();
        $("body").removeClass("modal-open");
        $(".modal-backdrop").remove();
        $("#main-modal").removeClass("show");
    });

    /**
     * generic toastr alert when dispacth event
     * from livewire component
     */
    Livewire.on("alert", (data) => {
        toastr.options = {
            closeButton: true,
            preventDuplicates: true,
            progressBar: true,
        };
        toastr[data[0].type](data[0].message);
    });

    /**
     * generic swal sweet alert when dispacth event
     * from livewire component
     */
    Livewire.on("swal-alert", function ([data]) {
        Swal.fire({
            title: data?.title ?? "Are you sure?",
            text: data?.description,
            icon: data?.iconType ?? "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                if (data?.type === "delete") {
                    Livewire.dispatch("delete", { id: data?.id });
                } else {
                    Livewire.dispatch(data?.type, { id: data?.id });
                }
            }
        });
    });

    Livewire.on('feather-icons', function (data) {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });

    Livewire.on('quill-editor', function (data) {
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

        const quill = new Quill('.editor', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow'
        });
    });

    Livewire.on('editor-value-set', function (data) {
        // Populate hidden form field with Quill editor content
        let desc = document.querySelector('input[name=description]');
        desc.value = quill.root.innerHTML;
    });

    Livewire.on('initialize-markup-editor', function (data) {
        var converter = new Showdown.Converter();
        var mte = new MTE(document.getElementsByTagName('textarea')[0]);

        // Store converter in a data attribute for later access
        // let textarea = data['textarea'];
        $('textarea[name="'+data['textarea']+'"').data('converter', converter);

        if (data['heading'] == false) {
            // Hide the editor toolbar if heading is false
        $('.editor-toolbar').css('display', 'none');
        } else {
            // Add class to heading icon
            $('.fa-header').addClass('fa-heading');
        }
    });

    Livewire.on('markup-editor-change', function (data) {
        // Retrieve converter from the textarea's data attribute
        var converter = $('textarea[name="'+data['textarea']+'"').data('converter');
        $('.markup-preview').html(converter.makeHtml(data['value']));
    });

    // dispatch flatpickr
    Livewire.on('flatpickr', function (data) {
        flatpickr('.flatpickr-basic');
        flatpickr('.flatpickr-range',{
             mode: "range",
             maxDate: 'today',
        });
    });

    // Digital signature pad
    Livewire.on('digital-signature-pad', function (data) {
        window.requestAnimFrame = (function(callback) {
            return window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimaitonFrame ||
                function(callback) {
                    window.setTimeout(callback, 1000 / 60);
                };
        })();

        var canvas = document.getElementById(data['canvas_id']);
        var ctx = canvas.getContext("2d");
        ctx.strokeStyle = data['ctx_strokes_style'];
        ctx.lineWidth = data['ctx_line_width'];

        var drawing = false;
        var mousePos = {x: 0, y: 0};
        var lastPos = mousePos;

        canvas.addEventListener("mousedown", function(e) {
            drawing = true;
            lastPos = getMousePos(canvas, e);
        }, false);

        canvas.addEventListener("mouseup", function(e) {
            drawing = false;
        }, false);

        canvas.addEventListener("mousemove", function(e) {
            mousePos = getMousePos(canvas, e);
        }, false);

        // Add touch event support for mobile
        canvas.addEventListener("touchstart", function(e) {

        }, false);

        canvas.addEventListener("touchmove", function(e) {
            var touch = e.touches[0];
            var me = new MouseEvent("mousemove", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(me);
        }, false);

        canvas.addEventListener("touchstart", function(e) {
            mousePos = getTouchPos(canvas, e);
            var touch = e.touches[0];
            var me = new MouseEvent("mousedown", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(me);
        }, false);

        canvas.addEventListener("touchend", function(e) {
            var me = new MouseEvent("mouseup", {});
            canvas.dispatchEvent(me);
        }, false);

        function getMousePos(canvasDom, mouseEvent) {
            var rect = canvasDom.getBoundingClientRect();
            return {
                x: mouseEvent.clientX - rect.left,
                y: mouseEvent.clientY - rect.top
            }
        }

        function getTouchPos(canvasDom, touchEvent) {
            var rect = canvasDom.getBoundingClientRect();
            return {
                x: touchEvent.touches[0].clientX - rect.left,
                y: touchEvent.touches[0].clientY - rect.top
            }
        }

        function renderCanvas() {
            if (drawing) {
                ctx.moveTo(lastPos.x, lastPos.y);
                ctx.lineTo(mousePos.x, mousePos.y);
                ctx.stroke();
                lastPos = mousePos;
            }
        }

        // Prevent scrolling when touching the canvas
        document.body.addEventListener("touchstart", function(e) {
            if (e.target == canvas) {
                e.preventDefault();
            }
        }, false);

        document.body.addEventListener("touchend", function(e) {
            if (e.target == canvas) {
                e.preventDefault();
            }
        }, false);

        document.body.addEventListener("touchmove", function(e) {
            if (e.target == canvas) {
                e.preventDefault();
            }
        }, false);

        (function drawLoop() {
            requestAnimFrame(drawLoop);
            renderCanvas();
        })();

        function clearCanvas() {
            canvas.width = canvas.width;
        }

        // Set up the UI
        var sigText = document.getElementById("sig-dataUrl");
        var sigImage = document.getElementById("sig-image");
        var clearButton = document.getElementById(data['clear_button_id']);
        var submitButton = document.getElementById(data['sign_submit_button_id']);

        clearButton.addEventListener("click", function(event) {
            event.preventDefault();
            clearCanvas();
        }, false);

        submitButton.addEventListener("click", function(event) {
            event.preventDefault()
            var dataUrl = canvas.toDataURL();

            canvas.toBlob(function(blob) {

            var fd = new FormData($(data['form_id'])[0]);
            fd.append('digital_signature', blob);

            var $form = $('.report-form');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: data['url'],
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $('input[name="' + data['digital_signature_pad'] + '"]').val(response.data.signature);
                        // @this.set('form.digital_signature_pad', response.data.signature);
                    $('.user-sign').attr('src', data['storage_path'] + '/' + response.data.signature);
                    clearCanvas();
                    Livewire.dispatch('close-main-modal');

                },
                error: function(response) {

                }
            });
            }, "image/png");

        }, false);
    });
});
