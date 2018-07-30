$(document).on('click', '.clear_hist', function (e) {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': getToken()}
    });

    $.ajax({
        url: "/dashboard/mailing-history/delete",
        cache: false,
        data: {'id': $(this).data("user")},
        type: "POST",
        success: function (data) {
            var str = '<tr>';
                str += '<td colspan="7">';
                str +=    '<p class="text-center">No mails sent</p>';
                str += '</td>';
                str +='</tr>';
            $(".mailing tbody").html(str);
        }
    });
});