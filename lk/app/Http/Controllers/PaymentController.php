<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\Payment;
use App\Models\User;
use App\Models\HistoryPayment;
use App\Models\PaymentCredit;

use App\Helpers\Tinkoff;
use App\Helpers\HelperPayment;

class PaymentController extends Controller
{

    public function payment(Request $request)
    {
        if ($request->has('OrderId')) {
            $payment = Payment::find($request->OrderId);
            $payment->payment_id = $request->PaymentId;
            $payment->card = $request->Pan;
            $payment->updated_at = date("d-m-Y H:i:s");
            if ($request->Status == 'CONFIRMED' && $request->Success == true) {
                $user = User::find($payment->user_id);
                $user->balance = $user->balance + $payment->paysum;
                $user->save();

                $payment->after_balance = $user->balance;
                $payment->status = HelperPayment::CD_STATUS_PAID;
                HistoryPayment::create([
                    'user_id' => $user->id,
                    'type_transaction' => '10',
                    'paysum' => $payment->paysum,
                    'paybonus' => 0,
                    'before_balance' => $user->balance - $payment->paysum,
                    'after_balance' => $user->balance,
                    'before_bonus' => $user->bonus,
                    'after_bonus' => $user->bonus
                ]);
            } else {
                $payment->status = HelperPayment::CD_STATUS_ERROR;
            }
            $payment->save();
            return response('OK');
        }
    }

    public function generalHistory(Request $request)
    {
        if ($request->user()->role == 'ROLE_ADMIN') {
            return response()->json(Payment::with('user')
                ->with('requisite')
                ->orderBy('created_at', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->paginate(10), 200);
        }
        return response()->json([
            'Дотсуп запрещён!'
        ], 403);
    }


    public function paymentDocument(Request $request, int $id)
    {
        $payment = Payment::with('requisite')->findOrFail($id);
        $pdfPath = '/payment/pdf/' . date("dmY", strtotime($payment->created_at)) . '-' . str_pad($payment->id . $payment->requisites_id, 6, '000000', STR_PAD_LEFT) . '.pdf';

        return Storage::disk('public')->download($pdfPath);
        // response(
        //     Storage::disk('public')->download($pdfPath)
        // )
        //     ->header('Accept-Ranges', 'bytes')
        //     ->header('Content-type', 'application/pdf')
        //     ->header('Content-length', Storage::disk('public')->size($pdfPath))
        //     ->header('Content-Range', 'bytes 0-' . Storage::disk('public')->size($pdfPath));
    }
}
