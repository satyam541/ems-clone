<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Exports\DailyReportExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel as BaseExcel;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $details;
    public function __construct( $details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
        $filename = "Daily_Reports.xlsx";

        $attachment = Excel::raw(
            new DailyReportExport($this), 
            BaseExcel::XLSX
        );
        return $this->view('email.sendReportMail')
            ->subject("Work Report")            
            ->attachData($attachment, $filename);
    }
}
