<?php
namespace LNbitsSatsPayPlugin;

/**
 * For calling LNbits API
 */
class API {

    protected $url;
    protected $api_key;
    protected $wallet_id;
    protected $watch_only_wallet_id;

    public function __construct($url, $api_key, $wallet_id, $watch_only_wallet_id) {
        $this->url = rtrim($url,"/");
        $this->api_key = $api_key;
        $this->wallet_id = $wallet_id;
        $this->watch_only_wallet_id = $watch_only_wallet_id;
    }

    public function createInvoice($amount, $memo, $order_id) {
        $c = new CurlWrapper();
        $order = wc_get_order($order_id);
        $data = array(
            "onchainwallet" => $this->watch_only_wallet_id,
            "lnbitswallet" => $this->wallet_id,
            "description" => $memo,
            "webhook" => "https://hookb.in/r1j0z3rX7ytqdJ3qVqVR",
            "completelink" => $order->get_checkout_order_received_url(),
            "completelinktext" => "Payment Received. Back",
            "time"=> 1440,
            "amount"=> $amount
        );
        $headers = array(
            'X-Api-Key' => $this->api_key,
            'Content-Type' => 'application/json'
        );
        $response = $c->post($this->url.'/satspay/api/v1/charge', array(), $data, $headers);
        return $response;
    }

    public function checkChargePaid($payment_id) {
        $c = new CurlWrapper();
        $headers = array(
            'X-Api-Key' => $this->api_key,
            'Content-Type' => 'application/json'
        );
        return $c->get($this->url.'/satspay/api/v1/charge/'.$payment_id, array(), $headers);
    }
}
