<?php

namespace App\Mail;

use App\Drivers\StorageDriver;
use App\Http\Controllers\Publico; 
use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels; 

class DocumentoElectronicoMail extends Mailable
{

    use Queueable, SerializesModels;

    private array $parameters;
    private array $filesAttach;
    private string $fromName;
    private string $mySubject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $Params, String $FromName, String $Subject)
    {
        $this->parameters = $Params;
        $this->fromName = $FromName;
        $this->mySubject = $Subject;
    }



    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->parameters += ['app_logo' => StorageDriver::ImageToBase64()];

        $this->from(config('mail.from.address'),  $this->fromName);
        $this->subject($this->mySubject);


        $this->markdown('templates.email.facturaelectronica', $this->parameters);

        return $this;
    }
}