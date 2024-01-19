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
            CREATE PROCEDURE sp_get_doc_issuer_received
                @idusuario bigint,
                @startdate DATE,
                @enddate DATE
            AS
            BEGIN
                SELECT
                    SUBSTRING(Tr.key_access_sri, 5,4) as year,
                    SUBSTRING(Tr.key_access_sri, 3,2) as month,
                    SUBSTRING(Tr.key_access_sri, 1,2) as day,
                    Tr.emission_date,
                    Tr.date_time_authorization,
                    CONCAT( CONCAT(C.commercial_name, ' - '), C.company_name) AS 'document_issuer',
                    Tr.document_type,
                    Tr.document_number,
                    Tr.key_access_sri
                FROM
                    transactions Tr  
                INNER JOIN companies C ON Tr.document_issuer_id = C.id
                WHERE Tr.document_receiver_id =@idusuario AND Tr.emission_date BETWEEN @startdate AND @enddate;
            END;
        ");

        DB::statement("
            CREATE PROCEDURE sp_get_doc_company_received
                @idcompany bigint,
                @startdate DATE,
                @enddate DATE
            AS
            BEGIN
				DECLARE @idusuario INT;

				SELECT @idusuario = U.id
				FROM users U
				INNER JOIN companies C ON U.DNI = C.RUC
				WHERE C.id = @idcompany;

				SELECT
                    SUBSTRING(Tr.key_access_sri, 5,4) as year,
                    SUBSTRING(Tr.key_access_sri, 3,2) as month,
                    SUBSTRING(Tr.key_access_sri, 1,2) as day,
                    Tr.emission_date,
                    Tr.date_time_authorization,
                    CONCAT( CONCAT(C.commercial_name, ' - '), C.company_name) AS 'document_issuer',
                    Tr.document_type,
                    Tr.document_number,
                    Tr.key_access_sri
                FROM
                    transactions Tr  
                INNER JOIN companies C ON Tr.document_issuer_id = C.id

				WHERE Tr.document_receiver_id=@idusuario AND Tr.emission_date BETWEEN @startdate AND @enddate;
            END;
        ");

        DB::statement("
            CREATE PROCEDURE sp_get_doc_company_issuer
                @idcompany bigint,
                @startdate DATE,
                @enddate DATE
            AS
            BEGIN
				SELECT
                    SUBSTRING(Tr.key_access_sri, 5,4) as year,
                    SUBSTRING(Tr.key_access_sri, 3,2) as month,
                    SUBSTRING(Tr.key_access_sri, 1,2) as day,
                    Tr.emission_date,
                    Tr.date_time_authorization,
                    Tr.document_receiver_name AS 'document_receiver',
                    Tr.document_type,
                    Tr.document_number,
                    Tr.key_access_sri
                FROM
                    transactions Tr

				WHERE Tr.document_issuer_id=@idcompany AND Tr.emission_date BETWEEN @startdate AND @enddate;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE sp_get_doc_issuer_received');
        DB::statement('DROP PROCEDURE sp_get_doc_company_received');
        DB::statement('DROP PROCEDURE sp_get_doc_company_issuer');
    }
};
