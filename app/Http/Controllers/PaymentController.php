<?php



namespace App\Http\Controllers;

use App\Models\Product;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SebastianBergmann\Environment\Console;

class PaymentController extends Controller
{
    public function showQrPopup($id)
    {
        $product = Product::findOrFail($id);

        $info = new IndividualInfo(
            bakongAccountID: 'nat_kimsreng@aclb',
            merchantName: 'NAT KIMSRENG',
            merchantCity: 'Phnom Penh',
            currency: KHQRData::CURRENCY_KHR,
            amount: (int)($product->price * 4100),     // ← THIS IS CORRECT
            billNumber: 'TEST-' . $product->id . '-' . time(),
        );

        // Generate KHQR payload string (NOT an image yet)
        $qr = BakongKHQR::generateIndividual($info);
        $khqrString = $qr->data['qr']; // EMV / KHQR string

        return view('frontend.payment.popup', [
            // Use SVG so we don't require the Imagick extension
            'qrSvg'   => QrCode::format('svg')
                ->size(320)
                ->margin(1)
                ->generate($khqrString),
            'md5'     => $qr->data['md5'],
            'product' => $product,
            'amount'  => $product->price,
        ]);
    }

    public function checkPayment($md5)
    {
        try {
            $token = config('services.bakong.token'); // Or env('BAKONG_TOKEN')
            if (empty($token)) {
                return response()->json(['paid' => false, 'error' => 'No API token configured']);
            }

            $isTest = filter_var(config('services.bakong.test_mode', true), FILTER_VALIDATE_BOOLEAN);
            $client = new \KHQR\BakongKHQR($token);
            $response = $client->checkTransactionByMD5($md5, $isTest);

        // Parse status (adapt based on your needs; API returns 'responseCode' 0 for success)
        $data = $response['data'] ?? $response ?? [];
        $status = $data['transactionStatus'] ?? $data['status'] ?? null;
        $paid = in_array(strtoupper((string) $status), ['SUCCESS', 'SUCCESSFUL', 'COMPLETED']);

        return response()->json(['paid' => $paid]);

    } catch (\Throwable $e) {
        \Log::error('KHQR check failed: ' . $e->getMessage());
        return response()->json(['paid' => false, 'error' => 'Check failed']);
    }
}
}
