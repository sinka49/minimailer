@extends('backend::layouts.dash')

@section('js')
    @parent
    <script>
        function toggleIcon(e)
        {
            $(e.target)
                    .prev('.panel-heading')
                    .find(".more-less")
                    .toggleClass('glyphicon-plus glyphicon-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);
    </script>
@endsection

@section('page-content')
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4><i class="fa fa-fw fa-share"></i>Affiliate Program</h4>
            </div>
            <div class="panel-body">

               <div class="form-group ">
                    <label for="affiliate_url" class="control-label">My Referral's Stats</label>
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Email</th>
                            <th>Sponsor_id</th>
                            <th>Status</th>
                            <th>Total Refferals</th>
                            <th>Upgraded Refferals</th>
                            <th>Subscription</th>
                            <th>Register</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if( empty($users) || count($users) == 0 )
                            <tr>
                                <td colspan="7">
                                    <p class="text-center">No affiliated users found</p>
                                </td>
                            </tr>
                        @else
                            @foreach($users as $a)
                                <tr>
                                    <td>{{$a->user_id}}</td>
                                    <td>{{$a->email}}</td>
                                    <td>@if($a->parent_id){{\App\Models\User::find($a->parent_id)->email}}@endif</td>
                                    <td>{{$a->status}}</td>
                                    <td>{{$a->totalCount}}</td>
                                    <td>{{$a->totalActive}}</td>
                                    <td>@if(count($a->subC)){{$a->subC->product}} <br>{{$a->subC->status}}@endif</td>
                                    <td>@if(count($a->subC)){{$a->subC->start}}@endif</td>

                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                   {{ $users->links() }}
                </div>
            </div>
        </div>
@endsection