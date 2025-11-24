-- Tabla para el módulo adoleMas.php
-- ADOLESCENTES ENTRE 12 Y 17 AÑOS, DISFUNCIÓN FAMILIAR Y CONSUMO DE SPA / PREVENCIÓN EN SSR

CREATE TABLE `vsp_adoleMas` (
  `id_adoleMas` int(11) NOT NULL AUTO_INCREMENT,
  `idpeople` int(11) NOT NULL COMMENT 'ID de la persona',
  `fecha_seg` date DEFAULT NULL COMMENT 'Fecha Seguimiento',
  `numsegui` varchar(3) NOT NULL COMMENT 'Seguimiento N°',
  `evento` varchar(3) NOT NULL COMMENT 'Evento',
  `estado_s` varchar(3) DEFAULT NULL COMMENT 'Estado',
  `motivo_estado` varchar(3) DEFAULT NULL COMMENT 'Motivo de Estado',
  `tipo_caso` varchar(2) DEFAULT NULL COMMENT 'Tipo de Población (idcatalogo=197)',
  `etapa` varchar(3) DEFAULT NULL COMMENT 'Etapa (idcatalogo=136)',
  
  -- Sección: ADOLESCENTES ENTRE 12 Y 17 AÑOS, DISFUNCIÓN FAMILIAR Y CONSUMO DE SPA
  `asis_ctrpre` varchar(2) DEFAULT NULL COMMENT 'Entrevista motivacional (idcatalogo=170)',
  `exam_lab` varchar(2) DEFAULT NULL COMMENT 'Apropiación de prácticas saludables (idcatalogo=170)',
  `esqu_vacuna` varchar(2) DEFAULT NULL COMMENT 'Involucramiento parental (idcatalogo=170)',
  `cons_micronutr` varchar(2) DEFAULT NULL COMMENT 'Fortalecimiento de autonomía Reproductiva (idcatalogo=170)',
  `avance_habilidades` varchar(2) DEFAULT NULL COMMENT 'Se identifica avance en el fortalecimiento de habilidades socio emocionales (idcatalogo=170)',
  
  -- Sección: ADOLESCENTES ENTRE 12 Y 17 AÑOS, DISFUNCIÓN FAMILIAR Y PREVENCIÓN EN SSR
  `educ_sexualidad` varchar(2) DEFAULT NULL COMMENT 'Educación integral para la sexualidad en el adolescente (idcatalogo=170)',
  `dialogo_familiar` varchar(2) DEFAULT NULL COMMENT 'Dialogo interfamiliar (idcatalogo=170)',
  `autonomia_reproductiva` varchar(2) DEFAULT NULL COMMENT 'Fortalecimiento de autonomía Reproductiva (idcatalogo=170)',
  `seguim_planificacion` varchar(2) DEFAULT NULL COMMENT 'Seguimiento a acceso a método de planificación familiar (idcatalogo=170)',
  `otros_riesgos_sm` varchar(2) DEFAULT NULL COMMENT 'Se identifican otros riesgos en SM (idcatalogo=170)',
  
  -- Sección: INFORMACIÓN ACCIONES
  `estrategia_1` varchar(3) DEFAULT NULL COMMENT 'Estrategia PF_1 (idcatalogo=90)',
  `estrategia_2` varchar(3) DEFAULT NULL COMMENT 'Estrategia PF_2 (idcatalogo=90)',
  `acciones_1` varchar(3) DEFAULT NULL COMMENT 'Acción 1 (idcatalogo=22)',
  `desc_accion1` varchar(3) DEFAULT NULL COMMENT 'Descripción Acción 1 (idcatalogo=75)',
  `acciones_2` varchar(3) DEFAULT NULL COMMENT 'Acción 2 (idcatalogo=22)',
  `desc_accion2` varchar(3) DEFAULT NULL COMMENT 'Descripción Acción 2 (idcatalogo=75)',
  `acciones_3` varchar(3) DEFAULT NULL COMMENT 'Acción 3 (idcatalogo=22)',
  `desc_accion3` varchar(3) DEFAULT NULL COMMENT 'Descripción Acción 3 (idcatalogo=75)',
  `activa_ruta` varchar(2) DEFAULT NULL COMMENT 'Ruta Activada (idcatalogo=170)',
  `ruta` varchar(3) DEFAULT NULL COMMENT 'Ruta (idcatalogo=79)',
  `novedades` varchar(3) DEFAULT NULL COMMENT 'Novedades (idcatalogo=77)',
  `signos_covid` varchar(2) DEFAULT NULL COMMENT '¿Signos y Síntomas para Covid19? (idcatalogo=170)',
  `caso_afirmativo` varchar(500) DEFAULT NULL COMMENT 'Relacione Cuales signos y sintomas, Y Atención Recibida Hasta el Momento',
  `otras_condiciones` varchar(500) DEFAULT NULL COMMENT 'Otras Condiciones de Riesgo que Requieren una Atención Complementaria',
  `observaciones` varchar(1500) DEFAULT NULL COMMENT 'Observaciones',
  
  -- Sección: CIERRE DE CASO
  `cierre_caso` varchar(2) DEFAULT NULL COMMENT 'Cierre de Caso (idcatalogo=170)',
  `motivo_cierre` varchar(2) DEFAULT NULL COMMENT 'Motivo Cierre (idcatalogo=198)',
  `fecha_cierre` date DEFAULT NULL COMMENT 'Fecha de Cierre',
  `aplica_tamiz` varchar(2) DEFAULT NULL COMMENT 'Aplica Tamizaje Cope (idcatalogo=170)',
  `liker_dificul` varchar(3) DEFAULT NULL COMMENT 'Liker de Dificultades (idcatalogo=78)',
  `liker_emocion` varchar(3) DEFAULT NULL COMMENT 'Liker de Emociones (idcatalogo=78)',
  `liker_decision` varchar(3) DEFAULT NULL COMMENT 'Liker de Decisiones (idcatalogo=78)',
  `cope_afronta` varchar(3) DEFAULT NULL COMMENT 'Total Afrontamiento (idcatalogo=140)',
  `cope_evitacion` varchar(3) DEFAULT NULL COMMENT 'Total Evitación (idcatalogo=141)',
  `incremen_afron` varchar(3) DEFAULT NULL COMMENT 'Se Evidencia Incremento Estrategias de Afrontamiento (idcatalogo=142)',
  `incremen_evita` varchar(3) DEFAULT NULL COMMENT 'Se Evidencia Decremento Estrategias de Evitación (idcatalogo=143)',
  `redu_riesgo_cierre` varchar(2) DEFAULT NULL COMMENT '¿Reducción del riesgo? (idcatalogo=170)',
  
  -- Campos de control
  `users_bina` varchar(60) NOT NULL COMMENT 'Usuarios Equipo',
  `equipo_bina` varchar(7) NOT NULL COMMENT 'Equipo',
  `usu_creo` varchar(18) NOT NULL COMMENT 'Usuario que creó el registro',
  `fecha_create` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `usu_update` varchar(18) DEFAULT NULL COMMENT 'Usuario que actualizó',
  `fecha_update` timestamp NULL DEFAULT NULL COMMENT 'Fecha de actualización',
  `estado` varchar(2) DEFAULT NULL COMMENT 'Estado del registro (A=Activo)',
  
  PRIMARY KEY (`idpeople`,`numsegui`,`evento`),
  UNIQUE KEY `id_adoleMas` (`id_adoleMas`),
  KEY `idpeople` (`idpeople`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Adolescentes 12-17 años con disfunción familiar y consumo SPA/Prevención SSR';
