<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Model;

class DocEmailPendingSend extends Model
{
    protected $table = 'doc_email_pending_send_view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'SRI_Key',
        'RUC',
        'commercial_name',
        'company_name',
        'patch_folder',
        'patch_logo',
        'can_send_email',
        'can_used_smtp',
        'smtp_server',
        'smtp_port',
        'smtp_email',
        'smtp_type_security',
        'smtp_user',
        'smtp_password',
        'document_receiver_name',
        'accept_terms_send',
        'send_to',
        'sent_cc',
        'day',
        'month',
        'year',
        'document_number',
        'document_type',
        'date_time_authorization',
        'amount'
    ];
}
