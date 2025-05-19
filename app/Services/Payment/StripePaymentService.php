<?php

namespace App\Services\Payment;

use Carbon\Carbon;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\PaymentGatewayInterface;
use App\Services\Payment\BasePaymentService;

class StripePaymentService extends BasePaymentService implements PaymentGatewayInterface
{

    protected mixed $api_key;
    public function __construct()
    {
        $this->base_url =env("STRIPE_BASE_URL");
        $this->api_key = env("STRIPE_SECRET_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' =>'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $this->api_key,
        ];

    }

    public function sendPayment(Request $request): array
    {

        //order_id
        $orderId = $request->input('orderId');
        $order = Order::find($orderId);
        if(!$order){
            return ['success' => false,'url'=>route('payment.failed')];
        }
        $data = $this->formatData([
            "amount" => $order->price_after_discount,
            "currency" => "USD",
            "host" => $request->getSchemeAndHttpHost(),
        ]);
        $response =$this->buildRequest('POST', '/v1/checkout/sessions', $data, 'form_params');
        if($response->getData(true)['success']) {
            $order->status = OrderStatus::CONFIRM->value;
            $order->save();
            foreach ($order->items as $item) {
            $item->product->decrement('quantity', $item->qty);
            }

            return ['success' => true, 'url' => $response->getData(true)['data']['url']];
        }
        return ['success' => false,'url'=>route('payment.failed')];
    }

    public function callBack(Request $request): bool
    {
        $session_id = $request->get('session_id');
        $response=$this->buildRequest('GET','/v1/checkout/sessions/'.$session_id);
        DB::table('payment_callback')->insert([
        //session_id ,name ,email, currency ,status ,country ,payment_status,amount_total
            'session_id'=>$request->get('session_id'),
            'name'=>$response->getData(true)['data']['customer_details']['name']??null,
            'email'=>$response->getData(true)['data']['customer_details']['email']??null,
            'currency'=>$response->getData(true)['data']['currency']??null,
            'status'=>$response->getData(true)['data']['status']??null,
            'country'=>$response->getData(true)['data']['customer_details']['address']['country']??null,
            'payment_status'=>$response->getData(true)['data']['payment_status']??null,
            'amount_total'=>$response->getData(true)['data']['amount_total']??null,
            'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=>Carbon::now()->format('Y-m-d H:i:s'),
        ]);
         if($response->getData(true)['success']&& $response->getData(true)['data']['payment_status']==='paid') {
             return true;
         }
        return false;

    }

    public function formatData(array $data): array
    {
        return [
            "success_url" =>$data['host'].'/api/payment/callback?session_id={CHECKOUT_SESSION_ID}',
            "line_items" => [
                [
                    "price_data"=>[
                        "unit_amount" => $data['amount']*100,
                        "currency" => $data['currency'],
                        "product_data" => [
                            "name" => "order",
                            "description" => "description of ORDER"
                        ],
                    ],
                    "quantity" => 1,
                ],
            ],
            "mode" => "payment",
        ];
    }

}
