

    $('#card_number2').validateCreditCard(function(result) {

        if (result.valid){

                $('#pay2').removeAttr("disabled");
                $('#pay2').removeClass("disabled");


        }
        else {

                $('#pay2').addClass("disabled");
                $('#pay2').attr("disabled","disabled");

        }

    });

    $('#card_number1').validateCreditCard(function(result) {

        if (result.valid){

            $('#pay1').removeAttr("disabled");
            $('#pay1').removeClass("disabled");


        }
        else {

            $('#pay1').addClass("disabled");
            $('#pay1').attr("disabled","disabled");

        }

    });

    $('#card_number3').validateCreditCard(function(result) {

        if (result.valid){

            $('#pay3').removeAttr("disabled");
            $('#pay3').removeClass("disabled");


        }
        else {

            $('#pay3').addClass("disabled");
            $('#pay3').attr("disabled","disabled");

        }

    });


