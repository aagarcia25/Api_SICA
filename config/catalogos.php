<?php

// config/catalogos.php
return [
    'Cat_Escolaridad' => [
        'id' => 'id',
        'fields' => [
            'Nombre' => 'string,200', // Nombre de la escolaridad
            'deleted' => 'tinyInteger',
            'UltimaActualizacion' => 'datetime',
            'FechaCreacion' => 'datetime',
            'ModificadoPor' => 'char,36',
            'CreadoPor' => 'char,36',
        ],
        'unique' => ['Nombre'], // Clave única por nombre
    ],
    'Cat_Institucion_Educativa' => [
        'id' => 'id',
        'fields' => [
            'Nombre' => 'string,200', // Nombre de la institución educativa
            'deleted' => 'tinyInteger',
            'UltimaActualizacion' => 'datetime',
            'FechaCreacion' => 'datetime',
            'ModificadoPor' => 'char,36',
            'CreadoPor' => 'char,36',
        ],
        'unique' => ['Nombre'], // Clave única por nombre
    ],



    // otros catálogos
];
