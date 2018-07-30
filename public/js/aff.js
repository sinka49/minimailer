$(document).ready(function(){
    $('.check_all').on('click', function (e) {

        if ($(this).prop('checked') == true)
            $('.emails').prop('checked', true);
        else
            $('.emails').prop('checked', false);
    });
})

$(document).ready(function(){
    $('.smtp_check_all').on('click', function (e) {

        if ($(this).prop('checked') == true)
            $('.smtp_checkbox').prop('checked', true);
        else
            $('.smtp_checkbox').prop('checked', false);
    });
})

$(document).on('click', '.send_message', function (e) {
    $('.log_table').hide();
    $('.loader-image-div').show();


    var data = $("#affiliate_send").serialize();
    $.post(
        '/dashboard/affiliate-program/mail',
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