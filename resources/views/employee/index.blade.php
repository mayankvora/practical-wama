<!DOCTYPE html>
<html lang="en">

<head>
    <title>Employee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <style>
        .customer-form .form-group label.error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Employee
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary add-employee">
                            Add Employee
                        </button>
                        <button type="button" class="btn btn-danger" id="bulk-delete">
                            Bulk Delete
                        </button>
                    </div>
                </div>
            </div>

            <form id="customer-form" class="customer-form p-2">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact No</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact No." required>
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="hobby_ids">Hobbies</label>
                    @foreach ($hobbies as $hobby)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hobby_{{ $hobby->id }}"
                                name="hobby_ids[]" value="{{ $hobby->id }}">
                            <label class="form-check-label" for="hobby_{{ $hobby->id }}">{{ $hobby->name }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <label for="profile_pic">Profile Pic</label>
                    <input type="file" class="form-control-file" id="profile_pic" name="profile_pic" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary cancel-form">Cancel</button>
            </form>
            <div id='divTable' class="p-2">
                <table id="customerTable" class="display">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Hobby</th>
                            <th>Category</th>
                            <th>Profile pic</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            $('#customer-form').hide();

            $(document).on('click', '.add-employee', function() {
                $('#divTable').hide();
                $('#customer-form').show();
            });

            $(document).on('click', '.cancel-form', function() {
                $('#divTable').show();
                $('#customer-form').hide();
            });

            var table = $('#customerTable').DataTable({
                aLengthMenu: [
                    [10, 30, 50, -1],
                    [10, 30, 50, "All"],
                ],
                ordering: false,
                iDisplayLength: 10,
                language: {
                    search: "",
                    searchPlaceholder: "Search"
                },
                processing: true,
                serverSide: true,
                ajax: {
                    type: "POST",
                    url: "{{ URL::to('get-employee') }}"
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'select'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'contact_no'
                    },
                    {
                        data: 'hobby'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'profile_pic'
                    },
                    {
                        data: 'action'
                    },
                ],
            });

            $(document).on('click', '.edit-employee', function() {
                $('.hobby-checkbox-'+$(this).data('id')).removeClass('d-none');
                $('.hobby-text-'+$(this).data('id')).removeClass('d-none');
                $('.select-category-'+$(this).data('id')).removeClass('d-none');
                $('.category-text-'+$(this).data('id')).addClass('d-none');
                
                var row = $(this).closest('tr');
                var fields = row.find('.editable');
                var checkboxFields = row.find('.editable-checkbox input[type="checkbox"]');
                var selectField = row.find('.editable-select select');
                fields.each(function() {
                    var currentValue = $(this).text();
                    $(this).html('<input type="text" class="form-control" value="' + currentValue +
                        '">');
                });
                selectField.prop('disabled', false);
                $('.profile-pic-cell-' + $(this).data('id')).removeClass('d-none');
                $(this).addClass('save-employee').removeClass('edit-employee').text('Save');
            });

            $(document).on('click', '.save-employee', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var fields = row.find('.editable');
                var checkboxFields = row.find('.editable-checkbox input[type="checkbox"]');
                var selectField = row.find('.editable-select select');
                var selectedCategory = selectField.val();

                var formData = new FormData();
                formData.append('id', id);
                var valid = true;
                fields.each(function() {
                    var value = $(this).find('input').val().trim();
                    if (value === '') {
                        valid = false;
                        return false;
                    }
                    formData.append($(this).data('field'), value);
                });
                if (!selectedCategory) {
                    valid = false;
                }
                if (!valid) {
                    swal("Please fill in all required fields.", {
                        icon: "error",
                    });
                    return;
                }
                var checkboxValues = [];
                checkboxFields.each(function() {
                    if ($(this).prop('checked')) {
                        checkboxValues.push($(this).val());
                    }
                });
                console.log(checkboxValues);
                formData.append('hobby_ids', checkboxValues);
                formData.append('category_id', selectedCategory);
                var imageFile = $('#profile-pic')[0].files[0];
                if (imageFile) {
                    formData.append('profile_pic', imageFile);
                }

                $.ajax({
                    url: "{{ route('employee.update') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        swal(response.message, {
                            icon: "success",
                        });
                        $('#customerTable').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON
                            .errors.contact_no) {
                            var errorMessage = xhr.responseJSON.errors.contact_no[0];
                            swal(errorMessage, {
                                icon: "error",
                            });
                        } else {
                            swal(xhr.responseText, {
                                icon: "error",
                            });
                        }
                        $('#customerTable').DataTable().ajax.reload(null, false);
                    }
                });

                $(this).addClass('edit-employee').removeClass('save-employee').text('Edit');
                selectField.prop('disabled', true);
                selectField.data('prevValue', selectedCategory);
            });

            $('#bulk-delete').on('click', function(e) {
                e.preventDefault();
                var allids = [];

                $('input:checkbox[name=bulkdelete]:checked').each(function() {
                    allids.push($(this).val());
                });
                if (allids.length <= 0) {
                    swal("Please select employee", {
                        icon: "error",
                    });
                } else {
                    deleteEmployee('multiple', '', allids)
                }
            });

            $(document).on('click', '.delete-employee', function() {
                var employeeId = $(this).data('id');
                deleteEmployee('single', employeeId, [])
            })

            function deleteEmployee(type, employeeId, allids) {
                swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover record!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            var url = "{{ route('employee.delete') }}";
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: "DELETE",
                                url: url,
                                data: {
                                    type: type,
                                    employeeId: employeeId,
                                    allids: allids
                                },
                                dataType: 'json',
                            }).done(function(data) {
                                if (data.status == 1) {
                                    swal("Your employee has been deleted!", {
                                        icon: "success",
                                    });
                                    $('#customerTable').DataTable().ajax.reload(null, false);
                                } else {
                                    swal("Something went to wrong!", {
                                        icon: "error",
                                    });
                                }
                            }).fail(function() {});
                        }
                    });
            }


            $.validator.addMethod("lettersonly", function(value, element) {
                return this.optional(element) || /^[a-z\s]+$/i.test(value);
            });

            jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
                phone_number = phone_number.replace(/\s+/g, "");
                return this.optional(element) || phone_number.length > 9 &&
                    phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
            }, "Please specify a valid phone number");

            // Validate form using jQuery Validation
            $('#customer-form').validate({
                rules: {
                    name: {
                        required: true,
                        lettersonly: true
                    },
                    contact_no: {
                        required: true,
                        phoneUS: true
                    },
                    category_id: {
                        required: true
                    },
                    'hobby_ids[]': {
                        required: true,
                        minlength: 1
                    },
                    profile_pic: {
                        required: true,
                        extension: "jpg,jpeg,png"
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: "{{ route('employee.store') }}",
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            swal(response.message, {
                                icon: "success",
                            });
                            $('#customer-form')[0].reset();
                            $('#divTable').show();
                            $('#customer-form').hide();
                            $('#customerTable').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors && xhr
                                .responseJSON.errors.contact_no) {
                                var errorMessage = xhr.responseJSON.errors.contact_no[0];
                                swal(errorMessage, {
                                    icon: "error",
                                });
                            } else {
                                swal(xhr.responseText, {
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
