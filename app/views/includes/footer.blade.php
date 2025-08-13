<footer class="navbar navbar-default navbar-fixed-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-xs-6 text-muted">
                <span class="copyright"><a href="http://www.oxobox.tv/" target="_blank">Oxobox</a> © <?=date("Y")?></span>

                <ul class="list-inline social-buttons text-center">
                    <li><a href="https://www.twitter.com/oxobox" target="_blank"><i class="fa fa-twitter"></i></a>
                    </li>
                    <li><a href="https://www.facebook.com/oxobox" target="_blank"><i class="fa fa-facebook"></i></a>
                    </li>
                    <li><a href="https://www.linkedin.com/company/oxobox-tv" target="_blank"><i class="fa fa-linkedin"></i></a>
                    </li>
                </ul>

                <a href="<?=URL::to('/');?>/#privacypolicy" class="text-muted hidden-xs">@lang('footer.privacypolivy')</a>
                -
                <a href="<?=URL::to('/');?>/#termsofuse" class="text-muted hidden-xs">@lang('footer.termsofuser')</a>

            </div>
            <div class="col-md-4 col-xs-6">
                @include('includes.langselect')
            </div>
        </div>
    </div>
</footer>