<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        @include('includes.header-contents')
    </div>
    <span>
        <cookie-consent></cookie-consent>
    </span>
</nav>

<uib-alert type="@{{flashMessageType}}" close="flashMessage='';" ng-show="!!flashMessage" class="navbar-alert">@{{flashMessage}}</uib-alert>