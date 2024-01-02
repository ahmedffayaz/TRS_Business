<div class="card">
    <div class="card-header">
        <h4 class="card-title">Projects</h4>
        <div>
            <a href="#" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button" wire:click="openOffcanvas">Add Project</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive overflow-visible">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Budget</th>
                        <th>Members</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex flex-column"><a href="#" class="user_name text-truncate text-body"><span class="fw-bolder">
                                        Interapptive</span></a><small class="emp_post text-muted"><strong>Client:
                                    </strong>ABC</small>
                            </div>
                        </td>
                        <td>
                            16 Sep 2021
                        </td>
                        <td>
                            25 Oct 2025
                        </td>
                        <td>
                            <span class="badge rounded-pill badge-light-warning">In Progress</span>
                        </td>
                        <td>$23183</td>
                        <td><span class="badge rounded-pill badge-light-primary me-1">3</span></td>
                        <td><span class="badge rounded-pill badge-light-primary me-1">5000</span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                    <span wire:ignore><i data-feather="more-vertical"></i></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">
                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                        <span>Edit</span>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <x-modal-offcanvas wireIgnoreSelf="wire:ignore.self">
        <form class="modal-content pt-0" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <button type="button" class="btn-close" wire:click="closeOffcanvas">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="modalOffcanvasLabel">{{ $form->isUpdate ? 'Edit Project' : 'Add Project' }}
                </h5>
            </div>
            <div class="modal-body flex-grow-1">

            </div>
        </form>
    </x-modal-offcanvas>
</div>
