<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Stripeから送られてくる署名を検証するための設定
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // 通知が本当にStripeから来たものか検証
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 支払い完了イベントをキャッチ
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object; // Stripeのセッション情報

            // storePurchaseで保存した「stripe_checkout_id」を元に対象データを検索
            $soldItem = SoldItem::where('stripe_checkout_id', $session->id)->first();

            if ($soldItem) {
                // ここで支払いステータスを更新（例：statusカラムがある場合）
                // $soldItem->update(['status' => 'paid']);

                Log::info('支払い完了を確認: SoldItem ID ' . $soldItem->id);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
