import toastr from "toastr";
import Quill from "quill";

document.addEventListener("livewire:initialized", () => {
    /**
     * show offcanvas modal
     */
    Livewire.on("select-container", (data) => {
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                placeholder: "Select Value",
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

    Livewire.on('editor-value-set', function (data) { alert('ds');
        // Populate hidden form field with Quill editor content
        let desc = document.querySelector('input[name=description]'); console.log(desc);
        desc.value = quill.root.innerHTML; console.log(desc.value);
    });
});
