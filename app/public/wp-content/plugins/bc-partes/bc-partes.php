<?php
/*
Plugin Name: AB Carga Partes
Description: Agrega nuevas partes a las categor�as existentes.
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
    asignarPapa('Antiguo Testamento', 'Libros hist�ricos del Antiguo Testamento');
    asignarPapa('Antiguo Testamento', 'Libros po�ticos');
    asignarPapa('Antiguo Testamento', 'Profetas mayores');
    asignarPapa('Antiguo Testamento', 'Profetas menores');

    // asignarPapa('El Pentateuco', 'G�nesis');
    // asignarPapa('G�nesis', 'Cuatro eventos');
    // asignarPapa('Cuatro eventos', 'La Creaci�n');
    // asignarCategoriaPadre('La Creaci�n', 'G�nesis', 1, 2);
    // asignarPapa('Cuatro eventos', 'La Ca�da');
    // asignarCategoriaPadre('La Ca�da', 'G�nesis', 3, 4);
    // asignarPapa('Cuatro eventos', 'El Diluvio');
    // asignarCategoriaPadre('El Diluvio', 'G�nesis', 5, 9);
    // asignarPapa('Cuatro eventos', 'La Dispersi�n de las Naciones');
    // asignarCategoriaPadre('La Dispersi�n de las Naciones', 'G�nesis', 10, 11);
    // asignarPapa('G�nesis', 'Cuatro personajes');
    // asignarPapa('Cuatro personajes', 'La historia de Abraham');
    // asignarCategoriaPadre('La historia de Abraham', 'G�nesis', 12, 25);
    // asignarPapa('Cuatro personajes', 'La historia de Isaac');
    // asignarCategoriaPadre('La historia de Isaac', 'G�nesis', 26, 26);
    // asignarPapa('Cuatro personajes', 'La historia de Jacob');
    // asignarCategoriaPadre('La historia de Jacob', 'G�nesis', 27, 36);
    // asignarPapa('Cuatro personajes', 'La historia de Jos�');
    // asignarCategoriaPadre('La historia de Jos�', 'G�nesis', 37, 50);

    // asignarPapa('El Pentateuco', 'Exodo');
    // asignarPapa('Exodo', 'La liberaci�n de Dios para Israel');
    // asignarPapa('La liberaci�n de Dios para Israel', 'La necesidad de la liberaci�n');
    // asignarCategoriaPadre('La necesidad de la liberaci�n', 'Exodo', 1, 1);
    // asignarPapa('La liberaci�n de Dios para Israel', 'La preparaci�n de l�deres para la liberaci�n');
    // asignarCategoriaPadre('La preparaci�n de l�deres para la liberaci�n', 'Exodo', 2, 4);
    // asignarPapa('La liberaci�n de Dios para Israel', 'La redenci�n de Israel de Egipto por parte de Dios');
    // asignarCategoriaPadre('La redenci�n de Israel de Egipto por parte de Dios', 'Exodo', 5, 14);
    // asignarPapa('La liberaci�n de Dios para Israel', 'La preservaci�n de Israel en el desierto');
    // asignarCategoriaPadre('La preservaci�n de Israel en el desierto', 'Exodo', 15, 18);
    // asignarPapa('Exodo', 'La revelaci�n de Dios a Israel');
    // asignarPapa('La revelaci�n de Dios a Israel', 'La revelaci�n del antiguo convenio');
    // asignarCategoriaPadre('La revelaci�n del antiguo convenio', 'Exodo', 19, 31);
    // asignarPapa('La revelaci�n de Dios a Israel', 'La respuesta de Israel al convenio');
    // asignarCategoriaPadre('La respuesta de Israel al convenio', 'Exodo', 32, 40);

    // asignarPapa('El Pentateuco', 'Lev�tico');
    // asignarPapa('Lev�tico', 'El acercamiento aceptable a Dios por medio del sacrificio');
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre el sacrificio');
    // asignarCategoriaPadre('Leyes sobre el sacrificio', 'Lev�tico', 1, 7);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre el sacerdocio aar�nico');
    // asignarCategoriaPadre('Leyes sobre el sacerdocio aar�nico', 'Lev�tico', 8, 10);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre la purificaci�n');
    // asignarCategoriaPadre('Leyes sobre la purificaci�n', 'Lev�tico', 11, 15);
    // asignarPapa('El acercamiento aceptable a Dios por medio del sacrificio', 'Leyes sobre la expiaci�n nacional');
    // asignarCategoriaPadre('Leyes sobre la expiaci�n nacional', 'Lev�tico', 16, 17);
    // asignarPapa('Lev�tico', 'El acercamiento aceptable a Dios por medio de la santificaci�n');
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificaci�n', 'Leyes de santificaci�n para el pueblo');
    // asignarCategoriaPadre('Leyes de santificaci�n para el pueblo', 'Lev�tico', 18, 20);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificaci�n', 'Leyes de santificaci�n para el sacerdocio');
    // asignarCategoriaPadre('Leyes de santificaci�n para el sacerdocio', 'Lev�tico', 21, 22);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificaci�n', 'Leyes de santificaci�n para la adoraci�n');
    // asignarCategoriaPadre('Leyes de santificaci�n para la adoraci�n', 'Lev�tico', 23, 24);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificaci�n', 'Leyes de santificaci�n en la tierra prometida');
    // asignarCategoriaPadre('Leyes de santificaci�n en la tierra prometida', 'Lev�tico', 25, 26);
    // asignarPapa('El acercamiento aceptable a Dios por medio de la santificaci�n', 'Leyes de santificaci�n por medio de votos');
    // asignarCategoriaPadre('Leyes de santificaci�n por medio de votos', 'Lev�tico', 27, 27);

    // asignarPapa('El Pentateuco', 'N�meros');
    // asignarPapa('N�meros', 'La preparaci�n de la primera generaci�n para heredar la tierra prometida');
    // asignarPapa('La preparaci�n de la primera generaci�n para heredar la tierra prometida', 'La organizaci�n de Israel');
    // asignarCategoriaPadre('La organizaci�n de Israel', 'N�meros', 1, 4);
    // asignarPapa('La preparaci�n de la primera generaci�n para heredar la tierra prometida', 'La santificaci�n de Israel');
    // asignarCategoriaPadre('La santificaci�n de Israel', 'N�meros', 5, 10);
    // asignarPapa('N�meros', 'El fracaso de la primera generaci�n en heredar la tierra prometida');
    // asignarPapa('El fracaso de la primera generaci�n en heredar la tierra prometida', 'El fracaso de Israel en camino a Cades');
    // asignarCategoriaPadre('El fracaso de Israel en camino a Cades', 'N�meros', 11, 12);
    // asignarPapa('El fracaso de la primera generaci�n en heredar la tierra prometida', 'El fracaso de Israel en Cades');
    // asignarCategoriaPadre('El fracaso de Israel en Cades', 'N�meros', 13, 14);
    // asignarPapa('El fracaso de la primera generaci�n en heredar la tierra prometida', 'El fracaso de Israel en el desierto');
    // asignarCategoriaPadre('El fracaso de Israel en el desierto', 'N�meros', 15, 19);
    // asignarPapa('El fracaso de la primera generaci�n en heredar la tierra prometida', 'El fracaso de Israel en camino a Moab');
    // asignarCategoriaPadre('El fracaso de Israel en camino a Moab', 'N�meros', 20, 25);
    // asignarPapa('N�meros', 'La preparaci�n de la nueva generaci�n para heredar la tierra prometida');
    // asignarPapa('La preparaci�n de la nueva generaci�n para heredar la tierra prometida', 'La reorganizaci�n de Israel');
    // asignarCategoriaPadre('La reorganizaci�n de Israel', 'N�meros', 26, 27);
    // asignarPapa('La preparaci�n de la nueva generaci�n para heredar la tierra prometida', 'Las regulaciones sobre ofrendas y votos');
    // asignarCategoriaPadre('Las regulaciones sobre ofrendas y votos', 'N�meros', 28, 30);
    // asignarPapa('La preparaci�n de la nueva generaci�n para heredar la tierra prometida', 'La conquista y divisi�n de Israel');
    // asignarCategoriaPadre('La conquista y divisi�n de Israel', 'N�meros', 31, 36);

    // asignarPapa('El Pentateuco', 'Deuteronomio');
    // asignarPapa('Deuteronomio', 'Primer discurso de Mois�s');
    // asignarPapa('Primer discurso de Mois�s', 'Lo que Dios ha hecho por Israel');
    // asignarCategoriaPadre('Lo que Dios ha hecho por Israel', 'Deuteronomio', 1, 4);
    // asignarPapa('Deuteronomio', 'Segundo discurso de Mois�s');
    // asignarPapa('Segundo discurso de Mois�s', 'Las estipulaciones del convenio');
    // asignarCategoriaPadre('Las estipulaciones del convenio', 'Deuteronomio', 5, 11);
    // asignarPapa('Segundo discurso de Mois�s', 'Explicaci�n de leyes adicionales');
    // asignarCategoriaPadre('Explicaci�n de leyes adicionales', 'Deuteronomio', 12, 26);
    // asignarPapa('Deuteronomio', 'Tercer discurso de Mois�s');
    // asignarPapa('Tercer discurso de Mois�s', 'La confirmaci�n del convenio');
    // asignarCategoriaPadre('La confirmaci�n del convenio', 'Deuteronomio', 27, 28);
    // asignarPapa('Tercer discurso de Mois�s', 'El establecimiento del convenio');
    // asignarCategoriaPadre('El establecimiento del convenio', 'Deuteronomio', 29, 30);
    // asignarPapa('Tercer discurso de Mois�s', 'La transici�n prof�tica de Mois�s a Josu�');
    // asignarCategoriaPadre('La transici�n prof�tica de Mois�s a Josu�', 'Deuteronomio', 31, 34);

    // asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Josu�');
    // asignarPapa('Josu�', 'La conquista de Cana�n');
    // asignarPapa('La conquista de Cana�n', 'La preparaci�n de Israel para la conquista');
    // asignarCategoriaPadre('La preparaci�n de Israel para la conquista', 'Josu�', 1, 5);
    // asignarPapa('La conquista de Cana�n', 'Israel conquista Can�an');
    // asignarCategoriaPadre('Israel conquista Can�an', 'Josu�', 6, 12);
    // asignarPapa('Josu�', 'El asentamiento en Cana�n');
    // asignarPapa('El asentamiento en Cana�n', 'Asentamiento al este del Jord�n');
    // asignarCategoriaPadre('Asentamiento al este del Jord�n', 'Josu�', 13, 13);
    // asignarPapa('El asentamiento en Cana�n', 'Asentamiento al oeste del Jord�n');
    // asignarCategoriaPadre('Asentamiento al oeste del Jord�n', 'Josu�', 14, 19);
    // asignarPapa('El asentamiento en Cana�n', 'Asentamiento de la tribu de Lev�');
    // asignarCategoriaPadre('Asentamiento de la tribu de Lev�', 'Josu�', 20, 21);
    // asignarPapa('El asentamiento en Cana�n', 'Las condiciones para la permanencia en Can�an');
    // asignarCategoriaPadre('Las condiciones para la permanencia en Can�an', 'Josu�', 22, 24);

    // asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Jueces');
    // asignarPapa('Jueces','El deterioro de Israel');
    // asignarPapa('El deterioro de Israel','El fracaso de Israel en completar la conquista');
    // asignarCategoriaPadre('El fracaso de Israel en completar la conquista', 'Jueces', 1, 1);
    // asignarPapa('El deterioro de Israel','Las consecuencias por no completar la conquista');
    // asignarCategoriaPadre('Las consecuencias por no completar la conquista', 'Jueces', 2, 2);
    // asignarPapa('Jueces','La liberaci�n de Israel');
    // asignarPapa('La liberaci�n de Israel','La campa�a del sur');
    // asignarCategoriaPadre('La campa�a del sur', 'Jueces', 3, 3);
    // asignarPapa('La liberaci�n de Israel','La primera campa�a del norte');
    // asignarCategoriaPadre('La primera campa�a del norte', 'Jueces', 4, 5);
    // asignarPapa('La liberaci�n de Israel','La campa�a central');
    // asignarCategoriaPadre('La campa�a central', 'Jueces', 6, 9);
    // asignarPapa('La liberaci�n de Israel','La campa�a del este y segunda campa�a del norte');
    // asignarCategoriaPadre('La campa�a del este y segunda campa�a del norte', 'Jueces', 10, 12);
    // asignarPapa('La liberaci�n de Israel','La campa�a del oeste');
    // asignarCategoriaPadre('La campa�a del oeste', 'Jueces', 13, 16);
    // asignarPapa('Jueces','�La depravaci�n de Israel');
    // asignarPapa('La depravaci�n de Israel','El fracaso de Israel por la idolatr�a');
    // asignarCategoriaPadre('El fracaso de Israel por la idolatr�a', 'Jueces', 17, 18);
    // asignarPapa('La depravaci�n de Israel','El fracaso de Israel por la inmoralidad');
    // asignarCategoriaPadre('El fracaso de Israel por la inmoralidad', 'Jueces', 19, 19);
    // asignarPapa('La depravaci�n de Israel','El fracaso de Israel por la guerra entre las tribus');
    // asignarCategoriaPadre('El fracaso de Israel por la guerra entre las tribus', 'Jueces', 20, 21);

    // asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Rut');
    // asignarPapa('Rut','La demostraci�n del amor de Rut');
    // asignarPapa('La demostraci�n del amor de Rut','La decisi�n de Rut de permanecer con Noem�');
    // asignarCategoriaPadre('La decisi�n de Rut de permanecer con Noem�','Rut',1,1);
    // asignarPapa('La demostraci�n del amor de Rut','La devoci�n de Rut al cuidar de Noem�');
    // asignarCategoriaPadre('La devoci�n de Rut al cuidar de Noem�','Rut',2,2);
    // asignarPapa('Rut','La recompensa del amor de Rut');
    // asignarPapa('La recompensa del amor de Rut','Rut procura la redenci�n de Booz');
    // asignarCategoriaPadre('Rut procura la redenci�n de Booz','Rut',3,3);
    // asignarPapa('La recompensa del amor de Rut','Rut obtiene la redenci�n de Booz');
    // asignarCategoriaPadre('Rut obtiene la redenci�n de Booz','Rut',4,4);

    // asignarPapa('Libros hist�ricos del Antiguo Testamento', '1 Samuel');
    // asignarPapa('1 Samuel', 'Samuel, el �ltimo juez');
    // asignarPapa('Samuel, el �ltimo juez','Primera transici�n: De Eli a Samuel');
    // asignarCategoriaPadre('Primera transici�n: De Eli a Samuel','1 Samuel',1,3);
    // asignarPapa('Samuel, el �ltimo juez','La judicatura de Samuel');
    // asignarCategoriaPadre('La judicatura de Samuel','1 Samuel',4,7);
    // asignarPapa('1 Samuel', 'Sa�l, el primer rey');
    // asignarPapa('Sa�l, el primer rey','Segunda transici�n: De Samuel a Sa�l');
    // asignarCategoriaPadre('Segunda transici�n: De Samuel a Sa�l','1 Samuel',8,12);
    // asignarPapa('Sa�l, el primer rey','El reinado de Sa�l');
    // asignarCategoriaPadre('El reinado de Sa�l','1 Samuel',13,15);
    // asignarPapa('Sa�l, el primer rey','Tercera transici�n: De Sa�l a David');
    // asignarCategoriaPadre('Tercera transici�n: De Sa�l a David','1 Samuel',16,31);

    // asignarPapa('Libros hist�ricos del Antiguo Testamento', '2 Samuel');
    // asignarPapa('2 Samuel','Los triunfos de David');
    // asignarPapa('Los triunfos de David','Triunfos pol�ticos de David');
    // asignarCategoriaPadre('Triunfos pol�ticos de David','2 Samuel',1,5);
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

    asignarPapa('Libros hist�ricos del Antiguo Testamento', '1 Reyes');
    asignarCategoriaPadre('1 Reyes','1 Reyes',1,22); 

    asignarPapa('Libros hist�ricos del Antiguo Testamento', '2 Reyes');  
    asignarCategoriaPadre('2 Reyes','2 Reyes',1,25);

    asignarPapa('Libros hist�ricos del Antiguo Testamento', '1 Cr�nicas');    
    asignarCategoriaPadre('1 Cr�nicas','1 Cr�nicas',1,29);

    asignarPapa('Libros hist�ricos del Antiguo Testamento', '2 Cr�nicas');
    asignarCategoriaPadre('2 Cr�nicas','2 Cr�nicas',1,36);

    asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Esdras');
    asignarCategoriaPadre('Esdras','Esdras',1,10);

    asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Nehem�as');
    asignarCategoriaPadre('Nehem�as','Nehem�as',1,13);

    asignarPapa('Libros hist�ricos del Antiguo Testamento', 'Ester');
    asignarCategoriaPadre('Ester','Ester',1,10);

    // asignarPapa('Libros po�ticos', 'Job');
    // asignarPapa('Job','El dilema de Job');
    // asignarPapa('El dilema de Job','Introducci�n del dilema de Job');
    // asignarCategoriaPadre('Introducci�n del dilema de Job','Job',1,2);
    // asignarPapa('Job','Los debates de Job');
    // asignarCategoriaPadre('El primer ciclo de debate','Job',3,14);
    // asignarCategoriaPadre('El segundo ciclo de debate','Job',15,21);
    // asignarCategoriaPadre('El tercer ciclo de debate','Job',22,26);
    // asignarCategoriaPadre('La defensa final de Job','Job',27,31);
    // asignarCategoriaPadre('La soluci�n de Elih�','Job',32,37);
    // asignarPapa('Job','La redenci�n de Job');
    // asignarCategoriaPadre('La primera controversia de Job con Dios','Job',38,39);
    // asignarCategoriaPadre('La segunda controversia de Job con Dios','Job',40,42);

    // asignarPapa('Libros po�ticos', 'Salmos');
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

    // asignarPapa('Libros po�ticos', 'Proverbios');
    // asignarPapa('Proverbios','Proverbios atribuidos al rey Salom�n');
    // asignarPapa('Proverbios atribuidos al rey Salom�n','Proverbios para la juventud');
    // asignarPapa('Proverbios atribuidos al rey Salom�n','Proverbios de Salom�n');
    // asignarPapa('Proverbios','Proverbios de varios autores');
    // asignarPapa('Proverbios de varios autores','Las palabras de Agur');
    // asignarPapa('Proverbios de varios autores','Las palabras del rey Lemuel');
    // asignarCategoriaPadre('Proverbios para la juventud','Proverbios',1,9);
    // asignarCategoriaPadre('Proverbios de Salom�n','Proverbios',10,29);
    // asignarCategoriaPadre('Las palabras de Agur','Proverbios',30,30);
    // asignarCategoriaPadre('Las palabras del rey Lemuel','Proverbios',31,31);

    // asignarPapa('Libros po�ticos', 'Eclesiast�s');
    // asignarPapa('Eclesiast�s','Pruebas de que todo es vanidad');
    // asignarPapa('Pruebas de que todo es vanidad','La prueba de la experiencia');
    // asignarCategoriaPadre('La prueba de la experiencia','Eclesiast�s',1,2);
    // asignarPapa('Pruebas de que todo es vanidad','La prueba de la observaci�n');
    // asignarCategoriaPadre('La prueba de la observaci�n','Eclesiast�s',3,6);
    // asignarPapa('Eclesiast�s','Consejos para lidiar con la vanidad');
    // asignarPapa('Consejos para lidiar con la vanidad','C�mo enfrentar un mundo inicuo');
    // asignarCategoriaPadre('C�mo enfrentar un mundo inicuo','Eclesiast�s',7,12);
    
    // asignarPapa('Libros po�ticos', 'Cantares');
    // asignarPapa('Cantares','El Cantar de los Cantares');
    // asignarPapa('El Cantar de los Cantares','El nacimiento del amor');
    // asignarPapa('El Cantar de los Cantares','La expansi�n del amor');
    // asignarCategoriaPadre('El nacimiento del amor','Cantares',1,4);
    // asignarCategoriaPadre('La expansi�n del amor','Cantares',5,8);

    // asignarPapa('Profetas mayores', 'Isa�as');
    // asignarPapa('Isa�as','Profec�as de juicio');
    // asignarPapa('Profec�as de juicio','Contexto del profeta Isa�as');
    // asignarCategoriaPadre('Contexto del profeta Isa�as','Isa�as',1,1);
    // asignarPapa('Profec�as de juicio','Primeras profec�as de Isa�as');
    // asignarCategoriaPadre('Primeras profec�as de Isa�as','Isa�as',2,5);
    // asignarPapa('Profec�as de juicio','El llamamiento del profeta Isa�as');
    // asignarCategoriaPadre('El llamamiento del profeta Isa�as','Isa�as',6,6);
    // asignarPapa('Profec�as de juicio','Primeras profec�as mesi�nicas');
    // asignarCategoriaPadre('Primeras profec�as mesi�nicas','Isa�as',7,12);
    // asignarPapa('Profec�as de juicio','Cargas contra las naciones');
    // asignarCategoriaPadre('Cargas contra las naciones','Isa�as',13,23);
    // asignarPapa('Profec�as de juicio','Profec�as de la venida del reino de Dios');
    // asignarCategoriaPadre('Profec�as de la venida del reino de Dios','Isa�as',24,27);
    // asignarPapa('Profec�as de juicio','Profec�as de ayes y esperanza');
    // asignarCategoriaPadre('Profec�as de ayes y esperanza','Isa�as',28,35);
    // asignarPapa('Isa�as','Material hist�rico sobre Ezequ�as');
    // asignarPapa('Material hist�rico sobre Ezequ�as','Ezequ�as es librado de Asiria');
    // asignarCategoriaPadre('Ezequ�as es librado de Asiria','Isa�as',36,37);
    // asignarPapa('Material hist�rico sobre Ezequ�as','Ezequ�as es librado de la enfermedad');
    // asignarCategoriaPadre('Ezequ�as es librado de la enfermedad','Isa�as',38,38);
    // asignarPapa('Material hist�rico sobre Ezequ�as','El pecado de Ezequ�as');
    // asignarCategoriaPadre('El pecado de Ezequ�as','Isa�as',39,39);
    // asignarPapa('Isa�as','Profec�as de salvaci�n y de esperanza');
    // asignarPapa('Profec�as de salvaci�n y de esperanza','Profec�as de la restauraci�n de Israel');
    // asignarCategoriaPadre('Profec�as de la restauraci�n de Israel','Isa�as',40,48);
    // asignarPapa('Profec�as de salvaci�n y de esperanza','Profec�as del sufrimiento del Mes�as');
    // asignarCategoriaPadre('Profec�as del sufrimiento del Mes�as','Isa�as',49,53);
    // asignarPapa('Profec�as de salvaci�n y de esperanza','Profec�as de la redenci�n de Israel');
    // asignarCategoriaPadre('Profec�as de la redenci�n de Israel','Isa�as',54,59);
    // asignarPapa('Profec�as de salvaci�n y de esperanza','Profec�as del futuro glorioso de Israel');
    // asignarCategoriaPadre('Profec�as del futuro glorioso de Israel','Isa�as',60,66);

    // asignarPapa('Profetas mayores', 'Jerem�as');
    // asignarCategoriaPadre('Comisi�n y llamamiento del profeta Jerem�as','Jerem�as',1,1);
    // asignarCategoriaPadre('La condenaci�n de Jud�','Jerem�as',2,25);
    // asignarCategoriaPadre('Los conflictos de Jerem�as','Jerem�as',26,29);
    // asignarCategoriaPadre('La futura restauraci�n de Jerusal�n','Jerem�as',30,33);
    // asignarCategoriaPadre('La presente ca�da de Jerusal�n','Jerem�as',34,45);
    // asignarCategoriaPadre('Profec�as contra Egipto','Jerem�as',46,46);
    // asignarCategoriaPadre('Profec�as contra Filistea','Jerem�as',47,47);
    // asignarCategoriaPadre('Profec�as contra Moab','Jerem�as',48,48);
    // asignarCategoriaPadre('Profec�as contra varias naciones','Jerem�as',49,49);
    // asignarCategoriaPadre('Profec�as contra Babilonia','Jerem�as',50,51);
    // asignarCategoriaPadre('El sitio de Jerusal�n y el exilio a Babilonia','Jerem�as',52,52);

    // asignarPapa('Profetas mayores', 'Lamentaciones');
    // asignarCategoriaPadre('Destrucci�n de Jerusal�n','Lamentaciones',1,1);
    // asignarCategoriaPadre('La ira de Dios y las tristezas de Jerusal�n','Lamentaciones',2,2);
    // asignarCategoriaPadre('Oraci�n por misericordia sobre Jerusal�n','Lamentaciones',3,3);
    // asignarCategoriaPadre('La siega de Jerusal�n','Lamentaciones',4,4);
    // asignarCategoriaPadre('Oraci�n por la restauraci�n de Jerusal�n','Lamentaciones',5,5);

    // asignarPapa('Profetas mayores', 'Ezequiel');
    // asignarCategoriaPadre('La visi�n de Ezequiel de la gloria de Dios','Ezequiel',1,1);
    // asignarCategoriaPadre('La comisi�n y llamamiento de Ezequiel','Ezequiel',2,3);
    // asignarCategoriaPadre('Cuatro se�ales del juicio venidero','Ezequiel',4,5);
    // asignarCategoriaPadre('Dos mensajes del juicio venidero','Ezequiel',6,7);
    // asignarCategoriaPadre('La visi�n del juicio venidero','Ezequiel',8,11);
    // asignarCategoriaPadre('Se�ales, mensajes y par�bolas del juicio venidero','Ezequiel',12,24);
    // asignarCategoriaPadre('Juicio sobre Am�n, Edom y Filistea','Ezequiel',25,25);
    // asignarCategoriaPadre('Juicio sobre Tiro, Sid�n y Egipto','Ezequiel',26,32);
    // asignarCategoriaPadre('Atalaya de restauraci�n','Ezequiel',33,35);
    // asignarCategoriaPadre('Promesas de restauraci�n','Ezequiel',36,37);
    // asignarCategoriaPadre('Victoria sobre Gog y Magog','Ezequiel',38,39);
    // asignarCategoriaPadre('La restauraci�n de Israel en el reino','Ezequiel',40,48);

    // asignarPapa('Profetas mayores', 'Daniel');
    // asignarCategoriaPadre('Contexto hist�rico de Daniel','Daniel',1,1);
    // asignarCategoriaPadre('El sue�o de Nabucodonosor','Daniel',2,2);
    // asignarCategoriaPadre('La imagen de oro y el horno ardiente','Daniel',3,3);
    // asignarCategoriaPadre('Nabucodonosor sue�a con un gran �rbol','Daniel',4,4);
    // asignarCategoriaPadre('Belsasar y la escritura sobre el muro','Daniel',5,5);
    // asignarCategoriaPadre('Decreto de Dar�o y rescate de los leones','Daniel',6,6);
    // asignarCategoriaPadre('La visi�n de las cuatro bestias','Daniel',7,7);
    // asignarCategoriaPadre('La visi�n del carnero y el macho cabr�o','Daniel',8,8);
    // asignarCategoriaPadre('Visi�n de las setenta semanas','Daniel',9,9);
    // asignarCategoriaPadre('Visi�n de Daniel sobre el futuro de Israel','Daniel',10,12);
    
    // asignarPapa('Profetas menores', 'Oseas');
    // asignarCategoriaPadre('La esposa ad�ltera y su marido fiel','Oseas',1,3);
    // asignarCategoriaPadre('Israel ad�ltero y su Se�or fiel','Oseas',4,14);
    
    // asignarPapa('Profetas menores', 'Joel');
    // asignarCategoriaPadre('El d�a del Se�or en el pasado','Joel',1,1);
    // asignarCategoriaPadre('El d�a del Se�or en el futuro','Joel',2,3);
    
    // asignarPapa('Profetas menores', 'Am�s');
    // asignarCategoriaPadre('Damasco, Gaza, Tiro, Edom y Am�n','Am�s',1,1);
    // asignarCategoriaPadre('Moab, Jud� e Israel','Am�s',2,2);
    // asignarCategoriaPadre('Primer serm�n: el Israel presente','Am�s',3,3);
    // asignarCategoriaPadre('Segundo serm�n: el Israel pasado','Am�s',4,4);
    // asignarCategoriaPadre('Tercer serm�n: el Israel futuro','Am�s',5,6);
    // asignarCategoriaPadre('La langosta, el fuego y la plomada de alba�il','Am�s',7,7);
    // asignarCategoriaPadre('El canastillo de fruta de verano','Am�s',8,8);
    // asignarCategoriaPadre('El Se�or en el altar','Am�s',9,9);

    // asignarPapa('Profetas menores', 'Abd�as');
    // asignarCategoriaPadre('El juicio contra Edom y el d�a del Se�or','Abd�as',1,1);

    // asignarPapa('Profetas menores', 'Jon�s');
    // asignarCategoriaPadre('La primera comisi�n de Jon�s', 'Jon�s', 1, 2);
    // asignarCategoriaPadre('La segunda comisi�n de Jon�s', 'Jon�s', 3, 4);

    // asignarPapa('Profetas menores', 'Miqueas');
    // asignarCategoriaPadre('La predicci�n del juicio contra Israel y Jud�','Miqueas',1,3);
    // asignarCategoriaPadre('La predicci�n del reino venidero','Miqueas',4,5);
    // asignarCategoriaPadre('El caso de Dios contra Israel','Miqueas',6,6);
    // asignarCategoriaPadre('Esperanza futura para el pueblo de Dios','Miqueas',7,7);
    
    // asignarPapa('Profetas menores', 'Nah�m');
    // asignarCategoriaPadre('Se decreta la destrucci�n de N�nive','Nah�m',1,1);
    // asignarCategoriaPadre('Se describe la destrucci�n de N�nive','Nah�m',2,2);
    // asignarCategoriaPadre('Se justifica la destrucci�n de N�nive','Nah�m',3,3);
    
    // asignarPapa('Profetas menores', 'Habacuc');
    // asignarCategoriaPadre('Las preguntas de Habacuc','Habacuc',1,2);
    // asignarCategoriaPadre('La alabanza de Habacuc','Habacuc',3,3);
    
    // asignarPapa('Profetas menores', 'Sofon�as');
    // asignarCategoriaPadre('El juicio sobre Jud� y esperanza del remanente','Sofon�as',1,3);
    
    // asignarPapa('Profetas menores', 'Hageo');
    // asignarCategoriaPadre('La gloria del templo y las bendiciones de la obediencia','Hageo',1,2);
    
    // asignarPapa('Profetas menores', 'Zacar�as');
    // asignarCategoriaPadre('Las ocho visiones de Zacar�as','Zacar�as',1,6);
    // asignarCategoriaPadre('Los cuatro mensajes de Zacar�as','Zacar�as',7,38);
    // asignarCategoriaPadre('Las dos cargas de Zacar�as','Zacar�as',9,14);
    
    // asignarPapa('Profetas menores', 'Malaqu�as');
    // asignarCategoriaPadre('Privilegios, contaminaci�n y promesas de la naci�n','Malaqu�as',1,4);
}

function librosNuevoTestamento()
{
    asignarPapa('Escrituras', 'Nuevo Testamento');
    asignarPapa('Nuevo Testamento', 'Los evangelios');
    asignarPapa('Nuevo Testamento', 'Libros hist�ricos del Nuevo Testamento');
    asignarPapa('Nuevo Testamento', 'Ep�stolas paulinas');
    asignarPapa('Nuevo Testamento', 'Ep�stolas universales');
    asignarPapa('Nuevo Testamento', 'Libros prof�ticos');

    asignarPapa('Los evangelios', 'Mateo');
    asignarCategoriaPadre('Mateo', 'Mateo', 1, 28);
    asignarPapa('Los evangelios', 'Marcos');
    asignarCategoriaPadre('Marcos', 'Marcos', 1, 16);
    asignarPapa('Los evangelios', 'Lucas');
    asignarCategoriaPadre('Lucas', 'Lucas', 1, 24);
    asignarPapa('Los evangelios', 'Juan');
    asignarCategoriaPadre('Juan', 'Juan', 1, 21);

    asignarPapa('Libros hist�ricos del Nuevo Testamento', 'Hechos');
    asignarCategoriaPadre('Hechos', 'Hechos', 1, 28);

    asignarPapa('Ep�stolas paulinas', 'Romanos');
    asignarCategoriaPadre('Romanos', 'Romanos', 1, 28);
    asignarPapa('Ep�stolas paulinas', '1 Corintios');
    asignarCategoriaPadre('1 Corintios', '1 Corintios', 1, 28);
    asignarPapa('Ep�stolas paulinas', '2 Corintios');
    asignarCategoriaPadre('2 Corintios', '2 Corintios', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'G�latas');
    asignarCategoriaPadre('G�latas', 'G�latas', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Efesios');
    asignarCategoriaPadre('Efesios', 'Efesios', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Filipenses');
    asignarCategoriaPadre('Filipenses', 'Filipenses', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Colosenses');
    asignarCategoriaPadre('Colosenses', 'Colosenses', 1, 28);
    asignarPapa('Ep�stolas paulinas', '1 Tesalonicenses');
    asignarCategoriaPadre('1 Tesalonicenses', '1 Tesalonicenses', 1, 28);
    asignarPapa('Ep�stolas paulinas', '2 Tesalonicenses');
    asignarCategoriaPadre('2 Tesalonicenses', '2 Tesalonicenses', 1, 28);
    asignarPapa('Ep�stolas paulinas', '1 Timoteo');
    asignarCategoriaPadre('1 Timoteo', '1 Timoteo', 1, 28);
    asignarPapa('Ep�stolas paulinas', '2 Timoteo');
    asignarCategoriaPadre('2 Timoteo', '2 Timoteo', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Tito');
    asignarCategoriaPadre('Tito', 'Tito', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Filem�n');
    asignarCategoriaPadre('Filem�n', 'Filem�n', 1, 28);
    asignarPapa('Ep�stolas paulinas', 'Hebreos');
    asignarCategoriaPadre('Hebreos', 'Hebreos', 1, 28);

    asignarPapa('Ep�stolas universales', 'Santiago');
    asignarCategoriaPadre('Santiago', 'Santiago', 1, 28);
    asignarPapa('Ep�stolas universales', '1 Pedro');
    asignarCategoriaPadre('1 Pedro', '1 Pedro', 1, 28);
    asignarPapa('Ep�stolas universales', '2 Pedro');
    asignarCategoriaPadre('2 Pedro', '2 Pedro', 1, 28);
    asignarPapa('Ep�stolas universales', '1 Juan');
    asignarCategoriaPadre('1 Juan', '1 Juan', 1, 28);
    asignarPapa('Ep�stolas universales', '2 Juan');
    asignarCategoriaPadre('2 Juan', '2 Juan', 1, 1);
    asignarPapa('Ep�stolas universales', '3 Juan');
    asignarCategoriaPadre('3 Juan', '3 Juan', 1, 1);
    asignarPapa('Ep�stolas universales', 'Judas');
    asignarCategoriaPadre('Judas', 'Judas', 1, 1);

    asignarPapa('Libros prof�ticos', 'Apocalipsis');
    asignarCategoriaPadre('Apocalipsis', 'Apocalipsis', 1, 28);
}

function librosLibroDeMormon()
{
    asignarPapa('Escrituras', 'Libro de Morm�n');
    asignarPapa('Libro de Morm�n', 'Planchas menores');
    asignarPapa('Libro de Morm�n', 'Puente editorial');
    asignarPapa('Libro de Morm�n', 'Planchas mayores');
    asignarPapa('Libro de Morm�n', 'Escritos de Morm�n');
    asignarPapa('Libro de Morm�n', 'Adiciones de Moroni');

    asignarPapa('Planchas menores', '1 Nefi');
    asignarCategoriaPadre('1 Nefi', '1 Nefi', 1, 22);

    asignarPapa('Planchas menores', '2 Nefi');
    asignarPapa('Planchas menores', 'Jacob');
    asignarPapa('Planchas menores', 'En�s');
    asignarPapa('Planchas menores', 'Jarom');
    asignarPapa('Planchas menores', 'Omni');
    asignarPapa('Puente editorial', 'Palabras de Morm�n');
    asignarPapa('Planchas mayores', 'Mos�ah');
    asignarPapa('Planchas mayores', 'Alma');
    asignarPapa('Planchas mayores', 'Helam�n');
    asignarPapa('Planchas mayores', '3 Nefi');
    asignarPapa('Planchas mayores', '4 Nefi');
    asignarPapa('Escritos de Morm�n', 'Morm�n');
    asignarPapa('Adiciones de Moroni', 'Eter');
    asignarPapa('Adiciones de Moroni', 'Moroni');
}

function librosDoctrinaYConvenios()
{
    asignarPapa('Escrituras', 'Doctrina y Convenios');

    asignarPapa('Doctrina y Convenios', 'Nueva York');
    asignarPapa('Nueva York', 'Traducci�n del Libro de Morm�n');
    asignarCategoriaPadre('Traducci�n del Libro de Morm�n', 'Doctrina y Convenios', 2, 19);
    asignarPapa('Nueva York', 'Organizaci�n de la Iglesia');
    asignarCategoriaPadre('Organizaci�n de la Iglesia', 'Doctrina y Convenios', 20, 28);
    asignarPapa('Nueva York', 'Difusi�n de la Iglesia');
    asignarCategoriaPadre('Difusi�n de la Iglesia', 'Doctrina y Convenios', 29, 29);
    asignarCategoriaPadre('Difusi�n de la Iglesia', 'Doctrina y Convenios', 74, 74);
    asignarCategoriaPadre('Difusi�n de la Iglesia', 'Doctrina y Convenios', 30, 36);
    asignarPapa('Nueva York', 'Movimiento de la Iglesia');
    asignarCategoriaPadre('Movimiento de la Iglesia', 'Doctrina y Convenios', 37, 40);

    asignarPapa('Doctrina y Convenios', 'Ohio');
    asignarPapa('Ohio', 'La Consagraci�n');
    asignarCategoriaPadre('La Consagraci�n','Doctrina y Convenios',41,56);
    asignarPapa('Ohio', 'Viaje a Missouri');
    asignarCategoriaPadre('Viaje a Missouri','Doctrina y Convenios',57,62);
    asignarPapa('Ohio', 'Traducci�n y revelaciones');
    asignarCategoriaPadre('Traducci�n y revelaciones','Doctrina y Convenios',63,73);
    asignarCategoriaPadre('Traducci�n y revelaciones','Doctrina y Convenios',1,1);
    asignarCategoriaPadre('Traducci�n y revelaciones','Doctrina y Convenios',75,83);
    asignarCategoriaPadre('Traducci�n y revelaciones','Doctrina y Convenios',133,133);
    asignarCategoriaPadre('Traducci�n y revelaciones','Doctrina y Convenios',107,107);
    asignarPapa('Ohio', 'Construcci�n del templo de Kirtland');
    asignarCategoriaPadre('Construcci�n del templo de Kirtland','Doctrina y Convenios',84,97);
    asignarCategoriaPadre('Construcci�n del templo de Kirtland','Doctrina y Convenios',99,99);
    asignarPapa('Ohio', 'La redenci�n de Si�n');
    asignarCategoriaPadre('La redenci�n de Si�n','Doctrina y Convenios',98,98);
    asignarCategoriaPadre('La redenci�n de Si�n','Doctrina y Convenios',100,106);
    asignarPapa('Ohio', 'La casa del Se�or en Kirtland');
    asignarCategoriaPadre('La casa del Se�or en Kirtland','Doctrina y Convenios',107,112);
    asignarCategoriaPadre('La casa del Se�or en Kirtland','Doctrina y Convenios',134,134);
    asignarCategoriaPadre('La casa del Se�or en Kirtland','Doctrina y Convenios',137,137);

    asignarPapa('Doctrina y Convenios', 'Missouri');
    asignarPapa('Missouri', 'Eventos en Far West');
    asignarCategoriaPadre('Eventos en Far West','Doctrina y Convenios',113,120);
    asignarPapa('Missouri', 'La c�rcel de Liberty');
    asignarCategoriaPadre('La c�rcel de Liberty','Doctrina y Convenios',121,123);

    asignarPapa('Doctrina y Convenios', 'Illinois');
    asignarPapa('Illinois', 'La obra del templo de Nauvoo');
    asignarCategoriaPadre('La obra del templo de Nauvoo','Doctrina y Convenios',124,129);
    asignarPapa('Illinois', 'Recepci�n de doctrinas eternas');
    asignarCategoriaPadre('Recepci�n de doctrinas eternas','Doctrina y Convenios',130,132);
    asignarCategoriaPadre('Recepci�n de doctrinas eternas','Doctrina y Convenios',135,135);

    asignarPapa('Doctrina y Convenios', 'El oeste');
    asignarPapa('El oeste','Instrucciones en Winter Quarters');
    asignarCategoriaPadre('Instrucciones en Winter Quarters', 'Doctrina y Convenios', 136, 136);
    asignarPapa('El oeste', 'Declaraciones Oficiales');
    asignarCategoriaPadre('Declaraciones Oficiales', 'Declaraci�n Oficial', 1, 2);
    asignarPapa('El oeste','Revelaci�n sobre la redenci�n de los muertos');
    asignarCategoriaPadre('Revelaci�n sobre la redenci�n de los muertos', 'Doctrina y Convenios', 138, 138);
}

function librosPerlaDeGranPrecio()
{
    asignarPapa('Escrituras', 'Perla de Gran Precio');
    asignarPapa('Perla de Gran Precio', 'Relacionados con el Antiguo Testamento');
    asignarPapa('Perla de Gran Precio', 'Relacionados con el Nuevo Testamento');
    asignarPapa('Perla de Gran Precio', 'Relacionados con la Restauraci�n');
    asignarPapa('Relacionados con el Antiguo Testamento', 'Libro de Mois�s');
    asignarCategoriaPadre('Libro de Mois�s', 'Mois�s', 1, 8);
    asignarPapa('Relacionados con el Antiguo Testamento', 'Libro de Abraham');
    asignarCategoriaPadre('Libro de Abraham', 'Abraham', 1, 5);
    asignarPapa('Relacionados con el Nuevo Testamento', 'Jos� Smith - Mateo');
    asignarCategoriaPadre('Jos� Smith - Mateo', 'Jos� Smith-Mateo', 1, 1);
    asignarPapa('Relacionados con la Restauraci�n', 'Jos� Smith - Historia');
    asignarCategoriaPadre('Jos� Smith - Historia', 'Jos� Smith-Historia', 1, 1);
    asignarPapa('Relacionados con la Restauraci�n', 'Art�culos de Fe');
    asignarCategoriaPadre('Art�culos de Fe', 'Art�culos de Fe', 1, 1);
}
