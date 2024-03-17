import * as RequestApi from '../../../request-api.js';

var token = window.params.token

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).on('click', '#show_modal', function (e) {
    $('#modal-date-range').modal('show');
});

$(document).on('click', '#check-time-record', function (e) {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var monthId = $('#month_id').val();
    var workScheduleId = $('#work_schedule_id').val();
    var year = $('#year').val();
    var timeRecordCheckUrl = window.params.timeRecordCheckRoute
    if (startDate.trim() === '' || endDate.trim() === '') {
        Swal.fire(
            'ผิดพลาด!',
            'โปรดกำหนดช่วงเวลา',
            'error'
        );
        return;
    }
    if (!isEndDateAfterStartDate(startDate, endDate)) {
        Swal.fire(
            'ผิดพลาด!',
            'โปรดกำหนดช่วงเวลาให้ถูกต้อง',
            'error'
        );
        return;
    }

    var data = {
        'startDate': startDate,
        'endDate': endDate,
        'monthId': monthId,
        'workScheduleId': workScheduleId,
        'year': year,
    }

    RequestApi.postRequest(data, timeRecordCheckUrl, token).then(response => {
        $('#table_container').html(response);
        $('#modal-date-range').modal('hide');
        $('#filter-container').show();
        
    }).catch(error => {

    })
});

$(document).on('click', '#user', function (e) {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var monthId = $('#month_id').val();
    var workScheduleId = $('#work_schedule_id').val();
    var userId = $(this).data('id');
    var year = $('#year').val();
    var viewUserUrl = window.params.viewUserRoute;

    var data = {
        'startDate': startDate,
        'endDate': endDate,
        'monthId': monthId,
        'workScheduleId': workScheduleId,
        'year': year,
        'userId': userId,
    }
    
    RequestApi.postRequest(data, viewUserUrl, token).then(response => {
        $('#table_modal_container').html(response);
        $('.input-time-format').inputmask('99:99:99');
        $('#modal-user-time-record').modal('show');
    }).catch(error => {

    })

});

$(document).on('change', '#hour', function () {
    var value = $(this).val();
    var overtimeId = $(this).data('overtimeid');
    var userId = $(this).data('userid');
    console.log(value, overtimeId, userId);

    var updateHourUrl = window.params.updateHourRoute
    var dataSet = {
        'overtimeId': overtimeId,
        'userId': userId,
        'val': value
    }
    RequestApi.postRequest(dataSet, updateHourUrl, token).then(response => {
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขชั่วโมงล่วงเวลาสำเร็จ '
        })
    }).catch(error => { })
});

$(document).on('click', '.btnSaveBtn', function (e) {
    // Prevent the default click behavior (if necessary)
    e.preventDefault();
    var filter = $('.radio-group input[type="radio"]:checked').attr('id').split('_')[1];
    // var page = $(this).attr('href').split('page=')[1];

    // Get the corresponding row of the clicked button
    var row = $(this).closest('tr');

    // Get the ID of the row from the "data-id" attribute
    var workScheduleAssignmentUserId = row.data('id');

    // Find the input elements for time_in and time_out within the row
    var timeInInput = row.find('input[id^="time_in"]');
    var timeOutInput = row.find('input[id^="time_out"]');

    // Get the values of time_in and time_out
    var timeInValue = timeInInput.val();
    var timeOutValue = timeOutInput.val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var monthId = $('#month_id').val();
    var workScheduleId = $('#work_schedule_id').val();
    var year = $('#year').val();

    var updateUrl = window.params.updateRoute;
  

    var data = {
        'timeInValue': timeInValue,
        'timeOutValue': timeOutValue,
        'workScheduleAssignmentUserId': workScheduleAssignmentUserId,
        'startDate': startDate,
        'endDate': endDate,
        'monthId': monthId,
        'workScheduleId': workScheduleId,
        'year': year,
    }
    
    data.filter = filter;

    
    if (timeInValue === '' || timeOutValue === '') {
        Swal.fire(
            'ผิดพลาด!',
            'กรุณากรอกเวลาให้ครบ',
            'error'
        );
        return;
    }
    
    RequestApi.postRequest(data, updateUrl, token).then(response => {
        $('#table_container').html(response);
        $('#error_' + workScheduleAssignmentUserId).hide();
        Toast.fire({
            icon: 'success',
            title: 'แก้ไขรายการสำเร็จ เวลาเข้า ' + timeInValue + ' เวลาออก ' + timeOutValue
        })
    }).catch(error => {

    })

    // You can perform any additional actions with the retrieved values here
});


$(document).on('click', '#add_note', function (e) {
    $('#modal-add-note').modal('show');
});

// Attach an event listener for the checkbox change
$(document).on('change', '#auto_text', function (e) {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var dateRangeMessage = '';
    if (startDate != '' && endDate != '')
    {
        dateRangeMessage = ' (' + startDate + ' - ' + endDate  + ')'
    }
    if ($('#auto_text').prop('checked')) {
        // Set the message in the textarea
        $('#note').val('ตรวจแล้ว' + dateRangeMessage);
    } else {
        // If checkbox is unchecked, clear the textarea
        $('#note').val('');
    }
});

$(document).on('click', '#save_note', function (e) {
    var monthId = $('#month_id').val();
    var workScheduleId = $('#work_schedule_id').val();
    var year = $('#year').val();
    var note = $('#note').val();
    var saveNoteUrl = window.params.saveNoteRoute;

    var data = {
        'note': note,
        'monthId': monthId,
        'workScheduleId': workScheduleId,
        'year': year,
    }
    RequestApi.postRequest(data, saveNoteUrl, token).then(response => {
        $('#modal-add-note').modal('hide');
    }).catch(error => {

    })

});

function isEndDateAfterStartDate(startDate, endDate) {
    var parsedStartDate = moment(startDate, 'DD/MM/YYYY', true); // Parse start date
    var parsedEndDate = moment(endDate, 'DD/MM/YYYY', true); // Parse end date

    if (!parsedStartDate.isValid() || !parsedEndDate.isValid()) {
        // Handle the case when either start date or end date is not a valid date
        return false;
    }

    if (parsedEndDate.isBefore(parsedStartDate)) {
        // Handle the case when endDate is before startDate
        return false;
    }

    return true;
}


$(document).on('click', '.btnAttachment', function (e) {
    e.preventDefault();
    var workScheduleAssignmentId = $(this).data('id');
    var getImageUrl = window.params.getImageRoute;
    $('#workScheduleAssignmentUserId').val(workScheduleAssignmentId);
    
    var data = {
        'workScheduleAssignmentId': workScheduleAssignmentId
    }
    console.log(workScheduleAssignmentId)
    RequestApi.postRequest(data, getImageUrl, token).then(response => {
        var imageUrl = response.file;
        var baseUrl = window.location.origin; // Get the base URL of the application
        $('#modal-user-time-record').modal('hide');
        if (imageUrl) {
            var workScheduleAssignmentUserFileId = response.id;
            $('#workScheduleAssignmentUserFileId').val(workScheduleAssignmentUserFileId);
            // console.log(workScheduleAssignmentUserFileId)
            var fullImageUrl = baseUrl + '/storage/uploads/attachment/' + imageUrl;
            
            $('#modal-attachment').modal('show');
            $('#modal-attachment img').attr('src', fullImageUrl);
            $('#delete-image').attr('data-id', workScheduleAssignmentUserFileId);
            $('#delete-image').removeClass('d-none'); // Show the "ลบ" button
            $('#btnAddFile').addClass('d-none'); 
        } else {
            $('#modal-attachment').modal('show');
            $('#modal-attachment img').removeAttr('src');
        }
    }).catch(error => {

    })
});

$(document).on('click', '#btnAddFile', function (e) {
    e.preventDefault();
    $('#file-input').trigger('click');
});

$(document).on('change', '#file-input', function (event) {
    
    var fileInput = event.target;
    var uploadImageUrl = window.params.uploadImageRoute;
    var workScheduleAssignmentId = $('#workScheduleAssignmentUserId').val();

    var formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('workScheduleAssignmentId', workScheduleAssignmentId);


    RequestApi.postRequestFormData(formData, uploadImageUrl, token).then(response => {
        var workScheduleAssignmentUserFileId = response.id;
        var imageUrl = response.file;
        var baseUrl = window.location.origin; // Get the base URL of the application
        $('#workScheduleAssignmentUserFileId').val(workScheduleAssignmentUserFileId);
        if (imageUrl) {
            var fullImageUrl = baseUrl + '/storage/uploads/attachment/' + imageUrl;
            $('#modal-attachment').modal('show');
            $('#modal-attachment img').attr('src', fullImageUrl);
            $('#delete-image').attr('data-id', workScheduleAssignmentUserFileId);
            $('#delete-image').removeClass('d-none'); // Show the "ลบ" button
            $('#btnAddFile').addClass('d-none'); // Show the "ลบ" button
        } else {
            $('#modal-attachment').modal('show');
            $('#modal-attachment img').removeAttr('src');

        }
        $('#file-input').val('');
    }).catch(error => {

    })
});

$(document).on('click', '#delete-image', function (e) {
    e.preventDefault();
    var workScheduleAssignmentUserFileId = $('#workScheduleAssignmentUserFileId').val();
    var deleteImageDelete = window.params.deleteImageRoute;

    var data = {
        'workScheduleAssignmentUserFileId': workScheduleAssignmentUserFileId
    }
    RequestApi.postRequest(data, deleteImageDelete, token).then(response => {
        $('#modal-attachment img').removeAttr('src'); 
        $('#delete-image').attr('data-id', '');
        $('#delete-image').addClass('d-none');
        $('#modal-attachment').modal('hide');
        // $('#modal-user-time-record').modal('show');
        $('#btnAddFile').removeClass('d-none');
    }).catch(error => {

    })
});

 $('#modal-attachment').on('hidden.bs.modal', function () {
        // ตั้งค่า scroll กลับมาที่ตำแหน่งเดิมของ Modal แรก
     // $('#modal-user-time-record').modal('handleUpdate');
     // ตั้งค่า scroll กลับมาที่ตำแหน่งเดิมของ Modal แรก
    $('#modal-user-time-record').modal('show');
     console.log('here');

    });

// $(document).on('click', '#close-image-modal', function (e) {
//     e.preventDefault();
//     $('#modal-attachment').modal('hide'); 
//     $('#modal-user-time-record').modal('handleUpdate');

// });

$(document).on('click', '.show-leave-attachment', function (e) {
    e.preventDefault();
    var workScheduleAssignmentUserId = $(this).data('id');
    var data = {
        'workScheduleAssignmentUserId': workScheduleAssignmentUserId
    }
    var getLeaveAttachmentUrl = window.params.getLeaveAttachmentRoute;

    RequestApi.postRequest(data, getLeaveAttachmentUrl, token).then(response => {
        var imageUrl = response;
        var baseUrl = window.location.origin;
        var fullImageUrl = baseUrl + '/storage/uploads/attachment/' + imageUrl;
        $('#modal-leave-attachment').modal('show');
        $('#modal-leave-attachment img').attr('src', fullImageUrl);
    }).catch(error => {

    })
});

$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    var filter = $('.radio-group input[type="radio"]:checked').attr('id').split('_')[1];
    var page = $(this).attr('href').split('page=')[1];
    var data = {
        'startDate': $('#startDate').val(),
        'endDate': $('#endDate').val(),
        'monthId': $('#month_id').val(),
        'workScheduleId': $('#work_schedule_id').val(),
        'year': $('#year').val(),
    };
    var timeRecordCheckUrl = window.params.timeRecordCheckRoute;

    data.page = page; 
    data.filter = filter;
    
    RequestApi.postRequest(data, timeRecordCheckUrl, token)
        .then(response => {
            $('#table_container').html(response);
            // $('#modal-date-range').modal('hide');
        })
        .catch(error => {
            // Handle error if necessary
        });
});

$('.radio-group input[type="radio"]').on('change', function () {
    var filter = $(this).attr('id').split('_')[1];
    var data = {
        'startDate': $('#startDate').val(),
        'endDate': $('#endDate').val(),
        'monthId': $('#month_id').val(),
        'workScheduleId': $('#work_schedule_id').val(),
        'year': $('#year').val(),
    };
    var timeRecordCheckUrl = window.params.timeRecordCheckRoute;

    // data.page = page;
    data.filter = filter;

    RequestApi.postRequest(data, timeRecordCheckUrl, token)
        .then(response => {
            $('#table_container').html(response);
            // $('#modal-date-range').modal('hide');
        })
        .catch(error => {
            // Handle error if necessary
        });
});






