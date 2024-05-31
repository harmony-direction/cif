import * as RequestApi from '../../request-api.js';
var token = window.params.token
var userId = $('#userId').val();
var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
$(document).on('keyup', 'input[name="search_query"]', function () {
    var searchInput = $(this).val();
    var searchUrl = window.params.searchRoute

    var data = {
        'searchInput': searchInput
    }
    RequestApi.postRequest(data, searchUrl, token).then(response => {
        $('#table_container').html(response);
    }).catch(error => { })
});

$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    var searchInput = $('#search_query').val();
    var page = $(this).attr('href').split('page=')[1];
    var url = "/groups/user-management-system/setting/userinfo/search?page=" + page
    var data = {
        'searchInput': searchInput
    }

    RequestApi.postRequest(data, url, token).then(response => {
        $('#table_container').html(response);
    }).catch(error => { })
});

$(document).on('click', '#btn-add-salary', function (e) {
    e.preventDefault();
    $('#modal-add-salary').modal('show');
});

$(document).on('click', '#save-add-salary', function (e) {
    e.preventDefault();
    var storeSalaryUrl = window.params.storeSalaryRoute;
    var salary = parseInt($('#salary').val());
    var salaryAdjustDate = $('#salaray-adjustment-date').val();

    // Validate salaryAdjustDate using a regular expression for the format
    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(salaryAdjustDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }

    // Validate salary as a number
    if (isNaN(salary)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรอกจำนวนไม่ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }

    var data = {
        'userId': userId,
        'salary': salary,
        'salaryAdjustDate': salaryAdjustDate
    }
    RequestApi.postRequest(data, storeSalaryUrl, token).then(response => {
        $('#modal-add-salary').modal('hide');
        $('#salary_table_container').html(response);
    }).catch(error => { })
});

$(document).on('click', '.btn-update-salary', function (e) {
    e.preventDefault();
    var salaryRecordId = $(this).data('id')
    var getSalaryUrl = window.params.getSalaryRoute;
    var data = {
        'userId': userId,
        'salaryRecordId': salaryRecordId
    }

    RequestApi.postRequest(data, getSalaryUrl, token).then(response => {
        var recordDateFormatted = response.record_date.split('-').reverse().join('/');
        $('#salary-record-id').val(response.id);
        $('#update-salaray-adjustment-date').val(recordDateFormatted);
        $('#update-salary').val(response.salary);
        $('#modal-update-salary').modal('show');
    }).catch(error => { })

});

$(document).on('click', '#save-update-salary', function (e) {
    e.preventDefault();
    var updateSalaryUrl = window.params.updateSalaryRoute;
    var salary = parseInt($('#update-salary').val());
    var salaryAdjustDate = $('#update-salaray-adjustment-date').val();
    var salaryRecordId = $('#salary-record-id').val();

    // Validate salaryAdjustDate using a regular expression for the format
    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(salaryAdjustDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }

    // Validate salary as a number
    if (isNaN(salary)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรอกจำนวนไม่ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }

    var data = {
        'userId': userId,
        'salary': salary,
        'salaryAdjustDate': salaryAdjustDate,
        'salaryRecordId': salaryRecordId
    }
    RequestApi.postRequest(data, updateSalaryUrl, token).then(response => {
        $('#modal-update-salary').modal('hide');
        $('#salary_table_container').html(response);
    }).catch(error => { })
});

$(document).on('click', '.btn-delete-salary', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deleteSalaryUrl = window.params.deleteSalaryRoute;
            var salaryRecordId = $(this).data('id')
            var data = {
                'userId': userId,
                'salaryRecordId': salaryRecordId
            }
            RequestApi.postRequest(data, deleteSalaryUrl, token).then(response => {
                $('#salary_table_container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '.btn-leave-increment-setting', function (e) {
    e.preventDefault();

    $('#modal-leave-increment-setting').modal('show');
});

$(document).on('click', '#update-workschedule', function (e) {
    e.preventDefault();
    $('#modal-workschedule-month').modal('show');
});

$(document).on('click', '#save-update-workschedule', function (e) {
    e.preventDefault();
    var selectedMonths = [];
    $('input[type="checkbox"]').each(function () {
        if ($(this).prop('checked')) {
            var monthId = $(this).attr('id').replace('month_', ''); // Extract the month ID
            selectedMonths.push(monthId);
        }
    });

    if (selectedMonths.length === 0) {
        return; // Exit the function
    }

    var updateWorkScheduleUrl = window.params.updateWorkScheduleRoute;
    var workScheduleId = $('#workScheduleId').val();
    var data = {
        'userId': userId,
        'workScheduleId': workScheduleId,
        'selectedMonths': selectedMonths
    }
    RequestApi.postRequest(data, updateWorkScheduleUrl, token).then(response => {
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขรายการสำเร็จ '
        })
        $('#modal-workschedule-month').modal('hide');
    }).catch(error => { })
});


$(document).on('click', '#update-payday', function (e) {
    e.preventDefault();
    var selectedPaydayIds = $('#payday').val();
    var updatePaydayUrl = window.params.updatePaydayRoute;
    if (selectedPaydayIds.length === 0) {
        return; // Exit the function
    }
    var data = {
        'userId': userId,
        'selectedPaydayIds': selectedPaydayIds
    }
    RequestApi.postRequest(data, updatePaydayUrl, token).then(response => {
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขรายการสำเร็จ '
        })
    }).catch(error => { })
});


$(document).on('change', '#overtime-approver', function (e) {
    e.preventDefault();
    var getApproverUrl = window.params.getApproverRoute;
    var approverId = $(this).val();
    var data = {
        'approverId': approverId
    }
    if (!approverId) {
        return;
    }

    RequestApi.postRequest(data, getApproverUrl, token).then(response => {
        var authorizedUsersContainer = $('#overtime_authorized_container');
        authorizedUsersContainer.empty(); // Clear existing content

        response.forEach(authorizedUser => {
            var listItem = $('<li>').text(authorizedUser.name + ' ' + authorizedUser.lastname);
            authorizedUsersContainer.append(listItem);
        });
    }).catch(error => {
        console.error(error);
    });
});

$(document).on('click', '#update-overtime-approver', function (e) {
    e.preventDefault();
    var updateApproverUrl = window.params.updateApproverRoute;
    var approverId = $('#overtime-approver').val();
    var data = {
        'userId': userId,
        'approverId': approverId
    }

    RequestApi.postRequest(data, updateApproverUrl, token).then(response => {
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขรายการสำเร็จ '
        })
    }).catch(error => {
        console.error(error);
    });
});

$(document).on('change', '#leave-approver', function (e) {
    e.preventDefault();
    var getApproverUrl = window.params.getApproverRoute;
    var approverId = $(this).val();
    var data = {
        'approverId': approverId
    }
    if (!approverId) {
        return;
    }

    RequestApi.postRequest(data, getApproverUrl, token).then(response => {
        var authorizedUsersContainer = $('#leave_authorized_container');
        authorizedUsersContainer.empty(); // Clear existing content

        response.forEach(authorizedUser => {
            var listItem = $('<li>').text(authorizedUser.name + ' ' + authorizedUser.lastname);
            authorizedUsersContainer.append(listItem);
        });
    }).catch(error => {
        console.error(error);
    });
});

$(document).on('click', '#update-leave-approver', function (e) {
    e.preventDefault();
    var updateApproverUrl = window.params.updateApproverRoute;
    var approverId = $('#leave-approver').val();
    var data = {
        'userId': userId,
        'approverId': approverId
    }

    RequestApi.postRequest(data, updateApproverUrl, token).then(response => {
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขรายการสำเร็จ '
        })
    }).catch(error => {
        console.error(error);
    });
});

$(document).on('click', '#btn-add-position', function (e) {
    e.preventDefault();
    $('#modal-add-position').modal('show');
});

$(document).on('click', '#save-add-position', function (e) {
    e.preventDefault();

    var storePositionUrl = window.params.storePositionRoute;
    var position = $('#position').val();
    var positionAdjustDate = $('#position-adjustment-date').val();

    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(positionAdjustDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }

    var data = {
        'userId': userId,
        'position': position,
        'positionAdjustDate': positionAdjustDate
    }

    RequestApi.postRequest(data, storePositionUrl, token).then(response => {
        $('#modal-add-position').modal('hide');
        $('#position-histories-container').html(response);
    }).catch(error => {
        console.error(error);
    });
});

$(document).on('click', '.btn-update-position-history', function (e) {
    e.preventDefault();

    var positionHistoryId = $(this).data('id');
    var getPositionUrl = window.params.getPositionRoute;
    var data = {
        'positionHistoryId': positionHistoryId
    }
    RequestApi.postRequest(data, getPositionUrl, token).then(response => {

        $('#update-position-modal-container').html(response);
        $('#modal-update-position').modal('show');
    }).catch(error => {
        console.error(error);
    });

});

$(document).on('click', '#save-update-position', function (e) {
    e.preventDefault();

    var positionHistoryId = $('#positionHistoryId').val();
    var updatePositionUrl = window.params.updatePositionRoute;
    var position = $('#update-position').val();
    var positionAdjustDate = $('#update-position-adjustment-date').val();

    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(positionAdjustDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return; // Return early if validation fails
    }
    var data = {
        'userId': userId,
        'positionHistoryId': positionHistoryId,
        'position': position,
        'positionAdjustDate': positionAdjustDate,
    }

    RequestApi.postRequest(data, updatePositionUrl, token).then(response => {

        $('#modal-update-position').modal('hide');
        $('#position-histories-container').html(response);
    }).catch(error => {
        console.error(error);
    });

});

$(document).on('click', '.btn-delete-position-history', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deletePositionUrl = window.params.deletePositionRoute;
            var positionHistoryId = $(this).data('id');

            var data = {
                'userId': userId,
                'positionHistoryId': positionHistoryId
            }
            RequestApi.postRequest(data, deletePositionUrl, token).then(response => {
                $('#position-histories-container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '#btn-add-education', function (e) {
    e.preventDefault();
    $('#modal-add-education').modal('show');
});

$(document).on('click', '#save-add-education', function (e) {
    e.preventDefault();
    var storeEducationUrl = window.params.storeEducationRoute;
    var educationLevel = $('#education-level').val();
    var educationBranch = $('#education-branch').val();
    var graduatedYear = $('#graduated-year').val();

    if (educationLevel === '' || educationBranch === '' || graduatedYear === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }
    var data = {
        'userId': userId,
        'educationLevel': educationLevel,
        'educationBranch': educationBranch,
        'graduatedYear': graduatedYear
    }
    RequestApi.postRequest(data, storeEducationUrl, token).then(response => {
        $('#education-container').html(response);
        $('#modal-add-education').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-update-education', function (e) {
    e.preventDefault();

    var educationId = $(this).data('id');
    var getEducationUrl = window.params.getEducationRoute;
    var data = {
        'educationId': educationId
    }
    RequestApi.postRequest(data, getEducationUrl, token).then(response => {
        $('#update-education-level').val(response.level);
        $('#update-education-branch').val(response.branch);
        $('#update-graduated-year').val(response.year);
        $('#educationId').val(response.id);
        $('#modal-update-education').modal('show');
    }).catch(error => {
        console.error(error);
    });

});

$(document).on('click', '#save-update-education', function (e) {
    e.preventDefault();
    var updateEducationUrl = window.params.updateEducationRoute;
    var educationLevel = $('#update-education-level').val();
    var educationBranch = $('#update-education-branch').val();
    var graduatedYear = $('#update-graduated-year').val();
    var educationId = $('#educationId').val();

    if (educationLevel === '' || educationBranch === '' || graduatedYear === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }
    var data = {
        'userId': userId,
        'educationLevel': educationLevel,
        'educationBranch': educationBranch,
        'graduatedYear': graduatedYear,
        'educationId': educationId
    }
    RequestApi.postRequest(data, updateEducationUrl, token).then(response => {
        $('#education-container').html(response);
        $('#modal-update-education').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-delete-education', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deleteEducationUrl = window.params.deleteEducationRoute;
            var educationId = $(this).data('id');

            var data = {
                'userId': userId,
                'educationId': educationId
            }
            RequestApi.postRequest(data, deleteEducationUrl, token).then(response => {
                $('#education-container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '#btn-add-training', function (e) {
    e.preventDefault();
    $('#modal-add-training').modal('show');
});

$(document).on('click', '#save-add-training', function (e) {
    e.preventDefault();
    var storeTrainingUrl = window.params.storeTrainingRoute;
    var trainingCourse = $('#training-course').val();
    var trainingOrganizer = $('#training-organizer').val();
    var trainingYear = $('#training-year').val();

    if (trainingCourse === '' || trainingOrganizer === '' || trainingYear === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }
    var data = {
        'userId': userId,
        'trainingCourse': trainingCourse,
        'trainingOrganizer': trainingOrganizer,
        'trainingYear': trainingYear
    }
    RequestApi.postRequest(data, storeTrainingUrl, token).then(response => {
        $('#training-container').html(response);
        $('#modal-add-training').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-update-training', function (e) {
    e.preventDefault();

    var trainingId = $(this).data('id');
    var getTrainingUrl = window.params.getTrainingRoute;
    var data = {
        'trainingId': trainingId
    }
    RequestApi.postRequest(data, getTrainingUrl, token).then(response => {
        $('#update-training-course').val(response.course);
        $('#update-training-organizer').val(response.organizer);
        $('#update-training-year').val(response.year);
        $('#trainingId').val(response.id);
        $('#modal-update-training').modal('show');
    }).catch(error => {
        console.error(error);
    });

});

$(document).on('click', '#save-update-training', function (e) {
    e.preventDefault();
    var updateTrainingUrl = window.params.updateTrainingRoute;
    var trainingCourse = $('#update-training-course').val();
    var trainingOrganizer = $('#update-training-organizer').val();
    var trainingYear = $('#update-training-year').val();
    var trainingId = $('#trainingId').val();

    if (trainingCourse === '' || trainingOrganizer === '' || trainingYear === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }
    var data = {
        'userId': userId,
        'trainingCourse': trainingCourse,
        'trainingOrganizer': trainingOrganizer,
        'trainingYear': trainingYear,
        'trainingId': trainingId
    }
    RequestApi.postRequest(data, updateTrainingUrl, token).then(response => {
        $('#training-container').html(response);
        $('#modal-update-training').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-delete-training', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deleteTrainingUrl = window.params.deleteTrainingRoute;
            var trainingId = $(this).data('id');

            var data = {
                'userId': userId,
                'trainingId': trainingId
            }
            RequestApi.postRequest(data, deleteTrainingUrl, token).then(response => {
                $('#training-container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '#btn-add-punishment', function (e) {
    e.preventDefault();
    $('#modal-add-punishment').modal('show');
});

$(document).on('click', '#save-add-punishment', function (e) {
    e.preventDefault();
    var storePunishmentUrl = window.params.storePunishmentRoute;
    var puhishment = $('#punishment').val();
    var puhishmentRecordDate = $('#punishment-record-date').val();

    if (puhishment === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }

    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(puhishmentRecordDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return;
    }

    var data = {
        'userId': userId,
        'puhishment': puhishment,
        'puhishmentRecordDate': puhishmentRecordDate
    }

    RequestApi.postRequest(data, storePunishmentUrl, token).then(response => {
        $('#punishment-container').html(response);
        $('#modal-add-punishment').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-update-punishment', function (e) {
    e.preventDefault();

    var punishmentId = $(this).data('id');
    var getPunishmentUrl = window.params.getPunishmentRoute;
    var data = {
        'punishmentId': punishmentId
    }
    RequestApi.postRequest(data, getPunishmentUrl, token).then(response => {
        var recordDateFormatted = response.record_date.split('-').reverse().join('/');
        $('#update-punishment').val(response.punishment);
        $('#update-punishment-record-date').val(recordDateFormatted);
        $('#punishmentId').val(response.id);
        $('#modal-update-punishment').modal('show');
    }).catch(error => {
        console.error(error);
    });

});

$(document).on('click', '#save-update-punishment', function (e) {
    e.preventDefault();
    var updatePunishmentUrl = window.params.updatePunishmentRoute;
    var puhishment = $('#update-punishment').val();
    var puhishmentRecordDate = $('#update-punishment-record-date').val();
    var punishmentId = $('#punishmentId').val();

    if (puhishment === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });

        return false; // Prevent form submission
    }

    var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!datePattern.test(puhishmentRecordDate)) {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกวันที่ให้ถูกต้อง',
            icon: 'error',
            heightAuto: false
        });
        return;
    }

    var data = {
        'userId': userId,
        'puhishment': puhishment,
        'puhishmentRecordDate': puhishmentRecordDate,
        'punishmentId': punishmentId
    }

    RequestApi.postRequest(data, updatePunishmentUrl, token).then(response => {
        $('#punishment-container').html(response);
        $('#modal-update-punishment').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-delete-punishment', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deletePunishmentUrl = window.params.deletePunishmentRoute;
            var punishmentId = $(this).data('id');

            var data = {
                'userId': userId,
                'punishmentId': punishmentId
            }
            RequestApi.postRequest(data, deletePunishmentUrl, token).then(response => {
                $('#punishment-container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '#btn-add-user-attachment', function (e) {
    e.preventDefault();
    $('#attachment').val('');
    $('#attachment-file').text('');
    $('#modal-add-attachment').modal('show');
});


$(document).on('click', '#btn-add-attachment', function (e) {
    $('#file-input').trigger('click');
});

$(document).on('change', '#file-input', function (event) {
    var selectedFile = event.target.files[0]; // Get the selected file

    if (selectedFile) {
        $('#attachment-file').text(selectedFile.name); // Display the file name
    } else {
        $('#attachment-file').text(''); // Clear the file name if no file is selected
    }
});

$(document).on('click', '#save-add-attachment', function (e) {
    e.preventDefault();
    var storeAttachmentUrl = window.params.storeAttachmentRoute;
    var attachment = $('#attachment').val();
    var selectedFile = $('#file-input')[0].files[0];
    var link = $('#link').val();
    var selection;
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    if ($('#radFile').is(':checked')) {
        selection = 1;
        $('#file_wrapper').show();
        $('#link_wrapper').hide();
    } else if ($('#radLink').is(':checked')) {
        selection = 2;
        $('#file_wrapper').hide();
        $('#link_wrapper').show();
    }


    if (attachment === '') {
        Swal.fire({
            title: 'ข้อผิดพลาด',
            text: 'กรุณากรอกข้อมูลให้ครบถ้วน',
            icon: 'error',
            heightAuto: false
        });
        return false; // Prevent form submission
    }

    if (selection == 1) {
        if (!selectedFile) {
            Swal.fire({
                title: 'ข้อผิดพลาด',
                text: 'กรุณาเลือกไฟล์แนบ',
                icon: 'error',
                heightAuto: false
            });
            return false; // Prevent form submission
        }
    } else if (selection == 2) {
        if (link === '') {
            Swal.fire({
                title: 'ข้อผิดพลาด',
                text: 'กรุณากรอกลิงก์ไฟล์',
                icon: 'error',
                heightAuto: false
            });
            return false; // Prevent form submission
        }
    }


    var formData = new FormData();
    formData.append('file', selectedFile);
    formData.append('userId', userId);
    formData.append('name', attachment);
    formData.append('link', link);
    formData.append('type', selection);


    RequestApi.postRequestFormData(formData, storeAttachmentUrl, token).then(response => {
        $('#user-attachment-container').html(response);
        $('#modal-add-attachment').modal('hide');
    }).catch(error => { })
});

$(document).on('click', '.btn-delete-user-attachment', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'ลบรายการ',
        text: 'ต้องการลบรายการหรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        heightAuto: false
    }).then((result) => {
        if (result.isConfirmed) {
            var deleteAttachmentUrl = window.params.deleteAttachmentRoute;
            var attachmentId = $(this).data('id');

            var data = {
                'userId': userId,
                'attachmentId': attachmentId
            }

            RequestApi.postRequest(data, deleteAttachmentUrl, token).then(response => {
                $('#user-attachment-container').html(response);
            }).catch(error => { })
        }
    });

});

$(document).on('click', '.btn-update-user-diligence-allowance', function (e) {
    e.preventDefault();
    var getDiligenceAllowanceClassifyUrl = window.params.getDiligenceAllowanceClassifyRoute;
    var userDiligenceAllowanceId = $(this).data('id');

    var data = {
        'userId': userId,
        'userDiligenceAllowanceId': userDiligenceAllowanceId
    }

    RequestApi.postRequest(data, getDiligenceAllowanceClassifyUrl, token).then(response => {
        $('#user-diligence-allowance-id').val(userDiligenceAllowanceId);
        $('#update-user-diligence-allowance-modal-container').html(response);
        $('#modal-update-user-diligence-allowance').modal('show');
    }).catch(error => { })


});

$(document).on('click', '#save-update-user-diligence-allowance', function (e) {
    e.preventDefault();
    var updateDiligenceAllowanceClassifyUrl = window.params.updateDiligenceAllowanceClassifyRoute;
    var userDiligenceAllowanceId = $('#user-diligence-allowance-id').val();
    var diligenceAllowanceClassifyId = $('#diligence-allowance-classify').val();

    var data = {
        'userId': userId,
        'userDiligenceAllowanceId': userDiligenceAllowanceId,
        'diligenceAllowanceClassifyId': diligenceAllowanceClassifyId
    }

    RequestApi.postRequest(data, updateDiligenceAllowanceClassifyUrl, token).then(response => {

        $('#dilegence-allowance-container').html(response);
        $('#modal-update-user-diligence-allowance').modal('hide');

    }).catch(error => { })


});

$(document).on('click', '#radFile, #radLink', function () {
    var selection = 0;

    if ($('#radFile').is(':checked')) {
        selection = 1;
        $('#file_wrapper').show();
        $('#link_wrapper').hide();
    } else if ($('#radLink').is(':checked')) {
        selection = 2;
        $('#file_wrapper').hide();
        $('#link_wrapper').show();
    }

    // console.log('Selection:', selection);
});

$(document).on('click', '.btn-update-leave', function (e) {
    e.preventDefault();
    $('#user-leave-id').val($(this).data('id'));
    $('#update-user-leave').val($(this).data('count'));
    $('#modal-update-user-leave').modal('show');
});

$(document).on('click', '#save-update-user-leave', function (e) {
    e.preventDefault();
    var updateUserLeaveUrl = window.params.updateUserLeaveRoute;
    var userLeaveId = $('#user-leave-id').val();
    var userId = $('#userId').val();
    var leave = $('#update-user-leave').val();
    if (leave == '') {
        return
    }
    var data = {
        'userId': userId,
        'userLeaveId': userLeaveId,
        'leave': leave
    }

    RequestApi.postRequest(data, updateUserLeaveUrl, token).then(response => {

        $('#user-leave-container').html(response);
        $('#modal-update-user-leave').modal('hide');

    }).catch(error => { })

});


// ให้เรียกฟังก์ชันนี้เมื่อคลิกปุ่ม "บันทึก"
// document.getElementById('save-update-leave-increment').addEventListener('click', function () {
$(document).on('click', '#save-update-leave-increment', function (e) {
    var updateLeaveIncrementUrl = window.params.updateLeaveIncrementRoute;
    var userId = $('#userId').val();
    var jsonData = [];
    var tableRows = document.querySelectorAll('#module_modal_table tbody tr');

    tableRows.forEach(function (row) {
        var leaveType = {
            id: row.querySelector('td:nth-child(1)').getAttribute('data-id'),
            name: row.querySelector('td:nth-child(1)').textContent.trim(),
            type: row.querySelector('select').value,
            months: [],
            quantity: row.querySelector('input[type="text"]').value
        };

        // ดึงข้อมูลจากเดือนและ checkbox
        var monthCells = row.querySelectorAll('td:nth-child(n+3):not(:last-child)');
        monthCells.forEach(function (cell, index) {
            var monthId = cell.querySelector('input').getAttribute('data-month');
            var isChecked = cell.querySelector('input').checked ? 1 : 0; // เปลี่ยน true เป็น 1 และ false เป็น 0

            leaveType.months.push({
                monthId: monthId,
                isChecked: isChecked
            });
        });
        jsonData.push(leaveType);
    });

     var data = {
        'userId': userId,
        'jsonData': jsonData
    }

    RequestApi.postRequest(data, updateLeaveIncrementUrl, token).then(response => {
        Swal.fire({
            icon: 'info',
            title: 'สำเร็จ!',
            text: 'คลิก OK เพื่อรีโหลดการตั้งค่าเพิ่มวันลา',
            heightAuto: false
        }
        ).then(function() {
            window.location.reload();
        });

    }).catch(error => { })
});






