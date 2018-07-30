<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Ruseller Email Newsletter</title>

    <style type="text/css">

        a {color: #4A72AF;}

        body, #header h1, #header h2, p {margin: 0; padding: 0;}

        #main {border: 1px solid #cfcece;}

        img {display: block;}

        #top-message p, #bottom-message p {color: #3f4042; font-size: 12px; font-family: Arial, Helvetica, sans-serif; }

        #header h1 {color: #ffffff !important; font-family: "Lucida Grande", "Lucida Sans", "Lucida Sans Unicode", sans-serif; font-size: 24px; margin-bottom: 0!important; padding-bottom: 0; }

        #header h2 {color: #ffffff !important; font-family: Arial, Helvetica, sans-serif; font-size: 24px; margin-bottom: 0 !important; padding-bottom: 0; }

        #header p {color: #ffffff !important; font-family: "Lucida Grande", "Lucida Sans", "Lucida Sans Unicode", sans-serif; font-size: 12px;  }

        h1, h2, h3, h4, h5, h6 {margin: 0 0 0.8em 0;}

        h3 {font-size: 28px; color: #444444 !important; font-family: Arial, Helvetica, sans-serif; }

        h4 {font-size: 22px; color: #4A72AF !important; font-family: Arial, Helvetica, sans-serif; }

        h5 {font-size: 18px; color: #444444 !important; font-family: Arial, Helvetica, sans-serif; }

        p {font-size: 14px; color: #444444 !important; font-family: "Lucida Grande", "Lucida Sans", "Lucida Sans Unicode", sans-serif; line-height: 1.5;}

    </style>

</head>



<body>


<table id="main" width="600" align="center" cellpadding="0" cellspacing="15" bgcolor="ffffff">

    <tr>

        <td>

            <table id="header" cellpadding="10" cellspacing="0" align="center" bgcolor="8fb3e9">

                <tr>

                    <td width="570" bgcolor="7aa7e9"><h1>Theminimailer</h1></td>

                </tr>



            </table><!-- header -->

        </td>

    </tr><!-- header -->

    <tr>
        <td>
            <table id="content-1" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <td width="100%" valign="top" colspan="3">
                        <h3>You bought a subscription for ${{$price}} for a period of {{$period}} days</h3>
                    </td>
                </tr>
            </table><!-- content 1 -->
        </td>
    </tr>
    <tr>
        <td>
            <table id="content-1" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <td width="100%" valign="top" colspan="3">
                        <h5>Subscription is renewed automatically. The subsequent cost of the subscription is ${{$priceNew}} for a period of {{$period}} days. To cancel a subscription, register and make a cancellation in your account </h5>
                    </td>
                </tr>
            </table><!-- content 1 -->
        </td>
    </tr>
    <tr>

        <td>

            <table id="content-2" cellpadding="0" cellspacing="0" align="center">

                <tr>

                    <td width="570"><p>To register the profile of theminimailer for this email, you just need to register with http://theminimailer.com. To register with another email, go to <a href="http://theminimailer.com/dashboard/register?token={{$token}}">http://theminimailer.com/dashboard/register?token={{$token}}</a></p></td>

                </tr>

            </table><!-- content-2 -->

        </td>

    </tr>


</table>



</body>

</html>
