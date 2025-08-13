<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" ng-click="close()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">@yield('modal-title')</h4>
    </div>
    @if (!isset($hideModalForm) || !$hideModalForm)
    <form role="form" name="modalForm" class="form-horizontal" data-ng-submit="@yield('modal-submit', '')">
    @endif
        <div class="modal-body modal-fixed">
            @yield('modal-content')
        </div>
        <div class="modal-footer">
            @yield('modal-actions')
        </div>
    @if (!isset($hideModalForm) || !$hideModalForm)
    </form>
    @endif
</div>