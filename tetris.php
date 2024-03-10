<?php

enum Movimiento { // clase para enumarar los movimientos
    case DOWN;
    case LEFT;
    case RIGHT;
    case ROTATE;
    case QUIT;
}

function tetris() {
    $game = 
        [["🔳", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔳", "🔳", "🔳", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"],
        ["🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲", "🔲"]];

    
    imprimirPantalla($game);

    $estadoInicial = 0;
    $input = '';

    while ($input != 'x') { // funciona con true, pero continúa un error en caso de querer salir, o forzar el cierre. no es la condición correcta
        $input = trim(fgets(STDIN));

        switch ($input) {
            case 'a':
                $movimiento = Movimiento::LEFT;
                break;
            case 'd':
                $movimiento = Movimiento::RIGHT;
                break;
            case 's':
                $movimiento = Movimiento::DOWN;
                break;
            case 'r':
                $movimiento = Movimiento::ROTATE;
                break;
            case 'x':
                echo "fin\n";
                break;
        }

        $result = moverPieza($game, $movimiento, $estadoInicial);
        
        $game = $result["game"];
        $estadoInicial = $result["estadoInicial"];
    }

    echo "\nPartida terminada. ¡Gracias por jugar!\n";
}
/**
 * @array $game
 */

function moverPieza($game, Movimiento $movimiento, $estadoInicial){
    
    $nuevaPantalla = array_fill(0, 10, array_fill(0, 10, "🔲")); // crea una nueva matriz de 10x10 de los colores blancos
    //$movimientoValido = true;

    $rotacionPieza = 0;
    $rotaciones = [ // tuplas de coordenadas
        [["x" => 1, "y" => 1],
        ["x" => 0, "y" => 0],
        ["x" => -2, "y" => 0],
        ["x" => -1, "y" => -1]],
        [["x" => 0, "y" => 1],
        ["x" => -1, "y" => 0],
        ["x" => 0, "y" => -1],
        ["x" => 1, "y" => -2]],
        [["x" => 0, "y" => 2],
        ["x" => 1, "y" => 1],
        ["x" => -1, "y" => 1],
        ["x" => -2, "y" => 0]],
        [["x" => 0, "y" => 1],
        ["x" => 1, "y" => 0],
        ["x" => 2, "y" => -1],
        ["x" => 1, "y" => -2]],
    ];

    $nuevaRotacion = $estadoInicial;
    if ($movimiento == Movimiento::ROTATE) {
        $nuevaRotacion = ($estadoInicial == 3) ? 0 : $estadoInicial + 1;
    }
    
    // recorro el array para acceder a las filas y a las columnas
    foreach($game as $indiceFila => $fila) { // acordarse del simbolo de asociación
        foreach($fila as $indiceColumna => $columna) {
            if ($columna == "🔳") {

                $nuevoIndiceFila = 0;
                $nuevoIndiceColumna = 0;

                switch ($movimiento) {
                    case Movimiento::DOWN:
                        $nuevoIndiceFila = $indiceFila + 1; // el color negro siempre se mantiene en la misma fila, sólo baja.
                        $nuevoIndiceColumna = $indiceColumna; // si vas para abajo siempre te mantenes en la misma fila, indp donde estés
                        break;
                    case Movimiento::LEFT:
                        $nuevoIndiceFila = $indiceFila;
                        $nuevoIndiceColumna = $indiceColumna - 1; // para movernos a los costados, nos movemos en uno por las columnas
                        break;
                    case Movimiento::RIGHT:
                        $nuevoIndiceFila = $indiceFila;
                        $nuevoIndiceColumna = $indiceColumna + 1; // tanto para izq como para der
                        break;
                    case Movimiento::ROTATE: // acceder a las coordenadas de la rotacion
                        $nuevoIndiceFila = $indiceFila + $rotaciones[$nuevaRotacion][$rotacionPieza]["x"];
                        $nuevoIndiceColumna = $indiceColumna + $rotaciones[$nuevaRotacion][$rotacionPieza]["y"];
                        $rotacionPieza += 1;
                        break;
                    }
                if($nuevoIndiceFila > 9 || $nuevoIndiceColumna > 9 || $nuevoIndiceColumna < 0) {
                    echo "\nMovimiento inválido\n";
                    return ["game" => $game, "estadoInicial" => $estadoInicial];
                } else {
                    $nuevaPantalla[$nuevoIndiceFila][$nuevoIndiceColumna] = "🔳";
                }
            }
        }
    }
/*
    $movimientoValido = false; <- utilizada en el if de arriba

    if(!$movimientoValido) {
        die(); // funcion de stackoverflow pero no see
    }

    if($movimientoValido) {
        imprimirPantalla($nuevaPantalla); //imprime el nuevo tetris con el desplazamiento
    }
*/
    imprimirPantalla($nuevaPantalla);

    return ["game" => $nuevaPantalla, "estadoInicial" => $nuevaRotacion];
}

// La función implode sirve para concatenar los elementos que van siendo recorridos por map, la función 'strval' convierte a string los elementos de la fila. También puedo usar un foreach con otro foreach anidado.

function imprimirPantalla($game){
    echo "\nMover: s abajo, a izq, d der, x salir: ";
    echo "Partida tetris\n";
    foreach ($game as $fila) {
        echo implode(" ", array_map('strval', $fila));
        echo "\n";
    }
}

// ejecutar
$gameplay = tetris();