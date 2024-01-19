import $ from "jquery";
import toastr from "toastr";
import select2 from "select2";
select2();

document.addEventListener("livewire:initialized", () => {
    /**
     * show offcanvas modal
     */
    Livewire.on("select-container", (data) => {
        $("#role-select").each(function () {
            var $this = $(this);
            $this.select2('destroy');
            // var formRole = data.roles;
            $this.select2({
                placeholder: "Select Value",
                dropdownAutoWidth: true,
                width: "100%",
                dropdownParent: $this.parent(),
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
                }
            }
        });
    });
});
