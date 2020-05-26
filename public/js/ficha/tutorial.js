function onClickNext(driver, doAction, delayTime = 200)
{
    driver.preventMove();
    doAction();
    if(delayTime)
    {
        setTimeout(() => {
            driver.moveNext();
        }, delayTime);
    }else
        driver.moveNext();
}

const driver = new Driver({
    doneBtnText: 'Finalizar',
    closeBtnText: 'Cerrar',
    onReset: () =>
    {
        localStorage.setItem('tutorial', 'true');
        $('#datos-basicos-tab').click();
    }
});


$(function () {
    driver.defineSteps([
        {
            element: '#progreso-view',
            popover: {
                title: 'Progreso',
                description: 'Desde este componente usted podrá ver su progreso a medida que llena los datos requeridos.',
                position: 'left-center'
            }
        },
        {
            element: '#tab-list',
            popover: {
                title: 'Tabs',
                description: 'Usted Podrá navegar en las diferentes vistas, a través de las tabs mostradas',
                position: 'top-center'
            }
        },
        {
          element: '#datos-personales-form',
          popover: {
            title: 'Datos Personales',
            description: 'En este apartado usted deberá ingresar los datos personales que aún se encuentren sin llenar.',
            position: 'top-center'
          }
        },
        {
            element: '#imagen-perfil-form',
            popover: {
              title: 'Imagen de perfil',
              description: 'Usted podrá actualizar su imagen de perfil profesional y aprobarala.',
              position: 'left'
            }
        },
        {
            element: '#documento-info-form',
            popover: {
              title: 'Documento',
              description: 'En este apartado usted deberá ingresar la fecha de expedición de su documento, en caso de que este'+
              ' se encuentre con inconsistencias por favor comunicarse con Admisiones y Registro.',
              position: 'top-center'
            }
        },
        {
            element: '#info-contacto-form',
            popover: {
              title: 'Contacto',
              description: 'Usted deberá proveer información de contacto actualizada.',
              position: 'top-center'
            }
        },
        {
            element: '#btn-guardar',
            popover: {
              title: 'Finalizar',
              description: 'Una vez que finalice, rectifique su información y pulse el botón “guardar”.',
              position: 'top-center'
            },
            onNext: () =>
            {
                driver.preventMove();
                $('#datos-academicos-tab').click();
                setTimeout(() => {
                    driver.moveNext();
                  }, 200);
            }
        },
        {
            element: '#btn-añadir-datos-academicos',
            popover: {
              title: 'Añadir Datos Academicos',
              description: 'Usted podrá añadir un nuevo dato académico presionando el elemento.',
              position: 'right-center'
            },
            onNext: () =>
            {
                onClickNext(driver, () =>
                {
                    $('#modalAddInfoAcademica').modal('show');
                }, 300)
            }
        },
        {
            element: '#form-modalAddInfoAcademica',
            stageBackground: 'transparent',
            opacity: 0,
            popover: {
              title: 'Formulario Datos Academicos',
              description: 'Desde este apartado usted deberá ingresar toda la información requerida de su estudio.',
              position: 'bottom-center'
            },
            onNext: () => {
                onClickNext(driver, () => $('#modalAddInfoAcademica').modal('hide'), null)
            }
        },
        {
            element: '#table-datos-academicos',
            popover: {
              title: 'Datos Académicos Guardados',
              description: 'Usted Podrá ver sus datos académicos en esta tabla una vez haya enviado uno.',
              position: 'top-center'
            },
            onNext: () => {
                onClickNext(driver, () => $('#hoja-vida-tab').click())
            }
        },
        {
            element: '#perfil-profesional-form',
            popover: {
              title: 'Perfil Profesional',
              description: 'Usted deberá diligenciar su perfil profesional, una vez hecho esto presionar el botón “guardar”',
              position: 'top-center'
            }
        },
        {
            element: '#info-idiomas-form',
            popover: {
              title: 'Idiomas',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar los idiomas que usted domine',
              position: 'left-center'
            }
        },
        {
            element: '#distinciones-form',
            popover: {
              title: 'Distinciones',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar las distinciones que se le hayan otorgado.',
              position: 'top-center'
            }
        },
        {
            element: '#asociaciones-form',
            popover: {
              title: 'Asociaciones',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar las asociaciones a las que pertenezca, en caso de no pertenecer a ninguna diligenciar “No tengo”.',
              position: 'top-center'
            }
        },
        {
            element: '#consejos-form',
            popover: {
              title: 'Asociaciones',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar los consejos a los que pertenezca, en caso de no pertenecer a ninguno diligenciar “No tengo”.',
              position: 'top-center'
            }
        },
        {
            element: '#discapacidad-form',
            popover: {
              title: 'Asociaciones',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar las discapacidades que used tenga, en caso de no tener ninguna diligenciar “No tengo”.',
              position: 'top-center'
            },
            onNext: () => onClickNext(driver,() => $('#datos-laborales-tab').click())
        },
        {
            element: '#actualidad-laboral-form',
            popover: {
              title: 'Actualidad Laboral',
              description: 'Usted deberá seleccionar si se encuentra actualmente laborando.',
              position: 'top-center'
            }
        },
        {
            element: '#xp-laboral-form',
            popover: {
              title: 'Experiencia',
              description: 'Desde este apartado usted podrá añadir/editar/eliminar su experiencia laboral, la tabla tendrá una información breve acerca de sus experiencias laborales.',
              position: 'top-center'
            },
            onNext: () =>
            {
                localStorage.setItem('tutorial', 'true');
                
            }
        }
    ]);
    
    if(!localStorage.getItem('tutorial'))
    {
        driver.start();
    }
});
