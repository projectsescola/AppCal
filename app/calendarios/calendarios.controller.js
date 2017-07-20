app.controller('calendariosController', function($scope, $mdDialog, $mdToast, calendariosFactory){
 
    // leer calendarios
    $scope.readCalendarios = function(){
 
        // usar calendarios factory
        calendariosFactory.readCalendarios().then(function successCallback(response){
            $scope.calendarios = response.data.registros;
        }, function errorCallback(response){
            $scope.showToast("Unable to read record.");
        });
 
    }
     
    // show 'create product form' in dialog box
    $scope.showCreateCalendarioForm = function(event){
     
        $mdDialog.show({
            controller: DialogController,
            templateUrl: './app/calendarios/create_calendario.template.html',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            scope: $scope,
            preserveScope: true,
            fullscreen: true // Only for -xs, -sm breakpoints.
        });
    }
     
    // create new product
    $scope.createCalendario = function(){
     
        calendariosFactory.createCalendario($scope).then(function successCallback(response){
     
            // tell the user new product was created
            $scope.showToast(response.data.message);
     
            // refresh the list
            $scope.readCalendarios();
     
            // close dialog
            $scope.cancel();
     
            // remove form values
            $scope.clearCalendarioForm();
     
        }, function errorCallback(response){
            $scope.showToast("Unable to create record.");
        });
    }

    // clear variable / form values
    $scope.clearCalendarioForm = function(){
        $scope.tipo = "";
        $scope.descripcion = "";
        $scope.curso_practica_id = "";
        $scope.escuela_barco_id = "";
        $scope.patron_id = "";
        $scope.capacidad_maxima = "";
        $scope.alumnos_apuntados = "";
        $scope.observaciones = "";
        $scope.estado = "";
        $scope.modalidad_horario = "";
    }

    // show toast message
    $scope.showToast = function(message){
        $mdToast.show(
            $mdToast.simple()
                .textContent(message)
                .hideDelay(3000)
                .position("top right")
        );
    }
 
    // readOneProduct will be here
     
    // methods for dialog box
    function DialogController($scope, $mdDialog) {
        $scope.cancel = function() {
            $mdDialog.cancel();
        };
    }
});