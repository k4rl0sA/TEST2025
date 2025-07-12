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
