<?php

namespace App\Http\Controllers;


require __DIR__ . '/vendor/autoload.php';
use Sample\PayPalClient;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;


class PaypalController extends Controller
{
    
  public static function refundOrder($captureId, $debug=false)

  {

    $request = new CapturesRefundRequest($captureId);

    $request->body = self::buildRequestBody();

    // 3. Call PayPal to refund a capture

    $client = PayPalClient::client();

    $response = $client->execute($request);


    if ($debug)

    {

      print "Status Code: {$response->statusCode}\n";

      print "Status: {$response->result->status}\n";

      print "Order ID: {$response->result->id}\n";

      print "Links:\n";

      foreach($response->result->links as $link)

      {

        print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";

      }

      // To toggle printing the whole response body comment/uncomment

      // the following line

      echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";

    }

    return $response;

  }

}
