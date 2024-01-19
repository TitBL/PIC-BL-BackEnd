<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Attributes\Description;
use Illuminate\Support\Collection;

/**
 * Class Permissions
 * This class defines a set of permissions using enums for managing access control in the application.
 * 
 * @method static static Config_Roles_visualizar()
 * @method static static Config_Roles_crear()
 * @method static static Config_Roles_editar()
 * @method static static Config_Roles_hab_des()
 * @method static static Config_param_gene()
 * @method static static Config_Empresa_visualizar()
 * @method static static Config_Empresa_crear()
 * @method static static Config_Empresa_editar()
 * @method static static Config_Empresa_hab_des()
 * @method static static Config_Usuario_visualizar()
 * @method static static Config_Usuario_crear()
 * @method static static Config_Usuario_editar()
 * @method static static Config_Usuario_hab_des()
 * @method static static Config_Usuario_asignar_empresas()
 * @method static static MyDoc_Recibidos_viualizar()
 * @method static static MyDoc_Recibidos_descargar()
 * @method static static MyDoc_Recibidos_reenviar()
 * @method static static EmpresaDoc_Recibidos_visualizar()
 * @method static static EmpresaDoc_Recibidos_descargar()
 * @method static static EmpresaDoc_Recibidos_reenviar()
 * @method static static EmpresaDoc_Emitidos_visualizar()
 * @method static static EmpresaDoc_Emitidos_descargar()
 * @method static static EmpresaDoc_Emitidos_reenviar()
 * @method static static Reportes_Emitidos_1()
 * @method static static Reportes_Emitidos_2()
 * 
 * @package App\Enums
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
final class Permissions extends Enum
{
    #[Description('Puede visualizar Rol.')]
    const Config_Roles_visualizar = 10001;

    #[Description('Puede crear un nuevo Rol.')]
    const Config_Roles_crear = 10002;

    #[Description('Puede editar el ROL seleccionado.')]
    const Config_Roles_editar = 10003;

    #[Description('Puede habilitar/deshabilitar Rol.')]
    const Config_Roles_hab_des = 10004;

    #[Description('Puede editar los parámetros generales.')]
    const Config_param_gene = 10101;

    #[Description('Puede visualizar empresa.')]
    const Config_Empresa_visualizar = 10201;

    #[Description('Puede crear una nueva empresa.')]
    const Config_Empresa_crear = 10202;

    #[Description('Puede editar la empresa seleccionado.')]
    const Config_Empresa_editar = 10203;

    #[Description('Puede habilitar/deshabilitar empresa.')]
    const Config_Empresa_hab_des = 10204;

    #[Description('Puede visualizar usuario.')]
    const Config_Usuario_visualizar = 10301;

    #[Description('Puede crear un nuevo usuario.')]
    const Config_Usuario_crear = 10302;

    #[Description('Puede editar el usuario seleccionado.')]
    const Config_Usuario_editar = 10303;

    #[Description('Puede habilitar/deshabilitar usuario.')]
    const Config_Usuario_hab_des = 10304;

    #[Description('Puede asignar empresas a usuario.')]
    const Config_Usuario_asignar_empresas = 10305;

    #[Description('Puede visualizar los documentos recibidos.')]
    const MyDoc_Recibidos_viualizar = 20101;

    #[Description('Puede descargar los documentos recibidos.')]
    const MyDoc_Recibidos_descargar = 20102;

    #[Description('Puede enviar por email los documentos recibidos.')]
    const MyDoc_Recibidos_reenviar = 20103;

    #[Description('Puede visualizar los documentos recibidos.')]
    const EmpresaDoc_Recibidos_visualizar = 30101;

    #[Description('Puede descargar los documentos recibidos.')]
    const EmpresaDoc_Recibidos_descargar = 30102;

    #[Description('Puede enviar por email los documentos recibidos.')]
    const EmpresaDoc_Recibidos_reenviar = 30103;

    #[Description('Puede visualizar los documentos emitidos.')]
    const EmpresaDoc_Emitidos_visualizar = 30201;

    #[Description('Puede descargar los documentos emitidos.')]
    const EmpresaDoc_Emitidos_descargar = 30202;

    #[Description('Puede re-enviar por email los documentos emitidos.')]
    const EmpresaDoc_Emitidos_reenviar = 30203;

    #[Description('Por definir.')]
    const Reportes_Emitidos_1 = 40101;

    #[Description('Por definir.')]
    const Reportes_Emitidos_2 = 40201;

    /**
     * Get the list of permissions for the master role.
     *
     * @return Collection
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function getListMasterPermissions(): Collection
    {
        return self::transformPermissions(self::$permisos);
    }

    /**
     * Get the list of permissions for the consumer role.
     *
     * @return Collection
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function getListConsumerPermissions(): Collection
    {
        $filteredPermissions = array_filter(self::$permisos, function ($permiso) {
            return end($permiso) === 'C';
        });

        return self::transformPermissions($filteredPermissions);
    }

    /**
     * Get the list of all permissions.
     *
     * @return Collection
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public static function getAllPermissions(): Collection
    {
        $filteredPermissions = array_filter(self::$permisos, function ($permiso) {
            return end($permiso) !== 'R';
        });

        return self::transformPermissions($filteredPermissions);
    }

    /**
     * Transform the format of permissions.
     *
     * @param array $permissions
     * @return Collection
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    private static function transformPermissions(array $permissions): Collection
    {
        $transformedPermissions = collect();

        foreach ($permissions as $permiso) {
            $transformedPermissions->push([
                'id' => $permiso[0],
                'modulo' => $permiso[1],
                'seccion' => $permiso[2],
                'permiso' => $permiso[3],
            ]);
        }

        return $transformedPermissions;
    }

    // Default permissions for the database
    private static $permisos = [
        [Permissions::Config_Roles_visualizar, 'Configuraciones', 'Roles y Permisos', 'Puede visualizar Rol.', 'R'],
        [Permissions::Config_Roles_crear, 'Configuraciones', 'Roles y Permisos', 'Puede crear un nuevo Rol.', 'R'],
        [Permissions::Config_Roles_editar, 'Configuraciones', 'Roles y Permisos', 'Puede editar el ROL seleccionado.', 'R'],
        [Permissions::Config_Roles_hab_des, 'Configuraciones', 'Roles y Permisos', 'Puede habilitar/deshabilitar Rol.', 'R'],
        [Permissions::Config_param_gene, 'Configuraciones', 'Parámetros', 'Puede editar los parámetros generales.', 'R'],
        [Permissions::Config_Empresa_visualizar, 'Configuraciones', 'Empresa', 'Puede visualizar empresa.', 'P'],
        [Permissions::Config_Empresa_crear, 'Configuraciones', 'Empresa', 'Puede crear una nueva empresa.', 'P'],
        [Permissions::Config_Empresa_editar, 'Configuraciones', 'Empresa', 'Puede editar la empresa seleccionado.', 'P'],
        [Permissions::Config_Empresa_hab_des, 'Configuraciones', 'Empresa', 'Puede habilitar/deshabilitar empresa.', 'P'],
        [Permissions::Config_Usuario_visualizar, 'Configuraciones', 'Usuario', 'Puede visualizar usuario.', 'P'],
        [Permissions::Config_Usuario_crear, 'Configuraciones', 'Usuario', 'Puede crear un nuevo usuario.', 'P'],
        [Permissions::Config_Usuario_editar, 'Configuraciones', 'Usuario', 'Puede editar el usuario seleccionado.', 'P'],
        [Permissions::Config_Usuario_hab_des, 'Configuraciones', 'Usuario', 'Puede habilitar/deshabilitar usuario.', 'P'],
        [Permissions::Config_Usuario_asignar_empresas, 'Configuraciones', 'Usuario', 'Puede asignar empresas a usuario.', 'P'],
        [Permissions::MyDoc_Recibidos_viualizar, 'Mis Documentos', 'Recibidos', 'Puede visualizar los documentos recibidos.', 'C'],
        [Permissions::MyDoc_Recibidos_descargar, 'Mis Documentos', 'Recibidos', 'Puede descargar los documentos recibidos.', 'C'],
        [Permissions::MyDoc_Recibidos_reenviar, 'Mis Documentos', 'Recibidos', 'Puede enviar por email los documentos recibidos.', 'C'],
        [Permissions::EmpresaDoc_Recibidos_visualizar, 'Documentos Empresas', 'Recibidos', 'Puede visualizar los documentos recibidos.', 'P'],
        [Permissions::EmpresaDoc_Recibidos_descargar, 'Documentos Empresas', 'Recibidos', 'Puede descargar los documentos recibidos.', 'P'],
        [Permissions::EmpresaDoc_Recibidos_reenviar, 'Documentos Empresas', 'Recibidos', 'Puede enviar por email los documentos recibidos.', 'P'],
        [Permissions::EmpresaDoc_Emitidos_visualizar, 'Documentos Empresas', 'Emitidos', 'Puede visualizar los documentos emitidos.', 'P'],
        [Permissions::EmpresaDoc_Emitidos_descargar, 'Documentos Empresas', 'Emitidos', 'Puede descargar los documentos emitidos.', 'P'],
        [Permissions::EmpresaDoc_Emitidos_reenviar, 'Documentos Empresas', 'Emitidos', 'Puede re-enviar por email los documentos emitidos.', 'P'],
        [Permissions::Reportes_Emitidos_1, 'Reportes', 'Emitidos', 'Por definir.', 'P'],
        [Permissions::Reportes_Emitidos_2, 'Reportes', 'Emitidos', 'Por definir.', 'P'],
    ];
}
