import * as RequestApi from '../../../request-api.js';

var token = window.params.token
var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).on('keyup', 'input[name="search_query"]', function () {
    var overtimeId = $('#overtimeId').val();
    var searchInput = $(this).val();
    var searchUrl = window.params.searchRoute
    var dataSet = {
        'searchInput': searchInput,
        'overtimeId': overtimeId
    }
    RequestApi.postRequest(dataSet, searchUrl, token).then(response => {
        $('#table_container').html(response);
    }).catch(error => {})
});

$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    var overtimeId = $('#overtimeId').val();
    var searchInput = $('#search_query').val();
    var page = $(this).attr('href').split('page=')[1];
    var url = "/groups/document-system/overtime/document/assignment/search?page=" + page

    var dataSet = {
        'searchInput': searchInput,
        'overtimeId': overtimeId
    }

    RequestApi.postRequest(dataSet, url, token).then(response => {
        $('#table_container').html(response);
    }).catch(error => {})
});

$(document).on('change', '#userGroup', function (e) {
    var approverId = $(this).val(); // Get the selected value
    var importUserGroupRoute = window.params.importUserGroupRoute
    var overtimeId = $('#overtimeId').val();
    if (approverId === '') {
        return; // Return or exit the function
    }
    var dataSet = {
        approverId: approverId,
        overtimeId: overtimeId
    }

    var selectedText = $(this).find('option:selected').text();
    Swal.fire({
        title: 'นำเข้าพนักงาน',
        text: 'นำเข้าพนักงานจากกลุ่ม' + selectedText,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '##6495ed',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'นำเข้า',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: importUserGroupRoute,
                type: 'POST',
                headers: {
                    "X-CSRF-TOKEN": token
                },
                data: dataSet,
                success: function (response) {
                    window.location.reload();
                },
                error: function (xhr) {

                }
            });
        }
    });

});

$(document).on('click', '#import-employee-code', function (e) {
    e.preventDefault();
    $('#modal-import-employee-code').modal('show');
});

$(document).on('click', '#btn-import-employee-code', function (e) {
    e.preventDefault();
    var textareaContent = $('#employee-code').val(); // Get the content of the textarea
    var lines = textareaContent.split('\n'); // Split content by new lines
    var overtimeId = $('#overtimeId').val();
    var importEmployeeNoUrl = window.params.importEmployeeNoRoute
    // Clear previous output

    if (textareaContent.trim() === '') {
        return; // If the content is empty or only whitespace, return
    }
    
    $('#output').empty();
    var employeeNos = [];
    for (var i = 0; i < lines.length; i++) {
        var trimmedLine = lines[i].trim(); // Remove leading and trailing spaces
        if (trimmedLine !== "") {
            employeeNos.push(trimmedLine);
        }
    }

    var data = {
        'employeeNos': employeeNos,
        'overtimeId': overtimeId
    }

    RequestApi.postRequest(data, importEmployeeNoUrl, token).then(response => {
        window.location.reload();
    }).catch(error => { })

});

$(document).on('change', '#hour', function () {
    var value = $(this).val();
    var overtimeId = $('#overtimeId').val();
    var userId = $(this).data('user');

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