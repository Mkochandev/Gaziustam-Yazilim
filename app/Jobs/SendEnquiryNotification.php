<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
class SendEnquiryNotification implements ShouldQueue
{
    use Queueable;
    public int $tries=4;
    public function __construct(public string $reference) {}
    public function backoff(): array { return [60,300,1800]; }
    public function handle(): void
    {
        $to=config('studio.notify');
        if(!$to) throw new \RuntimeException('ENQUIRY_NOTIFY_TO yapılandırılmalı.');
        Mail::raw($this->reference."\n".url('/admin/talepler'),fn($m)=>$m->to($to)->subject('Yeni proje talebi '.$this->reference));
    }
}
