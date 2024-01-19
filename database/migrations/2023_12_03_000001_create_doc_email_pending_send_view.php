<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
  
        DB::statement("
            CREATE VIEW doc_email_pending_send_view AS
            SELECT
                N.id,
                N.id_trasaction AS SRI_Key,
                C.RUC,
                C.commercial_name,
                C.company_name,
                C.patch_folder, 
                C.patch_logo,
                C.can_send_email,
                C.can_used_smtp,
                C.smtp_server,
                C.smtp_port,
                C.smtp_email,
                C.smtp_type_security,
                C.smtp_user,
                C.smtp_password,
                Tr.document_receiver_name,
                COALESCE(U.terms_accept, 0) AS accept_terms_send,
                N.send_to, 
                N.sent_cc,                
                SUBSTRING(N.id_trasaction, 1,2) as day,
                SUBSTRING(N.id_trasaction, 3,2) as month,
                SUBSTRING(N.id_trasaction, 5,4) as year,
                Tr.document_number,
                Tr.document_type,
                Tr.date_time_authorization,
                Tr.amount
            FROM
                (
                    SELECT  
                        id,
                        reference AS id_trasaction,
                        send_to,
                        sent_cc
                    FROM
                        email_notifications
                    WHERE 
                        send_type = 1
                        AND state = 'PENDING SEND'
                ) AS N
            INNER JOIN transactions Tr ON N.id_trasaction = Tr.key_access_sri
            INNER JOIN companies C ON Tr.document_issuer_id = C.id
            LEFT JOIN (
                SELECT user_id, terms_accept
                FROM users_terms_conditions
                WHERE terms_accept = 1
            ) U ON Tr.document_receiver_id = U.user_id
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        DB::statement('DROP VIEW IF EXISTS doc_email_pending_send_view');
    }
};
