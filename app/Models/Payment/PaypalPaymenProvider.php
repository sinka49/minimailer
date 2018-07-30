<?php if ( ! defined( 'BASEPATH' ) ) {
    exit( 'No direct script access allowed' );
}

use App\Models\Product;
use Illuminate\Support\Facades\Config;
use Error;
use App\Models\Payment\PaymentProvider;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;

Class PaypalPaymentProvider extends PaymentProvider{

    /**
     * Pay payment access
     * @var array
     */
    public $config = [];
    /**
     * Redirect link
     * @var string
     */
    private $shopSuccessUrl;
    /**
     * Redirect link
     * @var string
     */
    private $shopFailUrl;
    /**
     * @var string
     */
    private $error;
    /**
     * @var
     */
    private $details;
    /**
     * @var
     */
    private $response;
    /**
     * @var
     */
    private $link;
    /**
     * @var
     */
    private $cancelUrl;

    /**
     * Step 1
     *
     * @param $order
     *
     * @return bool
     */
    public function request( $order ) {
        $ci = &get_instance();
        $ci->load->model( 'Orders_model', 'Orders' );

        if ( ! $ci->Orders->checkOrderIsNotPayed( $order ) ) {
            $this->setError( 'Order is payed' );

            return false;
        }

        $url       = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        $returnUrl = "$url/callback/paypal_successpayment";

        $cancelUrl = $this->cancelUrl;
        if ( ! $this->cancelUrl ) {
            $cancelUrl = "$url/payments/paypal_cancel";
        }

        $product = $this->getProductDetails( $order );

        if ( ! $product ) {
            return false;
        }

        $shippingTotal  = new BasicAmountType( $product['currencyId'], $product['shippingTotal'] );
        $handlingTotal  = new BasicAmountType( $product['currencyId'], $product['handlingTotal'] );
        $insuranceTotal = new BasicAmountType( $product['currencyId'], $product['insuranceTotal'] );
        // details about payment
        $paymentDetails = new PaymentDetailsType();

        $itemAmount = new BasicAmountType( $product['currencyId'], $product['itemAmount'] );

        $itemDetails           = new PaymentDetailsItemType();
        $itemDetails->Name     = $product['itemName'];
        $itemDetails->Amount   = $itemAmount;
        $itemDetails->Quantity = $product['itemQuantity'];

        $itemDetails->ItemCategory = $product['itemCategory'];
        $itemDetails->Tax          = new BasicAmountType( $product['currencyId'], $product['itemSalesTax'] );

        $paymentDetails->PaymentDetailsItem = $itemDetails;
        //Payment details
        $paymentDetails->ItemTotal  = new BasicAmountType( $product['currencyId'], $product['itemTotalValue'] );
        $paymentDetails->TaxTotal   = new BasicAmountType( $product['currencyId'], $product['taxTotalValue'] );
        $paymentDetails->OrderTotal = new BasicAmountType( $product['currencyId'], $product['orderTotalValue'] );
        /*
         * How you want to obtain payment. When implementing parallel payments, this field is required and must be set to Order. When implementing digital goods, this field is required and must be set to Sale. If the transaction does not include a one-time purchase, this field is ignored. It is one of the following values:
           Sale � This is a final sale for which you are requesting payment (default).
           Authorization � This payment is a basic authorization subject to settlement with PayPal Authorization and Capture.
           Order � This payment is an order authorization subject to settlement with PayPal Authorization and Capture.
         */
        $paymentDetails->PaymentAction = 'Order';

        $paymentDetails->HandlingTotal  = $handlingTotal;
        $paymentDetails->InsuranceTotal = $insuranceTotal;
        $paymentDetails->ShippingTotal  = $shippingTotal;

        $setECReqDetails                   = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails   = $paymentDetails;
        $setECReqDetails->InvoiceID        = $product['InvoiceID'];
        $setECReqDetails->OrderDescription = $product['description'];

        $setECReqDetails->CancelURL = $cancelUrl;
        $setECReqDetails->ReturnURL = $returnUrl;

        $setECReqDetails->NoShipping         = 2;
        $setECReqDetails->AddressOverride    = 0;
        $setECReqDetails->ReqConfirmShipping = 0;

        // Billing agreement details
        $billingAgreementDetails                              = new BillingAgreementDetailsType( 'None' );
        $billingAgreementDetails->BillingAgreementDescription = 'MerchantInitiatedBilling';
        $setECReqDetails->BillingAgreementDetails             = array( $billingAgreementDetails );


        $setECReqType                                   = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq                                       = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest            = $setECReqType;
        /*
         * 	 ## Creating service wrapper object
        Creating service wrapper object to make API call and loading
        Configuration::getAcctAndConfig() returns array that contains credential and config parameters
        */
        $paypalService = new PayPalAPIInterfaceServiceService( $this->config );
        try {
            /* wrap API method calls on the service object with a try catch */
            $setECResponse = $paypalService->SetExpressCheckout( $setECReq );
        } catch ( Exception $ex ) {
            $this->setError( $ex->getCode() . $ex->getMessage() );

            return true;
        }
        if ( isset( $setECResponse ) ) {
            $this->response = $setECResponse;
            if ( $setECResponse->Ack == 'Success' ) {
                $this->link = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=' . $setECResponse->Token;

                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function getConfig() {
        return $this->config;
    }

    /**
     * Create payment details for paypal from order
     *
     * @param $order
     *
     * @return array
     */
    private function getProductDetails( $order ) {

        if ( ! isset( $order, $order->sent_data, $order->sent_data->product ) ) {
            $this->setError( 'Empty product in order' );

            return false;
        }

        $product = $order->sent_data->product;

        $default = [
            'currencyId'      => 'RUB',
            'itemQuantity'    => 1,
            'itemAmount'      => 0,
            'itemTotalValue'  => 0,
            'orderTotalValue' => 0,
            'description'     => 'description',
            'itemName'        => 'itemName',
            'InvoiceID'       => 0,

            'shippingTotal'  => 0,
            'handlingTotal'  => 0,
            'taxTotalValue'  => 0,
            'insuranceTotal' => 0,
            'itemSalesTax'   => 0,
            'itemCategory'   => 'Physical',
            'paymentAction'  => 'Sale'
        ];

        if ( $product->currency != 'RUR' ) {
            $default['currencyId'] = $product->currency;
        }

        $default['itemAmount']      = $product->cost;
        $default['itemTotalValue']  = $product->cost;
        $default['orderTotalValue'] = $product->cost;
        $default['itemName']        = $product->name;
        $default['description']     = $product->name;
        $default['InvoiceID']       = $order->id;

        return $default;
    }

    /**
     * @param array $config
     */
    public function setConfig( $config ) {
        $this->config = $config;
    }

    public function setConfigFromOrder( $orderData ) {
        $ci = &get_instance();
        $ci->load->model('Orders_model', 'Orders');

        $psAccounts = $ci->Orders->getSentData($orderData, 'product/ps_data', true);

        if( !$psAccounts ){
            $this->setError( 'Wrong ps account in the product' );
            return false;
        }

        return $this->setConfigFromPsConfig($psAccounts, 'paypalAccount');
    }

    /**
     * @param $psConfig
     * @param $psName
     *
     * @return bool
     */
    public function setConfigFromPsConfig( $psConfig, $psName ) {

        if( !isset( $psConfig->$psName ) ){
            $this->setError( 'Wrong ps account in the product, cant find name ' . $psName );
            return false;
        }

        $name = $psConfig->$psName;

        $ci = &get_instance();

        $config = $ci->config->item('paypal', 'payment_systems');

        if( !$config || !isset($config[$name]) ){
            $this->setError( 'Wrong ps account in the product, cant get ps data' );
            return false;
        }
        $this->config = $config[$name];
        return true;
    }
    /**
     * Step 2
     *
     * @param $token
     *
     * @return \PayPal\Service\PayPalAPI\GetExpressCheckoutDetailsResponseType
     */
    public function getPaymentDetails( $token, $orderData ) {
        // this section is optional if parameters required for DoExpressCheckout is retrieved from your database
        $getExpressCheckoutDetailsRequest                        = new GetExpressCheckoutDetailsRequestType( $token );
        $getExpressCheckoutReq                                   = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        $this->setConfigFromOrder($orderData);

        $paypalService = new PayPalAPIInterfaceServiceService( $this->getConfig() );
        try {
            $getECResponse = $paypalService->GetExpressCheckoutDetails( $getExpressCheckoutReq );
        } catch ( Exception $ex ) {
            return $this->setError( 'Error' . $ex->getCode() . $ex->getMessage() );
        }

        $this->details = $getECResponse->GetExpressCheckoutDetailsResponseDetails;

        return $this->details;
    }

    /**
     * Step 3
     *
     * @param $token
     * @param $payerId
     * @param $order
     *
     * @return bool
     */
    public function confirmPayment( $token, $payerId, $order ) {

        $order = $this->getProductDetails( $order );
        if ( ! $order ) {
            return false;
        }

        $paymentAction = urlencode( $order['paymentAction'] );

        $paypalService = new PayPalAPIInterfaceServiceService( $this->getConfig() );

        /*
         * The total cost of the transaction to the buyer. If shipping cost (not applicable to digital goods) and tax charges are known, include them in this value. If not, this value should be the current sub-total of the order. If the transaction includes one or more one-time purchases, this field must be equal to the sum of the purchases. Set this field to 0 if the transaction does not include a one-time purchase such as when you set up a billing agreement for a recurring payment that is not immediately charged. When the field is set to 0, purchase-specific fields are ignored.
        */
        $orderTotal             = new BasicAmountType();
        $orderTotal->currencyID = $order['currencyId'];
        $orderTotal->value      = $order['itemAmount'];

        $paymentDetails             = new PaymentDetailsType();
        $paymentDetails->OrderTotal = $orderTotal;

        /*
         * Your URL for receiving Instant Payment Notification (IPN) about this transaction. If you do not specify this value in the request, the notification URL from your Merchant Profile is used, if one exists.
         */
        if ( isset( $order['notifyURL'] ) ) {
            $paymentDetails->NotifyURL = $order['notifyURL'];
        }

        $DoECRequestDetails                    = new DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID           = $payerId;
        $DoECRequestDetails->Token             = $token;
        $DoECRequestDetails->PaymentAction     = $paymentAction;
        $DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

        $DoECRequest                                         = new DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;


        $DoECReq                                  = new DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

        try {
            /* wrap API method calls on the service object with a try catch */
            $this->response = $paypalService->DoExpressCheckoutPayment( $DoECReq );
        } catch ( Exception $ex ) {
            $this->setError( 'Error' . $ex->getCode() . $ex->getMessage() );

            return false;
        }

        return true;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getLink() {
        return $this->link;
    }

    /**
     * @return bool
     */
    public function checkResponce() {
        $response = $this->response;

        if ( ! $this->response ) {
            $this->setError( 'Empty response' );

            return false;
        }

        return strtolower( $response->Ack ) == 'success';
    }

    public function setResponce( $response ) {
        $this->response = $response;

        return $this;
    }

    public function getResponceInvoiceId() {
        return $this->response->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;
    }

    public function getResponceTotalCost() {
        return $this->response->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value;
    }

    public function checkDetails( $order ) {
        if ( empty( $this->details ) ) {
            $this->setError( 'Empty details' );

            return false;
        }
        $details = $this->details;

        if ( $order->id != $details->InvoiceID ) {
            $this->setError( 'InvoiceID is not equal to order.ID' );

            return false;
        }

        $product = $order->sent_data->product;

        if ( ! isset( $details->PaymentDetails, $details->PaymentDetails[0], $details->PaymentDetails[0]->OrderTotal ) ) {
            $this->setError( 'Empty PaymentDetails' );

            return false;
        }

        $orderTotal = $details->PaymentDetails[0]->OrderTotal;

        return $this->checkPaymentData( $product, $orderTotal );
    }

    public function checkPaymentData( $product, $orderTotal ) {
        if ( $product->currency != 'RUR' || $orderTotal->currencyID != 'RUB' ) {
            $this->setError( 'Invalid currency' );

            return false;
        }

        if ( $product->cost != $orderTotal->value ) {
            $this->setError( 'Invalid amount' );

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getError() {
        return $this->error;
    }

    public function setError( $error ) {
        $this->error = $error;

        return $this;
    }

    public function getDetails() {
        return $this->details;
    }
}