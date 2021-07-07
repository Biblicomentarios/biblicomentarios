<?php
/*
Plugin Name: AB Carga Partes
Description: Agrega nuevas partes a las categorías existentes.
Author: JPMarichal
Version: 1.0.0
Author URI: https://biblicomentarios.com/
Last Updated: 18 Junio 2021
License: Private
Text Domain: biblicomentarios
*/
/* ============================================================= */
include_once 'funciones-basicas.php';

register_activation_hook(__FILE__, 'reordenarCategorias');

function reordenarCategorias()
{
    librosAntiguoTestamento();
    librosNuevoTestamento();
    librosLibroDeMormon();
    librosDoctrinaYConvenios();
    librosPerlaDeGranPrecio();
}

function librosAntiguoTestamento()
{
    asignarPapa('Escrituras', 'Antiguo Testamento');
    asignarPapa('Antiguo Testamento', 'El Pentateuco');
    asignarPapa('Antiguo Testamento', 'Libros históricos del Antiguo Testamento');
    asignarPapa('Antiguo Testamento', 'Libros poéticos');
    asignarPapa('Antiguo Testamento', 'Profetas mayores');
    asignarPapa('Antiguo Testamento', 'Profetas menores');

    // asignarPapa('El Pentateuco', 'Génesis');
    // asignarPapa('Génesis', 'Cuatro eventos');
    // asignarPapa('Cuatro eventos', 'La Creación');
    // asignarCategoriaPadre('La Creación', 'Génesis', 1, 2);
    // asignarPapa('Cuatro eventos', 'La Caída');
    // asignarCategoriaPadre('La Caída', 'Génesis', 3, 4);
    // asignarPapa('Cuatro eventos', 'El Diluvio');
    // asignarCategoriaPadre('El Diluvio', 'Génesis', 5, 9);
    // asignarPapa('Cuatro eventos', 'La Dispersión de las Naciones');
    // asignarCategoriaPadre('La Dispersión de las Naciones', 'Génesis', 10, 11);
    // asignarPapa('Génesis', 'Cuatro personajes');
    // asignarPapa('Cuatro personajes', 'La historia de Abraham');
    // asignarCategoriaPadre('La historia de Abraham', 'Génesis', 12, 25);
    // asignarPapa('Cuatro personajes', 'La historia de Isaac');
    // asignarCategoriaPadre('La historia de Isaac', 'Génesis', 26, 26);
    // asignarPapa('Cuatro personajes', 'La historia de Jacob');
    // asignarCategoriaPadre('La historia de Jacob', 'Génesis', 27, 36);
    // asignarPapa('Cuatro personajes', 'La historia de José');
    // asignarCategoriaPadre('La historia de José', 'Génesis', 37, 50);

    // asignarPapa('El Pentateuco', 'Exodo');
    // asignarPapa('Exodo', 'La liberación de Dios para Israel');
    // asignarPapa('La liberación de Dios para Israel', 'La necesidad de la liberación');
    // asignarCategoriaPadre('La necesidad de la liberación', 'Exodo', 1, 1);
    // asignarPapa('La liberación de Dios para Israel', 'La preparación de líderes para la liberación');
    // asignarCategoriaPadre('La preparación de líderes para la liberación', 'Exodo', 2, 4);
    // asignarPapa('La liberación de Dios para Israel', 'La redención de Israel de Egipto por parte de Dios');
    // asignarCategoriaPadre('La redención de Israel de Egipto por parte de Dios', 'Exodo', 5, 14);
    // asignarPapa('La liberación de Dios para Israel', 'La preservación de Israel en el desierto');
    // asignarCategoriaPadre('La preservación de Israel en el desierto', 'Exodo', 15, 18);
    // asignarPapa('Exodo', 'La revelación de Dios a Israel');
    // asignarPapa('La revelación de Dios a Israel', 'La revelación del antiguo convenio');
    // asignarCategoriaPadre('La revelación del antiguo convenio', 'Exodo', 19, 31);
    // asignarPapa('La revelación de Dios a Israel', 'La respuesta de Israel al convenio');
    // asignarCategoriaPadre('La respuesta de Israel al convenio', 'Exodo', 32, 40);

    // asignarPapa('El Pentateuco', 'Levítico');
    // asignarPapa('Levítico', 'El acercamiento aceptable a Dios por medio del sacrificio');
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre el sacrificio');
    // asignarCategoriaPadre('Leyes sobre el sacrificio', 'Levítico', 1, 7);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre el sacerdocio aarónico');
    // asignarCategoriaPadre('Leyes sobre el sacerdocio aarónico', 'Levítico', 8, 10);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre la purificación');
    // asignarCategoriaPadre('Leyes sobre la purificación', 'Levítico', 11, 15);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre la expiación nacional');
    // asignarCategoriaPadre('Leyes sobre la expiación nacional', 'Levítico', 16, 17);
    // asignarPapa('Levítico', 'El acercamiento aceptable a Dios por medio de la santificación');
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificación', 'Leyes de santificación para el pueblo');
    // asignarCategoriaPadre('Leyes de santificación para el pueblo', 'Levítico', 18, 20);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificación', 'Leyes de santificación para el sacerdocio');
    // asignarCategoriaPadre('Leyes de santificación para el sacerdocio', 'Levítico', 21, 22);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificación', 'Leyes de santificación para la adoración');
    // asignarCategoriaPadre('Leyes de santificación para la adoración', 'Levítico', 23, 24);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificación', 'Leyes de santificación en la tierra prometida');
    // asignarCategoriaPadre('Leyes de santificación en la tierra prometida', 'Levítico', 25, 26);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificación', 'Leyes de santificación por medio de votos');
    // asignarCategoriaPadre('Leyes de santificación por medio de votos', 'Levítico', 27, 27);

    // asignarPapa('El Pentateuco', 'Números');
    // asignarPapa('Números', 'La preparación de la primera generación para heredar la tierra prometida');
    // asignarPapa('La preparación de la primera generación para heredar la tierra prometida', 'La organización de Israel');
    // asignarCategoriaPadre('La organización de Israel', 'Números', 1, 4);
    // asignarPapa('La preparación de la primera generación para heredar la tierra prometida', 'La santificación de Israel');
    // asignarCategoriaPadre('La santificación de Israel', 'Números', 5, 10);
    // asignarPapa('Números', 'El fracaso de la primera generación en heredar la tierra prometida');
    // asignarPapa('El fracaso de la primera generación en heredar la tierra prometida', 'El fracaso de Israel en camino a Cades');
    // asignarCategoriaPadre('El fracaso de Israel en camino a Cades', 'Números', 11, 12);
    // asignarPapa('El fracaso de la primera generación en heredar la tierra prometida', 'El fracaso de Israel en Cades');
    // asignarCategoriaPadre('El fracaso de Israel en Cades', 'Números', 13, 14);
    // asignarPapa('El fracaso de la primera generación en heredar la tierra prometida', 'El fracaso de Israel en el desierto');
    // asignarCategoriaPadre('El fracaso de Israel en el desierto', 'Números', 15, 19);
    // asignarPapa('El fracaso de la primera generación en heredar la tierra prometida', 'El fracaso de Israel en camino a Moab');
    // asignarCategoriaPadre('El fracaso de Israel en camino a Moab', 'Números', 20, 25);
    // asignarPapa('Números', 'La preparación de la nueva generación para heredar la tierra prometida');
    // asignarPapa('La preparación de la nueva generación para heredar la tierra prometida', 'La reorganización de Israel');
    // asignarCategoriaPadre('La reorganización de Israel', 'Números', 26, 27);
    // asignarPapa('La preparación de la nueva generación para heredar la tierra prometida', 'Las regulaciones sobre ofrendas y votos');
    // asignarCategoriaPadre('Las regulaciones sobre ofrendas y votos', 'Números', 28, 30);
    // asignarPapa('La preparación de la nueva generación para heredar la tierra prometida', 'La conquista y división de Israel');
    // asignarCategoriaPadre('La conquista y división de Israel', 'Números', 31, 36);

    // asignarPapa('El Pentateuco', 'Deuteronomio');
    // asignarPapa('Deuteronomio', 'Primer discurso de Moisés');
    // asignarPapa('Primer discurso de Moisés', 'Lo que Dios ha hecho por Israel');
    // asignarCategoriaPadre('Lo que Dios ha hecho por Israel', 'Deuteronomio', 1, 4);
    // asignarPapa('Deuteronomio', 'Segundo discurso de Moisés');
    // asignarPapa('Segundo discurso de Moisés', 'Las estipulaciones del convenio');
    // asignarCategoriaPadre('Las estipulaciones del convenio', 'Deuteronomio', 5, 11);
    // asignarPapa('Segundo discurso de Moisés', 'Explicación de leyes adicionales');
    // asignarCategoriaPadre('Explicación de leyes adicionales', 'Deuteronomio', 12, 26);
    // asignarPapa('Deuteronomio', 'Tercer discurso de Moisés');
    // asignarPapa('Tercer discurso de Moisés', 'La confirmación del convenio');
    // asignarCategoriaPadre('La confirmación del convenio', 'Deuteronomio', 27, 28);
    // asignarPapa('Tercer discurso de Moisés', 'El establecimiento del convenio');
    // asignarCategoriaPadre('El establecimiento del convenio', 'Deuteronomio', 29, 30);
    // asignarPapa('Tercer discurso de Moisés', 'La transición profética de Moisés a Josué');
    // asignarCategoriaPadre('La transición profética de Moisés a Josué', 'Deuteronomio', 31, 34);

    // asignarPapa('Libros históricos del Antiguo Testamento', 'Josué');
    // asignarPapa('Josué', 'La conquista de Canaán');
    // asignarPapa('La conquista de Canaán', 'La preparación de Israel para la conquista');
    // asignarCategoriaPadre('La preparación de Israel para la conquista', 'Josué', 1, 5);
    // asignarPapa('La conquista de Canaán', 'Israel conquista Canáan');
    // asignarCategoriaPadre('Israel conquista Canáan', 'Josué', 6, 12);
    // asignarPapa('Josué', 'El asentamiento en Canaán');
    // asignarPapa('El asentamiento en Canaán', 'Asentamiento al este del Jordán');
    // asignarCategoriaPadre('Asentamiento al este del Jordán', 'Josué', 13, 13);
    // asignarPapa('El asentamiento en Canaán', 'Asentamiento al oeste del Jordán');
    // asignarCategoriaPadre('Asentamiento al oeste del Jordán', 'Josué', 14, 19);
    // asignarPapa('El asentamiento en Canaán', 'Asentamiento de la tribu de Leví');
    // asignarCategoriaPadre('Asentamiento de la tribu de Leví', 'Josué', 20, 21);
    // asignarPapa('El asentamiento en Canaán', 'Las condiciones para la permanencia en Canáan');
    // asignarCategoriaPadre('Las condiciones para la permanencia en Canáan', 'Josué', 22, 24);

    // asignarPapa('Libros históricos del Antiguo Testamento', 'Jueces');
    // asignarPapa('Jueces','El deterioro de Israel');
    // asignarPapa('El deterioro de Israel','El fracaso de Israel en completar la conquista');
    // asignarCategoriaPadre('El fracaso de Israel en completar la conquista', 'Jueces', 1, 1);
    // asignarPapa('El deterioro de Israel','Las consecuencias por no completar la conquista');
    // asignarCategoriaPadre('Las consecuencias por no completar la conquista', 'Jueces', 2, 2);
    // asignarPapa('Jueces','La liberación de Israel');
    // asignarPapa('La liberación de Israel','La campaña del sur');
    // asignarCategoriaPadre('La campaña del sur', 'Jueces', 3, 3);
    // asignarPapa('La liberación de Israel','La primera campaña del norte');
    // asignarCategoriaPadre('La primera campaña del norte', 'Jueces', 4, 5);
    // asignarPapa('La liberación de Israel','La campaña central');
    // asignarCategoriaPadre('La campaña central', 'Jueces', 6, 9);
    // asignarPapa('La liberación de Israel','La campaña del este y segunda campaña del norte');
    // asignarCategoriaPadre('La campaña del este y segunda campaña del norte', 'Jueces', 10, 12);
    // asignarPapa('La liberación de Israel','La campaña del oeste');
    // asignarCategoriaPadre('La campaña del oeste', 'Jueces', 13, 16);
    // asignarPapa('Jueces','¿La depravación de Israel');
    // asignarPapa('La depravación de Israel','El fracaso de Israel por la idolatría');
    // asignarCategoriaPadre('El fracaso de Israel por la idolatría', 'Jueces', 17, 18);
    // asignarPapa('La depravación de Israel','El fracaso de Israel por la inmoralidad');
    // asignarCategoriaPadre('El fracaso de Israel por la inmoralidad', 'Jueces', 19, 19);
    // asignarPapa('La depravación de Israel','El fracaso de Israel por la guerra entre las tribus');
    // asignarCategoriaPadre('El fracaso de Israel por la guerra entre las tribus', 'Jueces', 20, 21);

    // asignarPapa('Libros históricos del Antiguo Testamento', 'Rut');
    // asignarPapa('Rut','La demostración del amor de Rut');
    // asignarPapa('La demostración del amor de Rut','La decisión de Rut de permanecer con Noemí');
    // asignarCategoriaPadre('La decisión de Rut de permanecer con Noemí','Rut',1,1);
    // asignarPapa('La demostración del amor de Rut','La devoción de Rut al cuidar de Noemí');
    // asignarCategoriaPadre('La devoción de Rut al cuidar de Noemí','Rut',2,2);
    // asignarPapa('Rut','La recompensa del amor de Rut');
    // asignarPapa('La recompensa del amor de Rut','Rut procura la redención de Booz');
    // asignarCategoriaPadre('Rut procura la redención de Booz','Rut',3,3);
    // asignarPapa('La recompensa del amor de Rut','Rut obtiene la redención de Booz');
    // asignarCategoriaPadre('Rut obtiene la redención de Booz','Rut',4,4);

    // asignarPapa('Libros históricos del Antiguo Testamento', '1 Samuel');
    // asignarPapa('1 Samuel', 'Samuel, el último juez');
    // asignarPapa('Samuel, el último juez','Primera transición: De Eli a Samuel');
    // asignarCategoriaPadre('Primera transición: De Eli a Samuel','1 Samuel',1,3);
    // asignarPapa('Samuel, el último juez','La judicatura de Samuel');
    // asignarCategoriaPadre('La judicatura de Samuel','1 Samuel',4,7);
    // asignarPapa('1 Samuel', 'Saúl, el primer rey');
    // asignarPapa('Saúl, el primer rey','Segunda transición: De Samuel a Saúl');
    // asignarCategoriaPadre('Segunda transición: De Samuel a Saúl','1 Samuel',8,12);
    // asignarPapa('Saúl, el primer rey','El reinado de Saúl');
    // asignarCategoriaPadre('El reinado de Saúl','1 Samuel',13,15);
    // asignarPapa('Saúl, el primer rey','Tercera transición: De Saúl a David');
    // asignarCategoriaPadre('Tercera transición: De Saúl a David','1 Samuel',16,31);

    // asignarPapa('Libros históricos del Antiguo Testamento', '2 Samuel');
    // asignarPapa('2 Samuel','Los triunfos de David');
    // asignarPapa('Los triunfos de David','Triunfos políticos de David');
    // asignarCategoriaPadre('Triunfos políticos de David','2 Samuel',1,5);
    // asignarPapa('Los triunfos de David','Triunfos espirituales de David');
    // asignarCategoriaPadre('Triunfos espirituales de David','2 Samuel',6,7);
    // asignarPapa('Los triunfos de David','Triunfos militares de David');
    // asignarCategoriaPadre('Triunfos militares de David','2 Samuel',8,10);
    // asignarPapa('2 Samuel','Las transgresiones de David');
    // asignarPapa('Las transgresiones de David','El adulterio de David');
    // asignarCategoriaPadre('El adulterio de David','2 Samuel',11,11);
    // asignarPapa('2 Samuel','Los problemas de David');
    // asignarPapa('Los problemas de David','Problemas en la casa de David');
    // asignarCategoriaPadre('Problemas en la casa de David','2 Samuel',12,13);
    // asignarPapa('Los problemas de David','Problemas en el reino de David');
    // asignarCategoriaPadre('Problemas en el reino de David','2 Samuel',14,24);

    asignarPapa('Libros históricos del Antiguo Testamento', '1 Reyes');
    asignarCategoriaPadre('1 Reyes','1 Reyes',1,22); 

    asignarPapa('Libros históricos del Antiguo Testamento', '2 Reyes');  
    asignarCategoriaPadre('2 Reyes','2 Reyes',1,25);

    asignarPapa('Libros históricos del Antiguo Testamento', '1 Crónicas');    
    asignarCategoriaPadre('1 Crónicas','1 Crónicas',1,29);

    asignarPapa('Libros históricos del Antiguo Testamento', '2 Crónicas');
    asignarCategoriaPadre('2 Crónicas','2 Crónicas',1,36);

    asignarPapa('Libros históricos del Antiguo Testamento', 'Esdras');
    asignarCategoriaPadre('Esdras','Esdras',1,10);

    asignarPapa('Libros históricos del Antiguo Testamento', 'Nehemías');
    asignarCategoriaPadre('Nehemías','Nehemías',1,13);

    asignarPapa('Libros históricos del Antiguo Testamento', 'Ester');
    asignarCategoriaPadre('Ester','Ester',1,10);

    // asignarPapa('Libros poéticos', 'Job');
    // asignarPapa('Job','El dilema de Job');
    // asignarPapa('El dilema de Job','Introducción del dilema de Job');
    // asignarCategoriaPadre('Introducción del dilema de Job','Job',1,2);
    // asignarPapa('Job','Los debates de Job');
    // asignarCategoriaPadre('El primer ciclo de debate','Job',3,14);
    // asignarCategoriaPadre('El segundo ciclo de debate','Job',15,21);
    // asignarCategoriaPadre('El tercer ciclo de debate','Job',22,26);
    // asignarCategoriaPadre('La defensa final de Job','Job',27,31);
    // asignarCategoriaPadre('La solución de Elihú','Job',32,37);
    // asignarPapa('Job','La redención de Job');
    // asignarCategoriaPadre('La primera controversia de Job con Dios','Job',38,39);
    // asignarCategoriaPadre('La segunda controversia de Job con Dios','Job',40,42);

    // asignarPapa('Libros poéticos', 'Salmos');
    // asignarPapa('Salmos','Libro I (Salmos 1-41)');
    // asignarCategoriaPadre('Libro I (Salmos 1-41)','Salmos',1,41);
    // asignarPapa('Salmos','Libro II (Salmos 42-72)');
    // asignarCategoriaPadre('Libro II (Salmos 42-72)','Salmos',42,72);
    // asignarPapa('Salmos','Libro III (Salmos 73-89)');
    // asignarCategoriaPadre('Libro III (Salmos 73-89)','Salmos',73,89);
    // asignarPapa('Salmos','Libro IV (Salmos 90-106)');
    // asignarCategoriaPadre('Libro IV (Salmos 90-106)','Salmos',90,106);
    // asignarPapa('Salmos','Libro V (Salmos 107-150)');
    // asignarCategoriaPadre('Libro V (Salmos 107-150)','Salmos',107,150);

    // asignarPapa('Libros poéticos', 'Proverbios');
    // asignarPapa('Proverbios','Proverbios atribuidos al rey Salomón');
    // asignarPapa('Proverbios atribuidos al rey Salomón','Proverbios para la juventud');
    // asignarPapa('Proverbios atribuidos al rey Salomón','Proverbios de Salomón');
    // asignarPapa('Proverbios','Proverbios de varios autores');
    // asignarPapa('Proverbios de varios autores','Las palabras de Agur');
    // asignarPapa('Proverbios de varios autores','Las palabras del rey Lemuel');
    // asignarCategoriaPadre('Proverbios para la juventud','Proverbios',1,9);
    // asignarCategoriaPadre('Proverbios de Salomón','Proverbios',10,29);
    // asignarCategoriaPadre('Las palabras de Agur','Proverbios',30,30);
    // asignarCategoriaPadre('Las palabras del rey Lemuel','Proverbios',31,31);

    // asignarPapa('Libros poéticos', 'Eclesiastés');
    // asignarPapa('Eclesiastés','Pruebas de que todo es vanidad');
    // asignarPapa('Pruebas de que todo es vanidad','La prueba de la experiencia');
    // asignarCategoriaPadre('La prueba de la experiencia','Eclesiastés',1,2);
    // asignarPapa('Pruebas de que todo es vanidad','La prueba de la observación');
    // asignarCategoriaPadre('La prueba de la observación','Eclesiastés',3,6);
    // asignarPapa('Eclesiastés','Consejos para lidiar con la vanidad');
    // asignarPapa('Consejos para lidiar con la vanidad','Cómo enfrentar un mundo inicuo');
    // asignarCategoriaPadre('Cómo enfrentar un mundo inicuo','Eclesiastés',7,12);
    
    // asignarPapa('Libros poéticos', 'Cantares');
    // asignarPapa('Cantares','El Cantar de los Cantares');
    // asignarPapa('El Cantar de los Cantares','El nacimiento del amor');
    // asignarPapa('El Cantar de los Cantares','La expansión del amor');
    // asignarCategoriaPadre('El nacimiento del amor','Cantares',1,4);
    // asignarCategoriaPadre('La expansión del amor','Cantares',5,8);

    // asignarPapa('Profetas mayores', 'Isaías');
    // asignarPapa('Isaías','Profecías de juicio');
    // asignarPapa('Profecías de juicio','Contexto del profeta Isaías');
    // asignarCategoriaPadre('Contexto del profeta Isaías','Isaías',1,1);
    // asignarPapa('Profecías de juicio','Primeras profecías de Isaías');
    // asignarCategoriaPadre('Primeras profecías de Isaías','Isaías',2,5);
    // asignarPapa('Profecías de juicio','El llamamiento del profeta Isaías');
    // asignarCategoriaPadre('El llamamiento del profeta Isaías','Isaías',6,6);
    // asignarPapa('Profecías de juicio','Primeras profecías mesiánicas');
    // asignarCategoriaPadre('Primeras profecías mesiánicas','Isaías',7,12);
    // asignarPapa('Profecías de juicio','Cargas contra las naciones');
    // asignarCategoriaPadre('Cargas contra las naciones','Isaías',13,23);
    // asignarPapa('Profecías de juicio','Profecías de la venida del reino de Dios');
    // asignarCategoriaPadre('Profecías de la venida del reino de Dios','Isaías',24,27);
    // asignarPapa('Profecías de juicio','Profecías de ayes y esperanza');
    // asignarCategoriaPadre('Profecías de ayes y esperanza','Isaías',28,35);
    // asignarPapa('Isaías','Material histórico sobre Ezequías');
    // asignarPapa('Material histórico sobre Ezequías','Ezequías es librado de Asiria');
    // asignarCategoriaPadre('Ezequías es librado de Asiria','Isaías',36,37);
    // asignarPapa('Material histórico sobre Ezequías','Ezequías es librado de la enfermedad');
    // asignarCategoriaPadre('Ezequías es librado de la enfermedad','Isaías',38,38);
    // asignarPapa('Material histórico sobre Ezequías','El pecado de Ezequías');
    // asignarCategoriaPadre('El pecado de Ezequías','Isaías',39,39);
    // asignarPapa('Isaías','Profecías de salvación y de esperanza');
    // asignarPapa('Profecías de salvación y de esperanza','Profecías de la restauración de Israel');
    // asignarCategoriaPadre('Profecías de la restauración de Israel','Isaías',40,48);
    // asignarPapa('Profecías de salvación y de esperanza','Profecías del sufrimiento del Mesías');
    // asignarCategoriaPadre('Profecías del sufrimiento del Mesías','Isaías',49,53);
    // asignarPapa('Profecías de salvación y de esperanza','Profecías de la redención de Israel');
    // asignarCategoriaPadre('Profecías de la redención de Israel','Isaías',54,59);
    // asignarPapa('Profecías de salvación y de esperanza','Profecías del futuro glorioso de Israel');
    // asignarCategoriaPadre('Profecías del futuro glorioso de Israel','Isaías',60,66);

    // asignarPapa('Profetas mayores', 'Jeremías');
    // asignarCategoriaPadre('Comisión y llamamiento del profeta Jeremías','Jeremías',1,1);
    // asignarCategoriaPadre('La condenación de Judá','Jeremías',2,25);
    // asignarCategoriaPadre('Los conflictos de Jeremías','Jeremías',26,29);
    // asignarCategoriaPadre('La futura restauración de Jerusalén','Jeremías',30,33);
    // asignarCategoriaPadre('La presente caída de Jerusalén','Jeremías',34,45);
    // asignarCategoriaPadre('Profecías contra Egipto','Jeremías',46,46);
    // asignarCategoriaPadre('Profecías contra Filistea','Jeremías',47,47);
    // asignarCategoriaPadre('Profecías contra Moab','Jeremías',48,48);
    // asignarCategoriaPadre('Profecías contra varias naciones','Jeremías',49,49);
    // asignarCategoriaPadre('Profecías contra Babilonia','Jeremías',50,51);
    // asignarCategoriaPadre('El sitio de Jerusalén y el exilio a Babilonia','Jeremías',52,52);

    // asignarPapa('Profetas mayores', 'Lamentaciones');
    // asignarCategoriaPadre('Destrucción de Jerusalén','Lamentaciones',1,1);
    // asignarCategoriaPadre('La ira de Dios y las tristezas de Jerusalén','Lamentaciones',2,2);
    // asignarCategoriaPadre('Oración por misericordia sobre Jerusalén','Lamentaciones',3,3);
    // asignarCategoriaPadre('La siega de Jerusalén','Lamentaciones',4,4);
    // asignarCategoriaPadre('Oración por la restauración de Jerusalén','Lamentaciones',5,5);

    // asignarPapa('Profetas mayores', 'Ezequiel');
    // asignarCategoriaPadre('La visión de Ezequiel de la gloria de Dios','Ezequiel',1,1);
    // asignarCategoriaPadre('La comisión y llamamiento de Ezequiel','Ezequiel',2,3);
    // asignarCategoriaPadre('Cuatro señales del juicio venidero','Ezequiel',4,5);
    // asignarCategoriaPadre('Dos mensajes del juicio venidero','Ezequiel',6,7);
    // asignarCategoriaPadre('La visión del juicio venidero','Ezequiel',8,11);
    // asignarCategoriaPadre('Señales, mensajes y parábolas del juicio venidero','Ezequiel',12,24);
    // asignarCategoriaPadre('Juicio sobre Amón, Edom y Filistea','Ezequiel',25,25);
    // asignarCategoriaPadre('Juicio sobre Tiro, Sidón y Egipto','Ezequiel',26,32);
    // asignarCategoriaPadre('Atalaya de restauración','Ezequiel',33,35);
    // asignarCategoriaPadre('Promesas de restauración','Ezequiel',36,37);
    // asignarCategoriaPadre('Victoria sobre Gog y Magog','Ezequiel',38,39);
    // asignarCategoriaPadre('La restauración de Israel en el reino','Ezequiel',40,48);

    // asignarPapa('Profetas mayores', 'Daniel');
    // asignarCategoriaPadre('Contexto histórico de Daniel','Daniel',1,1);
    // asignarCategoriaPadre('El sueño de Nabucodonosor','Daniel',2,2);
    // asignarCategoriaPadre('La imagen de oro y el horno ardiente','Daniel',3,3);
    // asignarCategoriaPadre('Nabucodonosor sueña con un gran árbol','Daniel',4,4);
    // asignarCategoriaPadre('Belsasar y la escritura sobre el muro','Daniel',5,5);
    // asignarCategoriaPadre('Decreto de Darío y rescate de los leones','Daniel',6,6);
    // asignarCategoriaPadre('La visión de las cuatro bestias','Daniel',7,7);
    // asignarCategoriaPadre('La visión del carnero y el macho cabrío','Daniel',8,8);
    // asignarCategoriaPadre('Visión de las setenta semanas','Daniel',9,9);
    // asignarCategoriaPadre('Visión de Daniel sobre el futuro de Israel','Daniel',10,12);
    
    // asignarPapa('Profetas menores', 'Oseas');
    // asignarCategoriaPadre('La esposa adúltera y su marido fiel','Oseas',1,3);
    // asignarCategoriaPadre('Israel adúltero y su Señor fiel','Oseas',4,14);
    
    // asignarPapa('Profetas menores', 'Joel');
    // asignarCategoriaPadre('El día del Señor en el pasado','Joel',1,1);
    // asignarCategoriaPadre('El día del Señor en el futuro','Joel',2,3);
    
    // asignarPapa('Profetas menores', 'Amós');
    // asignarCategoriaPadre('Damasco, Gaza, Tiro, Edom y Amón','Amós',1,1);
    // asignarCategoriaPadre('Moab, Judá e Israel','Amós',2,2);
    // asignarCategoriaPadre('Primer sermón: el Israel presente','Amós',3,3);
    // asignarCategoriaPadre('Segundo sermón: el Israel pasado','Amós',4,4);
    // asignarCategoriaPadre('Tercer sermón: el Israel futuro','Amós',5,6);
    // asignarCategoriaPadre('La langosta, el fuego y la plomada de albañil','Amós',7,7);
    // asignarCategoriaPadre('El canastillo de fruta de verano','Amós',8,8);
    // asignarCategoriaPadre('El Señor en el altar','Amós',9,9);

    // asignarPapa('Profetas menores', 'Abdías');
    // asignarCategoriaPadre('El juicio contra Edom y el día del Señor','Abdías',1,1);

    // asignarPapa('Profetas menores', 'Jonás');
    // asignarCategoriaPadre('La primera comisión de Jonás', 'Jonás', 1, 2);
    // asignarCategoriaPadre('La segunda comisión de Jonás', 'Jonás', 3, 4);

    // asignarPapa('Profetas menores', 'Miqueas');
    // asignarCategoriaPadre('La predicción del juicio contra Israel y Judá','Miqueas',1,3);
    // asignarCategoriaPadre('La predicción del reino venidero','Miqueas',4,5);
    // asignarCategoriaPadre('El caso de Dios contra Israel','Miqueas',6,6);
    // asignarCategoriaPadre('Esperanza futura para el pueblo de Dios','Miqueas',7,7);
    
    // asignarPapa('Profetas menores', 'Nahúm');
    // asignarCategoriaPadre('Se decreta la destrucción de Nínive','Nahúm',1,1);
    // asignarCategoriaPadre('Se describe la destrucción de Nínive','Nahúm',2,2);
    // asignarCategoriaPadre('Se justifica la destrucción de Nínive','Nahúm',3,3);
    
    // asignarPapa('Profetas menores', 'Habacuc');
    // asignarCategoriaPadre('Las preguntas de Habacuc','Habacuc',1,2);
    // asignarCategoriaPadre('La alabanza de Habacuc','Habacuc',3,3);
    
    // asignarPapa('Profetas menores', 'Sofonías');
    // asignarCategoriaPadre('El juicio sobre Judá y esperanza del remanente','Sofonías',1,3);
    
    // asignarPapa('Profetas menores', 'Hageo');
    // asignarCategoriaPadre('La gloria del templo y las bendiciones de la obediencia','Hageo',1,2);
    
    // asignarPapa('Profetas menores', 'Zacarías');
    // asignarCategoriaPadre('Las ocho visiones de Zacarías','Zacarías',1,6);
    // asignarCategoriaPadre('Los cuatro mensajes de Zacarías','Zacarías',7,38);
    // asignarCategoriaPadre('Las dos cargas de Zacarías','Zacarías',9,14);
    
    // asignarPapa('Profetas menores', 'Malaquías');
    // asignarCategoriaPadre('Privilegios, contaminación y promesas de la nación','Malaquías',1,4);
}

function librosNuevoTestamento()
{
    asignarPapa('Escrituras', 'Nuevo Testamento');
    asignarPapa('Nuevo Testamento', 'Los evangelios');
    asignarPapa('Nuevo Testamento', 'Libros históricos del Nuevo Testamento');
    asignarPapa('Nuevo Testamento', 'Epístolas paulinas');
    asignarPapa('Nuevo Testamento', 'Epístolas universales');
    asignarPapa('Nuevo Testamento', 'Libros proféticos');

    asignarPapa('Los evangelios', 'Mateo');
    asignarCategoriaPadre('Mateo', 'Mateo', 1, 28);
    asignarPapa('Los evangelios', 'Marcos');
    asignarCategoriaPadre('Marcos', 'Marcos', 1, 16);
    asignarPapa('Los evangelios', 'Lucas');
    asignarCategoriaPadre('Lucas', 'Lucas', 1, 24);
    asignarPapa('Los evangelios', 'Juan');
    asignarCategoriaPadre('Juan', 'Juan', 1, 21);

    asignarPapa('Libros históricos del Nuevo Testamento', 'Hechos');
    asignarCategoriaPadre('Hechos', 'Hechos', 1, 28);

    asignarPapa('Epístolas paulinas', 'Romanos');
    asignarCategoriaPadre('Romanos', 'Romanos', 1, 28);
    asignarPapa('Epístolas paulinas', '1 Corintios');
    asignarCategoriaPadre('1 Corintios', '1 Corintios', 1, 28);
    asignarPapa('Epístolas paulinas', '2 Corintios');
    asignarCategoriaPadre('2 Corintios', '2 Corintios', 1, 28);
    asignarPapa('Epístolas paulinas', 'Gálatas');
    asignarCategoriaPadre('Gálatas', 'Gálatas', 1, 28);
    asignarPapa('Epístolas paulinas', 'Efesios');
    asignarCategoriaPadre('Efesios', 'Efesios', 1, 28);
    asignarPapa('Epístolas paulinas', 'Filipenses');
    asignarCategoriaPadre('Filipenses', 'Filipenses', 1, 28);
    asignarPapa('Epístolas paulinas', 'Colosenses');
    asignarCategoriaPadre('Colosenses', 'Colosenses', 1, 28);
    asignarPapa('Epístolas paulinas', '1 Tesalonicenses');
    asignarCategoriaPadre('1 Tesalonicenses', '1 Tesalonicenses', 1, 28);
    asignarPapa('Epístolas paulinas', '2 Tesalonicenses');
    asignarCategoriaPadre('2 Tesalonicenses', '2 Tesalonicenses', 1, 28);
    asignarPapa('Epístolas paulinas', '1 Timoteo');
    asignarCategoriaPadre('1 Timoteo', '1 Timoteo', 1, 28);
    asignarPapa('Epístolas paulinas', '2 Timoteo');
    asignarCategoriaPadre('2 Timoteo', '2 Timoteo', 1, 28);
    asignarPapa('Epístolas paulinas', 'Tito');
    asignarCategoriaPadre('Tito', 'Tito', 1, 28);
    asignarPapa('Epístolas paulinas', 'Filemón');
    asignarCategoriaPadre('Filemón', 'Filemón', 1, 28);
    asignarPapa('Epístolas paulinas', 'Hebreos');
    asignarCategoriaPadre('Hebreos', 'Hebreos', 1, 28);

    asignarPapa('Epístolas universales', 'Santiago');
    asignarCategoriaPadre('Santiago', 'Santiago', 1, 28);
    asignarPapa('Epístolas universales', '1 Pedro');
    asignarCategoriaPadre('1 Pedro', '1 Pedro', 1, 28);
    asignarPapa('Epístolas universales', '2 Pedro');
    asignarCategoriaPadre('2 Pedro', '2 Pedro', 1, 28);
    asignarPapa('Epístolas universales', '1 Juan');
    asignarCategoriaPadre('1 Juan', '1 Juan', 1, 28);
    asignarPapa('Epístolas universales', '2 Juan');
    asignarCategoriaPadre('2 Juan', '2 Juan', 1, 1);
    asignarPapa('Epístolas universales', '3 Juan');
    asignarCategoriaPadre('3 Juan', '3 Juan', 1, 1);
    asignarPapa('Epístolas universales', 'Judas');
    asignarCategoriaPadre('Judas', 'Judas', 1, 1);

    asignarPapa('Libros proféticos', 'Apocalipsis');
    asignarCategoriaPadre('Apocalipsis', 'Apocalipsis', 1, 28);
}

function librosLibroDeMormon()
{
    asignarPapa('Escrituras', 'Libro de Mormón');
    asignarPapa('Libro de Mormón', 'Planchas menores');
    asignarPapa('Libro de Mormón', 'Puente editorial');
    asignarPapa('Libro de Mormón', 'Planchas mayores');
    asignarPapa('Libro de Mormón', 'Escritos de Mormón');
    asignarPapa('Libro de Mormón', 'Adiciones de Moroni');

    asignarPapa('Planchas menores', '1 Nefi');
    asignarCategoriaPadre('1 Nefi', '1 Nefi', 1, 22);

    asignarPapa('Planchas menores', '2 Nefi');
    asignarPapa('Planchas menores', 'Jacob');
    asignarPapa('Planchas menores', 'Enós');
    asignarPapa('Planchas menores', 'Jarom');
    asignarPapa('Planchas menores', 'Omni');
    asignarPapa('Puente editorial', 'Palabras de Mormón');
    asignarPapa('Planchas mayores', 'Mosíah');
    asignarPapa('Planchas mayores', 'Alma');
    asignarPapa('Planchas mayores', 'Helamán');
    asignarPapa('Planchas mayores', '3 Nefi');
    asignarPapa('Planchas mayores', '4 Nefi');
    asignarPapa('Escritos de Mormón', 'Mormón');
    asignarPapa('Adiciones de Moroni', 'Eter');
    asignarPapa('Adiciones de Moroni', 'Moroni');
}

function librosDoctrinaYConvenios()
{
    asignarPapa('Escrituras', 'Doctrina y Convenios');

    asignarPapa('Doctrina y Convenios', 'Nueva York');
    asignarPapa('Nueva York', 'Traducción del Libro de Mormón');
    asignarCategoriaPadre('Traducción del Libro de Mormón', 'Doctrina y Convenios', 2, 19);
    asignarPapa('Nueva York', 'Organización de la Iglesia');
    asignarCategoriaPadre('Organización de la Iglesia', 'Doctrina y Convenios', 20, 28);
    asignarPapa('Nueva York', 'Difusión de la Iglesia');
    asignarCategoriaPadre('Difusión de la Iglesia', 'Doctrina y Convenios', 29, 29);
    asignarCategoriaPadre('Difusión de la Iglesia', 'Doctrina y Convenios', 74, 74);
    asignarCategoriaPadre('Difusión de la Iglesia', 'Doctrina y Convenios', 30, 36);
    asignarPapa('Nueva York', 'Movimiento de la Iglesia');
    asignarCategoriaPadre('Movimiento de la Iglesia', 'Doctrina y Convenios', 37, 40);

    asignarPapa('Doctrina y Convenios', 'Ohio');
    asignarPapa('Ohio', 'La Consagración');
    asignarCategoriaPadre('La Consagración','Doctrina y Convenios',41,56);
    asignarPapa('Ohio', 'Viaje a Missouri');
    asignarCategoriaPadre('Viaje a Missouri','Doctrina y Convenios',57,62);
    asignarPapa('Ohio', 'Traducción y revelaciones');
    asignarCategoriaPadre('Traducción y revelaciones','Doctrina y Convenios',63,73);
    asignarCategoriaPadre('Traducción y revelaciones','Doctrina y Convenios',1,1);
    asignarCategoriaPadre('Traducción y revelaciones','Doctrina y Convenios',75,83);
    asignarCategoriaPadre('Traducción y revelaciones','Doctrina y Convenios',133,133);
    asignarCategoriaPadre('Traducción y revelaciones','Doctrina y Convenios',107,107);
    asignarPapa('Ohio', 'Construcción del templo de Kirtland');
    asignarCategoriaPadre('Construcción del templo de Kirtland','Doctrina y Convenios',84,97);
    asignarCategoriaPadre('Construcción del templo de Kirtland','Doctrina y Convenios',99,99);
    asignarPapa('Ohio', 'La redención de Sión');
    asignarCategoriaPadre('La redención de Sión','Doctrina y Convenios',98,98);
    asignarCategoriaPadre('La redención de Sión','Doctrina y Convenios',100,106);
    asignarPapa('Ohio', 'La casa del Señor en Kirtland');
    asignarCategoriaPadre('La casa del Señor en Kirtland','Doctrina y Convenios',107,112);
    asignarCategoriaPadre('La casa del Señor en Kirtland','Doctrina y Convenios',134,134);
    asignarCategoriaPadre('La casa del Señor en Kirtland','Doctrina y Convenios',137,137);

    asignarPapa('Doctrina y Convenios', 'Missouri');
    asignarPapa('Missouri', 'Eventos en Far West');
    asignarCategoriaPadre('Eventos en Far West','Doctrina y Convenios',113,120);
    asignarPapa('Missouri', 'La cárcel de Liberty');
    asignarCategoriaPadre('La cárcel de Liberty','Doctrina y Convenios',121,123);

    asignarPapa('Doctrina y Convenios', 'Illinois');
    asignarPapa('Illinois', 'La obra del templo de Nauvoo');
    asignarCategoriaPadre('La obra del templo de Nauvoo','Doctrina y Convenios',124,129);
    asignarPapa('Illinois', 'Recepción de doctrinas eternas');
    asignarCategoriaPadre('Recepción de doctrinas eternas','Doctrina y Convenios',130,132);
    asignarCategoriaPadre('Recepción de doctrinas eternas','Doctrina y Convenios',135,135);

    asignarPapa('Doctrina y Convenios', 'El oeste');
    asignarPapa('El oeste','Instrucciones en Winter Quarters');
    asignarCategoriaPadre('Instrucciones en Winter Quarters', 'Doctrina y Convenios', 136, 136);
    asignarPapa('El oeste', 'Declaraciones Oficiales');
    asignarCategoriaPadre('Declaraciones Oficiales', 'Declaración Oficial', 1, 2);
    asignarPapa('El oeste','Revelación sobre la redención de los muertos');
    asignarCategoriaPadre('Revelación sobre la redención de los muertos', 'Doctrina y Convenios', 138, 138);
}

function librosPerlaDeGranPrecio()
{
    asignarPapa('Escrituras', 'Perla de Gran Precio');
    asignarPapa('Perla de Gran Precio', 'Relacionados con el Antiguo Testamento');
    asignarPapa('Perla de Gran Precio', 'Relacionados con el Nuevo Testamento');
    asignarPapa('Perla de Gran Precio', 'Relacionados con la Restauración');
    asignarPapa('Relacionados con el Antiguo Testamento', 'Libro de Moisés');
    asignarCategoriaPadre('Libro de Moisés', 'Moisés', 1, 8);
    asignarPapa('Relacionados con el Antiguo Testamento', 'Libro de Abraham');
    asignarCategoriaPadre('Libro de Abraham', 'Abraham', 1, 5);
    asignarPapa('Relacionados con el Nuevo Testamento', 'José Smith - Mateo');
    asignarCategoriaPadre('José Smith - Mateo', 'José Smith-Mateo', 1, 1);
    asignarPapa('Relacionados con la Restauración', 'José Smith - Historia');
    asignarCategoriaPadre('José Smith - Historia', 'José Smith-Historia', 1, 1);
    asignarPapa('Relacionados con la Restauración', 'Artículos de Fe');
    asignarCategoriaPadre('Artículos de Fe', 'Artículos de Fe', 1, 1);
}
