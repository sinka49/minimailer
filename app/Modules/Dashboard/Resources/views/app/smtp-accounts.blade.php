@extends('dashboard::layouts.dash')

@section('js')
    @parent
    <script>
      function loadXls(elem) {
            $(elem).click();
            $(elem).on('change', fileButtonChange);
            return false;
        }

        function fileButtonChange(){
            $('#loadForm').submit();
        }

        function deleteAccount(id) {
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': getToken()}
            });

            $.ajax({
                url: "/dashboard/smtp-account/delete",
                cache: false,
                data: {'id': $(id).data("id")},
                type: "POST",
                success: function (data) {
                    $(id).parent().parent().remove();
                }
            });
        }







    </script>
@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>SMTP Accounts</h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if( empty($smtpAccounts) || count($smtpAccounts) == 0 )
                        <tr>
                            <td colspan="7">
                                <p class="text-center">No accounts found</p>
                            </td>
                        </tr>
                    @else
                        @foreach($smtpAccounts as $a)
                            <tr>
                                <td>{{$a->id}}</td>
                                <td>{{$a->email}}</td>
                                <td>{{($a->enabled)?"active":"unused"}}</td>
                                <td class="text-center">
                                    <a href="{!! URL::to('/dashboard/smtp-account/edit', ['id' => $a->id]) !!}"
                                       class="btn btn-success btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm" id="del{{$a->id}}" onclick="deleteAccount('#del{{$a->id}}')" data-id={{$a->id}}>Del
                                        </button>

                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td colspan="4" class="text-center">
                            <a href="{!! URL::to('/dashboard/smtp-account/add') !!}" role="button"
                               class="btn btn-success" data-toggle="modal">Add account</a>

                            <form  class="visible-lg-inline" method="post" action="/dashboard/smtp-account/load" enctype="multipart/form-data" id="loadForm">
                                {{ csrf_field() }}
                                {!! Form::file('excel', ['style' => 'display: none;', 'id' => 'excel-file']) !!}
                                {!! HTML::link('#', 'Import XLS', ['class' => 'btn btn-primary', 'onclick' => 'return loadXls("#excel-file");']) !!}
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection