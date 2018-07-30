<div class="panel panel-default">
    <div class="panel-heading" role="tab">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#products-{{$id}}"
               aria-expanded="true"
               aria-controls="collapseOne">
                <div class="col-md-4">{{ $title }}</div>
                <div class="col-md-4">
                    @if(!empty($subscription) && ($product->id == $subscription->product_id))
                          {{$subscription->start}}
                        - {{$subscription->end}}
                    @endif
                </div>
                <div class="col-md-4">
                    <i class="more-less fa fa-angle-down pull-right"></i>
                    @if(!empty($subscription) && ($product->id == $subscription->product_id) )
                        <span class="label label-success pull-right">{{$subscription->status}}</span>
                    @endif
                </div>
                <div class="clearfix"></div>
            </a>
        </h4>
    </div>
    <div id="products-{{$id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3"><strong>Renew period:</strong></div>
                        <div class="col-md-9">{{$renew_period}} days</div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><strong>Initiate cost:</strong></div>
                        <div class="col-md-9">
                            @if(empty($initial_price))
                                Free
                            @else
                                {{$initial_price}}$
                            @endif
                        </div>
                    </div>
                    @if($renew_price)
                        <div class="row">
                            <div class="col-md-3"><strong>Prolongation cost:</strong></div>
                            <div class="col-md-9">{{$renew_price}}$</div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3"><strong>Description:</strong></div>
                        <div class="col-md-9">{!!$description !!}</div>
                    </div>
                    @if(!empty($subscription) && ($product->id == $subscription->product_id) )
                        @if( !empty($subscription->transaction_id) )
                            <div class="row">
                                <div class="col-md-3"><strong>Transaction ID:</strong></div>
                                <div class="col-md-9">
                                    <a href="{{URL::to('/dashboard/finance',['transactionId' => $subscription->transaction_id])}}">{{$subscription->transaction_id}} (see in Finance section)</a>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3"><strong>Created date:</strong></div>
                            <div class="col-md-9">{{date('m/d/Y', strtotime($subscription->created_at))}}</div>
                        </div>
                            <div class="row">
                                <div class="col-md-3"><strong>Purchase date:</strong></div>
                                <div class="col-md-9">{{$subscription->start}}</div>
                            </div>
                        <div class="row">
                            <div class="col-md-3"><strong>Date of update:</strong></div>
                            <div class="col-md-9">{{$subscription->end}}</div>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    @if(empty($subscription))
                            <div class="col-md-6"><a href="/dashboard/terms">Terms of Service</a></div>
                        <div class="col-md-6"> <button class="btn btn-success" onclick="buyNow(this)" type="submit" data-product="{{$product->id}}" >Buy now</button>
                        </div>
                    @elseif($product->id == $subscription->product_id)
                        <div class="row">
                            @if($subscription->status == "active")
                            <div class="col-md-6">
                                <h4>Days to
                                    update: {{$subscription->day_update}}</h4>
                            </div>

                            <div class="col-md-6">
                                <form action="/dashboard/subscription/cancel" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" name="subscription_id" value="{{$subscription->subscription_id}}">
                                    <button class="btn btn-danger pull-right" id="cancel" name="action" value="reject">Cancel</button>
                                </form>
                            </div>
                            @else
                                <div class="col-md-6">
                                    <h4>Days to
                                        end: {{$subscription->day_update}}</h4>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

        </div>
    </div>
</div>