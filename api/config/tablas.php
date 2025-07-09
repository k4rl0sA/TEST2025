<?php
// File: api/config/tablas.php

return [
    'usuarios' => [
        'pk'        => 'id_usuario',
        'fields'    => ['id_usuario', 'nombre', 'correo', 'perfil', 'subred', 'equipo', 'componente', 'estado', 'clave'],
        'editable'  => ['nombre', 'correo', 'perfil', 'subred', 'equipo', 'componente', 'estado'],
        'filters'   => ['nombre', 'correo', 'perfil', 'estado'],
        'order'     => ['nombre'],
        'hidden'    => ['clave'],
        'auth'      => ['roles' => ['admin', 'editor']]
    ],

    'geo_asig' => [
        'pk'        => 'id_asig',
        'fields'    => ['id_asig', 'idgeo', 'doc_asignado', 'usu_create', 'fecha_create', 'usu_update', 'fecha_update', 'estado'],
        'editable'  => ['doc_asignado', 'idgeo', 'estado'],
        'filters'   => ['doc_asignado', 'usu_create','idgeo', 'estado'],
        'order'     => ['fecha_create'],
        'hidden'    => [],
        'auth'      => ['roles' => ['admin']]
    ]
];
