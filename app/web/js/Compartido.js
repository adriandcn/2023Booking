function GetDataAjaxImagenes(calendarid) {

    console.log("Calendario ID: "+calendarid);
    var url = "https://127.0.0.1/BookiW/index.php?controller=pjAdminCalendars&action=pjActionCargarFotos&calendar="+calendarid;
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            console.log(data);

        },
        error: function (data) {
            var errors = data.responseJSON;
            if (errors) {
                $.each(errors, function (i) {
                    console.log(errors[i]);
                });
            }
        }
    });
}

function AjaxContainerRetrunMessage($formulario, $id) {
    url = "https://127.0.0.1/BookiW/index.php?controller=pjAdminCalendars&action=pjActionDeleteFotos&foto="+$id;
    console.log("url Borrar: "+url);
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            console.log(data);

        },
        error: function (data) {
            var errors = data.responseJSON;
            if (errors) {
                $.each(errors, function (i) {
                    console.log(errors[i]);
                });
            }
        }
    });

}

function GetDataAjaxImagenesAgrupamiento(calendarid) {
    console.log("Calendario ID: "+calendarid);
    var url = "https://127.0.0.1/BookiW/index.php?controller=pjAdminGroup&action=pjActionCargarFotos&agrupamiento="+calendarid;
    console.log('URL',url);
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            console.log(data);

        },
        error: function (data) {
            var errors = data.responseJSON;
            if (errors) {
                $.each(errors, function (i) {
                    console.log(errors[i]);
                });
            }
        }
    });
}

function AjaxContainerRetrunMessageAgrupamiento($formulario, $id) {
    url = "https://127.0.0.1/BookiW/index.php?controller=pjAdminGroup&action=pjActionDeleteFotos&foto="+$id;
    console.log("url Borrar: "+url);
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function (data) {
            console.log(data);
        },
        error: function (data) {
            var errors = data.responseJSON;
            if (errors) {
                $.each(errors, function (i) {
                    console.log(errors[i]);
                });
            }
        }
    });
}



