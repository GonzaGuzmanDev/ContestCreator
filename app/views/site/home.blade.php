@include('includes.header')
<div class="main-block panned">
    <div class="alert alert-top alert-@{{flashStatus}}" ng-show="flash">
        <button type="button" class="close" ng-click="flash='';"><span aria-hidden="true">&times;</span></button>
        <span ng-bind="flash"></span>
    </div>

    <uib-carousel interval="3000" no-wrap="false" class="slide">
        <? /** @var $contest Contest  */ ?>
        @foreach($slides as $slide)
        <uib-slide>
            <header class="intro {{$slide->class}}" style="background-image: url('<?=asset('img/slides/'.$slide->image);?>');">
                <div class="container">
                    <div class="intro-text">
                        <div class="intro-lead-in">{{$slide->title}}</div>
                        <div class="intro-heading">{{$slide->description}}</div>
                        @if($slide->link)
                        <!--<a href="{{$slide->link}}" class="page-scroll btn btn-lg btn-success">{{$slide->linkText}}</a>
                        <a ng-show="!currentUser" href="<?=URL::to('/');?>/#/loginApplyForContest" class="page-scroll btn btn-lg btn-success"> Solicitar Demo </a>
                        <a ng-show="currentUser" href="<?=URL::to('/');?>/#/applyForContest" class="page-scroll btn btn-lg btn-success"> Solicitar Demo </a>-->
                        @endif
                    </div>
                </div>
            </header>
        </uib-slide>
        @endforeach
            <?/*@foreach($contests as $contest)
                <uib-slide>
                    <a href="{{url('/'.$contest->code.'/#home')}}">
                        <header class="intro" style="background-image: url('{{$contest->getAssetURL(ContestAsset::BIG_BANNER)}}');"></header>
                    </a>
                </uib-slide>
            @endforeach*/?>
    </uib-carousel>

    <section id="services" class="bg-darkest-gray">
        <div class="">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">@lang('home.services')</h2>
                    <h3 class="section-subheading text-muted">@lang('home.services-description')</h3>
                </div>
            </div>
            <div class="row text-center">
                @lang('home.services-content')
            </div>
        </div>
    </section>

    <section id="portfolio">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">@lang('home.portfolio')</h2>
                    <h3 class="section-subheading text-muted">@lang('home.portfolio-description')</h3>
                </div>
            </div>
            <div class="row">
                @foreach($clients as $client)
                <div class="col-md-4 col-sm-6 portfolio-item">
                    <div class="portfolio-link">
                        <div class="portfolio-hover">
                            <div class="portfolio-hover-content">
                                <i class="fa fa-plus fa-3x"></i>
                            </div>
                        </div>
                        <img src="img/portfolio/{{$client->image}}" class="img-responsive" alt="">
                    </div>
                    <div class="portfolio-caption">
                        <h4>{{$client->title}}</h4>
                        <p class="text-muted">{{$client->description}}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="bg-darkest-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">@lang('home.contactus')</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form name="contactForm" id="contactForm" novalidate="" ng-controller="contactFormController" ng-submit="send()">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="@lang('home.contactus-name')" id="name" required="" ng-model="data.name">
                                    <div ng-show="errors.name" class="help-inline text-danger form-control-static">@{{errors.name.toString()}}</div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="@lang('home.contactus-email')" id="email" required="" ng-model="data.email">
                                    <div ng-show="errors.email" class="help-inline text-danger form-control-static">@{{errors.email.toString()}}</div>
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control" placeholder="@lang('home.contactus-phone')" id="phone" required="" ng-model="data.phone">
                                    <div ng-show="errors.phone" class="help-inline text-danger form-control-static">@{{errors.phone.toString()}}</div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="@lang('home.contactus-message')" id="message" required="" ng-model="data.message"></textarea>
                                    <div ng-show="errors.message" class="help-inline text-danger form-control-static">@{{errors.message.toString()}}</div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" ng-disabled="contactForm.$invalid || sending" class="btn btn-lg btn-info">@lang('home.contactus-send')</button>
                                    <br/>
                                    <i class="fa fa-spin fa-2x fa-spinner" ng-show="sending"></i>
                                    <div class="alert alert-@{{flashStatus}}" ng-show="flash">
                                        <button type="button" class="close" ng-click="flash='';"><span aria-hidden="true">&times;</span></button>
                                        <span ng-bind="flash"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fa fa-map-marker"></i> Oxobox Argentina</h4>
                                <iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=Manuela%20Pedraza%202174%2C%20Buenos%20Aires%2C%20Argentina&key=AIzaSyD6TWX8L-rTfhu4O6dfmlF-7lHHI1xNNNc"></iframe>

                                <address>
                                    Manuela Pedraza 2174 C1429CCF
                                    <br/>
                                    Buenos Aires, Argentina
                                    <br/>
                                    <i class="fa fa-phone"></i> Tel. +5411 4511-3380/81/82
                                    <br/>
                                    <i class="fa fa-envelope"></i> <a href="mailto:info@oxobox.tv">info@oxobox.tv</a>
                                </address>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('includes.footer')
</div>
