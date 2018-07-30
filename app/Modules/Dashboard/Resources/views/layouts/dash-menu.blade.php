<div class="list">
    <a href="/dashboard/home"><i class="fa fa-home" aria-hidden="true"></i>   &nbsp;&nbsp;Home</a>
    <a href="/dashboard/sender"><i class="fa fa-envelope-o" aria-hidden="true"></i>  &nbsp; &nbsp;The Mini-Mailer</a>
    <a href="/dashboard/smtp-accounts"><i class="fa fa-users" aria-hidden="true"></i>   &nbsp;&nbsp;SMTP Accounts</a>
    <a href="/dashboard/mailing-history"><i class="fa fa-history" aria-hidden="true"></i>  &nbsp; &nbsp;Mailing History</a>
    <a href="/dashboard/trainings"><i class="fa fa-graduation-cap" aria-hidden="true"></i> &nbsp;  &nbsp;Training Center</a>
    <a href="/dashboard/subscription"><i class="fa fa-cogs" aria-hidden="true"></i> &nbsp; &nbsp; Manage Subscription</a>
    @if(Auth::user()->role != "admin")<a href="/dashboard/finance"><i class="fa fa-money" aria-hidden="true"></i></i> &nbsp;  &nbsp;Finance</a>@endif
    <a href="/dashboard/affiliate-program"><i class="fa fa-bar-chart" aria-hidden="true"></i>  &nbsp; &nbsp;Affiliate Program</a>
    <a href="/dashboard/support"><i class="fa fa-question" aria-hidden="true"></i>  &nbsp; Support</a>

</div>
