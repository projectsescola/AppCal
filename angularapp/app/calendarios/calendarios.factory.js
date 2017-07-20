app.factory("calendariosFactory", function($http){
 
    var factory = {};
 
    // leer todos los calendarios
    factory.readCalendarios = function(){
        return $http({
            method: 'GET',
            url: 'http://calendarios.escolaportbarcelona.com/api/v1/cursos'
        });
    };
     
    // crear calendario
    /*factory.createCalendario = function($scope){
        return $http({
            method: 'POST',
            data: {
                'tipo' : $scope.tipo,
                'descripcion' : $scope.descripcion,
                'curso_practica_id' : $scope.curso_practica_id,
                'escuela_barco_id' : $scope.escuela_barco_id,
                'patron_id' : $scope.patron_id,
                'capacidad_maxima' : $scope.capacidad_maxima,
                'alumnos_apuntados' : $scope.alumnos_apuntados,
                'observaciones' : $scope.observaciones,
                'estado' : $scope.estado,
                'modalidad_horario' : $scope.modalidad_horario,
            },
            url: 'http://localhost/calendaris/ApiCalendarios/calendario/create.php'
        });
    };*/

    // readOneProduct will be here
     
    return factory;
});