import $ from 'jquery'
import toastr from 'toastr';



document.addEventListener('livewire:initialized', () => {
    /**
    * show offcanvas modal
    */
    Livewire.on('open-offcanvas', (data) => {
        $('#modal-offcanvas').show();
        $('#modal-offcanvas').addClass('show');
    });

    /**
    * hide offcanvas modal
    */
    Livewire.on('close-offcanvas', (data) => {
        $('#modal-offcanvas').hide();
        $('#modal-offcanvas').removeClass('show');
    });

    /**
    * generic toastr alert when dispacth event
    * from livewire component
    */
    Livewire.on('alert', (data) => {
        toastr.options = {
            "closeButton": true,
            "preventDuplicates": true,
            "progressBar": true
        }
        toastr[data[0].type](data[0].message);
    });

    /**
    * generic swal sweet alert when dispacth event
    * from livewire component
    */
    Livewire.on('swal-alert', function ([data]) {
        Swal.fire({
            title: data?.title ?? 'Are you sure?',
            text: data?.description,
            icon: data?.iconType ?? 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                if (data?.type === 'delete') {
                    Livewire.dispatch('delete', { id: data?.id });
                }
            }
        });
    });
});

