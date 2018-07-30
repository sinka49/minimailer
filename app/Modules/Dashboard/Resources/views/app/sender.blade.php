@extends('dashboard::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}
    {!! ViewHelper::usePageScript('dash.sender') !!}
    {!! ViewHelper::jsGate( 'smtpAccounts', $smtpAccounts ) !!}

    <script>

       (function () {
            'use strict';
            var app;
            app = angular.module('app.minimailer', ['ui.bootstrap', 'ngResource']);
            app.factory("SmtpAccount", function ($resource) {
                return $resource("/dashboard/api/v1/smtp-account/:id");
            });

            app.controller('ModalDemoCtrl', function ($uibModal, $log, $document) {
                var $ctrl = this;
                $ctrl.items = ['item1', 'item2', 'item3'];

                $ctrl.animationsEnabled = true;

                $ctrl.open = function (size, parentSelector) {
                    var parentElem = parentSelector ?
                            angular.element($document[0].querySelector('.modal-demo ' + parentSelector)) : undefined;
                    $ctrl.parentElem = parentElem;

                    var modalInstance = $uibModal.open({
                        animation: $ctrl.animationsEnabled,
                        ariaLabelledBy: 'modal-title',
                        ariaDescribedBy: 'modal-body',
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        controllerAs: '$ctrl',
                        size: size,
                        appendTo: parentElem,
                        resolve: {
                            items: function () {
                                return $ctrl.items;
                            }
                        }
                    });

                    modalInstance.result.then(function (selectedItem) {
                        $ctrl.selected = selectedItem;
                    }, function () {
                        $log.info('Modal dismissed at: ' + new Date());
                    });
                };

                $ctrl.close = function () {

                };

                $ctrl.openComponentModal = function (element) {
                    var modalInstance = $uibModal.open({
                        animation: $ctrl.animationsEnabled,
                        component: 'modalComponent',
                        resolve: {
                            element: element
                        }
                    });

                    modalInstance.result.then(function (selectedItem) {
                        $ctrl.selected = selectedItem;
                    }, function () {
                        $log.info('modal-component dismissed at: ' + new Date());
                    });
                };

                $ctrl.toggleAnimation = function () {
                    $ctrl.animationsEnabled = !$ctrl.animationsEnabled;
                };
            });


// Please note that the close and dismiss bindings are from $uibModalInstance.

            app.component('modalComponent', {
                templateUrl: '/static/dialog.tmpl.html',
                bindings: {
                    resolve: '<',
                    close: '&'
//                    dismiss: '&'
                },
                controller: function (SmtpAccount) {
                    var $ctrl = this;

                    $ctrl.$onInit = function () {
                    console.log( $ctrl.parentElem );
//                        console.log($ctrl.resolve.element);
//                        var id = $ctrl.resolve.element.property('data-id');
//                        console.log(id);


                        $ctrl.SmtpAccount = SmtpAccount.get({id: 1});
                    };
                    $ctrl.ok = function () {
                        $ctrl.close({$value: $ctrl.selected.item});
                    };

                    $ctrl.cancel = function () {
                        $ctrl.dismiss({$value: 'cancel'});
                    };
                }
            });
        })();

    </script>
@endsection


@section('page-content')

    <div class="col-md-5">

        <div class="row">
            <h4>SMTP accounts</h4>
            <table class="table table-hover table-bordered smtp-account">
                <thead>
                <tr>
                    <th class="col-md-1">
                        Select
                    </th>
                    <th>ID</th>
                    <th>Account</th>
                </tr>
                </thead>
                <tbody>
                @if( !empty($smtpAccounts) || count($smtpAccounts) )
                    @foreach($smtpAccounts as $account)
                        <tr>
                            <td class="text-center">
                                <input name="smtpAccounts[]" class="smtp_checkbox" type="checkbox" value="{{ $account->id }}">
                            </td>
                            <td>{{ $account->id }}</td>
                            <td>
                                {{ $account->email }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div class="form-group clear">
                <input type="checkbox" id="smtp_check_all" name="check_all" class="smtp_check_all"/>
                <label for="smtp_check_all">
                    Check/Un-check All
                </label>
            </div>
            <br/>
            <h4>Addressees</h4>
            <table class="table table-hover table-bordered email_table">
                <thead>
                <th class="col-md-1">Select</th>
                <th>To</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="form-group clear">
                <input type="checkbox" id="email_check_all" name="check_all" class="email_check_all"/>
                <label for="email_check_all">
                    Check/Un-check All
                </label>
            </div>
            <div class="clear">
                <button type="button" class="btn btn-warning btn-sm  clear_list">Clear List
                </button>
            </div>
            <br>
            <h4>Insert emails in column or Import csv</h4>
            <div class="clear">
                <textarea  id="addText" cols="45" rows="10"></textarea>
                <div class="col-md-7 form-group but">

                    <button type="button" class="btn btn-primary btn-sm  import_csv">Import CSV
                    </button>
                    <button type="button" class="btn btn-primary btn-sm addText">Add
                    </button>
                </div>

            </div>



            <div class="clear">

                <div class="browse_file_container" style="/*display:none*/">
                    <br/>
                    <label id="drop" class="dropzone"><span>Drop CSV file here or click</span> <br> <br> <br>  <input type="file" name="csv-file" id="csv-file"/></label>


                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-12 logs_container">
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
        <div class="row">
            <div class="col-md-12">
                {!! BootForm::text('from_email', 'From (Email)', Auth::user()->email, [ 'class'=>'form-control from_email']) !!}
                {!! BootForm::text('from_name', 'From (Name)', Auth::user()->name, [ 'class'=>'form-control from_name']) !!}
                {!! BootForm::text('subject', 'Subject','',['class'=>'form-control subject', 'placeholder' => 'Enter Subject']) !!}
                {!! BootForm::textarea('body', 'Body','',['class'=>'form-control body', 'rows'=>'10', 'name'=>'body', 'id'=>'editor', 'placeholder'=>'Enter Email Body']) !!}
                {!! BootForm::button('Send Message',['type' => 'button', 'class' => 'btn btn-success btn-block send_message']) !!}
            </div>
        </div>
    </div>
@endsection