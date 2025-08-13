<?php

class ContestAdminController extends \BaseController {

    /**
     * @param $contest
     * @param string $relation
     * @param string $extraWith
     * @return Contest
     */
    private function getContest($contest, $relation = null, $extraWith = null){
        $query = Contest::where('code', '=', $contest);
        switch($relation){
            case "inscription":
                $query->with(['InscriptionMetadataFields' => function($query)
                {
                    $query->where('role', Inscription::INSCRIPTOR)->orderBy('order', 'asc');
                },
                    'inscriptionTypes' => function($query)
                    {
                        $query->where('role', Inscription::INSCRIPTOR)->orderBy('name', 'asc');
                    }]);
                break;
            case "entry":
                $query->with(['EntryMetadataFields' => function($query)
                {
                    $query->orderBy('order', 'asc');
                },
                    'categories' => function($query)
                    {
                        //$query->orderBy('name', 'asc');
                    },
                    'childrenCategories' => function($query)
                    {
                        //$query->orderBy('name', 'asc');
                    }]);
                break;
            case "userfiles":
                $query->with(['contestFiles' => function($query)
                {
                    $query->whereUserId(Auth::id())->orderBy('name', 'asc');
                }
                ]);
                break;
        }
        if($extraWith != null){
            $query->with($extraWith);
        }
        $con = $query->firstOrFail();
        if(!$con){
            App::abort(404, Lang::get('contest.notfound'));
        }
        return $con;
    }

    public function getData($contestCode, $inscription=false){
        $contest = $this->getContest($contestCode, $inscription);
        return $contest->toJson(JSON_NUMERIC_CHECK);
    }

    public function isContestAdmin($contestCode){
        $contest = $this->getContest($contestCode);
        return $contest->isAdmin();
    }

    /* Views para administrador de contest */

    public function getHomeView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.home', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.home', ['contest' => $contest, 'superadmin' => false, 'permits'=> $permits]);
        }
    }

    public function getInscriptionsView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-inscriptions', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-inscriptions', ['contest' => $contest, 'superadmin' => false, 'permits'=> $permits]);
        }
    }

    public function getCategoriesView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-categories', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-categories', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getImportContestView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-import-contest', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-import-contest', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getEntriesView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-entries', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-entries', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getStyleView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-style', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-style', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getPaymentsView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-payments', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-payments', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getBillingsSetupView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-billing-setup', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-billing-setup', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getInscriptionsListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-inscriptions-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN) || $contest->isColaborator($permits, Contest::VIEWER))
                return View::make('admin.contests.form-inscriptions-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getInscriptionView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-inscription', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN) || $contest->isColaborator($permits, Contest::VIEWER))
                return View::make('admin.contests.form-inscription', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getInscriptionDeleteView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-inscription', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getDeadlinesView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-deadlines', ['active'=>'deadlines', 'contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-deadlines', ['active'=>'deadlines', 'contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }

    }

    public function getPagesListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-pages-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-pages-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getAssetsListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-assets-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-assets-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getPageView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-page', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN))
                return View::make('admin.contests.form-page', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getPageDeleteView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-page', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getAssetDeleteView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-asset', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getVotingListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.voting-sessions', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN) || $contest->isColaborator($permits, Contest::VOTING))
                return View::make('admin.contests.voting-sessions', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getVotingView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-voting', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN) || $contest->isColaborator($permits, Contest::VOTING))
                return View::make('admin.contests.form-voting', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getVotingSessionDeleteView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-voting-session', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getVotingSessionDeleteJudgeView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-voting-session-judge', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getVotingSessionSendInvitesView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.voting-seccion-send-invites', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getSendNewsletterView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.send-newsletter', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getVotingSessionAutoAbstainView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.voting-session-auto-abstain', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getBillingView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-billing', ['contest' => $contest, 'admin' => true, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::BILLING) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-billing', ['contest' => $contest, 'admin' => true, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getBillView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-bill', ['contest' => $contest, 'admin' => true, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::BILLING) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-bill', ['contest' => $contest, 'admin' => true, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getMailView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-mail', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-mail', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getNewslettersListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-newsletters-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-newsletters-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getNewsletterView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-newsletter', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-newsletter', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getCollectionsListView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-collections-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-collections-list', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getCollectionView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-collection', ['contest' => $contest, 'superadmin' => false, 'permits'=>true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-collection', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }

    public function getCollectionDeleteView($contestCode){
        $contest = $this->getContest($contestCode);
        return View::make('admin.contests.delete-collection', ['contest' => $contest, 'superadmin' => false]);
    }

    public function getMetaAnalysisView($contestCode){
        $contest = $this->getContest($contestCode);
        if($this->isContestAdmin($contestCode))
            return View::make('admin.contests.form-meta-analysis', ['contest' => $contest, 'superadmin' => false, 'permits'=> true]);
        else{
            $data = $contest->getUserInscription(Auth::user(), Inscription::COLABORATOR);
            $permits = $data['permits'];
            if($contest->isColaborator($permits, Contest::DESIGN) || $contest->isColaborator($permits, Contest::ADMIN))
                return View::make('admin.contests.form-meta-analysis', ['contest' => $contest, 'superadmin' => false, 'permits'=>$permits]);
        }
    }


}