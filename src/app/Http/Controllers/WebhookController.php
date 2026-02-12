<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Stripeの秘密鍵をセット
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // 2. Webhookシークレットと、届いたデータ（中身と署名）を取得
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // 3. データの「真偽」を検証する
            // 悪意のある人がStripeのフリをして偽の通知を送ってきても、ここでブロック（エラー）されます。
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            // もしハンコが偽物だったり、設定が間違っていたらエラーを返して終了します。
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // 4.支払い完了イベントをキャッチ
        if (
            $event->type === 'checkout.session.completed' ||
            $event->type === 'checkout.session.async_payment_succeeded'
        ) {

            // 5. 通知データの中から「セッション情報」を取り出す
            $session = $event->data->object;

            // 【デバッグ用】実際に届いたIDをログに出してみる
            \Log::info("届いたセッションID: " . $session->id);

            // 6. DBから、対象の購入データを探す
            // storePurchaseで作っておいた「stripe_checkout_id」を頼りに検索します。
            $soldItem = SoldItem::where('stripe_checkout_id', $session->id)->first();

            // 7. データが見つかり、かつ、まだ「未払い(pending)」状態なら更新
            if ($soldItem && $soldItem->status === 'pending') {
                // ここでステータスを「支払い済み(paid)」に書き換えます！
                $soldItem->update(['status' => 'paid']);

                // ログに記録を残しておくと、後でトラブルがあった時に確認しやすくなります。
                \Log::info("決済完了アップデート成功: SoldItem ID {$soldItem->id}");
            } else {
                // 【デバッグ用】見つからなかった場合にログを出す
                \Log::warning("DBに該当するIDがありませんでした: " . $session->id);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
