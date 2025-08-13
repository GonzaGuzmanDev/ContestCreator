<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        @include('includes.header-contents')
    </div>
</nav>

<uib-alert type="@{{flashMessageType}}" close="flashMessage='';" ng-show="!!flashMessage" class="navbar-alert">@{{flashMessage}}</uib-alert>