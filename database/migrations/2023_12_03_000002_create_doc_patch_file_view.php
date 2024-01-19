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
        /* crea una funcion scalar*/
        DB::statement("
           CREATE FUNCTION dbo.GetFolderPath
            (
                @key_access_sri VARCHAR(255),
                @extension VARCHAR(255)
            )
            RETURNS VARCHAR(255)
            AS
            BEGIN
                DECLARE @FolderPath VARCHAR(255);

                SET @FolderPath = CONCAT(
                    SUBSTRING(@key_access_sri, 5, 4),
                    '/',
                    SUBSTRING(@key_access_sri, 3, 2),
                    '/',
                    SUBSTRING(@key_access_sri, 1, 2),
                    '/',
                    @key_access_sri,
                    @extension
                );

                RETURN @FolderPath;
            END;
        ");

        DB::statement("
            CREATE VIEW doc_patch_file_view AS
            SELECT 
                N.key_access_sri AS SRI_Key,  
                CONCAT( C.patch_folder,dbo.GetFolderPath(N.key_access_sri, '.pdf') ) as PDF_Patch,
                CONCAT( C.patch_folder,dbo.GetFolderPath(N.key_access_sri, '.xml') ) as XML_Patch
            FROM dbo.transactions as N 
            INNER JOIN
                dbo.companies AS C ON N.document_issuer_id = C.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS doc_patch_file_view');
        DB::statement('DROP FUNCTION IF EXISTS dbo.GetFolderPath');
    }
};
