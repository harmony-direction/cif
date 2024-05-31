import * as RequestApi from '../../../request-api.js';

var token = window.params.token

$(document).on('click', '#search_overtime', function (e) {
    e.preventDefault();
    var companyDepartmentId = $('#companyDepartment').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var searchUrl = window.params.searchRoute

    // if (startDate == '' || endDate == '') {
    //     return
    // }
    var data = {
        'companyDepartmentId': companyDepartmentId,
        'startDate': startDate,
        'endDate': endDate,
    }

    RequestApi.postRequest(data, searchUrl, token).then(response => {
        $('#table_container').html(response);
        // $('#modal-users').modal('show');
    }).catch(error => {
    })
});


$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();

    var companyDepartmentId = $('#companyDepartment').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    var page = $(this).attr('href').split('page=')[1];
    var url = "/groups/document-system/overtime/document/search?page=" + page

    var data = {
        'companyDepartmentId': companyDepartmentId,
        'startDate': startDate,
        'endDate': endDate,
    }

    console.log(data)

    RequestApi.postRequest(data, url, token).then(response => {
        $('#table_container').html(response);
    }).catch(error => { })
});
document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', '#bulk-delete', function (e) {
        e.preventDefault();
        // Find all checkboxes with the class "overtime-checkbox"
        // Find all checked checkboxes with the class "overtime-checkbox"
        var companyDepartmentId = $('#companyDepartment').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var bulkDeleteUrl = window.params.bulkDeleteRoute
        var selectedCheckboxes = $("input.overtime-checkbox:checked");

        // Create an array to store the values of selected checkboxes
        var selectedValues = [];

        // Iterate through the selected checkboxes and add their values to the array
        selectedCheckboxes.each(function () {

            console.log($(this).val())
            selectedValues.push($(this).val());
        });

        if (selectedValues.length == 0) {
            console.log(selectedValues.length)
            return
        }

        var data = {
            'selectedOvertime': selectedValues,
            'companyDepartmentId': companyDepartmentId,
            'startDate': startDate,
            'endDate': endDate,
        }

        RequestApi.postRequest(data, bulkDeleteUrl, token).then(response => {
            $('#table_container').html(response);
            // $('#modal-users').modal('show');
        }).catch(error => {
        })

    });
});
