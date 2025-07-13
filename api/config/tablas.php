<?php
// File: api/config/tablas.php

return [
   'usuarios' => [
        'fields' => ['id_usuario', 'nombre', 'correo', 'perfil', 'subred', 'equipo', 'componente', 'estado'],
        'editable' => ['nombre', 'correo', 'perfil', 'subred', 'equipo', 'componente', 'estado', 'clave'],
        'hidden' => ['clave'],
        'order' => ['id_usuario', 'nombre', 'correo'],
        'filters' => ['nombre', 'correo', 'perfil', 'subred', 'equipo', 'componente'],
        'validation' => [
            'crear' => [
                'nombre' => ['required', 'string', 'max:100'],
                'correo' => ['required', 'email', 'unique:usuarios', 'max:100'],
                'clave' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
                'perfil' => ['required', 'string', 'in:admin,user,supervisor']
            ],
            'actualizar' => [
                'correo' => ['sometimes', 'email', 'unique:usuarios', 'max:100'],
                'clave' => ['sometimes', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
                'perfil' => ['sometimes', 'string', 'in:admin,user,supervisor']
            ]
        ],
        'callbacks' => [
            'before_create' => 'hashPassword',
            'before_update' => 'hashPassword'
        ]
    ],
    'geo_gest' => [
        'fields' => [
            'id_ges', 'idgeo', 'direccion_nueva', 'vereda_nueva', 
            'cordxn', 'cordyn', 'estado_v', 'motivo_estado', 
            'usu_creo', 'fecha_create', 'usu_update', 'fecha_update', 'estado'
        ],
        'editable' => [
            'direccion_nueva', 'vereda_nueva', 'cordxn', 'cordyn', 
            'estado_v', 'motivo_estado', 'usu_update', 'estado'
        ],
        'hidden' => [],
        'order' => ['id_ges', 'fecha_create', 'idgeo'],
        'filters' => ['direccion_nueva', 'vereda_nueva', 'estado_v', 'usu_creo', 'estado'],
        'validation' => [
            'crear' => [
                'idgeo' => ['required', 'integer'],
                'estado_v' => ['required', 'string', 'max:3'],
                'usu_creo' => ['required', 'string', 'max:18']
            ],
            'actualizar' => [
                'estado_v' => ['sometimes', 'string', 'max:3'],
                'motivo_estado' => ['sometimes', 'string', 'max:3']
            ]
        ],
        'callbacks' => [
            'before_create' => 'setGeoGestDefaults',
            'before_update' => 'setUpdateInfo'
        ],
        'primary_key' => ['idgeo', 'estado_v', 'usu_creo'] // Clave primaria compuesta
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
