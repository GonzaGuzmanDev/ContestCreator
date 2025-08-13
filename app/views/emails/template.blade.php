<table style="height:100%!important;margin:0;padding:0;width:100%!important;background-color:#ddd!important" height="100%" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
    <tr>
        <td style="border-collapse:collapse" align="center" valign="top">
            <table style="background-color:#ddd!important" border="0" cellpadding="10" cellspacing="0" width="600">
                <tbody>
                <tr>
                    <td style="border-collapse:collapse" valign="top">
                        <br/>
                    </td>
                </tr>
                </tbody>
            </table>
            <table style="border:1px solid #ddd!important;background-color:#fff" border="0" cellpadding="0" cellspacing="0" width="600">
                <tbody>
                <tr>
                    <td style="border-collapse:collapse" align="center" valign="top">
                        <table style="background-color:#fff!important;border-bottom:0" border="0" cellpadding="0" cellspacing="0" width="600">
                            <tbody>
                            <tr>
                                <td><br/></td>
                            </tr>
                            <tr>
                                <td style="border-collapse:collapse;color:#202020!important;font-family:Arial!important;font-size:34px!important;font-weight:bold!important;line-height:100%;padding:0;text-align:left;vertical-align:middle">
                                    <div style="color:#444!important;font-family:Arial!important;font-size:14px!important;line-height:150%;text-align:left;width:540px;margin:auto;">
                                        @if(isset($contestTicket))
                                            {{ $contestTicket->getAsset(ContestAsset::SMALL_BANNER_HTML)->content }}
                                        @else
                                            <a href="<?=url('/')?>" title="Oxobox" target="_blank"><img src="<?=asset('img/logo.png')?>" alt="Oxobox" style="border:0;line-height:100%;outline:none;text-decoration:none" border="0"></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-collapse:collapse" align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="600">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse;background-color:#fff" valign="top">
                                    <table style="width:600px;TABLE-LAYOUT:fixed;word-break:normal" border="0" cellpadding="30" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td style="border-collapse:collapse" valign="top">
                                                <div style="color:#444!important;font-family:Arial!important;font-size:14px!important;line-height:150%;text-align:left;width:540px;margin: 20px auto;">
                                                    @yield('content')
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-collapse:collapse" align="center">
                        <table bgcolor="#CCCCCC" border="0" cellpadding="0" cellspacing="0" width="540">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse" height="1">
                                    <p style="font-size:0;line-height:0"></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-collapse:collapse" align="center" valign="top">
                        <table style="background-color:#fff!important;border-top:0" border="0" cellpadding="0" cellspacing="0" width="600">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse" valign="top">
                                    <table border="0" cellpadding="30" cellspacing="0" width="100%">
                                        <tbody>
                                        <tr>
                                            <td style="border-collapse:collapse" valign="top" width="600">
                                                <div style="color:#777!important;font-family:Arial!important;font-size:11px!important;line-height:125%;text-align:left;width: 540px;margin: 20px auto;">
                                                    <p style="color:#777!important;font-family:Arial!important;font-size:11px!important;line-height:125%;text-align:left">
                                                        @lang('email.ifyouneedhelp')
                                                        <a style="color:#269dcf!important;font-weight:bold!important;text-decoration:none!important" href="<?=url('/#/help')?>" target="_blank">@lang('email.oxoboxhelp')</a>.
                                                        <br/>
                                                        @lang('email.unsubscribe')
                                                        <a style="color:#269dcf!important;font-weight:bold!important;text-decoration:none!important" href="<?=url('/#/account/config')?>" target="_blank"><?=url('/#/account/config')?></a>.
                                                    </p>
                                                    <p style="color:#777!important;font-family:Arial!important;font-size:11px!important;line-height:125%;text-align:left">
                                                        © <?=date("Y")?> @lang('email.copyright')
                                                        <br>
                                                        <a style="color:#269dcf!important;font-weight:bold!important;text-decoration:none!important" href="<?=url('/#/privacypolicy')?>" target="_blank">@lang('email.privacypolicy')</a>
                                                        |
                                                        <a style="color:#269dcf!important;font-weight:bold!important;text-decoration:none!important" href="<?=url('/#/termsofuse')?>" target="_blank">@lang('email.termsofuser')</a>
                                                    </p>
                                                    @if(isset($contestTicket))
                                                        <a href="<?=url('/')?>" title="Oxobox" target="_blank"><img src="<?=asset('img/logo.png')?>" alt="Oxobox" style="border:0;line-height:100%;outline:none;text-decoration:none" border="0"></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
        </td>
    </tr>
    </tbody>
</table>