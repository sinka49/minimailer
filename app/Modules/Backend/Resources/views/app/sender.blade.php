@extends('backend::layouts.dash')

@section('js')
    @parent
    {!! ViewHelper::useCkeditor() !!}
    {!! ViewHelper::usePageScript('dash.sender') !!}
@endsection

@section('page-content')
    <div class="row">
        <div class="col-md-5">
            <div class="row">
                <div style="display: none">
                    <label class="col-md-3">Proxy</label>
                    <div class="col-md-9">
                        <input type="text" name="proxy" class="form-control proxy" placeholder="Enter Proxy"/>
                    </div>
                    <br/>
                    <br/>
                </div>

                <label class="col-md-3">Email</label>
                <div class="col-md-9">
                    <input type="email" name="email" class="form-control email" placeholder="Enter Email"/>
                </div>
                <br/>
                <br/>
                <label class="col-md-3">Password</label>
                <div class="col-md-9">
                    <input type="password" name="password" class="form-control password"
                           placeholder="Enter Password"/>
                </div>
                <br/>
                <br/>
                <label class="col-md-3">SMTP</label>
                <div class="col-md-9">
                    <input type="text" name="smtp" class="form-control smtp" placeholder="Enter SMTP"/>
                </div>
                <br/>
                <br/>
                <label class="col-md-3">Port</label>
                <div class="col-md-9">
                    <input type="text" name="port" class="form-control port" placeholder="Enter Port"/>
                </div>
                <br/>
                <br/>
                <label class="col-md-3">Enable SSL</label>
                <div class="col-md-9">
                    <input type="text" name="ssl" class="form-control ssl" placeholder="Enter SSL"/>
                </div>

                <br/>
                <table class="table table-hover table-bordered email_table">
                    <thead>
                    <th class="col-md-1">Select</th>
                    <th>To</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="form-group">
                    <input type="checkbox" name="check_all" class="email_check_all"/>
                    <label>
                        Check/Un-check All
                    </label>
                </div>

                <div class="col-md-7">
                    <button type="button" class="btn btn-warning btn-sm pull-left clear_list">Clear List
                    </button>
                    <button type="button" class="btn btn-primary btn-sm pull-right import_csv">Import CSV
                    </button>
                </div>
                <div class="col-md-9 pull-right">
                    <br/>
                    <div class="browse_file_container" style="display:none">
                        <div id="drop" style="display:none"><span>Drop PDF file here</span> or click</div>
                        <input type="file" name="csv-file" id="csv-file"/>
                        <label style="display:none"><input type="checkbox" name="merge-table"> Merge
                            table</label>
                        <label style="display:none"><input type="checkbox"
                                                           name="merge-table-remove-first-line"> Merge table
                            and remove first line</label>
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
                    {!! BootForm::text('from_name', 'From (Name)', Auth::user()->name, [ 'class'=>'form-control from_name']) !!}
                    {!! BootForm::text('subject', 'Subject','',['class'=>'form-control subject', 'placeholder' => 'Enter Subject']) !!}
                    {!! BootForm::textarea('body', 'Body','',['class'=>'form-control body', 'rows'=>'10', 'name'=>'body', 'id'=>'editor', 'placeholder'=>'Enter Email Body']) !!}
                    {!! BootForm::button('Send Message',['type' => 'button', 'class' => 'btn btn-success btn-block send_message']) !!}
                </div>
            </div>
        </div>
    </div>
@endsection