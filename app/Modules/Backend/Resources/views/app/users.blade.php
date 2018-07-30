@extends('backend::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}

    <script>
        $(function () {
            var editor = CKEDITOR.replace('editor');
            $('#check_all_users').on('change', function ($e) {
                var checkStatus = $(this).prop('checked');
                $('.us').each(function () {
                    $(this).prop('checked', checkStatus);
                });

            });

            $('#action').on('change', function ($e) {
                if (!$('#email-form').hidden) {
                    $('#email-form').hide();
                }
                if (this.value == 'send') {
                    $('#email-form').show();
                }
            });

            $(document).on('click', '.send_message', function (e) {
                var email_addresses = [];
                var counter = 0;
                $('.log_table').hide();


                $('.loader-image-div').show();
                $('.us').each(function (index, ele) {
                    if ($(this).prop('checked'))
                        email_addresses.push( $(ele).val() );
                });

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': getToken()}
                });
                 $.post(
                        '/opera/send-emails',
                        {
                            subject: $('input#subject').val(),
                            body: editor.getData(),
                            email: $('input#email').val(),
                            email_addresses: email_addresses,
                            send_message: 'send_message'
                        },
                        function (res) {
                            var log_html = '';
                            if (res.status == true) {
                                console.log(res[0]);
                                var num_rows = Object.keys(res).length;
                                for (var counter = 0; counter < num_rows - 1; counter++) {
                                    log_html += '<tr><td>' + res[counter]['email'] + '</td>';
                                    var status_html = res[counter]['status'] == true ? '<span class="text-success">Sent</span>' : '<span class="text-danger">Failed</span>';
                                    log_html += '<td>' + status_html + '</td>';
                                    log_html += '<td>' + res[counter]['message'] + '</td></tr>';
                                }
                            }
                            else if (res.status == false)
                                alert(res.message);
                            $('.log_table tbody').html(log_html);
                            $('.log_table').show();
                            $('.loader-image-div').hide();
                        },
                        'JSON'
                );
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Users</h4>
            </div>
            <div class="panel-body">
                {!! BootForm::open() !!}
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>
                            {!! BootForm::checkbox('checked', false, 'all', false, ['id' => 'check_all_users']) !!}
                        </th>
                        <th class="col-md-1">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Subscription</th>
                    </tr>
                    </thead>
                    {{csrf_field()}}
                    <tbody>
                    @if( empty($users) || count($users) == 0 )
                        <tr>
                            <td colspan="7">
                                <p class="text-center">No users found</p>
                            </td>
                        </tr>
                    @else
                        @foreach($users as $u)
                            <tr>
                                <td>
                                    {!! BootForm::checkbox("users[]", false, $u->id, false, ['class' => 'us']) !!}
                                </td>
                                <td>{{$u->id}}</td>
                                <td>{{$u->name}}</td>
                                <td>{{$u->email}}</td>
                                <td>{{$u->status}}</td>
                                <td>{{$u->role}}</td>
                                <td>{{$u->sub}}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                {{ $users->links() }}
                <div class="row">
                    <div class="col-md-2">
                        {!! BootForm::select('action',false, ['action' => 'Action','send' => 'Send Email', 'block' => 'Block', 'unblock' => 'Unblock', 'remove' => 'Remove'],null,['id'=>'action']) !!}
                    </div>
                    <div class="col-md-4">
                        {!! BootForm::button('Apply',['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" id="email-form" style="display: none;">
                            {!! BootForm::text('email', 'From', 'info@theminimailer.com', [ 'class'=>'form-control from_name']) !!}
                            {!! BootForm::text('subject', 'Subject','',['class'=>'form-control subject', 'placeholder' => 'Enter Subject']) !!}
                            {!! BootForm::textarea('body', 'Body','',['class'=>'form-control body', 'rows'=>'10', 'name'=>'body', 'id'=>'editor', 'placeholder'=>'Enter Email Body']) !!}
                            {!! BootForm::button('Send Message',['type' => 'button', 'class' => 'btn btn-success btn-block send_message']) !!}

                            <div class="col-md-12 logs_container" style="padding: 0;">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4><i class="fa fa-fw fa-share"></i> Log</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover table-bordered log_table" style="display:none">
                                            <thead>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {!! BootForm::close() !!}
            </div>
        </div>
    </div>
@endsection