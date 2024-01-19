<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Entity\Company;
use App\Drivers\StorageDriver;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createSeedersRol();
        $this->createSeedersCompany();
    }





    function createSeedersRol(): void
    {
        DB::unprepared('SET IDENTITY_INSERT roles ON');
        foreach (self::$rolesSeeders as $rolU) {

            DB::table('roles')->insert([
                'id' => $rolU[0],
                'name' => $rolU[1],
                'description' => $rolU[2],
                'created_user' => 0,
            ]);
        }
        DB::unprepared('SET IDENTITY_INSERT roles OFF');
    }
    function createSeedersCompany(): void
    {
        DB::unprepared('SET IDENTITY_INSERT roles ON');
        foreach (self::$compaySeeders as $company) {

            $patch_folder = StorageDriver::GetCompanyFolder($company[1]);

            DB::table('companies')->insert([
                'RUC' => $company[1],
                'company_name ' => $company[2],
                'commercial_name' => $company[3],
                'can_send_email' => false,
                'can_used_smtp' => false,
                'patch_folder' =>  $patch_folder,
                'patch_logo' => $company[4],
                'created_user' => 0,
                'smtp_email' => "",
                'smtp_server' => "",
                'smtp_port' => "",
                'smtp_type_security' => 0,
                'smtp_user' => "",
                'smtp_password' => "",
                'api_key' => "",
            ]);

            StorageDriver::CreateDirectory($patch_folder);
        }
        DB::unprepared('SET IDENTITY_INSERT roles OFF');
    }


    /*
    * Roles de usuario para seeder en la base de datos
    */
    static $rolesSeeders = [

        [2, 'test', 'Rol de prueba para desarrollo.']
    ];

    /*
    * Empresas para seeder en la base de datos
    */
    static $compaySeeders = [
        [2, '1713226221001', 'BrotCorp Panaderia Alemana', 'PINEDA CANDO RODRIGO SALOMON', 'https://azulser.com/wp-content/uploads/2020/05/brotcorp.jpg'],
        [3, '1793194242001', 'Macchiata Brunch y Pizza', 'ROCCVILL CORP S.A.S.', 'https://azulser.com/wp-content/uploads/2022/05/Macchiata.png'],
        [4, '0501510507001', 'MUCKIS RESTAURANT', 'JORG THOMAS CONTAG LEHMAN', 'https://azulser.com/wp-content/uploads/2020/05/MUCKIS.png'],
        [5, '1803865763001', 'Diosolopay', 'Garcia Lopez Maria Augusta', 'https://azulser.com/wp-content/uploads/2020/05/dioselopay.jpg'],
        [6, '1391931884001', 'HOSTERIA MANDALA', 'MARE S.A.S.', 'https://azulser.com/wp-content/uploads/2020/05/Hoster%C3%ADa-Mandala.jpg'],
        [7, '1792052130001', 'WESTERN BAR LOS CHILLOS', 'CARRILLO RECALDE CIA. LTDA.', 'https://azulser.com/wp-content/uploads/2020/05/western.jpg'],
        [8, '1300241161001', 'PUERTO ACAPULCO MARISQUERIA', 'MENDOZA MENDOZA JAIME NOE RAMON', 'https://azulser.com/wp-content/uploads/2020/05/Puerto-Acapulco.jpg'],
        [9, '1793176666001', '3500', 'ALTURAGOURMET S.A.S.', 'https://azulser.com/wp-content/uploads/2022/07/3500.png'],
        [10, '1708752777001', 'CHEZ MATHILDE', 'BOLEK COBO LUMIR ANDRES', 'https://azulser.com/wp-content/uploads/2020/05/CHEZ-MATHILDE.jpg'],
        [11, '0927005397001', 'El Arrecife de las Conchas', 'MATUTE RODRIGUEZ POLETTE DOMENICA', 'https://azulser.com/wp-content/uploads/2020/05/arrecife.jpg'],
        [12, '1001969797001', 'RESTAURANTE ANCORA', 'BOSMEDIANO VACA GEMA ADRIANA', 'https://azulser.com/wp-content/uploads/2020/05/Hotel-Ancora-Ecuador.jpg'],
        [13, '1726554288001', 'HOTEL ANCORA', 'RODRIGUEZ NICOLAS JAVIER', 'https://azulser.com/wp-content/uploads/2020/05/Hotel-Ancora-Ecuador.jpg'],
        [14, '1718447863001', 'STOP CAFETERIA RESTAURANTE', 'JURADO POZO MARIA BELEN', 'https://azulser.com/wp-content/uploads/2020/05/STOPCafeteria-.jpg'],
        [15, '1705389961001', 'IBIZA MOTEL LOUNGE', 'DONOSO GONZALEZ ALBA CONSUELO', 'https://azulser.com/wp-content/uploads/2020/05/Ibiza-Motel-Lounge.jpg'],
        [16, '1790890155001', 'LA GAVIOTA AZUL', 'DAMICHEL CIA. LTDA.', 'https://azulser.com/wp-content/uploads/2020/05/Motel-Gaviota-Azul.jpg'],
        [17, '1722662333001', 'Restaurante La Delicia', 'VALLEJO MADERA LUISA MAGDALENA', 'https://azulser.com/wp-content/uploads/2022/07/La-Delicia.png'],
        [18, '1759992595001', 'GITANOS BRUNCH', 'PEREZ BETANCOURT HABRAN JOSE', 'https://azulser.com/wp-content/uploads/2022/08/GitanosBrunch.png'],
        [20, '2100486915001', 'LUCAS WINGS', 'VELASQUEZ VEGA ANA CAROLINA', 'https://azulser.com/wp-content/uploads/2022/08/LucasWings.png'],
        [21, '1718754318001', 'RUMILOMA LODGE', 'AGILA ORDONEZ JUAN CARLOS', 'https://azulser.com/wp-content/uploads/2022/09/Rumiloma2022.png'],
        [22, '1716914120001', 'CHICAGO STYLE PIZZA', 'CAZARES GOMEZ MARIA GABRIELA', 'https://azulser.com/wp-content/uploads/2020/05/Georgina.jpg'],
        [23, '1792933404001', 'EL POLLO FORASTERO', 'SOCIEDAD DE HECHO FEDAMEL', 'https://azulser.com/wp-content/uploads/2022/08/Forastero.png'],
        [24, '1793190464001', 'BRUNCH MODELO EXPRESS', 'MODEBRUNCH', '/logos/'],
        [26, '1705965539001', 'CafeOMI', 'PASQUEL ROMERO OSWALDO ROBERTO', 'https://azulser.com/wp-content/uploads/2020/05/OMI.jpg'],
        [27, '1714331996001', 'ALO PAELLA', 'LLANOS VELOZ BOLIVAR PATRICIO', 'https://azulser.com/wp-content/uploads/2022/09/AloPaellas.png'],
        [28, '0101812972001', 'VICTOR HUGO HOTEL PUERTO LOPEZ', 'NIVELO VILLALTA VICTOR ALBERTO', 'https://azulser.com/wp-content/uploads/2020/05/V%C3%ADctor-Hugo-Hotel.jpg'],
        [29, '1001354677001', 'LA PARRILLA DEL CHE', 'PITA SEVILLA EFREN HONORATO', 'https://azulser.com/wp-content/uploads/2020/05/PARRILLA-DEL-CHE.jpg'],
        [30, '1721560215001', 'Marisqueria los Popeyitos', 'SUAREZ MOREIRA SANDRA MARIA', 'https://azulser.com/wp-content/uploads/2022/09/Popeyitos.jpg'],
        [31, '1707294409001', 'POLLO REGALON', 'ANDRADE VASQUEZ JHOANA CRISTINA', 'https://azulser.com/wp-content/uploads/2022/09/PolloRegalon.jpg'],
        [32, '1718525064001', 'LA CAPILLA', 'CASTILLO LLIVIGANAY CECILIA DEL CARMEN', '/logos/'],
        [33, '1716960719001', 'CAFE UTE', 'MERA ROBLES MONICA PATRICIA', 'https://azulser.com/wp-content/uploads/2022/09/CafeUTE.png'],
        [34, '1722824735001', 'SALCERON UNO', 'GUASCO CEDENO NEY FABIAN', 'https://azulser.com/wp-content/uploads/2020/05/SALCERON.jpg'],
        [35, '1793191300001', 'CUXARA', 'CUXARARTESANAL S.A.S.', 'https://azulser.com/wp-content/uploads/2022/10/CUXARA.png'],
        [36, '1702091289001', 'LUIS DOMINGUEZ', 'DOMINGUEZ HERRERA LUIS HUMBERTO', 'https://azulser.com/wp-content/uploads/2022/10/LUIS-DOMINGUEZ.png'],
        [37, '0100999838001', 'EULALIA FONTANA', 'FONTANA ZAMORA EULALIA ALCIRA', 'https://azulser.com/wp-content/uploads/2022/10/EULALIA-FONTANA.png'],
        [38, '1710052851001', 'LA CARNICERIA', 'JARAMILLO DAVILA MARCO NICOLAS', 'https://azulser.com/wp-content/uploads/2022/10/La-Carniceria.png'],
        [39, '1714918859001', 'HOSTERIA SAGUAMBY', 'PROANO PROANO ADRIAN WLADIMIR', 'https://azulser.com/wp-content/uploads/2022/11/Saguamby.jpg'],
        [40, '1002263760001', 'JOSE EL CAPITAN CANGREJO', 'PASQUEL CAZAR PATRICIA ANGELITA', 'https://azulser.com/wp-content/uploads/2020/05/Jose-Capitán-Cangrejo.jpg'],
        [41, '1791774450001', 'PISCIANDES S.A.', 'PISCICOLA DE LOS ANDES PISCIANDES S.A.', 'https://azulser.com/wp-content/uploads/2021/03/PISCIANDES.png'],
        [42, '1105068033001', 'SIERRA VIRGEN', 'CEVALLOS GONZALEZ JUAN HERNAN', 'https://azulser.com/wp-content/uploads/2022/09/Sierra_Virgen.jpg'],
        [43, '1713436168001', 'ZAP SECURITY', 'PONTON FUENTES MARCELO FABIAN', 'https://azulser.com/wp-content/uploads/2022/10/ZAP-SECURITY.png'],
        [44, '1724306525001', 'POLLOS EL GORDITO', 'LINARES ALBORNOZ ABRAHAN VINICIO', 'https://azulser.com/wp-content/uploads/2022/10/PollosElGordito.png'],
        [45, '1722213871001', 'CASA COLONIAL', 'ARROYO ESPINOSA DENNYS CAROLINA', '/logos/'],
        [46, '1712417615001', 'LA EXQUISITA', 'CAMINO ATTI FRANCISCO ORLANDO', 'https://azulser.com/wp-content/uploads/2022/12/La-Exquisita.png'],
        [47, '1002313417001', 'PANADERIA', 'SANCHEZ GUEVARA HUGO RENE', '/logos/'],
        [48, '0701460115001', 'LA POSADA HOTEL', 'ROJAS CASTILLO SILVIA', 'https://azulser.com/wp-content/uploads/2022/11/Hotel-La-Posada.png'],
        [49, '1709694416001', 'CEVICHERIA VIEJO BOLO', 'MERIZALDE HORTENCIO BOLIVAR', 'https://azulser.com/wp-content/uploads/2022/12/Viejo-Bolo.png'],
        [50, '1724306517001', 'EL GORDITO', 'LINARES ALBORNOZ HEYDI MARGARITA', 'https://azulser.com/wp-content/uploads/2022/10/PollosElGordito.png'],
        [51, '1707967632001', 'RESTAURANTE YARAVI', 'VASQUEZ ROJAS JULIA MAGDALENA', 'https://azulser.com/wp-content/uploads/2020/05/Yaraví.jpg'],
        [52, '1722519400001', 'POLLOS EL GORDITO', 'LINARES ALBORNOZ MARTIN HOMERO', 'https://azulser.com/wp-content/uploads/2022/10/PollosElGordito.png'],
        [53, '1711684892001', 'CEVICHERIA EL VIEJO BOLO', 'CANDO TENESACA CARMEN AMELIA', 'https://azulser.com/wp-content/uploads/2022/12/Viejo-Bolo.png'],
        [54, '1705410403001', 'VHD RICOS HELADOS', 'ZHINDON MATUTE MARIA AZUCENA', 'https://azulser.com/wp-content/uploads/2022/12/RICOS-HELADOS.png'],
        [55, '1715738538001', 'CHEZ MATHILDE', 'MERINO JIMENEZ VALERIA ALEJANDRA', 'https://azulser.com/wp-content/uploads/2020/05/CHEZ-MATHILDE.jpg'],
        [56, '1101170494001', 'ORO NEGRO', 'CASTILLO ROSALES MARIA EUFEMIA', 'https://azulser.com/wp-content/uploads/2022/12/Oro-Negro.png'],
        [57, '1707514145001', 'VHD RICOS HELADOS', 'DIAZ CEVALLOS VICTOR HUGO', 'https://azulser.com/wp-content/uploads/2022/12/RICOS-HELADOS.png'],
        [58, '1718328659001', 'CASETA MANABITA', 'BARRETO ARTEAGA YESENIA ELIZABETH', '/logos/'],
        [59, '1716682065001', 'ENCUENTRO MANABITA', 'BASURTO ZAMBRANO YIPSON ALEXANDER', 'https://azulser.com/wp-content/uploads/2022/12/ENCUENTRO-MANABITA.png'],
        [60, '1709092082001', 'HELADERIA FONTANA', 'DOMINGUEZ FONTANA KARINA EULALIA', 'https://azulser.com/wp-content/uploads/2022/06/Fontana-1.png'],
        [61, '1103503866001', 'LA TRADICION', 'FABIAN JOSE CASTILLO LLIVIGANAY', 'https://azulser.com/wp-content/uploads/2022/12/LA-TRADICION.png'],
        [62, '0803745876001', 'FROZZEN AND COFFE', 'SOSA BARRIO YAHELY CRISTINA', '/logos/'],
        [63, '1710139096001', 'HELADERIA FONTANA', 'DOMINGUEZ FONTANA ALINA DE LOURDES', '/logos/'],
        [64, '1791844823001', 'EMPORIO LUXURY MOTELS', 'EMPOLUX EMPORIO LUXURY MOTELS CIA. LTDA.', '/logos/'],
        [65, '0912803293001', 'LE CAFE', 'NEUMANE PLAZA MICHELLE STELLA', '/logos/'],
        [66, '1709538134001', 'EL LENADOR PIZZERIA Y PARRILLADAS', 'GRIJALVA CUAMACAS JANNETH DEL ROCIO', '/logos/'],
        [67, '1793202831001', 'POLLO REGALON', 'MIKHUY S.A.S.', '/logos/'],
        [68, '1726077991001', 'GEORGINA CHICAGO STYLE PIZZA', 'CAMILA ANTONELLA ALVARO MUNOZ', '/logos/'],
        [70, '1703919835001', 'EL LENADOR PIZZERIA Y PARRILLADAS', 'PENA SALAZAR JAIME RODRIGO', '/logos/'],
        [71, '1793203958001', 'GAUDEO', 'GAUDEO S.A.S.', '/logos/'],
        [72, '1305197764001', 'CEVICHERIA SALSERON NUMERO DOS', 'CEDENO PARRAGA MARIANA ASTREA', '/logos/'],
        [74, '0802295550001', 'L ITALIANO', 'CARRASCO MONGE ZAIDA CLEOPATRA', '/logos/'],
        [76, '1591727253001', 'MAMALLACTA PARAMO LODGE', 'MAMALLACTA S.A.S.', '/logos/'],
        [77, '1710403930001', 'PAPA&PARRILLA', 'LEMA SINAILIN ELSA SARITA', '/logos/'],
        [78, '0104756143001', 'DANES PANADERIA Y PASTELERIA', 'MASACHE MASACHE MIRELLA DEL CISNE', '/logos/'],
        [79, '1712734704001', 'LA NATALIA', 'VACA GALINDO MARIA FERNANDA', '/logos/'],
        [81, '0993382684001', 'MECICONS', 'MECICONS S.A.S.', '/logos/'],
        [82, '1711208627001', 'HOTEL DEL PACIFICO', 'SACHERI RAMIA JUDY MARISOL', '/logos/'],
        [83, '1793205810001', 'LA CHAZCA', 'LA CHAMIZA S.A.', '/logos/'],
        [84, '1750773747001', 'LA TRADICION', 'MARIA JOSE CASTILLO LLIVIGANAY', '/logos/'],
        [85, '1721294963001', 'CHILLIN GROUP EC', 'CABEZAS MORA GONZALO FERNANDO', '/logos/'],
        [86, '1721223715001', 'HELADERIA FONTANA', 'FIGUEROA DOMINGUEZ CAMILA MICHELA', '/logos/'],
        [87, '1722720610001', 'POLLOS EL GORDITO', 'LINARES VILLALVA GINNA FERNANDA', '/logos/'],
        [88, '1103945000001', 'LA TABERNITA RESTOBAR', 'TORRES ROMERO HOLGER VICENTE', '/logos/'],
        [89, '1314039148001', 'NAHOMY', 'LUCAS PINCAY LEONEL ANTONIO', '/logos/']
    ];
};
