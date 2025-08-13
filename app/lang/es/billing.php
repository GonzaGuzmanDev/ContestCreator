<?php

return array(
    /*
    |--------------------------------------------------------------------------
    | Lenguaje utilizado en los emails
    |--------------------------------------------------------------------------
    */
    "transfer" => "Transferencia bancaria",
    "transfer.data" => "Datos para transferencia",
    "filltransfer" => "Complete los datos para que los usuarios puedan realizar la transferencia.",
    "check" => "Cheque",
    "check.data" => "Datos para cheques",
    "fillcheck" => "Complete los datos para que los usuarios puedan pagar con cheques.",
    "creditcard" => "Tarjeta de crédito",
    "creditcard.data" => "Datos para tarjeta de crédito",
    "fillcreditcard" => "Complete los datos para que los usuarios puedan pagar con tarjeta de crédito.",
    "other" => "Otro",
    "other.data" => "Datos para Otro",
    "fillother" => "Complete los datos para que los usuarios puedan pagar con otros medios de pago.",
    "TCO" => "2CheckOut",
    "TCO.sellerId" => "Seller Id",
    "TCO.fillsellerId" => "Ingrese su Seller Id",
    "TCO.privateKey" => "Clave privada",
    "TCO.fillprivateKey" => "Ingrese su Private Key",
    "TCO.publicKey" => "Clave pública",
    "TCO.fillpublicKey" => "Ingrese su Public Key",
    "TCO.whereissellerId" => "Dónde está mi Seller ID?",
    "TCO.wherearekeys" => "Dónde están mis claves?",

    "TCO.ccNo" => "Número de tarjeta de crédito",
    "TCO.expirationdate" => "Vencimiento",
    "TCO.expirationformat" => "(MM/AAAA)",
    "TCO.cvc" => "Código de seguridad",

    "MercadoPago" => "MercadoPago",
    "MercadoPago.shortName" => "Código de aplicación",
    "MercadoPago.fillshortName" => "Ingrese su Código de aplicación (mínimo 8 dígitos, sin mp-app-)",
    "MercadoPago.clientSecret" => "Client Secret",
    "MercadoPago.fillclientSecret" => "Ingrese su Client Secret",
    "MercadoPago.privateKey" => "Clave privada",
    "MercadoPago.fillprivateKey" => "Ingrese su Private Key",
    "MercadoPago.publicKey" => "Public Key",
    "MercadoPago.fillpublicKey" => "Ingrese su Public Key",
    "MercadoPago.accessToken" => "Access Token",
    "MercadoPago.fillaccessToken" => "Ingrese su Access Token",
    "MercadoPago.clientId" => "Client ID",
    "MercadoPago.fillclientId" => "Ingrese su Client ID",
    "MercadoPago.wherearekeys" => "Dónde están mis claves?",
    "MercadoPago.notificationsUrl" => "URL de notificaciones",
    "notificationsUrl" => "URL de notificaciones",
    "MercadoPago.notificationsUrlTodo" => "Ingrese a <a href='https://www.mercadopago.com.ar/ipn-notifications' target='_blank'>https://www.mercadopago.com.ar/ipn-notifications</a> y configure su cuenta de MercadoPago para que envíe las notificaciones a la dirección indicada en el campo superior",
    "MercadoPago.continuelink" => "Finalice el proceso de pago por MercadoPago<br><br>
                    <div class='text-center'><a class='btn btn-success btn-lg' href=':url' target='_blank'>
                    <i class='fa fa-external-link'></i> Ir a MercadoPago</a>
                    </div>",
    "MercadoPago.continueprocess" => "Retomar proceso de pago desde MercadoLibre",
    "MercadoPago.redirect-title" => "Pago Realizado",
    "MercadoPago.redirect-success" => "Pago Realizado",
    "MercadoPago.redirect-pending" => "Gracias por realizar tu pago a través de MercadoPago, el pago está pendiente de aprobación.",
    "MercadoPago.redirect-error" => "MercadoPago indica que hubo un problema al procesar el pago. Por favor reintentelo. ",
    "MercadoPago.back" => "Volver a mis inscripciones",
    "MercadoPago.explain" => "<i class='fa fa-info-circle'></i> Al aceptar aparecerá un enlace a MercadoPago para continuar el proceso de pago.",

    "ClicPago" => "ClicPago",
    "ClicPago.ProductLink" => "Link del producto",
    "ClicPago.fillProductLink" => "Complete el link del producto",
    "ClicPago.insertCompleteUrl" => "Ingrese el link completo del producto (http://...)",
    "ClicPago.continuelink" => "Finalice el proceso de pago por ClicPago<br><br>
        Ingrese 
         <a href='https://plataforma.clicpago.com/clicpago/public/comprar_codigo.do?method=init' target='_blank' style='text-decoration: underline;'>aquí</a>
         para comprar el código y luego continue en ClicPago para finalizar el pago
         <br>
        <form id=\"clicPagoForm\" target=\"\" name=\"clicPagoForm\"	method=\"post\" action=\":productlink\">
            <input type=\"hidden\" name=\"backURL\" value=\"{{':backURL'}}\" />
            <input type=\"hidden\" name=\"transactionBackURL\" value=\"{{':transactionBackURL'}}\" />
            <input type=\"hidden\" name=\"codigoTransaccionAdherente\" value=\"{{':codigoTransaccionAdherente'}}\" />
            <div class='text-center'>
                <button class='btn btn-success btn-lg' type='submit'>
                <i class='fa fa-external-link'></i> Ir a ClicPago</button>
            </div>
        </form>",
    "ClicPago.continueprocess" => "<form id=\"clicPagoForm\" target=\"\" name=\"clicPagoForm\"	method=\"post\" action=\":productlink\">
        Ingrese :productlink
         <a href='https://plataforma.clicpago.com/clicpago/public/comprar_codigo.do?method=init' target='_blank' style='text-decoration: underline;'>aquí</a>
         para comprar el código y luego continue en ClicPago para finalizar el pago
         <br>
            <input type=\"hidden\" name=\"backURL\" value=\":backURL\" />
            <input type=\"hidden\" name=\"transactionBackURL\" value=\":transactionBackURL\" />
            <input type=\"hidden\" name=\"codigoTransaccionAdherente\" value=\":codigoTransaccionAdherente\" />
            <button class='btn btn-success' type='submit'>
            <i class='fa fa-external-link'></i> Retomar proceso de pago desde ClicPago
            </button>
        </form>",

    "customApi.continuelink" => "Finalice el proceso de pago <br><br>
        Ingrese
        <form id=\"customApiForm\" target=\"\" name=\"customApiForm\"	method=\"post\" action=\":postURL\">
            <input type=\"hidden\" name=\":billingIdName\" value=\":billingId\" />
            <input type=\"hidden\" name=\":numberEntriesName\" value=\":numberOfEntries\" />
            <input type=\"hidden\" name=\":paymentStatusName\" value=\":paymentStatus\" />
            <div class='text-center'>
                <button class='btn btn-success btn-lg' type='submit'>
                <i class='fa fa-external-link'></i> Ir a Realizar Pago </button>
            </div>
        </form>",

    "customApi.continueprocess" => "<form id=\"customApiForm\" target=\"\" name=\"customApiForm\"	method=\"post\" action=\":postURL\">
         <br>
            <input type=\"hidden\" name=\":billingIdName\" value=\":billingId\" />
            <input type=\"hidden\" name=\":numberEntriesName\" value=\":numberOfEntries\" />
            <input type=\"hidden\" name=\":paymentStatusName\" value=\":paymentStatus\" />
            <button class='btn btn-success' type='submit'>
            <i class='fa fa-external-link'></i> Retomar proceso de pago
            </button>
        </form>",

    "id" => "ID de pago",
    "price" => "Precio",
    "date" => "Fecha",

    "mainPrice" => "Precio base",
    "mainCurrency" => "Tipo de moneda",
    "fillMainPrice" => "0.00",

    "sendingBilling" => "Realizando pago, por favor espere...",
    "sending" => "Guardando, por favor espere...",

    "successtitle" => "Pago",
    "success" => "Su pago ha sido procesado con éxito! Muchas gracias!",
    "pending" => "Su pago fue registrado y está pendiente de aprobación. Gracias.",

    "method" => "Método",
    "transactionid" => "ID de transacción externa",
    "entries" => "Inscripciones",
    "paymentData" => "Datos de pago",
    "description" => "Descripción",
    "nodescription" => "Sin descripción",
    "comments" => "Comentarios",
    "nocomments" => "Sin comentarios",

    "status" => "Estado",
    "status.pending" => "Verificación",
    "status.partiallypaid" => "Pago parcial",
    "status.success" => "Pagado",
    "status.error" => "Error",
    "status.waiting" => "Esperando Pago",
    "status.processing" => "Proceso",

    "unpaid" => "Sin pagar",
    "verify" => "Verificación",
    "notPayed" => "Sin pagar: ",
    "processing" => "En proceso",
    "swap" => "Canje",

    "changeStatus" => "Cambiar estado de pago",

    "approveBill" => "Aprobar pago",
    "rejectBill" => "Rechazar pago",
    "pendingBill" => "Marcar como pendiente",
    "processingBill" => "Marcar como en proceso",

    "payEntry" => "Pagar entry",
    "entryCat" => "Entry y categorías",

    "paidat" => "Fecha de pago",
    "paid_at" => "Pago",

    "entryPayments" => "Pagos del entry",

    "paymentsDone" => "Pagos realizados",
    "total" => "Total",

    "forcePaymentOnCreate" => "Prepago",
    "forcePaymentOnCreateExplain" => "Habilite esta opción para requerir el pago al momento de crear la inscripción.",
    "deleted" => "BORRADO",
    "deleted_tooltip" => "El entry fue borrado, pero el pago fue realizado",
    "total_billing" => "Dinero total (esperando verificacion + pagados)",
    "entry_deleted" => "EL ENTRY FUE BORRADO",
    "type_of_inscriptor" => "Tipo de inscriptor",

    "discounts" => "Descuentos",
    "discount.name" => "Nombre",
    "discount.from_entries" => "De",
    "discount.to_entries" => "a",
    "discount.entries" => "entries",
    "discount.start_at" => "Desde",
    "discount.end_at" => "Hasta",

    "not_invoiced" => "Sin facturar",
    "invoiced" => "Facturado",
    "payed" => "Pagado",

    "customApi" => "Tarjeta de credito(API)",
    "customApi.postURL" => "URL para el Post",
    "customApi.params" => "Nombre de los parametros para el post",
    "customApi.billingId" => "Billing Id",
    "customApi.numberOfEntries" => "Nro de Entries",
    "customApi.paymentStatus" => "Estado del pago",
    "customApi.price" => "Precio",
    "customApi.explain" => "Una vez que apriete el boton aceptar, apriete el boton de 'Ir a Realizar Pago' para seguir con el proceso de pago.",
);
