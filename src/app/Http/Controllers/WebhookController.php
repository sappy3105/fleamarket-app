<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Stripeの秘密鍵をセット
        Stripe::setApiKey(config('services.stripe.secret'));

        // 2. Webhookシークレット
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // 3. データの検証
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error("Webhook署名検証失敗: " . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $e) {
            Log::error("Webhookペイロード不正: " . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // 4. イベントの種類によって処理を分ける
        $session = $event->data->object;

        \Log::info("--- Webhook受信ログ開始 ---");
        \Log::info("イベントタイプ: " . $event->type);
        \Log::info("セッションID: " . $session->id);
        \Log::info("Stripe上の支払い状態(payment_status): " . $session->payment_status);
        \Log::info("--- Webhook受信ログ終了 ---");

        // --- ケースA: チェックアウト完了（カード決済の完了、またはコンビニ決済の番号発行時） ---
        if ($event->type === 'checkout.session.completed') {
            \Log::info("Checkout Session Completed 届きました: " . $session->id);

            $soldItem = SoldItem::where('stripe_checkout_id', $session->id)->first();

            if ($soldItem && $soldItem->status === 'pending') {
                // セッション内の支払い状況を確認
                if ($session->payment_status === 'paid') {
                    // カード決済など、この時点で支払いが済んでいる場合のみ更新
                    $soldItem->update(['status' => 'paid']);
                    \Log::info("即時決済完了（カード等）: SoldItem ID {$soldItem->id}");
                } else {
                    // コンビニ払いなど、支払いがまだの場合は pending のまま維持
                    \Log::info("支払い待ち状態（コンビニ等）: SoldItem ID {$soldItem->id} は pending を維持します");
                }
            } else {
                \Log::warning("対象のデータがないか、既に処理済みです: " . $session->id);
            }
        }

        // --- ケースB: 非同期決済の成功（コンビニで実際に現金が支払われた時） ---
        if ($event->type === 'checkout.session.async_payment_succeeded') {

            \Log::info("コンビニ入金通知が届きました: " . $session->id);

            $soldItem = SoldItem::where('stripe_checkout_id', $session->id)->first();

            if ($soldItem && $soldItem->status === 'pending') {
                // 実際にお金が払われたので、確実に paid に更新
                $soldItem->update(['status' => 'paid']);
                \Log::info("コンビニ入金確認アップデート成功: SoldItem ID {$soldItem->id}");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
