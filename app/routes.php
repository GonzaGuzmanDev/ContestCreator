<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

/** Authentication routes */
Route::group(array('prefix' => 'service'), function() {
    //Resource para autenticación
    Route::resource('authenticate', 'AuthenticationController');
    //Registro de usuarios
    Route::post('/register/', 'AuthenticationController@registerUser');
    //Chequeo de sesión
    Route::get('/check/{super?}', 'AuthenticationController@checkAuth');

    //Requerimiento de reestablecer contraseña
    Route::post('/rememberPassword/', 'RemindersController@postRemind');
    //Reestablecer contraseña
    Route::get('/password/reset/{token}', 'RemindersController@getReset');
    Route::post('/password/reset/', 'RemindersController@postReset');

    //Login/registro con redes sociales
    Route::get('/login/fb/', 'AuthenticationController@loginWithFacebook');
    Route::get('/login/tt/', 'AuthenticationController@loginWithTwitter');
    Route::get('/login/gp/', 'AuthenticationController@loginWithGoogle');

    Route::get('/logout', 'AuthenticationController@logout');

    //Desconección de redes sociales
    Route::group(['before' => 'serviceAuth'], function() {
        Route::get('/disconnect/fb/', 'AuthenticationController@disconnectWithFacebook');
        Route::get('/disconnect/tt/', 'AuthenticationController@disconnectWithTwitter');
        Route::get('/disconnect/gp/', 'AuthenticationController@disconnectWithGoogle');
    });
});

/** Site routes */
Route::get('/', 'SiteController@showIndex');
//Route::get('/ingestjudgeseffielatam', 'ContestController@customInsert');
Route::post('/contact', 'SiteController@postContact');
Route::post('/available/', 'SiteController@getAvailableNames');
//Route::get('/available/{param}', 'SiteController@getAvailableNames');

Route::group(['prefix' => 'view', 'after' => 'allowOrigin'], function() {
    /** Views routes */
    /** public: w/o authentication */
    Route::get('/home', 'SiteController@showWelcome');
    Route::get('/applyForContest', 'SiteController@getApplyForContest');
    Route::get('/loginApplyForContest', 'SiteController@getLoginApplyForContest');
    Route::get('/privacypolicy', 'SiteController@showPrivacy');
    Route::get('/termsofuse', 'SiteController@showTerms');
    Route::get('/login', function(){ return View::make('login.form'); });
    Route::get('/register', function(){ return View::make('login.register'); });
    Route::get('/password/reset/', function(){ return View::make('login.resetpassword-form'); });
    Route::get('/blank', function(){ return View::make('blank'); });
    Route::get('/gallery-modal', function(){ return View::make('metadata.gallery'); });

    /** Views with authentication */
    Route::group(['before' => 'serviceAuth'], function() {
        Route::get('/{folder}/{page}', function($folder, $page)
        {
            /** Account views */
            if(in_array($folder, ['account'])) {
                if (View::exists($folder . '.' . $page)) {
                    return View::make($folder . '.' . $page);
                }
            }
            App::abort(404);
            return Response::make('',404);
        });
    });
});

Route::get('/lang/{lang}', 'SiteController@setLocale');
/** User account routes */
Route::group(['prefix' => 'account', 'after' => 'allowOrigin'], function() {
    /** with authentication */
    Route::group(['before' => 'serviceAuth'], function() {
        //Post general data
        Route::post('/data/', 'UserController@postAccountData');
        //Send verification e-mail
        Route::get('/sendverifyemail/', 'UserController@getVerifyEmail');
        //Post
        Route::post('/security/', 'UserController@postAccountSecurity');
        //Post profile picture
        Route::post('/profilePicture/', 'UserController@postPicture');
        //Delete account controller
        Route::post('/deleteAccount/', 'UserController@postDeleteAccount');
        //Social data
        Route::get('/social/', 'UserController@getSocial');
        //Language data
        Route::get('/language/', 'UserController@getLanguage');
        Route::post('/language/', 'UserController@postLanguage');
        Route::post('/notifications/', 'UserController@postNotifications');
    });

    /** w/o authentication */
    //Verificación de e-mail
    Route::get('/verifyemail/{token}', 'UserController@getCompleteVerifyEmail');
});

/** Users Profiles routes */
Route::group(['prefix' => 'profile', 'after' => 'allowOrigin'], function() {
    Route::get('/{email}/picture/{version?}', 'UserController@getPicture');
});

/** Captcha routes */
Route::get('captcha/url', function(){
    return Captcha::img();
});
Route::get('captcha', function(){
    return Captcha::create();
});

/** Super Admin routes */
Route::group(['prefix' => 'admin', 'after' => 'allowOrigin'], function() {
    Route::get('/', function()
    {
        $allContests = Contest::basic()->opened()->get();
        return View::make('admin.index', ['allContests' => $allContests]);
    });

    Route::get('/backToAdmin', function(){
        $id = Session::pull( 'orig_user' );
        $orig_user = User::find( $id );
        Auth::login( $orig_user );
        return Redirect::back();
    });

    /** Views with admin authentication */
    Route::group(['before' => 'superAdminCheck'], function() {
        Route::get('/view/{page?}', array('before' => 'serviceAuth', function($page)
        {
            if(View::exists('admin.'.$page)){
                return View::make('admin.'.$page, ['superadmin' => true]);
            }
            if(View::exists($page)){
                return View::make($page, ['superadmin' => true]);
            }
            App::abort(404);
            return Response::make('',404);
        }));
        Route::get('/view/{folder?}/{page?}', array('before' => 'serviceAuth', function($folder, $page)
        {
            if(View::exists('admin.'.$folder.'.'.$page)){
                return View::make('admin.'.$folder.'.'.$page, ['superadmin' => true, 'colaborator'=>false]);
            }
            if(View::exists($folder.'.'.$page)){
                return View::make($folder.'.'.$page, ['superadmin' => true, 'colaborator'=>false]);
            }
            App::abort(404);
            return Response::make('',404);
        }));
        Route::get('/loginAs/{id}', function($id){
            Session::put( 'orig_user', Auth::id() );
            Auth::loginUsingId($id);
            return Redirect::to('/#home');
        });

        Route::get('/loginAsInscription/{inscription}', 'ContestController@loginAsInscription');
        Route::get('/fixBanners', 'ContestController@fixContestsBanners');
    });
});

/** API routes (Para admins, colaboradores y superadmins)   */
Route::group(['prefix' => 'api', 'after' => 'allowOrigin'], function() {
    Route::post('/contest/save/{id?}', 'ContestController@update');

    Route::group(['before' => 'superAdminCheck'], function() {
        Route::resource('/users', 'UserController');
        //Route::resource('/contests', 'ContestController');
        Route::resource('/formats', 'FormatController');
        Route::resource('/inscriptions', 'InscriptionsController');
        Route::get('/metrics', 'AdminController@getMetrics');
        Route::get('/gcmanager', 'AdminController@executeGCManager');
        Route::get('/assignQueuedToManualEncoder', 'AdminController@assignQueuedToManualEncoder');
        Route::post('/contests/', 'ContestController@index');
        //Route::post('/contest/save/{id?}', 'ContestController@update');
        Route::post('/contest/{contest}/invoice', 'ContestController@postInvoice');
        Route::get('/contest/{contest}', 'ContestController@getData');
        Route::get('/contest/{contest}/invoice/{code?}', 'ContestController@getInvoiceData');
        Route::post('/contest/delete/{contest}', 'ContestController@destroy');

        //Route::post('/contest/{contest}/inscription/{inscription?}', 'ContestController@postInscription');
        //Route::delete('/contest/{contest}/inscription/{inscription}', 'ContestController@destroyInscription');

        Route::post('/user/{id}/delete', 'UserController@destroy');
        Route::get('/user/{id}/inscriptionsData', 'UserController@getInscriptionsData');

        //Route::post('/contest/usersData', 'ContestController@getUsersData');
    });
    Route::post('/contest/usersData', 'ContestController@getUsersData');
    Route::get('/contest/{contest}/categoriesData', 'ContestController@getCategoriesData');
    Route::group(['before' => 'contestAdminCheck'], function() {
        Route::get('/contest/{contest}/inscriptionData', 'ContestController@getInscriptionData');
        //Route::get('/contest/{contest}/categoriesData', 'ContestController@getCategoriesData');
        Route::get('/contest/{contest}/contestsIds', 'ContestController@getContestsIds');
        Route::get('/contest/{contest}/entriesData', 'ContestController@getEntriesData');
        Route::get('/contest/{contest}/styleData', 'ContestController@getStyleData');
        Route::get('/contest/{contest}/adminInfo', 'ContestController@getAdminInformation');
        Route::get('/contest/{contest}/paymentsData', 'ContestController@getPaymentsData');

        Route::post('/contest/{contest}/inscriptionData', 'ContestController@postInscriptionData');
        Route::post('/contest/{contest}/categoriesData', 'ContestController@postCategoriesData');
        Route::post('/contest/{contest}/importContestData', 'ContestController@postImportContestData');
        Route::post('/contest/{contest}/entriesData', 'ContestController@postEntriesData');
        Route::post('/contest/{contest}/styles', 'ContestController@saveStyles');
        Route::post('/contest/{contest}/homeData', 'ContestController@saveHomeData');
        Route::post('/contest/{contest}/asset', 'ContestController@saveAsset');
        Route::post('/contest/{contest}/deadlines', 'ContestController@saveDeadlinesData');
        Route::post('/contest/{contest}/payments', 'ContestController@savePaymentsData');
        Route::post('/contest/{contest}/finishWizard', 'ContestController@finishWizard');

        #Todas las inscripciones del concurso
        Route::post('/contest/{contest}/allInscriptionsData', 'ContestController@getAllInscriptionsData');
        Route::get('/contest/{contest}/inscription/{inscription?}', 'ContestController@getInscription');
        Route::post('/contest/{contest}/inscription/{inscription?}', 'ContestController@postInscription');
        Route::delete('/contest/{contest}/inscription/{inscription?}', 'ContestController@destroyInscription');

        Route::post('/contest/{contest}/uploadFile', 'ContestController@postUploadFile');
        Route::post('/contest/{contest}/importUserList', 'ContestController@postImportUserList');
        Route::post('/contest/{contest}/resetPassword', 'ContestController@postResetPassword');
        #Páginas estáticas del concurso
        Route::post('/contest/{contest}/allPagesData', 'ContestController@getAllPagesData');
        Route::get('/contest/{contest}/page/{page?}', 'ContestController@getPage');
        Route::post('/contest/{contest}/page/', 'ContestController@postPage');
        Route::delete('/contest/{contest}/page/{page?}', 'ContestController@destroyPage');

        #Contest assets
        Route::post('/contest/{contest}/allAssetsData', 'ContestController@getAllAssetsData');
        Route::delete('/contest/{contest}/asset/{assetId?}', 'ContestController@destroyAsset');

        #newsletter
        Route::post('/contest/{contest}/newsletter/', 'ContestController@postNewsletter');
        Route::post('/contest/{contest}/allNewslettersData', 'ContestController@getAllNewslettersData');
        Route::get('/contest/{contest}/newsletter/{newsletter?}', 'ContestController@getNewsletter');

        #sesiones de votacion
        Route::get('/contest/{contest}/voting/{voting?}', 'ContestController@getVoting');
        Route::get('/contest/{contest}/voting/{voting}/judges', 'ContestController@getVotingJudges');
        Route::post('/contest/{contest}/voting/{voting}/results', 'ContestController@getVotingResults');
        Route::post('/contest/{contest}/voting/list', 'ContestController@postVotingSessionList');
        Route::post('/contest/{contest}/voting/', 'ContestController@postVotingSession');
        Route::post('/contest/{contest}/shortList', 'ContestController@postShortList');
        Route::post('/contest/{contest}/voting/newgroup', 'ContestController@postVotingSessionNewGroup');
        //Route::post('/contest/{contest}/exportJudges', 'ContestController@postExportJudges');
        Route::post('/contest/{contest}/voting/{voting}/invite', 'ContestController@postVotingSessionInvites');
        Route::post('/contest/{contest}/voting/{voting}/sendInvites', 'ContestController@postVotingSessionSendInvites');
        Route::post('/contest/{contest}/sendNewsletter', 'ContestController@postSendNewsletter');
        Route::post('/contest/{contest}/voting/{voting}/autoAbstains', 'ContestController@postAutoAbstains');
        Route::post('/contest/{contest}/voting/{voting}/keys', 'ContestController@getVotingKeys');
        Route::post('/contest/{contest}/votingUserEntries', 'ContestController@postVotingUserEntries');
        Route::post('/contest/{contest}/votingGroupEntries', 'ContestController@postVotingGroupEntries');
        Route::post('/contest/{contest}/exportResults', 'ContestController@exportResults');
        Route::post('/contest/{contest}/saveExportTemplate', 'ContestController@saveExportTemplate');
        Route::post('/contest/{contest}/votingUserEntriesCategories', 'ContestController@postVotingUserEntriesCategories');
        Route::post('/contest/{contest}/groupEntriesCategories', 'ContestController@postGroupEntriesCategories');
        Route::post('/contest/{contest}/sessionEntries', 'ContestController@postSessionEntries');
        Route::post('/contest/{contest}/exportRanking', 'ContestController@postExportRanking');
        Route::post('/contest/{contest}/votingLobby', 'ContestController@postVotingLobby');
        Route::delete('/contest/{contest}/voting/{voting}', 'ContestController@destroyVotingSession');
        Route::delete('/contest/{contest}/votingGroup/{voting}', 'ContestController@destroyVotingGroup');
        Route::delete('/contest/{contest}/voting/{voting}/judge/{judge}', 'ContestController@destroyVotingSessionJudge');
        Route::delete('/contest/{contest}/newsletter/{newsletterId}/newsletterUser/{user}', 'ContestController@destroyNewsletterUser');

        #invitaciones
        Route::post('/contest/{contest}/invitationId', 'ContestController@postInvitationId');
        Route::post('/contest/{contest}/invitationPagination', 'ContestController@postInvitationData');
        Route::get('/contest/{contest}/invitationsData', 'ContestController@getInvitationsData');
        Route::get('/contest/{contest}/invitationData/{id?}', 'ContestController@getInvitationData');
        Route::delete('/contest/{contest}/invitation/{id?}', 'ContestController@destroyInvitation');

        #Billing
        Route::post('/contest/{contest}/billing', 'ContestController@getBillings');
        Route::get('/contest/{contest}/bill/{bill?}', 'ContestController@getBill');
        Route::post('/contest/{contest}/billStatus/', 'ContestController@postBillStatus');
        Route::post('/contest/{contest}/bill/', 'ContestController@postBill');

        Route::get('/contest/{contest}/votingSessions', 'ContestController@getVotingSessions');

        #Collections
        Route::get('/contest/{contest}/collection/{code?}', 'ContestController@getCollection');
        Route::post('/contest/{contest}/collection/', 'ContestController@postCollection');
        Route::post('/contest/{contest}/collection/list', 'ContestController@postCollectionList');
        Route::post('/contest/{contest}/collection/{voting}/keys', 'ContestController@getCollectionKeys');
        Route::post('/contest/{contest}/collection/{voting}/invite', 'ContestController@postCollectionInvites');
        Route::delete('/contest/{contest}/collection/{code}', 'ContestController@deleteCollection');
    });
    /*Route::get('/contest/{contest}/report-payment/clicpago/{code}', 'ContestController@reportPaymentClicpago');
    Route::post('/contest/{contest}/report-payment/clicpago/{code}', 'ContestController@reportPaymentClicpago');*/
    Route::post('/contest/{contest}/report-payment/customApi/{code?}', 'ContestController@reportPaymentCustomApi');
    Route::post('/contest/{contest}/report-payment/{code?}', 'ContestController@reportPayment');
    Route::post('/contest/{contest}/report-payment/stripe/{code?}', 'ContestController@reportPaymentStripe');
});

Route::group(['before' => 'superAdminCheck'], function() {
    Route::get('/loginAs/{contest}/{id}', function($contest, $id){
        Session::put( 'orig_user', Auth::id() );
        Auth::loginUsingId($id);
        return Redirect::to('/'.$contest.'/#home');
    });
});

/** Contests routes */
Route::pattern('contest', '[A-Za-z0-9\-]+');
Route::pattern('code', '[A-Za-z0-9\-!]+');
Route::group(['prefix' => '/{contest}'], function()
{
    Route::get('/static/{cat?}/entry/{entry?}', 'ContestController@exportStatic');
    Route::get('/static/{cat?}', 'ContestController@exportStatic');
    Route::get('/voting/{code}/static/{group?}', 'ContestController@exportStatic');
    Route::get('/voting/{code}/static/{group?}/{cat?}', 'ContestController@exportStatic');
    Route::get('/voting/{code}/static/{group?}/{cat?}/{entry?}', 'ContestController@exportStatic');
    Route::get('/voting/{code}/static/{group?}/entries', 'ContestController@exportStatic');
    //Index de contest
    Route::get('/', 'ContestController@getIndex');
    //JSON Data de Contest
    Route::get('/data/{inscription?}/{role?}', 'ContestController@getData');
    //JSON Data de user Inscription
    Route::get('/inscription', 'ContestController@getUserInscription');
    //Registro de usuarios en contest
    Route::post('/signup', 'ContestController@postUserInscription');
    Route::post('/updateInscription', 'ContestController@postUpdateUserInscription');
    Route::post('/inscriptionExists', 'ContestController@inscriptionExists');

    Route::get('/qrCode/{ticketCode}.png', 'ContestController@showQrCode');
    //Assets del contest
    Route::get('/asset/{id}', 'ContestController@getAsset');
    Route::post('/asset', 'ContestController@postNewAsset');
    //TODO Mover de acá
    Route::post('/inscriptionId', 'ContestController@postInscriptionId');

    Route::get('/invite/{inviteCode}', 'ContestController@getInviteCode');
    Route::post('/invite/{inviteCode}/login', 'ContestController@inviteLogin');
    Route::post('/invite/{inviteCode}/register', 'ContestController@inviteRegister');
    Route::post('/invite/{inviteCode}/reject', 'ContestController@inviteReject');

    Route::get('/invite-key/', 'ContestController@getInviteKeyIndex');
    Route::post('/invite-key/login', 'ContestController@inviteKeyLogin');
    //Public player for pages
    Route::get('/gallery-modal-public', function(){ return View::make('metadata.gallery'); });

    // SACAR DE ACA, ESTA PUBLICO, VER LUEGO COMO HACER
    Route::get('/fileUrl/{id}', 'ContestController@getFileUrl');
    Route::post('/entryCategoryVotePublic/', 'ContestController@postEntryCategoryVote');

    Route::get('/bigBanner', 'ContestController@getBigBanner');
    Route::get('/smallBanner', 'ContestController@getSmallBanner');

    //Assets del contest
    Route::get('/page/{page}/{num?}', 'ContestController@getPageContents');
    Route::get('/entry/{id}/bs/{status}', 'ContestController@getEntryBillingStatusPage');

    // Votacion publica y anonima
    Route::get('/voteAnonymousData/{code}', 'ContestController@getPublicAnonymousVotingSession');
    //**** Fin votacion publica y anonima ******************************

    //Collections
    Route::get('/collection/{code}', 'ContestController@getCollectionContents');
    Route::get('/collectionEntry/{code}/{id}', 'ContestController@getCollectionEntry');
    Route::get('/collectionMetadataFields/{id}', 'ContestController@getCollectionMetadataFields');
    Route::post('/collectionEntries', 'ContestController@getCollectionEntries');
    Route::post('/collectionInvitation', 'ContestController@getCollectionUser');


    Route::group(['before' => 'superAdminCheck'], function() {

    });

    /** User actions */
    Route::group(['before' => 'serviceAuth'], function() {
        Route::get('/categories', 'ContestController@getCategoriesDataByCode');
        //Route::get('/entries', 'ContestController@getEntries');
        Route::get('/exportEntriesData', 'ContestController@exportEntriesData');
        Route::get('/exportFiles', 'ContestController@exportFiles');
        Route::get('/exportResultsJson/{code}/{exportJson}', 'ContestController@exportResults');
        Route::get('/exportJudges/{code}', 'ContestController@postExportJudges');
        Route::get('/exportCredits', 'ContestController@exportCredits');

        Route::get('/export-pdf/{id?}', 'ContestController@exportPDF');

        Route::post('/entries', 'ContestController@getEntries');
        Route::post('/inscriptionForm', 'ContestController@getInscriptionForm');
        Route::post('/userEntries', 'ContestController@getUserEntries');
        Route::post('/entryLog', 'ContestController@getEntryLog');
        Route::post('/entryMessage', 'ContestController@postEntryMessage');
        Route::post('/adminNote', 'ContestController@postAdminNote');
        Route::post('/changeEntryStatus', 'ContestController@postEntryStatus');
        Route::post('/filterChangeEntryStatus', 'ContestController@filterChangeEntryStatus');
        Route::post('/checkFinalizedEntry', 'ContestController@postCheckFinalizedEntry');
        Route::post('/changeFileStatus', 'ContestController@postFileStatus');
        Route::get('/entryInscriptor/{id}', 'ContestController@getEntryInscriptor');
        Route::get('/checkMaxEntries/', 'ContestController@getCheckMaxEntries');
        Route::get('/entryJudge/{id}/{code}', 'ContestController@getEntryJudge');
        Route::get('/metadataFields', 'ContestController@getMetadataFields');
        Route::get('/metadataAnalytics', 'ContestController@getMetadataAnalytics');
        Route::get('/judgeMetadataFields', 'ContestController@getJudgeMetadataFields');
        Route::get('/entryCategory/{id}', 'ContestController@getEntryCategory');
        Route::get('/adminInfo', 'ContestController@getAdminInformation');
        Route::post('/entry/', 'ContestController@postEntry');
        Route::post('/uploadFile/', 'ContestController@addFile');
        Route::post('/files/new/', 'ContestController@newFile');
        Route::post('/files/uploaded/', 'ContestController@fileUploaded');
        Route::post('/files/uploadProgress/', 'ContestController@uploadProgress');
        Route::post('/files/uploadCanceled/', 'ContestController@uploadCanceled');
        Route::post('/files/uploadError/', 'ContestController@uploadError');
        Route::post('/deleteFile/', 'ContestController@deleteFile');
        Route::post('/deleteInscription/', 'ContestController@deleteInscription');
        Route::post('/contestStatusRequest/', 'ContestController@contestStatusRequest');
        Route::post('/saveFile/', 'ContestController@saveFile');
        Route::post('/reEncode/', 'ContestController@reEncode');
        Route::post('/createVersion/', 'ContestController@createVersion');
        Route::post('/makeThumbs/', 'ContestController@makeThumbs');
        Route::post('/files/', 'ContestController@getFiles');
        Route::post('/files/{id}', 'ContestController@getUserFiles');
        Route::get('/voting/', 'ContestController@getVoteSessionsList');
        Route::post('/selectedAutoAbstain/', 'ContestController@getSelectedAutoAbstain');
        Route::post('/vote/{code}', 'ContestController@getVoteData');
        Route::post('/shortList/{code}', 'ContestController@postShortlistParent');
        /*Route::get('/voting/{code}/static/{group?}', 'ContestController@exportStatic');
        Route::get('/voting/{code}/static/{group?}/{cat?}', 'ContestController@exportStatic');
        Route::get('/voting/{code}/static/{group?}/{cat?}/{entry?}', 'ContestController@exportStatic');*/
        Route::post('/userRole', 'ContestController@postUserRole');
        Route::post('/removeEntryFromCategory/', 'ContestController@postRemoveEntryFromCategory');
        Route::post('/cancelPayment/', 'ContestController@postCancelPayment');
        Route::post('/deleteEntry/', 'ContestController@deleteEntry');
        Route::post('/printer/', 'ContestController@printFile');
        Route::post('/entryCategoryVote/', 'ContestController@postEntryCategoryVote');
        Route::post('/userAutoAbstain/', 'ContestController@postUserAutoAbstain');
        Route::get('/payment/{id}', 'ContestController@getUserPayment');
        Route::post('/payments/', 'ContestController@getUserPayments');
        Route::post('/payTickets', 'ContestController@postPayTickets');
        Route::get('/t/{ticket}', 'ContestController@getTicketCheck');

        Route::post('/MeetUserInLobby/', 'ContestController@postMeetUserInLobby');
        Route::get('/getMeetUserInLobby/{id}', 'ContestController@getMeetUserInLobby');
        Route::post('/getUsersInLobby/', 'ContestController@getUsersInLobby');
        //Route::get('/fileUrl/{id}', 'ContestController@getFileUrl');
    });

    // Devuelve el thumb del File
    Route::get('/file/{code}/thumb', 'ContestFileController@getFileThumb');
    Route::get('/file/{code}/v/{versionId}.{extension}', 'ContestFileController@getFileVersion');

    /** Contest Views */
    Route::group(['prefix' => 'view', 'after' => 'allowOrigin'], function() {
        /** Public views */
        Route::get('/home', 'ContestController@getHome');
        Route::get('/signup', 'ContestController@getSignup');
        Route::get('/updateInscription', 'ContestController@getUpdateInscription');
        Route::get('/terms', 'ContestController@getTerms');
        /** Para registrarse sin estar logueado */
        Route::get('/files/panel', 'ContestController@getFilesPanelView');
        Route::get('/files/panelTech', 'ContestController@getTechFilesPanelView');
        Route::get('/invite/home', 'ContestController@getInviteHome');
        Route::get('/invite/key', 'ContestController@getInviteKey');
        Route::get('/invite/reject', 'ContestController@getInviteReject');
        Route::get('/pages', 'ContestController@getPagesView');
        Route::get('/anonymous', 'ContestController@getAnonymousVoteView');
        Route::get('/collections', 'ContestController@getCollectionsView');
        Route::get('/collection', 'ContestController@getCollectionEntryView');
        Route::get('/collection-key', 'ContestController@getCollectionKeyView');


        Route::get('/billing/redirect', 'ContestController@getEntryBillingRedirectHome');
        Route::get('/payments', 'ContestController@getPaymentsView');
        Route::get('/payment', 'ContestController@getPaymentView');
        /** Registered users views */
        Route::group(['before' => 'contestInscriptionCheck'], function() {
            Route::get('/voting', 'ContestController@getVoteSessionView');
            Route::get('/vote/{id?}', 'ContestController@getVoteView');
            Route::get('/entries', 'ContestController@getEntriesView');
            Route::get('/files', 'ContestController@getFilesView');
            Route::get('/tech', 'ContestController@getTechView');
            //Route::get('/filesPanel', 'ContestController@getFilesPanelView');
            Route::get('/entry', 'ContestController@getEntryView');

            Route::get('/deleteEntry/', function(){
                return View::make('contest.deleteEntry');
            });
            Route::get('/payEntry/', function(){
                return View::make('includes.entry-pay');
            });
            Route::get('/payTicket/', function(){
                return View::make('includes.ticket-pay');
            });
            Route::get('/inscription-form/', function(){
                return View::make('includes.inscription-form');
            });
            Route::get('/entryPayments/', function(){
                return View::make('includes.entry-payments-modal');
            });

            Route::get('/deleteFile', function () {
                return View::make('contest.deleteFile');
            });

            Route::get('/downloadFiles', function () {
                return View::make('contest.downloadFiles');
            });
            Route::get('/deleteInscription', function () {
                return View::make('contest.deleteInscription');
            });
        });
        /** Registered users views */
        Route::group(['before' => 'contestAdminCheck'], function() {
            Route::group(['before' => 'contestColaboratorCheck'], function() {
                Route::get('/admin/home', 'ContestAdminController@getHomeView');
            });
            Route::get('/admin/inscriptions', 'ContestAdminController@getInscriptionsView');
            Route::get('/admin/categories', 'ContestAdminController@getCategoriesView');
            Route::get('/admin/import-contest', 'ContestAdminController@getImportContestView');
            Route::get('/admin/entries', 'ContestAdminController@getEntriesView');
            Route::get('/admin/style', 'ContestAdminController@getStyleView');
            Route::get('/admin/billingsetup', 'ContestAdminController@getBillingsSetupView');
            Route::get('/admin/inscriptions-list', 'ContestAdminController@getInscriptionsListView');
            Route::get('/admin/inscription', 'ContestAdminController@getInscriptionView');
            Route::get('/admin/inscription/delete', 'ContestAdminController@getInscriptionDeleteView');
            Route::get('/admin/deadlines', 'ContestAdminController@getDeadlinesView');
            Route::get('/admin/pages', 'ContestAdminController@getPagesListView');
            Route::get('/admin/page', 'ContestAdminController@getPageView');
            Route::get('/admin/page/delete', 'ContestAdminController@getPageDeleteView');
            Route::get('/admin/assets', 'ContestAdminController@getAssetsListView');
            Route::get('/admin/asset/delete', 'ContestAdminController@getAssetDeleteView');
            Route::get('/admin/newsletters', 'ContestAdminController@getNewslettersListView');
            Route::get('/admin/newsletter', 'ContestAdminController@getNewsletterView');
            Route::get('/admin/voting-session/delete', 'ContestAdminController@getVotingSessionDeleteView');
            Route::get('/admin/voting-session/deleteJudge', 'ContestAdminController@getVotingSessionDeleteJudgeView');
            Route::get('/admin/voting-session/sendInvites', 'ContestAdminController@getVotingSessionSendInvitesView');
            Route::get('/admin/voting-session/sendNewsletter', 'ContestAdminController@getSendNewsletterView');
            Route::get('/admin/voting-session/autoAbstain', 'ContestAdminController@getVotingSessionAutoAbstainView');
            Route::get('/admin/voting-sessions', 'ContestAdminController@getVotingListView');
            Route::get('/admin/voting-session', 'ContestAdminController@getVotingView');
            Route::get('/admin/billing', 'ContestAdminController@getBillingView');
            Route::get('/admin/bill', 'ContestAdminController@getBillView');
            Route::get('/admin/mail', 'ContestAdminController@getMailView');
            Route::get('/admin/collections', 'ContestAdminController@getCollectionsListView');
            Route::get('/admin/collection', 'ContestAdminController@getCollectionView');
            Route::get('/admin/collection/delete', 'ContestAdminController@getCollectionDeleteView');
            Route::get('/admin/meta-analysis', 'ContestAdminController@getMetaAnalysisView');
        });
    });
});