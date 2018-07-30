var editor = null;

function handleFile(e,  files) {
    if (files === undefined) {
        files = e.target.files;
    }
    var f = files[0];
    var f__data = new FormData();
    $.each(files, function (key, value) {
        f__data.append(key, value);
    });

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': getToken()}
    });

    $.ajax({
        url: '/dashboard/load-csv',
        type: 'POST',
        data: f__data,
        contentType: false,
        processData: false,
        cache: false,
        dataType: 'json',
        success: function (res) {

            if (res.status == false) {
                alert(res.message);
                return;
            }
            var table_body_html = '';
            $('.email_table tbody').html(table_body_html);
            for (var i = 0; i < res.data.length; i++) {
                table_body_html += '<tr><td class="text-center"><input type="checkbox" class="email_checkbox" name="email[]" value="' + res.data[i] + '"></td>';
                table_body_html += '<td>' + res.data[i] + '</td></tr>';
            }
            $('.email_table tbody').html(table_body_html);
        }
    });
}
function getSenderData(){
    var accounts = [],
        email_addresses = [];
    $('input[name^=smtpAccounts]:checked').each(function(){
        accounts.push( $(this).val() );
    });
    $('.email_checkbox').each(function (index, ele) {
        if ($(ele).prop('checked'))
            email_addresses.push( $(ele).val() );
    });

    return {
        accounts: accounts,
        subject: $('input.subject').val(),
        body: editor.getData(),
        from_name: $('input.from_name').val(),
        from_email: $('input.from_email').val(),
        email_addresses: email_addresses,
        send_message: 'send_message'
    };
}

$(document).on('click', '.send_message', function (e) {
    $('.log_table').hide();
    $('.loader-image-div').show();

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': getToken()}
    });

    var data = getSenderData();
    $.post(
        '/dashboard/send-emails',
        data,
        function (res) {
            var log_html = '';
            if (res.status == true) {
                console.log(res);
                var num_rows = Object.keys(res).length;
                for (var counter = 0; counter < num_rows - 1; counter++) {
                    log_html += '<tr id="log_'+res[counter]['id']+'"><td>' + res[counter]['email'] + '</td>';
                    var status_html = res[counter]['status'] == true ?
                        '<span class="text-success">Sent</span>' :
                        '<span class="text-danger">Failed</span>';
                    log_html += '<td class="status">' + status_html + '</td>';
                    log_html += '<td>' + res[counter]['message'] + '</td></tr>';
                }
            }
            else if (res.status == false){
                alert(res.message);
            }
            $('.log_table tbody').html(log_html);
            $('.log_table').show();
            $('.loader-image-div').hide();
        },
        'JSON'
    );
});
$(document).on('click', '.email_check_all', function (e) {
    if ($(this).prop('checked') == true)
        $('.email_checkbox').prop('checked', true);
    else
        $('.email_checkbox').prop('checked', false);
});
$(document).on('click', '.smtp_check_all', function (e) {
    if ($(this).prop('checked') == true)
        $('.smtp_checkbox').prop('checked', true);
    else
        $('.smtp_checkbox').prop('checked', false);
});
$(document).on('click', '.clear_list', function (e) {
    $('.email_table tbody').html('');
});
$(document).on('click', '.import_csv', function (e) {
    $('.browse_file_container').toggle();
});

$(function () {
    editor = CKEDITOR.replace('editor');
    document.getElementById('csv-file').addEventListener('change', handleFile, false);
});
$(document).on('click', '.addText', function (e) {
    var text =  $("#addText").val();
    text = 	text.replace(new RegExp(" ",'g'),",").replace(new RegExp(";",'g'),",")
    var arrayRows = text.split("\n");
    var array = [];
    var emailArr = [];

    for(var i = 0; i<arrayRows.length; i++){
        array[i] = arrayRows[i].split(",");
    }

    for(var i = 0; i<array.length; i++){
        for(var j = 0; j<array.length; j++) {
            var temp = array[i][j];
            if(typeof temp !== "undefined" ){
                (isValidEmailAddress(temp)) ?
                    emailArr[emailArr.length] = temp : false;
            }
        }
    }
    var table_body_html = '';
    $('.email_table tbody').html(table_body_html);
    for (var i = 0; i < emailArr.length; i++) {
        table_body_html += '<tr><td class="text-center"><input type="checkbox" class="email_checkbox" name="email[]" value="' + emailArr[i] + '"></td>';
        table_body_html += '<td>' + emailArr[i] + '</td></tr>';
    }
    $('.email_table tbody').html(table_body_html);
});

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}


$(document).ready(function() {
    var dropZone = $('#drop'),
        dropText = $('#drop span'),
        maxFileSize = 1000000; // максимальный размер файла - 1 мб.
    if (typeof(window.FileReader) == 'undefined') {
        dropText.text('Не поддерживается браузером!');
        dropZone.addClass('error');
    }
    dropZone[0].ondragover = function() {
        dropZone.addClass('hover');
        return false;
    };

    dropZone[0].ondragleave = function() {
        dropZone.removeClass('hover');
        return false;
    };
    dropZone[0].ondrop = function(event) {
        event.preventDefault();
        dropZone.removeClass('hover');
        dropZone.addClass('drop');
        var file = event.dataTransfer.files[0];
        if (file.size > maxFileSize) {
            dropText.text('Файл слишком большой!');
            dropZone.addClass('error');
            return false;
        }
        var files = [];
        files[0] = file;
        handleFile(event,  files);
    };



});