<span class="entry-card" ng-if="entry.id">
    @if(Auth::User())
    <span class="entry-id" ng-class="{'text-muted': entry.status == {{Entry::INCOMPLETE}}, 'text-warning': entry.status == {{Entry::COMPLETE}}, 'text-success': entry.status == {{Entry::FINALIZE}}, 'text-info': entry.status == {{Entry::APPROVE}}, 'text-danger': entry.status == {{Entry::ERROR}}}">#@{{ entry.id | zpad:5}}</span>
    @endif
    <span ng-bind-html="getName() || getNoName()"></span>
</span>