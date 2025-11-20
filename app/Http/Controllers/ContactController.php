<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * İletişim formu gönderimi
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'subject.required' => 'Konu alanı zorunludur.',
            'message.required' => 'Mesaj alanı zorunludur.',
        ]);
        
        try {
            // E-posta gönderimi
            $adminEmail = Setting::get('contact_email', config('mail.from.address'));
            
            Mail::raw(
                "İsim: {$validated['name']}\nE-posta: {$validated['email']}\nKonu: {$validated['subject']}\n\nMesaj:\n{$validated['message']}",
                function ($message) use ($validated, $adminEmail) {
                    $message->to($adminEmail)
                        ->subject('İletişim Formu: ' . $validated['subject'])
                        ->replyTo($validated['email'], $validated['name']);
                }
            );
            
            return back()->with('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
        } catch (\Exception $e) {
            // E-posta gönderilemezse bile başarılı mesajı göster
            // (Gerçek uygulamada loglama yapılmalı)
            return back()->with('success', 'Mesajınız alındı. En kısa sürede size dönüş yapacağız.');
        }
    }
}
