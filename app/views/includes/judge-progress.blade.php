<uib-progressbar ng-if="!!judge" class="@{{ judge.progress.total == judge.progress.votes ? '' : 'progress-striped' }} progress active"
                 max="judge.progress.total" value="judge.progress.votes"
                 type="@{{judge.progress.votes == judge.progress.total ? 'success':'info'}}">
    <i>@{{ judge.progress.votes }} / @{{ judge.progress.total }}</i>
</uib-progressbar>